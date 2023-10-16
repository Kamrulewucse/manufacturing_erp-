<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetail;
use App\Models\Product;
use App\Models\ReceiptPayment;
use App\Models\ReceiptPaymentDetail;
use App\Models\ReceiptPaymentFile;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\TransactionLog;
use App\Models\ProductCategory;
use App\Models\PurchasePayment;
use App\Models\PurchaseInventory;
use App\Models\PurchaseInventoryLog;
use App\Models\PurchaseOrderProduct;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function purchaseOrder() {
        $suppliers = Client::where('type',2)->where('status', 1)->orderBy('name')->get();
        $products = Product::where('product_type',2)->where('status',1)->orderBy('name')->get();
        return view('purchase.purchase_order.create', compact('suppliers', 'products'));
    }

    public function purchaseOrderPost(Request $request)
    {

        $rules = [
            'supplier' => 'required',
            'date' => 'required|date',
            'product.*' => 'required|numeric|min:0',
            'quantity.*' => 'required|numeric|min:0',
            'unit_price.*' => 'required|numeric|min:0',
            'notes' => 'nullable',
            'supporting_document' => 'nullable',
            //'cash_account_code' => 'required',
            'paid' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
        ];


        if ($request->payment_type == '2') {
            $rules['cheque_no'] = 'required|string|max:255';
        }
        if ($request->paid > 0){
            $rules['cash_account_code'] = 'required';
            $rules['payment_type'] = 'required';
        }

        $request->validate($rules);

        $order = new PurchaseOrder();
        $order->supplier_id = $request->supplier;
        $order->date = Carbon::parse($request->date)->format('Y-m-d');
        $order->vat_percentage = $request->vat;
        $order->vat = 0;
        $order->discount_percentage = $request->discount_percentage;
        $order->discount = $request->discount;
        $order->paid = $request->paid;
        $order->total = 0;
        $order->due = 0;
        $order->save();
        $order->order_no = 'PO'.str_pad($order->id, 8, 0, STR_PAD_LEFT);
        $order->save();
        $counter = 0;
        $subTotal = 0;

        foreach ($request->product as $reqProduct) {
            $product = Product::where('id', $reqProduct)->first();
            if($product->quantity==0){
                $product->update([
                    'quantity' => $request->quantity[$counter],
                    'unit_price' => $request->unit_price[$counter],
                    'purchase_order_id' => $order->id,
                ]);
            }

            $productPurchaseOrder = PurchaseOrderProduct::create([
                'purchase_order_id' => $order->id,
                'supplier_id' => $request->supplier,
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => $request->quantity[$counter],
                'unit_price' => $request->unit_price[$counter],
                'total' => ($request->quantity[$counter] * $request->unit_price[$counter]),
            ]);


            $subTotal += ($request->quantity[$counter] * $request->unit_price[$counter]);

            $inventory = Inventory::where('product_id',$product->id)
                ->first();
                if ($inventory){
                    $inventory->update([
                        'quantity' => $inventory->quantity + $request->quantity[$counter],
                        'unit_price' => $request->unit_price[$counter],
                    ]);
                }else{
                    $inventory = Inventory::create([
                        'product_id' => $product->id,
                        'product_type' => 2, //row Material
                        'quantity' => $request->quantity[$counter],
                        'unit_price' => $request->unit_price[$counter],
                    ]);
                }

            // Inventory Log
            $log = InventoryLog::create([
                'purchase_order_id' => $order->id,
                'product_id' => $product->id,
                'type' => 1,
                'quantity' => $request->quantity[$counter],
                'unit_price' => $request->unit_price[$counter],
                'total' => $request->quantity[$counter] * $request->unit_price[$counter],
                'supplier_id' => $request->supplier,
                'date' => Carbon::parse($request->date)->format('Y-m-d'),
                'note' => 'Purchase Product',
            ]);

            $log->update([
                'inventory_id' => $inventory->id,
                'serial' => str_pad($inventory->id, 8, 0, STR_PAD_LEFT),
            ]);

            $productPurchaseOrder->update(['purchase_inventory_id' => $inventory->id]);
            $inventory->update([
                'serial' => str_pad($inventory->id, 8, 0, STR_PAD_LEFT),
            ]);

            $productPurchaseOrder->update(['serial' => $inventory->serial]);

            $counter++;
        }


        $total = $subTotal;
        $order->sub_total = $total;
        $order->total = $subTotal-$request->discount;
        $order->due =$subTotal-$request->discount - $request->paid;
        $order->save();

        // Purchase Journal
        $request['financial_year'] = convertDateToFiscalYear($request->date);

        $financialYear = financialYear($request->financial_year);

        $accountHead = AccountHead::where('client_id',$request->supplier)->first();

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
        $journalVoucher->purchase_order_id = $order->id;
        $journalVoucher->payee_depositor_account_head_id = $accountHead->id;
        $journalVoucher->notes = $request->note;
        $journalVoucher->save();

        //Debit->PURCHASE
        $purchaseAccountHead = AccountHead::where('id',91)->first();
        $detail = new JournalVoucherDetail();
        $detail->type = 1;
        $detail->journal_voucher_id = $journalVoucher->id;
        $detail->account_head_id = $purchaseAccountHead->id;
        $detail->amount = $order->total+$request->discount;
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
        $log->account_head_id = $purchaseAccountHead->id;
        $log->amount = $order->total+$request->discount;
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

        if($request->discount > 0){
            $purchaseDiscountHead = AccountHead::where('id',109)->first();
            $detail = new JournalVoucherDetail();
            $detail->type = 2;
            $detail->journal_voucher_id = $journalVoucher->id;
            $detail->account_head_id = $purchaseDiscountHead->id;
            $detail->amount = $request->discount;
            $detail->save();

            //Credit Discount
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
            $log->account_head_id = $purchaseDiscountHead->id;
            $log->amount = $request->discount;
            $log->notes = $request->note;
            $log->save();
        }

        ///Start from here

        if ($request->supporting_document) {
            // Upload Image
            $file = $request->file('supporting_document');

            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/supporting_document';
            $file->move($destinationPath, $filename);
            $path = 'uploads/supporting_document/' . $filename;

            $receiptPaymentFile = new ReceiptPaymentFile();
            $receiptPaymentFile->journal_voucher_id = $journalVoucher->id;
            $receiptPaymentFile->file = $path;
            $receiptPaymentFile->save();

        }

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
                $receiptPayment->payment_account_head_id = $request->cash_account_code;
                $receiptPayment->purchase_order_id = $order->id;
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
                $receiptPayment->purchase_order_id = $order->id;
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

        return redirect()->route('purchase_receipt.details', ['order' => $order->id]);
    }


    public function purchaseReceipt() {
        return view('purchase.receipt.all');
    }

    public function purchaseReceiptEdit(PurchaseOrder $order)
    {
        $suppliers = Supplier::where('status', 1)->orderBy('name')->get();
        $categories = ProductCategory::where('status',1)->get();
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $lcs = Lc::where('status',1)->get();
        $mobileBanks = MobileBanking::where('status', 1)->orderBy('name')->get();

        return view('purchase.receipt.edit', compact('suppliers',
            'categories','banks','lcs','mobileBanks','order'));
    }



    public function purchaseReceiptDetails(PurchaseOrder $order) {
//        dd($order);
        return view('purchase.receipt.details', compact('order'));
    }

    public function purchaseReceiptPrint(PurchaseOrder $order) {
//        dd('hi');
        return view('purchase.receipt.print', compact('order'));
    }

    public function supplierPayment(Request $request) {
        $suppliers = Client::where('type',2)->orderBy('name')->get();
        return view('purchase.supplier_payment.all',compact('suppliers'));
    }

    public function supplierPaymentGetOrders(Request $request) {
        $orders = PurchaseOrder::where('supplier_id', $request->supplierId)
            ->where('due', '>', 0)
            ->where('status', 1)
            ->orderBy('order_no')
            ->get()->toArray();
//        dd($orders);

        return response()->json($orders);
    }

    public function supplierPaymentGetRefundOrders(Request $request) {
        $orders = PurchaseOrder::where('supplier_id', $request->supplierId)
            ->where('refund', '>', 0)
            ->orderBy('order_no')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function supplierPaymentDetails(Client $supplier) {

        $receiptPayments=ReceiptPayment::where('client_id',$supplier->id)->where('client_type',1)->get();

        return view('purchase.receipt.supplier_payment_details',compact('receiptPayments'));
    }


    public function makePayment(Request $request) {


        $rules = [
            'supplier_id' => 'required',
            'payment_type' => 'required',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ];


        if ($request->payment_type == '2') {
            $rules['bank'] = 'required';
            $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }
        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            if ($request->payment_type == 1) {
                $cash = Cash::first();
                if ($request->amount > $cash->amount)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            } else {
                if ($request->account != '') {
                    $account = BankAccount::find($request->account);

                    if ($request->amount > $account->balance)
                        $validator->errors()->add('amount', 'Insufficient balance.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }


        $order =PurchaseOrder::find($request->order);
        $supplier = Supplier::find($request->supplier_id);


        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new PurchasePayment();
            $payment->purchase_order_id = $order->id;
            $payment->supplier_id = $request->supplier_id;
            $payment->type = 1;
            $payment->transaction_method = $request->payment_type;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();


            if ($request->payment_type == 1)
                Cash::first()->decrement('amount', $request->amount);
            else
                MobileBanking::first()->decrement('amount', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Paid to '.$supplier->name??'';
            $log->transaction_type = 3;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 1;
            $log->account_head_sub_type_id = 1;
            $log->amount = $request->amount;
            $log->notes = $request->note;
            $log->supplier_id = $request->supplier_id;
            $log->purchase_payment_id = $payment->id;
            $log->save();


        }else{
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/purchase_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/purchase_payment_cheque/'.$filename;
            }

            $payment = new PurchasePayment();
            $payment->purchase_order_id = null;
            $payment->supplier_id = $request->supplier_id;
            $payment->transaction_method = 2;
            $payment->bank_id = $request->bank;
            $payment->branch_id = $request->branch;
            $payment->bank_account_id = $request->account;
            $payment->cheque_no = $request->cheque_no;
            $payment->cheque_image = $image;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();

            BankAccount::find($request->account)->decrement('balance', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Paid to '.$supplier->name ?? '';
            $log->transaction_type = 3;
            $log->transaction_method = 2;
            $log->account_head_type_id = 1;
            $log->account_head_sub_type_id = 1;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->supplier_id = $request->supplier_id;
            $log->purchase_payment_id = $payment->id;
            $log->save();
        }

        $order->increment('paid', $request->amount);
        $order->decrement('due', $request->amount);

        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('purchase_receipt.payment_details', ['payment' => $payment->id])]);
    }

    public function purchasePaymentDetails(PurchaseOrder $order) {

        return view('purchase.receipt.all_details', compact('order'));
    }

    public function purchasePaymentPrint(PurchasePayment $payment) {
//        $payment->amount_in_word = DecimalToWords::convert($payment->amount,'Taka',
//            'Poisa');
        return view('purchase.receipt.payment_print', compact('payment'));
    }

    public function purchaseReceiptDatatable() {
        $query = PurchaseOrder::with('supplier');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('supplier', function(PurchaseOrder $order) {
                return $order->supplier->name ?? '';
            })
            ->addColumn('action', function(PurchaseOrder $order) {
//                $btn = '<a href="'.route('purchase_receipt.details', ['order' => $order->id]).'" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>
//                        <a href="'.route('purchase_receipt.qr_code', ['order' => $order->id]).'" class="btn btn-success btn-sm">Barcode</a>  ';
//                return $btn;
                $btn  = '<a href="' . route('purchase_receipt.details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a> ';
                if ($order->journalVoucher)
                $btn  .= '<a href="' . route('journal_voucher_details', ['journalVoucher'=>$order->journalVoucher->id]) . '" class="btn btn-dark btn-sm">JV</i></a> ';
                if($order->due > 0)
                $btn  .= '<a class="btn btn-info btn-sm btn-pay" role="button" data-id="'.$order->id.'" data-order="'.$order->order_no.'" data-due="'.$order->due.'">Pay</a> ';

                $btn  .= '<a href="' . route('purchase_receipt.payment_details', ['order' => $order->id]) . '" class="btn btn-warning btn-sm">Details</i></a> ';

               return $btn;
            })
            ->editColumn('date', function(PurchaseOrder $order) {
                return $order->date;
            })
            ->addColumn('quantity', function (PurchaseOrder $order) {
                return $order->quantity() ?? '';
            })
            ->editColumn('total', function(PurchaseOrder $order) {
                return '৳'.number_format($order->total, 2);
            })
            ->editColumn('paid', function(PurchaseOrder $order) {
                return '৳'.number_format($order->paid, 2);
            })
            ->editColumn('due', function(PurchaseOrder $order) {
                return '৳'.number_format($order->due, 2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }


    public function purchaseInventory() {
        return view('purchase.inventory.all');
    }

    public function purchaseInventoryDetails(PurchaseOrderProduct $product) {
        return view('purchase.inventory.details', compact('product'));
    }

    public function purchaseInventoryDatatable() {
        $query = PurchaseInventory::with('product', 'category', 'subcategory');

        return DataTables::eloquent($query)
            ->addColumn('product', function(PurchaseInventory $inventory) {
                return $inventory->product->name??'';
            })
            ->addColumn('category', function(PurchaseInventory $inventory) {
                return $inventory->product->category->name??'';
            })
            ->addColumn('subcategory', function(PurchaseInventory $inventory) {
                return $inventory->product->subcategory->name??'';
            })
            ->addColumn('action', function(PurchaseInventory $inventory) {
                return '<a href="'.route('purchase_inventory.details', ['product' => $inventory->purchase_product_id]).'" class="btn btn-primary btn-sm">Details</a>';
            })
            ->editColumn('quantity', function(PurchaseInventory $inventory) {
                return number_format($inventory->quantity, 2);
            })
            ->editColumn('total', function(PurchaseInventory $inventory) {
                return number_format($inventory->total, 2);
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function purchaseInventoryDetailsDatatable() {
        $query = PurchaseInventoryLog::where('purchase_product_id', request('product_id'))
            ->with('product', 'supplier', 'purchaseOrder');

        return DataTables::eloquent($query)
            ->editColumn('date', function(PurchaseInventoryLog $log) {
                return $log->date;
            })
            ->editColumn('type', function(PurchaseInventoryLog $log) {
                if ($log->type == 1)
                    return '<span class="badge badge-success">In</span>';
                elseif ($log->type == 2)
                    return '<span class="badge badge-danger">Out</span>';
                elseif ($log->type == 3)
                    return '<span class="badge badge-success">Add</span>';
                elseif ($log->type == 4)
                    return '<span class="badge badge-success">Technician Sale</span>';
                elseif ($log->type == 5)
                    return '<span class="badge badge-danger">Return</span>';
                else
                    return '';
            })
            ->editColumn('quantity', function(PurchaseInventoryLog $log) {
                return number_format($log->quantity, 2);
            })
            ->editColumn('selling_price', function(PurchaseInventoryLog $log) {
                return number_format($log->selling_price, 2);
            })
            ->editColumn('total', function(PurchaseInventoryLog $log) {
                return number_format($log->total, 2);
            })
            ->editColumn('unit_price', function(PurchaseInventoryLog $log) {
                if ($log->unit_price)
                    return '৳'.number_format($log->unit_price, 2);
                else
                    return '';
            })
            ->editColumn('supplier', function(PurchaseInventoryLog $log) {
                if ($log->supplier)
                    return $log->supplier->name;
                else
                    return '';
            })
            ->editColumn('purchase_order', function(PurchaseInventoryLog $log) {
                if ($log->purchaseOrder)
                    return '<a href="'.route('purchase_receipt.details', ['order' => $log->purchaseOrder->id]).'">'.$log->purchaseOrder->order_no.'</a>';
                else
                    return '';
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['type', 'order'])
            ->filter(function ($query) {
                if (request()->has('date') && request('date') != '') {
                    $dates = explode(' - ', request('date'));
                    if (count($dates) == 2) {
                        $query->where('date', '>=', $dates[0]);
                        $query->where('date', '<=', $dates[1]);
                    }
                }

                if (request()->has('type') && request('type') != '') {
                    $query->where('type', request('type'));
                }
            })
            ->rawColumns(['action','purchase_order','type'])
            ->toJson();
    }


    public function inventory() {
        return view('purchase.inventory.all');
    }

    public function inventoryDetails(Product $product) {
        return view('purchase.inventory.details', compact('product'));
    }

    public function inventoryDatatable() {

        $query = Inventory::with('product')->where('product_type',2);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('product', function(Inventory $inventory) {
                return $inventory->product->name??'';
            })

            ->addColumn('action', function (Inventory $inventory) {
                return '<a href="' . route('inventory.details', ['product' => $inventory->product_id, 'color' => $inventory->color_id, 'size' => $inventory->size_id]) . '" class="btn btn-primary btn-sm">Details</a>';
            })

            ->editColumn('quantity', function(Inventory $inventory) {
                return number_format($inventory->quantity, 2);
            })
            ->editColumn('total', function(Inventory $inventory) {
                return number_format($inventory->total, 2);
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function inventoryDetailsDatatable() {

        $query = InventoryLog::where('product_id', request('product_id'))
            ->with('product', 'supplier', 'purchaseOrder');



        return DataTables::eloquent($query)
            ->editColumn('date', function(InventoryLog $log) {
                return $log->date;
            })
            ->editColumn('type', function(InventoryLog $log) {
                if ($log->type == 1)
                    return '<span class="badge badge-success">In</span>';
                elseif ($log->type == 2)
                    return '<span class="badge badge-danger">Out</span>';
                else
                    return '';
            })
            ->editColumn('quantity', function(InventoryLog $log) {
                return number_format($log->quantity, 2);
            })
            ->editColumn('selling_price', function(InventoryLog $log) {
                return number_format($log->selling_price, 2);
            })
            ->editColumn('total', function(InventoryLog $log) {
                return number_format($log->total, 2);
            })
            ->editColumn('unit_price', function(InventoryLog $log) {
                if ($log->unit_price)
                    return '৳'.number_format($log->unit_price, 2);
                else
                    return '';
            })
            ->editColumn('supplier', function(InventoryLog $log) {
                if ($log->supplier)
                    return $log->supplier->name??'';
                else
                    return '';
            })
            ->editColumn('purchase_order', function(InventoryLog $log) {
                if ($log->purchaseOrder)
                    return '<a href="'.route('purchase_receipt.details', ['order' => $log->purchaseOrder->id]).'">'.$log->purchaseOrder->order_no.'</a>';
                else
                    return '';
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['type', 'order'])
            ->filter(function ($query) {
                if (request()->has('date') && request('date') != '') {
                    $dates = explode(' - ', request('date'));
                    if (count($dates) == 2) {
                        $query->where('date', '>=', $dates[0]);
                        $query->where('date', '<=', $dates[1]);
                    }
                }

                if (request()->has('type') && request('type') != '') {
                    $query->where('type', request('type'));
                }
            })
            ->rawColumns(['action','purchase_order','type'])
            ->toJson();
    }

    public function purchaseMakePayment(Request $request){

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
            $order = PurchaseOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $order = PurchaseOrder::where('id',$request->order)->first();

        $accountHead = AccountHead::where('client_id',$order->supplier_id)->first();

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
        $receiptPayment->purchase_order_id = $order->id;
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

        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('purchase_receipt.all')]);

    }
    public function supplierMakePayment(Request $request){
        $rules = [
            'payment_type' => 'required',
            'cash_account_code' => 'required',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->payment_type == 1) {
            $rules['cheque_no'] = 'required|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        // Supplier payment block
        $supplier = Client::find($request->supplier_id);

        $orders = PurchaseOrder::where('supplier_id',$supplier->id)
            ->orderBy('created_at','asc')
            ->get();

        $totalDue = $supplier->opening_due;//Modified by Hasan

        foreach ($orders as $order) {
            $totalDue += $order->due;
        }

        $amountToPay = $request->amount;
        $leftAmountToPay = 0;

        if($amountToPay>$totalDue){
            return response()->json(['message' => 'Given Amount is exceed total due.']);
        }else{
            //Firstly paid opening due then paid order due.
            //added by Hasan
            if($supplier->opening_due > 0){
                if ($request->amount > $supplier->opening_due){
                    $supplier->increment('opening_paid',$supplier->opening_due);
                    $amountToPay = $request->amount - $supplier->opening_due;
                }else{
                    $supplier->increment('opening_paid',$request->amount);
                    $amountToPay = 0;
                }
                $supplier->save();
            }
            //End added by Hasan

            foreach($orders as $k=>$order){
                $dueAmount = $order->due;

                if($amountToPay >= $dueAmount){
                    $order->increment('paid', $dueAmount);
                    $order->decrement('due', $dueAmount);

                    $amountToPay -= $dueAmount;

                    $leftAmountToPay = $amountToPay;
                }
                else{
                    if($leftAmountToPay >0){
                        $order->increment('paid', $leftAmountToPay);
                        $order->decrement('due', $leftAmountToPay);
                        break;
                    }
                    else{
                        if($k == 0){
                            $leftAmountToPay = $amountToPay;
                            $order->increment('paid', $leftAmountToPay);
                            $order->decrement('due', $leftAmountToPay);
                        }
                        break;
                    }
                }
            }
        }


        //create dynamic voucher no process start
        $accountHead = AccountHead::where('client_id',$supplier->id)->first();
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
        $receiptPayment->client_id = $supplier->id;
        $receiptPayment->receipt_payment_no = $voucherNo;
        $receiptPayment->financial_year = financialYear($request->financial_year);
        $receiptPayment->date = Carbon::parse($request->date)->format('Y-m-d');
        $receiptPayment->transaction_type = 2;
        $receiptPayment->payment_type = $request->payment_type;
        $receiptPayment->client_type = 1;
        $receiptPayment->cheque_no = $request->payment_type == 1 ? $request->cheque_no : null;
        $receiptPayment->payment_account_head_id = $request->cash_account_code;
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
//        $receiptPaymentDetail->account_head_id = $request->cash_account_code;
        $receiptPaymentDetail->account_head_id = $accountHead->id;//Hasan Ali changed
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

        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('supplier_payment.all')]);
    }
}
