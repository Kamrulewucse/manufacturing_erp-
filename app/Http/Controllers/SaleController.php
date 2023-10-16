<?php

namespace App\Http\Controllers;

use App\Enumeration\Role;
use App\Models\AccountHead;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Client;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetail;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\PurchaseInventory;
use App\Models\PurchaseInventoryLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseProduct;
use App\Models\ReceiptPayment;
use App\Models\ReceiptPaymentDetail;
use App\Models\ReceiptPaymentFile;
use App\Models\SalePayment;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{
    public function salesOrder()
    {
        $customers = Client::where('status', 1)->where('type',1)->orderBy('name')->get();
        $categories = ProductCategory::where('status', 1)->orderBy('name')->get();
        $bookings = Booking::where('status', 2)->where('quantity', '>', 0)->get();

        return view('sale.sales_order.create', compact(
            'customers',
            'categories','bookings'
        ));
    }

    public function  salesOrderPost(Request $request)
    {
//        return($request->all());

        $total = $request->total;
        $due = $request->due_total;

        $rules = [
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'product.*' => 'required',
            'quantity.*' => 'required|numeric|min:.01',
            'selling_price.*' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'advance_deduct' => 'nullable|numeric|min:0',
            'received_by' => 'nullable|max:255',
        ];


        if ($request->sale_type == 2) {
            $rules['booking'] = 'required';
        }

        if ($request->sale_type == 1) {
            $rules['customer'] = 'required';
        }

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

        if($request->sale_type == 1){
            $accountHead = AccountHead::where('client_id',$request->customer)->first();
            $client = Client::find($request->customer);
        }else if($request->sale_type == 2){
            $booking = Booking::where('id', $request->booking)->first();
            $client = Client::find($booking->customer_id);
            $accountHead = AccountHead::where('client_id',$client->id)->first();
        }



        $order = new SalesOrder();
        $order->client_id = $client->id;
        $order->date = Carbon::parse($request->date)->format('Y-m-d');
        $order->sub_total = 0;
        $order->note = $request->note;
        $order->received_by = $request->received_by;
        $order->discount = $request->discount;
        $order->advance_total = $request->advance_deduct_normal;
        $order->discount_percentage = $request->discount_percentage;
        $order->total = 0;
        $order->paid = $request->paid;
        $order->sale_type = $request->sale_type;
        $order->due = 0;

        $order->save();
        $order->order_no = 'SO'.str_pad($order->id, 8, 0, STR_PAD_LEFT);
        $order->save();

        $counter = 0;
        $subTotal = 0;
        $quantity = 0;

        if ($request->product) {
            foreach ($request->product as $reqProduct) {
                $inventory = Inventory::where('id', $reqProduct)
                    ->where('product_type', 3)
                    ->first();

                $totalInventory = Inventory::where('product_id', $inventory->product_id)
                    ->where('product_type', 1)
                    ->first();

                $product = Product::where('id',$inventory->product_id)->first();

                SalesOrderProduct::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'product_type' => $product->product_type,
                    'product_category_id' => $product->category_id,
                    'serial' => $inventory->serial,
                    'warranty' => $product->warranty,
                    'date' => Carbon::parse($request->date)->format('Y-m-d'),
                    'client_id' => $client->id,
                    'quantity' => 1,
                    'unit_price' => $request->unit_price[$counter],
                    'selling_price' => $request->selling_price[$counter],
                    'total' => 1 * $request->selling_price[$counter],
                ]);

                $inventoryLog = new InventoryLog();
                $inventoryLog->product_id = $product->id;
                $inventoryLog->type = 2;
                $inventoryLog->date = Carbon::parse($request->date)->format('Y-m-d');
                $inventoryLog->product_type = $inventory->type;
                $inventoryLog->quantity = 1;
                $inventoryLog->unit_price = $inventory->unit_price;
                $inventoryLog->selling_price = $request->selling_price[$counter];
                $inventoryLog->total = $request->selling_price[$counter] * 1;
                $inventoryLog->client_id = $client->id;
                $inventoryLog->inventory_id = $inventory->id;
                $inventoryLog->sales_order_id = $order->id;
                $inventoryLog->sales_order_no = str_pad($order->id, 8, 0, STR_PAD_LEFT);
                $inventoryLog->save();

                $inventory->decrement('quantity', 1);
                $totalInventory->decrement('quantity', 1);

                if($request->sale_type == 2){
                    $bookingDetail = BookingDetail::where('booking_id',$booking->id)->where('product_id',$product->id)->where('status',3)->first();
                   if($bookingDetail){
                       if($bookingDetail->delivery_quantity == 0) {
                           $bookingDetail->increment('delivery_quantity', 1);
                       }
                   }
                }

                $subTotal += 1 * $request->selling_price[$counter];
                $quantity += 1 ;

                $counter++;
            }
        }
        $total = $subTotal;
        $order->sub_total = $total;
        $order->total = $subTotal - $request->discount-$request->advance_deduct;
        $order->due =$subTotal-$request->discount-$request->paid-$request->advance_deduct-$request->advance_deduct_normal;

        $order->save();

        if($request->advance_deduct_normal > 0) {
            $client = Client::find($request->customer);
            $client->decrement('advance_amount', $request->advance_deduct_normal);
            $client->save();
        }

        if($request->sale_type == 2){
            $booking->increment('delivery_quantity', $quantity);
            if($booking->advance_amount > 0) {
                if ($request->advance_deduct<= $subTotal) {
                    $booking->decrement('advance_amount', $request->advance_deduct);
                    $order->booking_id = $booking->id;
                    $order->advance_total = $request->advance_deduct;
                    $order->due = $subTotal -$request->discount - $request->advance_deduct - $request->paid;
                    $order->save();
                }
                else {
                    $booking->decrement('advance_amount', $subTotal);
                    $order->booking_id = $booking->id;
                    $order->advance_total = $subTotal;
                    $order->due = 0;
                    $order->save();
                }
            }

        }

        // Sales Journal

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
        $journalVoucher->sales_order_id = $order->id;
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

        //credit->sales->Finish Product
        $salesAccountHead = AccountHead::where('id',92)->first();
        $detail = new JournalVoucherDetail();
        $detail->type = 2;
        $detail->journal_voucher_id = $journalVoucher->id;
        $detail->account_head_id = $salesAccountHead->id;
        $detail->amount = $order->sub_total;
        $detail->save();

        //credit->sales->Finish Product
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
        $log->account_head_id = $salesAccountHead->id;
        $log->amount = $order->sub_total;
        $log->notes = $request->note;
        $log->save();


        //advance adjustment
        if($request->advance_deduct > 0){
            $advanceAdjustmentHead = AccountHead::where('id',135)->first();
            $detail = new JournalVoucherDetail();
            $detail->type = 1;
            $detail->journal_voucher_id = $journalVoucher->id;
            $detail->account_head_id = $advanceAdjustmentHead->id;
            $detail->amount = $request->advance_deduct;
            $detail->save();


            //Debit adjustment
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
            $log->account_head_id = $advanceAdjustmentHead->id;
            $log->amount = $request->advance_deduct;
            $log->notes = $request->note;
            $log->save();
        }

        //discount
        if($request->discount > 0){
            $salesDiscountHead = AccountHead::where('id',127)->first();
            $detail = new JournalVoucherDetail();
            $detail->type = 1;
            $detail->journal_voucher_id = $journalVoucher->id;
            $detail->account_head_id = $salesDiscountHead->id;
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
            $log->transaction_type = 8;//debit
            $log->account_head_id = $salesDiscountHead->id;
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
                $receiptPayment->sales_order_id = $order->id;
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
                $receiptPayment->sales_order_id = $order->id;
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

        if($request->sale_type == 2) {
            $booking = Booking::where('id', $request->booking)->first();
            if($booking->quantity <= ($booking->delivery_quantity+$booking->cancel_quantity)){
                $booking->status= 3;
                $booking->save();
            }
        }

        return redirect()->route('sale_receipt.details', ['order' => $order->id]);
    }

    public function saleReceipt()
    {
        return view('sale.receipt.all');
    }

    public function saleJournalDetails(SalesOrder $order){

        $inWordAmount = new DecimalToWords();

        $journalVoucher = JournalVoucher::where('sales_order_id',$order->id)
            ->with('journalVoucherDebitDetails','journalVoucherCreditDetails')
            ->first();

        return view('sale.receipt.sale_journal_voucher',compact('inWordAmount','journalVoucher'));
    }


    public function makePayment(Request $request) {

        $rules = [
            'financial_year' => 'required',
            'order' => 'required',
            'payment_type' => 'required',
            'account' => 'required',
            'date' => 'required|date',
            'next_date' => 'nullable|date',
            'note' => 'nullable|string|max:255',
        ];
        if ($request->payment_step_no < 4){
            $rules['amount'] = 'required|numeric|min:1';
        }

        if ($request->payment_type == 1) {
            $rules['cheque_no'] = 'required';
            $rules['cheque_date'] = 'required|date';
        }
        if ($request->payment_step_no == 3){
            $rules['installments'] = 'required|integer|min:1';
        }

       $order =  SalesOrder::where('id',$request->order)->first();

        $customer = Customer::find($request->customer_id);


        if ($request->order_no != '') {
            $rules['amount'] = 'required|numeric|min:0|max:' . $order->due;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $payAmount = $request->amount ?? 0;


        $order->increment('paid', $payAmount);
        $order->decrement('due',$payAmount);

        //create dynamic voucher no process start
        $transactionType = 1;
        $financialYear = $request->financial_year;
        $cashBankAccountHeadId = $request->account;
        $payType = $request->payment_type;
        $voucherNo = generateVoucherReceiptNo($financialYear,$cashBankAccountHeadId,$transactionType,$payType);
        //create dynamic voucher no process end
        $receiptPaymentNoExplode = explode("-",$voucherNo);

        $receiptPaymentNoSl = $receiptPaymentNoExplode[1];
        $receiptPayment = new ReceiptPayment();

        $receiptPayment->receipt_payment_no = $voucherNo;
        $receiptPayment->financial_year = financialYear($request->financial_year);
        $receiptPayment->date = Carbon::parse($request->date)->format('Y-m-d');
        $receiptPayment->transaction_type = 1;
        $receiptPayment->payment_type = $request->payment_type;//cash == 2,bank =1

        $receiptPayment->account_head_id = $request->account;
        $receiptPayment->cheque_no = $request->cheque_no;
        if ($request->payment_type == 1){
            $receiptPayment->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
            $receiptPayment->issuing_bank_name = $request->issuing_bank_name;
            $receiptPayment->issuing_branch_name = $request->issuing_branch_name;
        }
        $receiptPayment->customer_id = $customer->id;
        $receiptPayment->sub_total = $payAmount;
        $receiptPayment->net_amount = $payAmount;
        $receiptPayment->sales_order_id = $order->id;
        $receiptPayment->notes = $request->note;
        $receiptPayment->save();

        //Bank/Cash Debit
        $log = new TransactionLog();
        $log->notes = $request->note;
        $log->receipt_payment_no = $receiptPayment->receipt_payment_no;
        $log->receipt_payment_sl = $receiptPaymentNoSl;
        $log->financial_year = $receiptPayment->financial_year;
        $log->customer_id = $receiptPayment->customer_id;
        $log->date = $receiptPayment->date;
        $log->receipt_payment_id = $receiptPayment->id;
        if($request->payment_type == 1){
            $log->cheque_no = $request->cheque_no;
            $log->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');

        }
        $log->transaction_type = 2;//Bank debit,Cash debit

        $log->payment_type = $request->payment_type;
        $log->account_head_id = $request->account;
        $log->amount = $receiptPayment->net_amount;
        $log->notes = $receiptPayment->notes;
        $log->sales_order_id = $order->id;
        $log->save();

        $receiptPaymentDetail = new ReceiptPaymentDetail();
        $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
        $receiptPaymentDetail->account_head_id = 9;
        $receiptPaymentDetail->amount = $payAmount;
        $receiptPaymentDetail->net_amount = $payAmount;
        $receiptPaymentDetail->save();

        //Credit Head Amount
        $log = new TransactionLog();
        $log->notes = $request->note;
        $log->receipt_payment_no = $voucherNo;
        $log->receipt_payment_sl = $receiptPaymentNoSl;
        $log->financial_year = financialYear($request->financial_year);
        $log->customer_id = $customer->id;
        $log->date = Carbon::parse($request->date)->format('Y-m-d');
        $log->receipt_payment_id = $receiptPayment->id;
        $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
        $log->payment_type = $request->payment_type;
        if($request->payment_type == 1){
            $log->cheque_no = $request->cheque_no;
            $log->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
        }
        $log->transaction_type = 2;//Account Head Credit
        $log->account_head_id = 9;
        $log->sales_order_id = $order->id;
        $log->amount = $payAmount;
        $log->notes = $request->note;
        $log->save();

        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('receipt_details', ['receiptPayment' => $receiptPayment->id])]);

    }

    public function saleMakeReceipt(Request $request){

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
            $order = SalesOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $order = SalesOrder::where('id',$request->order)->first();
        $accountHead = AccountHead::where('client_id',$order->client_id)->first();

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
            $receiptPayment->sales_order_id = $order->id;
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
            $receiptPayment->sales_order_id = $order->id;
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
            $log->transaction_type = 1;
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

        return response()->json(['success' => true, 'message' => 'Receipt has been completed.', 'redirect_url' => route('sale_receipt.all')]);
    }

    public function saleReceiptDetails(SalesOrder $order)
    {
        $customer_data = Client::find($order->client_id);
        return view('sale.receipt.details', compact('order', 'customer_data'));
    }

    public function saleReceiptAll(SalesOrder $order)
    {
        return view('sale.receipt.all_details', compact('order'));
    }

    public function individualSaleReceiptDetails(ReceiptPayment $payment)
    {
        $payment->amount_in_word = DecimalToWords::convert(
            $payment->amount,
            'Taka',
            'Poisa'
        );
        return view('sale.receipt.individual_payment_details', compact('payment'));
    }

    public function saleReceiptPrint(SalesOrder $order)
    {
        $order->amount_in_word = DecimalToWords::convert(
            $order->total,
            'Taka',
            'Poisa'
        );
        return view('sale.receipt.print', compact('order'));
    }
    public function saleReceiptPrintWithHeader(SalesOrder $order)
    {
        $order->amount_in_word = DecimalToWords::convert(
            $order->total,
            'Taka',
            'Poisa'
        );
        return view('sale.receipt.print_with_header', compact('order'));
    }

    public function salePaymentDetails(SalePayment $payment)
    {
        $payment->amount_in_word = DecimalToWords::convert(
            $payment->amount,
            'Taka',
            'Poisa'
        );

        return view('sale.receipt.payment_details', compact('payment'));
    }

    public function salePaymentPrint(SalePayment $payment)
    {
        $payment->amount_in_word = DecimalToWords::convert(
            $payment->amount,
            'Taka',
            'Poisa'
        );
        return view('sale.receipt.payment_print', compact('payment'));
    }

    public function customerPayment()
    {
        $customers = Client::where('type',1)->get();
        return view('sale.customer_payment.all',compact('customers'));
    }

    public function customerPaymentGetOrders(Request $request)
    {

        $query = SalesOrder::where('customer_id', $request->customerId);

        $orders = $query->where('due', '>', 0)
//            ->where('approve_status', 3)
            ->orderBy('order_no')
            ->get()->toArray();


        return response()->json($orders);
    }

    public function customerPaymentGetRefundOrders(Request $request)
    {
        $query = SalesOrder::where('customer_id', $request->customerId);
        if (Auth::user()->role == Role::$AUDIT) {
            $query->where('hide_show', Status::$SHOW);
        }

        $orders = $query->where('refund', '>', 0)
            ->orderBy('order_no')
            ->get()->toArray();

        return response()->json($orders);
    }

    public function customerMakePayment(Request $request)
    {
        $rules = [
            'order' => 'required',
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

        if ($request->order != '') {
            $order = SalesOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:' . $order->due;
        }


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $order = SalesOrder::find($request->order);

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
            $payment->transaction_method = $request->payment_type;
            $payment->amount = $request->amount;
            $payment->approved_status = 1;

            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();

            if ($request->payment_type == 1)
                Cash::first()->increment('amount', $request->amount);
            else
                MobileBanking::first()->increment('amount', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Payment from ' . $order->customer->name . ' for ' . $order->order_no;
            $log->transaction_type = 1;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->save();
        } else {
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/sales_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/sales_payment_cheque/' . $filename;
            }

            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
            $payment->transaction_method = 2;
            $payment->bank_id = $request->bank;
            $payment->branch_id = $request->branch;
            $payment->bank_account_id = $request->account;
            $payment->cheque_no = $request->cheque_no;
            $payment->cheque_image = $image;
            $payment->amount = $request->amount;
            $payment->approve_status = 1;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();

            BankAccount::find($request->account)->increment('balance', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Payment from ' . $order->customer->name . ' for ' . $order->order_no;
            $log->transaction_type = 1;
            $log->transaction_method = 2;
            $log->account_head_type_id = 2;
            $log->account_head_sub_type_id = 2;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->save();
        }

        $order->increment('paid', $request->amount);
        $order->decrement('due', $request->amount);

        $order->save();

        return response()->json(['success' => true, 'message' => 'Payment has been completed.', 'redirect_url' => route('sale_receipt.payment_details', ['payment' => $payment->id])]);
    }

    public function customerMakeRefund(Request $request)
    {
        $rules = [
            'order' => 'required',
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

        if ($request->order != '') {
            $order = SalesOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:' . $order->refund;
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

        $order = SalesOrder::find($request->order);

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
            $payment->type = 2;
            $payment->transaction_method = $request->payment_type;
            $payment->received_type = 1;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->note = $request->note;
            $payment->save();

            if ($request->payment_type == 1)
                Cash::first()->decrement('amount', $request->amount);
            else
                MobileBanking::first()->decrement('amount', $request->amount);

            $log = new TransactionLog();
            $log->hide_show = $order->hide_show;
            $log->date = $request->date;
            $log->particular = 'Refund to ' . $order->customer->name . ' for ' . $order->order_no;
            $log->transaction_type = 6;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 6;
            $log->account_head_sub_type_id = 6;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->save();
        } else {
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/sales_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/sales_payment_cheque/' . $filename;
            }

            $payment = new SalePayment();
            $payment->sales_order_id = $order->id;
            $payment->type = 2;
            $payment->transaction_method = 2;
            $payment->received_type = 1;
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
            $log->hide_show = $order->hide_show;
            $log->date = $request->date;
            $log->particular = 'Refund to ' . $order->customer->name . ' for ' . $order->order_no;
            $log->transaction_type = 6;
            $log->transaction_method = 2;
            $log->account_head_type_id = 6;
            $log->account_head_sub_type_id = 6;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->sale_payment_id = $payment->id;
            $log->save();
        }

        $order->decrement('refund', $request->amount);

        return response()->json(['success' => true, 'message' => 'Refund has been completed.', 'redirect_url' => route('sale_receipt.payment_details', ['payment' => $payment->id])]);
    }

    public function saleInformation()
    {
        return view('sale.product_sale_information.index');
    }

    public function saleInformationPost(Request $request)
    {
        $product = DB::table('purchase_order_purchase_product')
            ->where('serial_no', $request->serial)
            ->first();

        $sale = DB::table('purchase_product_sales_order')
            ->where('serial', $request->serial)->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Invalid serial.']);
        } elseif ($product->quantity > 1) {
            return response()->json(['success' => false, 'message' => 'This serial has many products.']);
        } elseif (!$sale) {
            return response()->json(['success' => false, 'message' => 'This serial not sell yet.']);
        } else {
            $order = SalesOrder::find($sale->sales_order_id);
            $purchaseOrder = PurchaseOrder::find($product->purchase_order_id);

            return response()->json(['success' => true, 'message' => 'This serial is sold.', 'redirect_url' => route('sale_information.print', ['purchaseOrder' => $purchaseOrder->id, 'saleOrder' => $order->id, 'serial' => $request->serial])]);
        }
    }

    public function saleInformationPrint(PurchaseOrder $purchaseOrder, SalesOrder $saleOrder)
    {
        $saleOrder->amount_in_word = DecimalToWords::convert(
            $saleOrder->total,
            'Taka',
            'Poisa'
        );

        return view('sale.product_sale_information.print', compact(
            'purchaseOrder',
            'saleOrder'
        ));
    }

    public function saleReceiptEdit(SalesOrder $order)
    {
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $brands = Brand::where('status', 1)->orderBy('name')->get();

        return view('admin.sale.receipt.edit', compact(
            'order',
            'banks',
            'customers',
            'categories',
            'brands'
        ));
    }


    public function saleReceiptEditPost(SalesOrder $order, Request $request)
    {
        // dd($request->all());
        $total = $request->total;
        $due = $request->due_total;


        $rules = [
            'customer_type' => 'required',
            'date' => 'required|date',
            'vat' => 'required|numeric|min:0',
            'ait' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'paid' => 'required|numeric|min:0|max:' . $total,
            'payment_type' => 'required',
            'category.*' => 'required',
            'product.*' => 'required',
            'brand.*' => 'required',
            'lc_no.*' => 'required',
            'quantity.*' => 'required|numeric|min:.01',
            'unit_price.*' => 'required|numeric|min:0',
        ];
        if ($request->customer_type == 1) {
            $rules['customer_name'] = 'required';
            $rules['mobile_no'] = 'required';
            $rules['address'] = 'nullable';
        }

        if ($request->customer_type == 2) {
            $rules['customer'] = 'required';
        }

        if ($request->payment_type == '2') {
            $rules['bank'] = 'required';
            $rules['branch'] = 'required';
            $rules['account'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }


        $request->validate($rules);



        if ($request->customer_type == 1) {
            $customer = new Customer();
            $customer->name = $request->customer_name;
            $customer->mobile_no = $request->mobile_no;
            $customer->address = $request->address;
            $customer->save();
        } else {
            $customer = Customer::find($request->customer);
        }

        $available = true;
        $message = '';
        $counter = 0;



        if ($request->product) {
            foreach ($request->product as $reqProduct) {
                $inventory = PurchaseInventory::where('product_id', $reqProduct)
                    ->where('category_id', $request->category[$counter])
                    ->where('brand_id', $request->brand[$counter])
                    ->where('lc_no', $request->lc_no[$counter])
                    ->first();

                if (1 > $inventory->quantity) {
                    $available = false;
                    $message = 'Insufficient ' . $inventory->product->name;
                    break;
                }
                $counter++;
            }
        }

        if (!$available) {
            return redirect()->back()->withInput()->with('message', $message);
        }



        $counter = 0;
        $subTotal = 0;
        $buyingPrice = 0;


        //Delete  Products
        $productsAll = ProductSalesOrder::where('sales_order_id', $order->id)->delete();
        $salePayments = SalePayment::where('sales_order_id', $order->id)->get();
        if ($salePayments) {
            foreach ($salePayments as $salePayment) {
                if ($salePayment->transaction_method == 1) {
                    Cash::first()->decrement('amount', $salePayment->amount);
                } elseif ($salePayment->transaction_method == 3) {
                    MobileBanking::first()->decrement('amount', $salePayment->amount);
                } elseif ($salePayment->transaction_method == 2) {
                    Bank::first()->decrement('amount', $salePayment->amount);
                }
                $logDelete = TransactionLog::where('sale_payment_id', $salePayment->id)->delete();
            }
        }
        SalePayment::where('sales_order_id', $order->id)->delete();
        $logDelete = TransactionLog::where('sales_order_id', $order->id)
            ->where('account_head_type_id', 5)
            ->where('transaction_type', 4)
            ->where('account_head_sub_type_id', 5)->delete();

        //Update Sale Order And Insert products

        $order->customer_id = $customer->id;
        $order->hide_show = $request->hide_show ? 1 : 2;
        $order->date = $request->date;
        $order->sub_total = 0;
        $order->vat_percentage = $request->vat;
        $order->vat = 0;
        $order->ait_percentage = $request->ait;
        $order->ait = 0;
        $order->vat_percentage = $request->discount;
        $order->discount = 0;
        $order->total = 0;
        $order->paid = $request->paid;
        $order->due = 0;
        $order->user_id = Auth::id();
        $order->approve_status = 0;
        $order->save();

        $order->save();

        $counter = 0;
        $subTotal = 0;
        $buyingPrice = 0;

        if ($request->product) {
            foreach ($request->product as $reqProduct) {
                $inventory = PurchaseInventory::where('product_id', $reqProduct)
                    ->where('category_id', $request->category[$counter])
                    ->where('brand_id', $request->brand[$counter])
                    ->where('lc_no', $request->lc_no[$counter])
                    ->first();

                $buyingPrice += $inventory->avg_unit_price * 1;

                $order->products()->attach($reqProduct, [
                    'name' => $inventory->product->name,
                    'category_id' => $inventory->category_id,
                    'brand_id' => $inventory->brand_id,
                    'lc_no' => $inventory->lc_no,
                    'quantity' => 1,
                    'unit_price' => $request->unit_price[$counter],
                    'total' => 1 * $request->unit_price[$counter],
                ]);

                $subTotal += 1 * $request->unit_price[$counter];
                $counter++;
            }
        }


        $order->sub_total = $subTotal;
        $vat = ($subTotal * $request->vat) / 100;
        $ait = ($subTotal * $request->ait) / 100;
        $discount = ($subTotal * $request->discount) / 100;
        $order->vat = $vat;
        $order->ait = $ait;
        $order->discount = $discount;
        $total = $subTotal + $vat +$ait - $discount;
        $order->total = $total;
        $due = $total - $request->paid;
        $order->due = $due;
        $order->save();

        //work
        // Delete Sale Payment And Log


        // dd($logDelete);
        // Update Order


        if ($request->paid > 0) {
            if ($request->payment_type == 1 || $request->payment_type == 3) {
                $payment = new SalePayment();
                $payment->sales_order_id = $order->id;
                $payment->transaction_method = $request->payment_type;
                $payment->received_type = 1;
                $payment->amount = $request->paid;
                $payment->date = $request->date;
                $payment->approve_status = 0;
                $payment->save();

                if ($request->payment_type == 1)
                    Cash::first()->increment('amount', $request->paid);
                elseif ($request->payment_type == 3)
                    MobileBanking::first()->increment('amount', $request->paid);

                //work
                $log = new TransactionLog();
                $log->date = $request->date;
                $log->hide_show = $order->hide_show;
                $log->particular = 'Payment for ' . $order->order_no;
                $log->transaction_type = 1;
                $log->transaction_method = $request->payment_type;
                $log->account_head_type_id = 2;
                $log->account_head_sub_type_id = 2;
                $log->amount = $request->paid;
                $log->approve_status = 0;
                $log->sale_payment_id = $payment->id;
                $log->save();
            } else {
                $image = 'img/no_image.png';

                if ($request->cheque_image) {
                    // Upload Image
                    $file = $request->file('cheque_image');
                    $filename = Uuid::uuid1()->toString() . '.' . $file->getClientOriginalExtension();
                    $destinationPath = 'public/uploads/sales_payment_cheque';
                    $file->move($destinationPath, $filename);

                    $image = 'uploads/sales_payment_cheque/' . $filename;
                }

                $payment = new SalePayment();
                $payment->sales_order_id = $order->id;
                $payment->transaction_method = 2;
                $payment->received_type = 1;
                $payment->bank_id = $request->bank;
                $payment->branch_id = $request->branch;
                $payment->bank_account_id = $request->account;
                $payment->cheque_no = $request->cheque_no;
                $payment->cheque_image = $image;
                $payment->amount = $request->paid;
                $payment->date = $request->date;
                $payment->approve_status = 0;
                $payment->save();

                BankAccount::find($request->account)->increment('balance', $request->paid);

                $log = new TransactionLog();
                $log->hide_show = $order->hide_show;
                $log->date = $request->date;
                $log->particular = 'Payment for ' . $order->order_no;
                $log->transaction_type = 1;
                $log->transaction_method = 2;
                $log->account_head_type_id = 2;
                $log->account_head_sub_type_id = 2;
                $log->bank_id = $request->bank;
                $log->branch_id = $request->branch;
                $log->bank_account_id = $request->account;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_image = $image;
                $log->amount = $request->paid;
                $log->approve_status = 0;
                $log->sale_payment_id = $payment->id;
                $log->save();
            }
        }

        // Buying Price log
        $log = new TransactionLog();
        $log->date = $request->date;
        $log->hide_show = $order->hide_show;
        $log->particular = 'Buying price for ' . $order->order_no;
        $log->transaction_type = 4;
        $log->transaction_method = 0;
        $log->account_head_type_id = 5;
        $log->account_head_sub_type_id = 5;
        $log->amount = $buyingPrice;
        $log->approve_status = 0;
        $log->sales_order_id = $order->id;
        $log->save();

        return redirect()->route('sale_receipt.details', ['order' => $order->id]);
    }

    public function saleProductDetails(Request $request) {
        $inventory = Inventory::where('id', $request->productId)
            ->where('product_type',3)
            ->where('quantity', '>', 0)
            ->first();
        $product = Product::with('unit')->where('id',$inventory->product_id)->first();



        $lastSellPrice = SalesOrderProduct::where('client_id',$request->customerId)->where('product_id', $inventory->product_id)->latest()->first();

        return response()->json([
            'inventory'=>$inventory,
            'product'=>$product,
            'lastSellPrice'=>$lastSellPrice ?? '',
        ]);
    }

    public function getServiceDetails(Request $request){
        $product = Product::with('unit')->where('id', $request->productId)->first();
        return response()->json([
            'product'=>$product,
        ]);
    }



//    public function saleBookingDetails(Request $request)
//    {
//        $booking = Booking::where('id', $request->bookingId)
//            ->where('status',3)
//            ->where('quantity', '>', 0)
//            ->first();
////        $product = Product::with('unit')->where('id',$inventory->product_id)->first();
//
//        return response()->json([
//            'booking'=>$booking,
////            'product'=>$product,
//        ]);
//    }

    public function getBookingDetails(Request $request)
    {
        $booking = Booking::where('id', $request->bookingId)
            ->where(function ($query) {
                $query->where('status', '=', 2)
                    ->orWhere('status', '=', 3);
            })
            ->where('quantity', '>', 0)
            ->first();

        $bookingDetails = view('layouts.partial.booking_details',compact('booking'))->render();

        return response()->json([
            'html'=>$bookingDetails,
            'advance'=>$booking->advance_amount,
        ]);
     }

    public function saleReceiptDatatable()
    {
        $query = SalesOrder::with('client');

        return DataTables::eloquent($query)
            ->addColumn('client', function (SalesOrder $order) {
                return $order->client->name ?? '';
            })
            ->addColumn('action', function (SalesOrder $order) {
                $btn = '<a href="' . route('sale_receipt.details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> </a> ';
                if($order->journalVoucher)
                $btn  .= '<a href="' . route('journal_voucher_details', ['journalVoucher'=>$order->journalVoucher->id]) . '" class="btn btn-primary btn-sm">JV</i></a> ';
                if($order->due > 0){
                    $btn  .= '<a class="btn btn-info btn-sm btn-pay" role="button" data-id="'.$order->id.'" data-order="'.$order->order_no.'" data-due="'.$order->due.'">Payment</a> ';
                }
                $btn  .= '<a href="' . route('sale_receipt_all_details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm">Details</i></a> ';

                return $btn;
            })
            ->editColumn('date', function (SalesOrder $order) {
                return $order->date;
            })
            ->editColumn('total', function (SalesOrder $order) {
                return '৳' . number_format($order->total, 2);
            })
            ->editColumn('paid', function (SalesOrder $order) {
                return '৳' . number_format($order->paid, 2);
            })
            ->editColumn('advance_total', function (SalesOrder $order) {
                return '৳' . number_format($order->advance_total, 2);
            })
            ->editColumn('due', function (SalesOrder $order) {
                return '৳' . number_format($order->due, 2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })

            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function customerPaymentDatatable()
    {
        $query = Customer::query();

        return DataTables::eloquent($query)
            ->addColumn('action', function (Customer $customer) {
                $btns = '<a class="btn btn-info btn-sm btn-pay" role="button" data-id="' . $customer->id . '" data-name="' . $customer->name . '">Payment</a>';

                if ($customer->refund > 0)
                    $btns .= ' <a class="btn btn-danger btn-sm btn-refund" role="button" data-id="' . $customer->id . '" data-name="' . $customer->name . '">Refund</a>';

                return $btns;
            })
            ->addColumn('paid', function (Customer $customer) {
                return number_format($customer->paid, 2);
            })
            ->addColumn('due', function (Customer $customer) {
                return number_format($customer->due, 2);
            })
            ->addColumn('total', function (Customer $customer) {
                return number_format($customer->total, 2);
            })
            ->addColumn('refund', function (Customer $customer) {
                return number_format($customer->refund, 2);
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function accountReceiptServiceAmount(Request $request)
    {
        //dd($request->all());
        $rules = [
            'financial_year' => 'required',
            'payment_type' => 'required',
            'account_receipt_amount' => 'required|numeric|min:1',
            'account' => 'required',
            'service_id' => 'required',
            'account_receipt_date' => 'required|date',
            'account_receipt_note' => 'nullable',
        ];

        if ($request->payment_type == 1) {
            $rules['cheque_date'] = 'required';
            $rules['cheque_no'] = 'nullable|string|max:255';
            $rules['cheque_image'] = 'nullable|image';
        }

        $service = Service::where('id', $request->service_id)
            ->first();

        $rules['account_receipt_amount'] = 'required|numeric|min:0|max:'.$service->service_due;

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        if ($request->account_receipt_amount > 0) {

            $transactionType = 2;
            $financialYear = $request->financial_year;
            $cashBankAccountHeadId = $request->account;
            $payType = $request->payment_type;
            $voucherNo = generateVoucherReceiptNo($financialYear, $cashBankAccountHeadId, $transactionType, $payType);
            //create dynamic voucher no process end
            $receiptPaymentNoExplode = explode("-", $voucherNo);

            $receiptPaymentNoSl = $receiptPaymentNoExplode[1];
            $receiptPayment = new ReceiptPayment();

            $receiptPayment->receipt_payment_no = $voucherNo;
            $receiptPayment->financial_year = financialYear($request->financial_year);
            $receiptPayment->date = Carbon::parse($request->account_receipt_date)->format('Y-m-d');
            $receiptPayment->transaction_type = 2;
            $receiptPayment->payment_type = $request->payment_type;//cash == 2,bank =1

            $receiptPayment->account_head_id = $request->account;
            $receiptPayment->cheque_no = $request->cheque_no;
            if ($request->payment_type == 1) {
                $receiptPayment->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
            }
            $receiptPayment->sub_total = $request->account_receipt_amount;
            $receiptPayment->net_amount = $request->account_receipt_amount;
            $receiptPayment->employee_id = $service->employee_id;
            $receiptPayment->service_id = $service->id;
            $receiptPayment->sales_order_id = $service->sales_order_id;
            $receiptPayment->customer_id = $service->customer_id??'';
            $receiptPayment->service_customer_id = $service->service_customer_id??null;
            $receiptPayment->notes = $request->account_receipt_note;
            $receiptPayment->save();

            //Bank/Cash Credit
            $log = new TransactionLog();
            $log->notes = $request->note;
            $log->receipt_payment_no = $receiptPayment->receipt_payment_no;
            $log->receipt_payment_sl = $receiptPaymentNoSl;
            $log->financial_year = $receiptPayment->financial_year;
            $log->employee_id = $receiptPayment->employee_id;
            $log->date = $receiptPayment->date;
            $log->receipt_payment_id = $receiptPayment->id;

            if ($request->payment_type == 1) {
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');

            }
            $log->transaction_type = 2;//Bank Credit,Cash credit

            $log->payment_type = $request->payment_type;
            $log->account_head_id = $request->account;
            $log->amount = $receiptPayment->net_amount;
            $log->notes = $receiptPayment->notes;
            $log->service_id = $service->id;
            $log->sales_order_id = $service->sales_order_id;
            $log->save();

            $receiptPaymentDetail = new ReceiptPaymentDetail();
            $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
            $receiptPaymentDetail->account_head_id = 12;
            $receiptPaymentDetail->amount = $request->account_receipt_amount;
            $receiptPaymentDetail->net_amount = $request->account_receipt_amount;
            $receiptPaymentDetail->save();

            //Debit Head Amount
            $log = new TransactionLog();
            $log->notes = $request->note;
            $log->receipt_payment_no = $voucherNo;
            $log->receipt_payment_sl = $receiptPaymentNoSl;
            $log->financial_year = financialYear($request->financial_year);
            $log->employee_id = $service->employee_id;
            $log->date = Carbon::parse($request->account_receipt_date)->format('Y-m-d');
            $log->receipt_payment_id = $receiptPayment->id;
            $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
            $log->payment_type = $request->payment_type;
            if ($request->payment_type == 1) {
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
            }
            $log->transaction_type = 1;//Account Head Debit
            $log->account_head_id = 12;
            $log->service_id = $service->id;
            $log->sales_order_id = $service->sales_order_id;
            $log->amount = $request->account_receipt_amount;
            $log->notes = $request->note;
            $log->save();

            if ($service->new_setup_status == 0 || $service->new_setup_status == 1){
                if ($service->saleOrder->due > 0){
                    if ($service->saleOrder->due > $request->account_receipt_amount){
                        $service->saleOrder->increment('paid', $request->account_receipt_amount);
                        $service->saleOrder->decrement('due', $request->account_receipt_amount);
                    }elseif ($service->saleOrder->due < $request->account_receipt_amount){

                        $increaseAmount =  $request->account_receipt_amount - $service->saleOrder->due;

                        $decrementAmount =  $request->account_receipt_amount - $increaseAmount;

                        $service->saleOrder->increment('paid', $decrementAmount);
                        $service->saleOrder->decrement('due', $decrementAmount);

                    }elseif($service->saleOrder->due == $request->account_receipt_amount){

                        $service->saleOrder->increment('paid', $request->account_receipt_amount);
                        $service->saleOrder->decrement('due', $request->account_receipt_amount);

                    }else{

                    }
                }
            }

            $service->increment('service_paid', $request->account_receipt_amount);
            $service->decrement('service_due', $request->account_receipt_amount);

            $technicianAmount = EmployeeCash::where('employee_id',$service->employee_id)->first();

            if ($technicianAmount){
                $technicianAmount->decrement('amount', $request->account_receipt_amount);
            }

            if ($service->service_due <= 0){
                $service->accounts_status = 1;
                $service->technician_status = 1;
                $service->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Receipt Successfully', 'redirect_url' => route('accessories_sale_receipt.all')]);
    }

    public function customerPaymentDetails(Client $customer) {
        $receiptPayments=ReceiptPayment::where('client_id',$customer->id)->where('client_type',2)->get();

        return view('sale.receipt.customer_payment_details',compact('receiptPayments'));
    }

    public function customerMakePaymentReceipt(Request $request){
        $rules = [
            'payment_type' => 'required',
            'cash_account_code' => 'required',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
        ];

        if ($request->payment_type == 1) {
            $rules['cheque_no'] = 'required|string|max:255';
            $rules['cheque_date'] = 'required|date';
            $rules['issuing_bank_name'] = 'nullable|string|max:255';
            $rules['issuing_branch_name'] = 'nullable|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        //Customer payment block

        $customer = Client::find($request->customer_id);

        $orders = SalesOrder::where('client_id',$customer->id)
            ->orderBy('created_at','asc')
            ->get();

        $totalDue = $customer->opening_due;//Modified by Hasan

        foreach ($orders as $order) {
            $totalDue += $order->due;
        }

        $amountToPay = $request->amount;
        $leftAmountToPay = 0;

        if($amountToPay>$totalDue){
            return response()->json(['message' => 'Given Amount is exceed total due.']);
        }else {
            //Firstly paid opening due then paid order due.
            if($customer->opening_due > 0){
                if ($request->amount > $customer->opening_due){
                    $customer->increment('opening_paid',$customer->opening_due);
                    $amountToPay = $request->amount - $customer->opening_due;
                }else{
                    $customer->increment('opening_paid',$request->amount);
                    $amountToPay = 0;
                }
                $customer->save();
            }
            foreach ($orders as $k => $order) {
                $dueAmount = $order->due;

                if ($amountToPay >= $dueAmount) {
                    $order->increment('paid', $dueAmount);
                    $order->decrement('due', $dueAmount);

                    //Pay the entire due amount of this order
                    $amountToPay -= $dueAmount;

                    $leftAmountToPay = $amountToPay;
                } else {
                    if ($leftAmountToPay > 0) {
                        $order->increment('paid', $leftAmountToPay);
                        $order->decrement('due', $leftAmountToPay);
                        break;
                    } else {
                        if ($k == 0) {
                            $leftAmountToPay = $amountToPay;
                            $order->increment('paid', $leftAmountToPay);
                            $order->decrement('due', $leftAmountToPay);
                        }
                        break;
                    }
                }
            }
        }

        if($request->advance > 0){
            $customer->increment('advance_amount',$request->advance);
            $customer->save();
        }

        $request['amount'] = $request->amount_advance;
        $accountHead = AccountHead::where('client_id',$customer->id)->first();
//        dd($accountHead);
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
            $receiptPayment->client_id = $customer->id;
            $receiptPayment->receipt_payment_no = $voucherNo;
            $receiptPayment->financial_year = financialYear($request->financial_year);
            $receiptPayment->date = Carbon::parse($request->date)->format('Y-m-d');
            $receiptPayment->transaction_type = 1;
            $receiptPayment->payment_type = 2;
            $receiptPayment->client_type = 2;
            $receiptPayment->payment_account_head_id = $request->cash_account_code;
            $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
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
//            $log->account_head_id = $request->cash_account_code;
            $log->account_head_id = $receiptPayment->payment_account_head_id;
            $log->amount = $request->amount;
            $log->notes = $receiptPayment->notes;
            $log->save();

            $receiptPaymentDetail = new ReceiptPaymentDetail();
            $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
//            $receiptPaymentDetail->account_head_id = $request->cash_account_code;
            $receiptPaymentDetail->account_head_id = $accountHead->id;//Hasan Ali changed
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
            $receiptPayment->client_id = $customer->id;
            $receiptPayment->receipt_payment_no = $voucherNo;
            $receiptPayment->financial_year = financialYear($request->financial_year);
            $receiptPayment->date = Carbon::parse($request->date)->format('Y-m-d');
            $receiptPayment->transaction_type = 2;
            $receiptPayment->payment_type = 1;
            $receiptPayment->client_type = 2;
            $receiptPayment->payment_account_head_id = $request->cash_account_code;
            $receiptPayment->cheque_no = $request->cheque_no;
            $receiptPayment->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
            $receiptPayment->issuing_bank_name = $request->issuing_bank_name;
            $receiptPayment->issuing_branch_name = $request->issuing_branch_name;
            $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
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
//            $receiptPaymentDetail->account_head_id = $request->cash_account_code;
            $receiptPaymentDetail->account_head_id = $accountHead->id;//Hasan Ali changed
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
            $log->payment_type = 1;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
            $log->account_head_id = $accountHead->id;
            $log->amount = $request->amount;
            $log->notes = $request->note;
            $log->save();
        }

        return response()->json(['success' => true, 'message' => 'Receipt has been completed.', 'redirect_url' => route('customer_payment.all')]);
    }

}
