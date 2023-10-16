<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetail;
use App\Models\Product;
use App\Models\ProductReturnOrder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\ReceiptPayment;
use App\Models\ReceiptPaymentDetail;
use App\Models\SalesOrder;
use App\Models\TransactionLog;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PurchaseReturnController extends Controller
{
    public function purchaseReturn(){
        $purchaseOrders = PurchaseOrder::get();
        return view('purchase.purchase_return.create', compact('purchaseOrders'));
    }

    public function purchaseReturnPost(Request $request)
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

        if ($request->payment_type == 1) {
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'required|string|max:255';
            $rules['cheque_date'] = 'required|date';
            $rules['cheque_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            $rules['issuing_bank_name'] = 'nullable';
            $rules['issuing_branch_name'] = 'nullable';
        }

        if ($request->paid > 0) {
            $rules['payment_type'] = 'required';
            $rules['account'] = 'required';
        }

        $request->validate($rules);

        $purchaseOrder = PurchaseOrder::where('id',$request->purchase_order)->first();
        $client = Client::where('id',$purchaseOrder->supplier_id)->first();


        $order = new ProductReturnOrder();
        $order->supplier_id = $client->id;
        $order->purchase_order_id = $purchaseOrder->id;
        $order->date = Carbon::parse($request->date)->format('Y-m-d');
        $order->deduction_amount = $request->deduction_amount;
        $order->deduction_amount_percentage = $request->deduction_amount_percentage;
        $order->total = $request->total;
        $order->paid = $request->paid;
        $order->note = $request->note;
        $order->due = $request->due_total;

        $order->save();
        $order->order_no = 'PR'.str_pad($order->id, 8, 0, STR_PAD_LEFT);
        $order->save();

        $counter = 0;

        if ($request->product) {
            foreach ($request->product as $reqProduct) {
                $inventory = Inventory::where('product_id', $reqProduct)
                    ->where('product_type', 2)
                    ->first();
                $product = Product::where('id',$reqProduct)->first();

                $inventoryLog = new InventoryLog();
                $inventoryLog->purchase_return_order_id = $order->id;
                $inventoryLog->client_id = $client->id;
                $inventoryLog->product_id = $product->id;
                $inventoryLog->product_type = $product->product_type;
                $inventoryLog->product_category_id = $product->category_id;
                $inventoryLog->date = Carbon::parse($request->date)->format('Y-m-d');
                $inventoryLog->type =3; //purchase return
                $inventoryLog->inventory_id = $inventory->id;
                $inventoryLog->quantity = $request->quantity[$counter];
                $inventoryLog->unit_price = $request->unit_price[$counter];
                $inventoryLog->note ='Purchase Return Product';
                $inventoryLog->total =$request->quantity[$counter] * $request->unit_price[$counter];
                $inventoryLog->save();
                $inventoryLog->serial =str_pad($inventoryLog->id, 8, 0, STR_PAD_LEFT);
                $inventoryLog->save();

                $inventory->decrement('quantity', $request->quantity[$counter]);
                if($product->purchase_order_id == $purchaseOrder->id){
                    $product->decrement('quantity', $request->quantity[$counter]);
                }

                $counter++;
            }
        }

        // Purchase Return Journal

        $accountHead = AccountHead::where('client_id',$client->id)->first();

        $request['financial_year'] = convertDateToFiscalYear($request->date);
        $financialYear = financialYear($request->financial_year);

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
        $journalVoucher->purchase_return_order_id = $order->id;
        $journalVoucher->payee_depositor_account_head_id = $accountHead->id;
        $journalVoucher->notes = $request->note;
        $journalVoucher->save();

        //Debit->customer
        $detail = new JournalVoucherDetail();
        $detail->type = 1;
        $detail->journal_voucher_id = $journalVoucher->id;
        $detail->account_head_id = $accountHead->id;
        $detail->amount =$order->total;
        $detail->save();

        //Debit->customer
        $log = new TransactionLog();
        $log->payee_depositor_account_head_id = $accountHead->id;
        $log->date = Carbon::parse($request->date)->format('Y-m-d');
        $log->receipt_payment_no = $jvNo;
        $log->jv_no = $jvNo;
        $log->financial_year = financialYear($request->financial_year);
        $log->jv_type = 1;
        $log->journal_voucher_id = $journalVoucher->id;
        $log->journal_voucher_detail_id = $detail->id;
        $log->transaction_type = 8;//debit
        $log->account_head_id = $accountHead->id;
        $log->amount = $order->total;
        $log->notes = $request->note;
        $log->save();

        //credit->purchase->return
        $purchaseReturnAccountHead = AccountHead::where('id',137)->first();
        $detail = new JournalVoucherDetail();
        $detail->type = 2;
        $detail->journal_voucher_id = $journalVoucher->id;
        $detail->account_head_id = $purchaseReturnAccountHead->id;
        $detail->amount = $order->total+$request->deduction_amount;
        $detail->save();

        //credit->purchase->return
        $log = new TransactionLog();
        $log->payee_depositor_account_head_id = $accountHead->id;
        $log->receipt_payment_no = $jvNo;
        $log->jv_no = $jvNo;
        $log->date = Carbon::parse($request->date)->format('Y-m-d');
        $log->financial_year = financialYear($request->financial_year);
        $log->jv_type = 2;
        $log->journal_voucher_id = $journalVoucher->id;
        $log->journal_voucher_detail_id = $detail->id;
        $log->transaction_type = 9;//credit
        $log->account_head_id = $purchaseReturnAccountHead->id;
        $log->amount = $order->total+$request->deduction_amount;
        $log->notes = $request->note;
        $log->save();


//        deduction_amount
        if($request->deduction_amount > 0){
            $purchaseReturnDeductionAmountHead = AccountHead::where('id',138)->first();
            $detail = new JournalVoucherDetail();
            $detail->type = 1;
            $detail->journal_voucher_id = $journalVoucher->id;
            $detail->account_head_id = $purchaseReturnDeductionAmountHead->id;
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
            $log->transaction_type = 8;//debit
            $log->account_head_id = $purchaseReturnDeductionAmountHead->id;
            $log->amount = $request->deduction_amount;
            $log->notes = $request->note;
            $log->save();
        }


        //Start from here

        if ($request->paid > 0) {
            //create dynamic voucher no process start
            if ($request->payment_type == 2){

                $transactionType = 1;
                $financialYear = $request->financial_year;
                $cashAccountId = null;
                $cashId = $request->account;
                $voucherNo = generateVoucherReceiptNo($financialYear,$cashAccountId,$cashId,$transactionType);

                $receiptPaymentNoExplode = explode("-",$voucherNo);
                $receiptPaymentNoSl = $receiptPaymentNoExplode[1];

                $receiptPayment = new ReceiptPayment();
                $receiptPayment->receipt_payment_no = $voucherNo;
                $receiptPayment->financial_year = financialYear($request->financial_year);
                $receiptPayment->date = Carbon::parse($request->date)->format('Y-m-d');
                $receiptPayment->transaction_type = 1;
                $receiptPayment->payment_type = 2;
                $receiptPayment->payment_account_head_id = $request->account;
                $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
                $receiptPayment->purchase_return_order_id = $order->id;
                $receiptPayment->amount = $request->paid;
                $receiptPayment->notes = $request->note;
                $receiptPayment->save();

                //Cash debit
                $log = new TransactionLog();
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->receipt_payment_no = $receiptPayment->receipt_payment_no;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = $receiptPayment->financial_year;
                $log->date = $receiptPayment->date;
                $log->receipt_payment_id = $receiptPayment->id;
                $log->transaction_type = 14; // Cash Debit
                $log->payment_type = 2;//Cash
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->account_head_id = $request->account;
                $log->amount = $request->paid;
                $log->notes = $receiptPayment->notes;
                $log->save();

                $receiptPaymentDetail = new ReceiptPaymentDetail();
                $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
                $receiptPaymentDetail->account_head_id = $request->account;
                $receiptPaymentDetail->amount = $request->paid;
                $receiptPaymentDetail->net_total = $request->paid;
                $receiptPaymentDetail->save();

                //Receipt Head Amount/Customer Credit
                $log = new TransactionLog();
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 1;
                $log->payment_type = 2;
                $log->account_head_id = $accountHead->id;
                $log->amount = $request->paid;
                $log->notes = $request->note;
                $log->save();

            } else if($request->payment_type == 1){
                //create dynamic voucher no process start
                $transactionType = 1;
                $financialYear = $request->financial_year;
                $bankAccountId = $request->account;
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
                $receiptPayment->payment_account_head_id = $request->account;
                $receiptPayment->cheque_no = $request->cheque_no;
                $receiptPayment->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
                $receiptPayment->issuing_bank_name = $request->issuing_bank_name;
                $receiptPayment->issuing_branch_name = $request->issuing_branch_name;
                $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
                $receiptPayment->purchase_return_order_id = $order->id;
                $receiptPayment->amount = $request->paid;
                $receiptPayment->notes = $request->note;
                $receiptPayment->save();

                //Cash debit
                $log = new TransactionLog();
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->receipt_payment_no = $receiptPayment->receipt_payment_no;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = $receiptPayment->financial_year;
                $log->date = $receiptPayment->date;
                $log->receipt_payment_id = $receiptPayment->id;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
                $log->transaction_type = 13;//Bank Debit
                $log->payment_type = 1;
                $log->account_head_id = $receiptPayment->payment_account_head_id;
                $log->amount = $request->paid;
                $log->notes = $receiptPayment->notes;
                $log->save();

                $receiptPaymentDetail = new ReceiptPaymentDetail();
                $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
                $receiptPaymentDetail->account_head_id = $request->account;
                $receiptPaymentDetail->amount = $request->paid;
                $receiptPaymentDetail->net_total = $request->paid;
                $receiptPaymentDetail->save();

                //Receipt Head Amount/Customer Credit
                $log = new TransactionLog();
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 1;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
                $log->account_head_id = $accountHead->id;
                $log->amount = $request->paid;
                $log->notes = $request->note;
                $log->save();
            }

        }

        return redirect()->route('purchase_return_receipt.details', ['order' => $order->id]);

    }

    public function purchaseReturnReceipt()
    {
        return view('purchase.receipt.purchase_return_receipt');
    }
    public function purchaseReturnReceiptDatatable()
    {
        $query = ProductReturnOrder::with('client');

        return DataTables::eloquent($query)
            ->addColumn('client', function (ProductReturnOrder $order) {
                return $order->client->name ?? '';
            })
            ->addColumn('action', function (ProductReturnOrder $order) {
                $btn = '<a href="' . route('purchase_return_receipt.details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> </a> ';
                if($order->journalVoucher)
                    $btn  .= '<a href="' . route('journal_voucher_details', ['journalVoucher'=>$order->journalVoucher->id]) . '" class="btn btn-dark btn-sm">JV</i></a> ';
                if($order->due > 0){
                    $btn  .= '<a class="btn btn-info btn-sm btn-pay" role="button" data-id="'.$order->id.'" data-order="'.$order->order_no.'" data-due="'.$order->due.'">Payment</a> ';
                }
                $btn  .= '<a href="' . route('purchase_return_payment_all_details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm">Details</i></a> ';

                return $btn;
            })
            ->editColumn('date', function (ProductReturnOrder $order) {
                return $order->date;
            })
            ->editColumn('total', function (ProductReturnOrder $order) {
                return '৳' . number_format($order->total, 2);
            })
            ->editColumn('paid', function (ProductReturnOrder $order) {
                return '৳' . number_format($order->paid, 2);
            })
            ->editColumn('due', function (ProductReturnOrder $order) {
                return '৳' . number_format($order->due, 2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })

            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function purchaseReturnReceiptDetails(ProductReturnOrder $order)
    {
        return view('purchase.receipt.purchase_return_receipt_details', compact('order'));
    }

    public function getPurchaseReturnOrderProduct(Request $request)
    {
        $product = PurchaseOrderProduct::where('purchase_order_id', $request->purchaseOrderId)->get()->toArray();

        return response()->json($product);
    }
    public function getPurchaseReturnDetails(Request $request){
        $product =Product::where('id', $request->productID)->first();

        $unit = Unit::where('id',$product->unit_id)->first();

        $purchaseDetail =PurchaseOrderProduct::where('purchase_order_id', $request->purchaseOrderID)->where('product_id',$request->productID)->first();

        return response()->json([
            'unit'=>$unit,
            'purchaseDetail'=>$purchaseDetail,
        ]);
    }
    public function purchaseReturnPaymentReceipt(Request $request){

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
            $rules['cheque_date'] = 'required|date';
            $rules['issuing_bank_name'] = 'nullable|string|max:255';
            $rules['issuing_branch_name'] = 'nullable|string|max:255';
        }

        if ($request->order != '') {
            $order = ProductReturnOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $order = ProductReturnOrder::where('id',$request->order)->first();
        $accountHead = AccountHead::where('client_id',$order->supplier)->first();

        $request['financial_year'] = convertDateToFiscalYear($request->date);

        //create dynamic voucher no process start
        if ($request->payment_type == 2){

            $transactionType = 1;
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
            $receiptPayment->transaction_type = 1;
            $receiptPayment->payment_type = 2;
            $receiptPayment->payment_account_head_id = $request->cash_account_code;
            $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
            $receiptPayment->purchase_return_order_id	 = $order->id;
            $receiptPayment->amount = $request->amount;
            $receiptPayment->notes = $request->note;
            $receiptPayment->save();

            //Cash debit
            $log = new TransactionLog();
            $log->payee_depositor_account_head_id = $accountHead->id;
            $log->receipt_payment_no = $receiptPayment->receipt_payment_no;
            $log->receipt_payment_sl = $receiptPaymentNoSl;
            $log->financial_year = $receiptPayment->financial_year;
            $log->date = $receiptPayment->date;
            $log->receipt_payment_id = $receiptPayment->id;
            $log->transaction_type = 14; // Cash Debit
            $log->payment_type = 2;//Cash
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

            //Receipt Head Amount/Customer Credit
            $log = new TransactionLog();
            $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
            $log->payee_depositor_account_head_id = $accountHead->id;
            $log->receipt_payment_no = $voucherNo;
            $log->receipt_payment_sl = $receiptPaymentNoSl;
            $log->financial_year = financialYear($request->financial_year);
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_id = $receiptPayment->id;
            $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
            $log->transaction_type = 1;
            $log->payment_type = 2;
            $log->account_head_id = $accountHead->id;
            $log->amount = $request->amount;
            $log->notes = $request->note;
            $log->save();

        } else if($request->payment_type == 1){

            //create dynamic voucher no process start
            $transactionType = 1;
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
            $receiptPayment->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
            $receiptPayment->issuing_bank_name = $request->issuing_bank_name;
            $receiptPayment->issuing_branch_name = $request->issuing_branch_name;
            $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
            $receiptPayment->purchase_return_order_id = $order->id;
            $receiptPayment->amount = $request->amount;
            $receiptPayment->notes = $request->note;
            $receiptPayment->save();

            //Cash debit
            $log = new TransactionLog();
            $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
            $log->payee_depositor_account_head_id = $accountHead->id;
            $log->receipt_payment_no = $receiptPayment->receipt_payment_no;
            $log->receipt_payment_sl = $receiptPaymentNoSl;
            $log->financial_year = $receiptPayment->financial_year;
            $log->date = $receiptPayment->date;
            $log->receipt_payment_id = $receiptPayment->id;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
            $log->transaction_type = 13;//Bank Debit
            $log->payment_type = 1;
            $log->account_head_id = $receiptPayment->payment_account_head_id;
            $log->amount = $request->amount;
            $log->notes = $receiptPayment->notes;
            $log->save();

            $receiptPaymentDetail = new ReceiptPaymentDetail();
            $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
            $receiptPaymentDetail->account_head_id = $request->cash_account_code;
            $receiptPaymentDetail->amount = $request->amount;
            $receiptPaymentDetail->net_total = $request->amount;
            $receiptPaymentDetail->save();

            //Receipt Head Amount/Customer Credit
            $log = new TransactionLog();
            $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
            $log->payee_depositor_account_head_id = $accountHead->id;
            $log->receipt_payment_no = $voucherNo;
            $log->receipt_payment_sl = $receiptPaymentNoSl;
            $log->financial_year = financialYear($request->financial_year);
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_id = $receiptPayment->id;
            $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
            $log->transaction_type = 2;
            $log->payment_type = 1;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
            $log->account_head_id = $accountHead->id;
            $log->amount = $request->amount;
            $log->notes = $request->note;
            $log->save();
        }

        $order->increment('paid',$request->amount);
        $order->decrement('due',$request->amount);

        return response()->json(['success' => true, 'message' => 'Receipt has been completed.', 'redirect_url' => route('purchase_return_receipt.all')]);
    }
    public function purchaseReturnPaymentReceiptAll(ProductReturnOrder $order)
    {
        return view('purchase.receipt.purchase_return_all_details', compact('order'));
    }
}
