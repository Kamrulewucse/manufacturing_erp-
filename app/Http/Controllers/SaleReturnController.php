<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetail;
use App\Models\Product;
use App\Models\ReceiptPaymentFile;
use App\Models\SaleProductReturnOrder;
use App\Models\ReceiptPayment;
use App\Models\ReceiptPaymentDetail;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\TransactionLog;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class SaleReturnController extends Controller
{
    public function saleReturnReceipt()
    {
        return view('sale.receipt.sale_return_receipt');
    }
    public function saleReturn(){
        $salesOrders = SalesOrder::get();
        return view('sale.sale_return.create', compact('salesOrders'));
    }

    public function saleReturnPost(Request $request)
    {
//        return($request->all());

        $rules = [
            'date' => 'required|date',
            'note' => 'nullable|string',
            'product.*' => 'required',
            'quantity.*' => 'required|numeric|min:.01',
            'deduction_amount' => 'required|numeric|min:0',
            'deduction_amount_percentage' => 'nullable|numeric|min:0',
        ];

        if ($request->payment_type == '2') {
            $rules['cheque_no'] = 'required|string|max:255';
        }
        if ($request->paid > 0){
            $rules['cash_account_code'] = 'required';
            $rules['payment_type'] = 'required';
        }

        $request->validate($rules);



        $saleOrder = SalesOrder::where('id',$request->sale_order)->first();
        $client = Client::where('id',$saleOrder->client_id)->first();


        $order = new SaleProductReturnOrder();
        $order->customer_id = $client->id;
        $order->sale_order_id = $saleOrder->id;
        $order->date = Carbon::parse($request->date)->format('Y-m-d');
        $order->revert_discount = $request->revert_discount;
        $order->deduction_amount = $request->deduction_amount;
        $order->deduction_amount_percentage = $request->deduction_amount_percentage;
        $order->total = $request->total;
        $order->note = $request->note;
        if($request->net_payable<0){
            $order->paid = $request->total;
            $order->due = 0;
        }else{
            $order->paid = $request->paid;
            $order->due = $request->due_total;
        }

        $order->save();
        $order->order_no = 'SR'.str_pad($order->id, 8, 0, STR_PAD_LEFT);
        $order->save();

        $counter = 0;

        if ($request->product) {
            foreach ($request->product as $reqProduct) {
                $inventory = Inventory::where('serial',$request->serial[$counter])
                    ->where('product_type', 3)
                    ->first();
                $inventoryOne = Inventory::where('product_id',$inventory->product_id)
                    ->where('product_type', 1)
                    ->first();
                $product = Product::where('id',$inventory->product_id)->first();

                $inventoryLog = new InventoryLog();
                $inventoryLog->sale_product_return_order_id = $order->id;
                $inventoryLog->client_id = $client->id;
                $inventoryLog->product_id = $product->id;
                $inventoryLog->serial_no = $inventory->serial;
                $inventoryLog->product_type = $product->product_type;
                $inventoryLog->product_category_id = $product->category_id;
                $inventoryLog->date = Carbon::parse($request->date)->format('Y-m-d');
                $inventoryLog->type =4; //Sale return
                $inventoryLog->inventory_id = $inventory->id;
                $inventoryLog->quantity = $request->quantity[$counter];
                $inventoryLog->unit_price = $request->unit_price[$counter];
                $inventoryLog->note ='Sale Return Product';
                $inventoryLog->total =$request->quantity[$counter] * $request->unit_price[$counter];
                $inventoryLog->save();
                $inventoryLog->serial =str_pad($inventoryLog->id, 8, 0, STR_PAD_LEFT);
                $inventoryLog->save();


                $inventory->quantity = 1;
                $inventoryOne->quantity = $inventoryOne->quantity + 1;
                $inventory->save();
                $inventoryOne->save();

                $counter++;
            }
        }



        if($request->net_payable<0){
            $netPayable = abs($request->net_payable);
            $saleOrder->due = $netPayable;
            $saleOrder->paid = $saleOrder->paid+ $request->total;
            $saleOrder->return_status = 1;
            $saleOrder->save();
        }else{
            $netPayable = abs($request->net_payable);
            $saleOrder->due = 0;
            $saleOrder->paid = $saleOrder->total;
            $saleOrder->return_amount = $netPayable;
            $saleOrder->return_status = 1;
            $saleOrder->save();
        }




        // Sale Return Journal


        $request['financial_year'] = convertDateToFiscalYear($request->date);

        $financialYear = financialYear($request->financial_year);

        $accountHead = AccountHead::where('client_id',$client->id)->first();

        $journalVoucherCheck = JournalVoucher::where('financial_year',$financialYear)
            ->orderBy('id','desc')->first();

        if ($journalVoucherCheck){
            $getJVLastNo = explode("-",$journalVoucherCheck->jv_no);
            $jvNo = 'JV-'.($getJVLastNo[1]+1);
        }else{
            $jvNo = 'JV-1';
        }

        $journalVoucher = new JournalVoucher();
        $journalVoucher->jv_no = $jvNo;
        $journalVoucher->financial_year = financialYear($request->financial_year);
        $journalVoucher->date = Carbon::parse($request->date)->format('Y-m-d');
        $journalVoucher->sale_product_return_order_id = $order->id;
        $journalVoucher->payee_depositor_account_head_id = $accountHead->id;
        $journalVoucher->notes = $request->note;
        $journalVoucher->save();

        //Debit->PURCHASE
        $saleReturnAccountHead = AccountHead::where('id',139)->first();
        $detail = new JournalVoucherDetail();
        $detail->type = 1;
        $detail->journal_voucher_id = $journalVoucher->id;
        $detail->account_head_id = $saleReturnAccountHead->id;
        $detail->amount = $order->total+$request->deduction_amount;
        $detail->save();

        //Debit->PURCHASE
        $log = new TransactionLog();
        $log->payee_depositor_account_head_id = $accountHead->id;
        $log->receipt_payment_no = $jvNo;
        $log->jv_no = $jvNo;
        $log->date = Carbon::parse($request->date)->format('Y-m-d');
        $log->financial_year = financialYear($request->financial_year);
        $log->jv_type = 1;
        $log->journal_voucher_id = $journalVoucher->id;
        $log->journal_voucher_detail_id = $detail->id;
        $log->transaction_type = 8;//debit
        $log->account_head_id = $saleReturnAccountHead->id;
        $log->amount = $order->total+$request->deduction_amount;
        $log->notes = $request->note;
        $log->save();
        //Credit->supplier
        $detail = new JournalVoucherDetail();
        $detail->type = 2;
        $detail->journal_voucher_id = $journalVoucher->id;
        $detail->account_head_id = $accountHead->id;
        $detail->amount = $order->total;
        $detail->save();

        //Credit->supplier
        $log = new TransactionLog();
        $log->payee_depositor_account_head_id = $accountHead->id;
        $log->date = Carbon::parse($request->date)->format('Y-m-d');
        $log->receipt_payment_no = $jvNo;
        $log->jv_no = $jvNo;
        $log->financial_year = financialYear($request->financial_year);
        $log->jv_type = 2;
        $log->journal_voucher_id = $journalVoucher->id;
        $log->journal_voucher_detail_id = $detail->id;
        $log->transaction_type = 9;//credit
        $log->account_head_id = $accountHead->id;
        $log->amount = $order->total;
        $log->notes = $request->note;
        $log->save();


//        if($request->revert_discount > 0){
//            $saleDiscountHead = AccountHead::where('id',127)->first();
//            $detail = new JournalVoucherDetail();
//            $detail->type = 1;
//            $detail->journal_voucher_id = $journalVoucher->id;
//            $detail->account_head_id = $saleDiscountHead->id;
//            $detail->amount = $request->revert_discount;
//            $detail->save();
//
//            //Credit deduction_amount
//            $log = new TransactionLog();
//            $log->payee_depositor_account_head_id = $accountHead->id;
//            $log->date = Carbon::parse($request->date)->format('Y-m-d');
//            $log->receipt_payment_no = $jvNo;
//            $log->jv_no = $jvNo;
//            $log->financial_year = financialYear($request->financial_year);
//            $log->jv_type = 1;
//            $log->journal_voucher_id = $journalVoucher->id;
//            $log->journal_voucher_detail_id = $detail->id;
//            $log->transaction_type = 9;//debit
//            $log->account_head_id = $saleDiscountHead->id;
//            $log->amount = $request->revert_discount;
//            $log->notes = $request->note;
//            $log->save();
//        }

        if($request->deduction_amount > 0){
            $saleDeductionAmountHead = AccountHead::where('id',140)->first();
            $detail = new JournalVoucherDetail();
            $detail->type = 2;
            $detail->journal_voucher_id = $journalVoucher->id;
            $detail->account_head_id = $saleDeductionAmountHead->id;
            $detail->amount = $request->deduction_amount;
            $detail->save();

            //Credit deduction_amount
            $log = new TransactionLog();
            $log->payee_depositor_account_head_id = $accountHead->id;
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_no = $jvNo;
            $log->jv_no = $jvNo;
            $log->financial_year = financialYear($request->financial_year);
            $log->jv_type = 2;
            $log->journal_voucher_id = $journalVoucher->id;
            $log->journal_voucher_detail_id = $detail->id;
            $log->transaction_type = 9;//debit
            $log->account_head_id = $saleDeductionAmountHead->id;
            $log->amount = $request->deduction_amount;
            $log->notes = $request->note;
            $log->save();
        }

        ///Start from here
        if ($request->paid > 0){
            if ($request->payment_type == 1){
                //create dynamic voucher no process start
                $transactionType = 2;
                $financialYear = $request->financial_year;
                $cashAccountId = null;
                $cashId = $request->cash_account_code;
                $voucherNo = generateVoucherReceiptNo($financialYear,$cashAccountId,$cashId,$transactionType);
                $receiptPaymentNoExplode = explode("-",$voucherNo);
                $receiptPaymentNoSl = $receiptPaymentNoExplode[1];

                $receiptPayment = new ReceiptPayment();
                $receiptPayment->receipt_payment_no = $voucherNo;
                $receiptPayment->financial_year = financialYear($request->financial_year);
                $receiptPayment->date = Carbon::parse($request->date)->format('Y-m-d');
                $receiptPayment->transaction_type = 2;
                $receiptPayment->payment_type = 2;
                $receiptPayment->type = 2; //sale return type
                $receiptPayment->payment_account_head_id = $request->cash_account_code;
                $receiptPayment->sale_product_return_order_id = $order->id;
                $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
                $receiptPayment->amount = $request->paid;
                $receiptPayment->notes = $request->note;
                $receiptPayment->save();

                //Cash Credit
                $log = new TransactionLog();
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->receipt_payment_no = $receiptPayment->receipt_payment_no;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = $receiptPayment->financial_year;
                $log->date = $receiptPayment->date;
                $log->receipt_payment_id = $receiptPayment->id;
                $log->transaction_type = 12;//Cash Credit
                $log->payment_type = 2;//Cash
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->account_head_id = $request->cash_account_code;
                $log->amount = $request->paid;
                $log->notes = $receiptPayment->notes;
                $log->save();

                $receiptPaymentDetail = new ReceiptPaymentDetail();
                $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
                $receiptPaymentDetail->account_head_id = $request->cash_account_code;
                $receiptPaymentDetail->amount = $request->paid;
                $receiptPaymentDetail->net_total = $request->paid;
                $receiptPaymentDetail->save();

                //Account Head Amount ->Debit
                $log = new TransactionLog();
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 2;
                $log->payment_type = 2;
                $log->payment_account_head_id = $request->cash_account_code;
                $log->account_head_id = $accountHead->id;
                $log->amount = $request->paid;
                $log->notes = $request->note;
                $log->save();
            }elseif ($request->payment_type == 2){
                //create dynamic voucher no process start
                $transactionType = 2;
                $financialYear = $request->financial_year;
                $bankAccountId = $request->cash_account_code;
                $cashId = null;
                $voucherNo = generateVoucherReceiptNo($financialYear, $bankAccountId, $cashId, $transactionType);
                //create dynamic voucher no process end
                $receiptPaymentNoExplode = explode("-", $voucherNo);

                $receiptPaymentNoSl = $receiptPaymentNoExplode[1];

                $receiptPayment = new ReceiptPayment();
                $receiptPayment->receipt_payment_no = $voucherNo;
                $receiptPayment->financial_year = financialYear($request->financial_year);
                $receiptPayment->date = Carbon::parse($request->date)->format('Y-m-d');
                $receiptPayment->transaction_type = 2;
                $receiptPayment->payment_type = 1;
                $receiptPayment->payment_account_head_id = $request->cash_account_code;
                $receiptPayment->cheque_no = $request->cheque_no;
                $receiptPayment->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $receiptPayment->sale_product_return_order_id = $order->id;
                $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
                $receiptPayment->amount = $request->paid;
                $receiptPayment->notes = $request->note;
                $receiptPayment->save();

                //Bank Credit
                $log = new TransactionLog();
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->receipt_payment_no = $receiptPayment->receipt_payment_no;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = $receiptPayment->financial_year;
                $log->date = $receiptPayment->date;
                $log->receipt_payment_id = $receiptPayment->id;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->cheque_no = $request->cheque_no;
                $log->transaction_type = 11;//Bank Credit
                $log->payment_type = 1;
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->account_head_id = $request->cash_account_code;
                $log->amount = $request->paid;
                $log->notes = $receiptPayment->notes;
                $log->save();

                $receiptPaymentDetail = new ReceiptPaymentDetail();
                $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
                $receiptPaymentDetail->account_head_id = $request->cash_account_code;
                $receiptPaymentDetail->amount = $request->paid;
                $receiptPaymentDetail->net_total = $request->paid;
                $receiptPaymentDetail->save();

                //Account Head Amount->Debit
                $log = new TransactionLog();
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 2;
                $log->payment_type = 1;
                $log->payment_account_head_id = $request->cash_account_code;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $accountHead->id;
                $log->amount = $request->paid;
                $log->notes = $request->note;
                $log->save();
            }
        }

        return redirect()->route('sale_return_receipt.details', ['order' => $order->id]);

    }


    public function saleReturnReceiptDatatable()
    {
        $query = SaleProductReturnOrder::with('client');

        return DataTables::eloquent($query)
            ->addColumn('client', function (SaleProductReturnOrder $order) {
                return $order->client->name ?? '';
            })
            ->addColumn('action', function (SaleProductReturnOrder $order) {
                $btn = '<a href="' . route('sale_return_receipt.details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> </a> ';
                if($order->journalVoucher)
                    $btn  .= '<a href="' . route('journal_voucher_details', ['journalVoucher'=>$order->journalVoucher->id]) . '" class="btn btn-dark btn-sm">JV</i></a> ';
                if($order->due > 0){
                    $btn  .= '<a class="btn btn-info btn-sm btn-pay" role="button" data-id="'.$order->id.'" data-order="'.$order->order_no.'" data-due="'.$order->due.'">Payment</a> ';
                }
                $btn  .= '<a href="' . route('sale_return_payment_all_details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm">Details</i></a> ';

                return $btn;
            })
            ->editColumn('date', function (SaleProductReturnOrder $order) {
                return $order->date;
            })
            ->editColumn('total', function (SaleProductReturnOrder $order) {
                return '৳' . number_format($order->total, 2);
            })
            ->editColumn('paid', function (SaleProductReturnOrder $order) {
                return '৳' . number_format($order->paid, 2);
            })
            ->editColumn('due', function (SaleProductReturnOrder $order) {
                return '৳' . number_format($order->due, 2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })

            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function saleReturnReceiptDetails(SaleProductReturnOrder $order)
    {
        return view('sale.receipt.sale_return_receipt_details', compact('order'));
    }

    public function getSaleReturnOrderProduct(Request $request)
    {
        $products = SalesOrderProduct::where('sales_order_id', $request->saleOrderId)->get()->toArray();
        $saleOrder = SalesOrder::where('id', $request->saleOrderId)->first();

        $data = [
            'products' => $products,
            'saleOrder' => $saleOrder,
        ];

        return response()->json($data);
    }
    public function getSaleReturnDetails(Request $request){

        $purchaseDetail =SalesOrderProduct::where('sales_order_id', $request->saleOrderId)->where('serial',$request->productSerial)->first();
        $product =Product::where('id', $purchaseDetail->product_id)->first();

        $unit = Unit::where('id',$product->unit_id)->first();



        return response()->json([
            'unit'=>$unit,
            'purchaseDetail'=>$purchaseDetail,
        ]);
    }
    public function saleReturnPaymentReceipt(Request $request){

            $rules = [
                'order' => 'required',
                'payment_type' => 'required',
                'cash_account_code' => 'required',
                'amount' => 'required|numeric|min:1',
                'date' => 'required|date',
                'note' => 'nullable|string|max:255',
            ];

            if ($request->payment_type == 1) {
                $rules['cheque_no'] = 'required|string|max:255';
            }
            if ($request->order != '') {
                $order = SaleProductReturnOrder::find($request->order);
                $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $order = SaleProductReturnOrder::where('id',$request->order)->first();

            $accountHead = AccountHead::where('client_id',$order->customer_id)->first();

            $request['financial_year'] = convertDateToFiscalYear($request->date);

            //create dynamic voucher no process start
            $transactionType = 2;
            $financialYear = $request->financial_year;
            $bankAccountId = $request->payment_type == 1 ? $request->cash_account_code : null;
            $cashId = $request->payment_type == 2 ? $request->cash_account_code : null;

            $voucherNo = generateVoucherReceiptNo($financialYear,$bankAccountId,$cashId,$transactionType);
            $receiptPaymentNoExplode = explode("-",$voucherNo);
            $receiptPaymentNoSl = $receiptPaymentNoExplode[1];

            $receiptPayment = new ReceiptPayment();
            $receiptPayment->receipt_payment_no = $voucherNo;
            $receiptPayment->financial_year = financialYear($request->financial_year);
            $receiptPayment->date = Carbon::parse($request->date)->format('Y-m-d');
            $receiptPayment->transaction_type = 2;
            $receiptPayment->payment_type = $request->payment_type;
            $receiptPayment->cheque_no = $request->payment_type == 1 ? $request->cheque_no : null;
            $receiptPayment->payment_account_head_id = $request->cash_account_code;
            $receiptPayment->sale_product_return_order_id = $order->id;
            $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
            $receiptPayment->amount = $request->amount;
            $receiptPayment->notes = $request->note;
            $receiptPayment->save();

            // Bank/Cash Credit
            $log = new TransactionLog();
            $log->payee_depositor_account_head_id = $accountHead->id;
            $log->receipt_payment_no = $receiptPayment->receipt_payment_no;
            $log->receipt_payment_sl = $receiptPaymentNoSl;
            $log->financial_year = $receiptPayment->financial_year;
            $log->date = $receiptPayment->date;
            $log->receipt_payment_id = $receiptPayment->id;
            $log->cheque_no = $request->payment_type == 1 ? $request->cheque_no : null;        $log->transaction_type = $request->payment_type == 1 ? 11 :  12;//Cash,bank Credit
            $log->payment_type = $request->payment_type;//Cash,bank
            $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
            $log->account_head_id = $request->cash_account_code;
            $log->amount = $request->amount;
            $log->notes = $receiptPayment->notes;
            $log->save();

            $receiptPaymentDetail = new ReceiptPaymentDetail();
            $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
            $receiptPaymentDetail->account_head_id = $request->cash_account_code;
            $receiptPaymentDetail->amount = $request->amount;
            $receiptPaymentDetail->net_total = $request->amount;
            $receiptPaymentDetail->save();

            //Account Head Amount ->Debit
            $log = new TransactionLog();
            $log->receipt_payment_no = $voucherNo;
            $log->receipt_payment_sl = $receiptPaymentNoSl;
            $log->financial_year = financialYear($request->financial_year);
            $log->payee_depositor_account_head_id = $accountHead->id;
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_id = $receiptPayment->id;
            $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
            $log->transaction_type = 2;
            $log->payment_type = $request->payment_type;
            $log->cheque_no = $request->payment_type == 1 ? $request->cheque_no : null;
            $log->payment_account_head_id = $request->cash_account_code;
            $log->account_head_id = $accountHead->id;
            $log->amount = $request->amount;
            $log->notes = $request->note;
            $log->save();

            $order->increment('paid',$request->amount);
            $order->decrement('due',$request->amount);

            return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('sale_return_receipt.all')]);

        }
    public function saleReturnPaymentReceiptAll(SaleProductReturnOrder $order)
    {
        return view('sale.receipt.sale_return_all_details', compact('order'));
    }
}
