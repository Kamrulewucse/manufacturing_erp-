<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetail;
use App\Models\OtherProduct;
use App\Models\Product;
use App\Models\ReceiptPayment;
use App\Models\ReceiptPaymentDetail;
use App\Models\ReceiptPaymentFile;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\ServiceOrder;
use App\Models\ServiceOrderDetails;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    public function serviceOrderCreate() {
        $rawMaterials = Product::where('product_type', 2)->where('quantity','>', 0)->where('status', 1)->get();
        $customers = Client::where('status', 1)->where('type',1)->orderBy('name')->get();
        return view('service.service_order.service_order', compact('customers','rawMaterials'));
    }

    public function serviceOrderCreatePost(Request $request){
//         return($request->all());
        $rules = [
            'product_type' => 'required',
            'customer' => 'required',
            'date' => 'required',
            'service_charge' => 'required'
            ];

        if($request->product_type ==1 ){
            $rules['product'] = 'required';
            $rules['serial'] = 'required';

        } else if($request->product_type ==2 ){

            $rules = [
                'product_name' => 'required',
                'new_serial_no' =>  [
                    'required',
                    Rule::unique('other_products')
                        ->where('new_serial_no', $request->new_serial_no)
                ],
            ];
        }
        if($request->service_row_product){
            $rules['service_row_product.*'] = 'required';
            $rules['row_product_selling_price.*'] = 'required';
            $rules['row_product_quantity.*'] = 'required';
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


        $accountHead = AccountHead::where('client_id',$request->customer)->first();
        $client = Client::find($request->customer);

        if($request->product_type == 1){
                $product = Product::find($request->product_id);
        }
        if($request->product_type == 3){
            $product = ServiceOrder::where('serial',$request->serial)->latest()->first();
        }

        if($request->product_type == 2){
            $product= new OtherProduct();
            $product->name = $request->product_name;
            $product->new_serial_no = $request->new_serial_no;
            $product->service_warranty = $request->service_warranty ?? '';
            $product->date = Carbon::parse($request->date)->format('Y-m-d');
            $product->save();

        }


        $order = new ServiceOrder();
        $order->product_id = $request->product_type == 3 ? $product->product_id : $product->id;
        $order->product_name = $request->product_type == 2 ? $product->name : $request->product;
        $order->category_id = $request->product_type == 1 ? $product->category_id : NULL;
        $order->serial = $request->product_type == 2 ? $request->new_serial_no : $request->serial;
        $order->product_type = $request->product_type;
        $order->service_warranty = $request->service_warranty ?? '';
        $order->service_payment_type = $request->service_payment_type;
        $order->client_id = $client->id;
        $order->date = Carbon::parse($request->date)->format('Y-m-d');
        $order->row_product_total = 0;
        $order->sub_total = $request->total;
        $order->service_charge = $request->service_payment_type == 1 ? $request->service_charge : 0;
        $order->total = $request->service_payment_type == 1 ? $request->total : 0;
        $order->paid = $request->service_payment_type == 1 ? $request->paid : 0;
        $order->due = $request->service_payment_type == 1 ? $request->due_total : 0;
        $order->note = $request->note;

        $order->save();
        $order->order_no = str_pad($order->id, 8, 0, STR_PAD_LEFT);
        $order->save();

        $counter = 0;
        $subTotal = 0;
        if ($request->service_row_product != '') {
            foreach ($request->service_row_product as $reqProduct) {
                $rowProduct = Product::find($reqProduct);

                $inventoryLog=InventoryLog::where('product_id', $rowProduct->id)->where('purchase_order_id','>',$rowProduct->purchase_order_id)->first();
                $totalQuantity = $request->row_product_quantity[$counter];
                if($rowProduct->quantity<$totalQuantity){
                    $rowProduct->update([
                        'quantity' => $rowProduct->quantity + $inventoryLog->quantity,
                    ]);
                }


                    ServiceOrderDetails::create([
                        'service_order_id' => $order->id,
                        'client_id' => $client->id,
                        'row_product_id' => $rowProduct->id,
                        'row_product_name' => $rowProduct->name,
                        'date' => Carbon::parse($request->date)->format('Y-m-d'),
                        'row_product_quantity' => $request->row_product_quantity[$counter],
                        'unit_price' => $request->row_product_unit_price[$counter],
                        'selling_price' => $request->row_product_selling_price[$counter],
                        'total' => $request->row_product_selling_price[$counter] * $request->row_product_quantity[$counter],
                    ]);
                    $subTotal += $request->row_product_quantity[$counter] * $request->row_product_selling_price[$counter];
                    $counter++;

            }
        }
        $order->row_product_total = $subTotal;
        $order->save();

        if ($request->service_row_product != '') {
            $count = 0;
            foreach ($request->service_row_product as $reqProduct) {
                $rowProduct = Product::find($reqProduct);
                $inventoryLog=InventoryLog::where('product_id', $rowProduct->id)->where('purchase_order_id','>',$rowProduct->purchase_order_id)->first();
                $totalQuantity = $request->row_product_quantity[$count];
                if($rowProduct->quantity<$totalQuantity){
                    $rowProduct->update([
                        'unit_price' => $inventoryLog->unit_price,
                        'purchase_order_id' => $inventoryLog->purchase_order_id,
                    ]);
                }

                $inventory = Inventory::where('product_id',$rowProduct->id)->where('product_type',2)->first();

                $rowProduct->decrement('quantity',$totalQuantity);
                $inventory->decrement('quantity',$totalQuantity);
                $count++;
            }
        }

        // service Journal

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
        $journalVoucher->service_order_id = $order->id;
        $journalVoucher->payee_depositor_account_head_id = $accountHead->id;
        $journalVoucher->notes = $request->note;
        $journalVoucher->save();

        //Debit->customer
        $detail = new JournalVoucherDetail();
        $detail->type = 1;
        $detail->journal_voucher_id = $journalVoucher->id;
        $detail->account_head_id = $accountHead->id;
        $detail->amount = $request->total;
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
        $log->amount = $request->total;
        $log->notes = $request->note;
        $log->save();

        //credit->service
        $serviceAccountHead = AccountHead::where('id',131)->first();
        $detail = new JournalVoucherDetail();
        $detail->type = 2;
        $detail->journal_voucher_id = $journalVoucher->id;
        $detail->account_head_id = $serviceAccountHead->id;
        $detail->amount = $request->total-$request->service_charge;
        $detail->save();

        //credit->service
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
        $log->account_head_id = $serviceAccountHead->id;
        $log->amount = $request->total-$request->service_charge;
        $log->notes = $request->note;
        $log->save();

        if($request->service_payment_type==1){
            //service Charge
            if($request->service_charge > 0){
                $serviceChargeHead = AccountHead::where('id',132)->first();
                $detail = new JournalVoucherDetail();
                $detail->type = 2;
                $detail->journal_voucher_id = $journalVoucher->id;
                $detail->account_head_id = $serviceChargeHead->id;
                $detail->amount = $request->service_charge;
                $detail->save();

                //Credit service Charge
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
                $log->account_head_id = $serviceChargeHead->id;
                $log->amount = $request->service_charge;
                $log->notes = $request->note;
                $log->save();
            }
            //payment
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
                    $receiptPayment->service_order_id = $order->id;
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
                    $receiptPayment->service_order_id = $order->id;
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
                    $log->transaction_type = 2;
                    $log->payment_type = 1;
                    $log->cheque_no = $request->cheque_no;
                    $log->cheque_date = Carbon::parse($request->cheque_date)->format('Y-m-d');
                    $log->account_head_id = $accountHead->id;
                    $log->amount = $request->paid;
                    $log->notes = $request->note;
                    $log->save();
                }

            }
        }



        return redirect()->route('service_receipt.details', ['order' => $order->id]);
    }

    public function serviceOrderReceipt(){
        return view('service.receipt.all');
    }

    public function serviceDatatable(){
        $query = serviceOrder::with('client');

        return DataTables::eloquent($query)
            ->addColumn('client', function (serviceOrder $order) {
                return $order->client->name ?? '';
            })
            ->addColumn('action', function (serviceOrder $order) {

                $btn = '<a href="' . route('service_receipt.details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> </a> ';
                if($order->journalVoucher)
                $btn .= '<a href="' . route('journal_voucher_details', ['journalVoucher'=>$order->journalVoucher->id]) . '" class="btn btn-dark btn-sm">JV</i></a> ';
                if($order->due > 0){
                     $btn  .= '<a class="btn btn-info btn-sm btn-pay" role="button" data-id="'.$order->id.'" data-order="'.$order->order_no.'" data-due="'.$order->due.'">Payment</a> ';
                }
                if($order->service_payment_type ==1)
                $btn  .= '<a href="' . route('service_receipt_all_details', ['order' => $order->id]) . '" class="btn btn-primary btn-sm">Details</i></a> ';
                return $btn;
            })
            ->editColumn('date', function (serviceOrder $order) {
                return $order->date;
            })
            ->editColumn('total', function (serviceOrder $order) {
                return '৳' . number_format($order->total, 2);
            })
            ->editColumn('paid', function (serviceOrder $order) {
                return '৳' . number_format($order->paid, 2);
            })
            ->editColumn('due', function (serviceOrder $order) {
                return '৳' . number_format($order->due, 2);
            })
            ->addColumn('service_payment_type', function(serviceOrder $order) {
                if ($order->service_payment_type == 1)
                    return '<span class="badge badge-success">Paid</span>';
                else
                    return '<span class="badge badge-warning">Unpaid</span>';
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action', 'status','service_payment_type'])
            ->toJson();
    }

    public function serviceReceiptDetails(ServiceOrder $order){

        return view('service.receipt.details', compact('order'));
    }


    public function serviceReceiptPrint(ServiceOrder $order) {
        $order->amount_in_word = DecimalToWords::convert(
            $order->total,
            'Taka',
            'Poisa'
        );
        return view('service.receipt.print', compact('order'));
    }

    public function serviceReceiptHeaderPrint(ServiceOrder $order){
        $order->amount_in_word = DecimalToWords::convert(
            $order->total,
            'Taka',
            'Poisa'
        );
        return view('service.receipt.print_with_header', compact('order'));
    }

    public function searchServiceProduct(Request $request)
    {


        $saleProduct = SalesOrderProduct::where('serial',$request->serial)->first();

//        $otherProduct = OtherProduct::where('new_serial_no',$request->serial)->latest()->first();



        if($saleProduct){
            if(!$saleProduct){
                return response()->json(['success' => false, 'message' => 'Product Not Found !']);
            }
            $product = [
                'serial' => $saleProduct->serial,
                'name' => $saleProduct->product->name,
                'product_id' => $saleProduct->product->id,
                'date' => Carbon::parse($saleProduct->date)->format('d-m-Y'),
                'warranty' => $saleProduct->warranty,
            ];
            return response()->json(['success' => true, 'product' => $product]);
        }

//        if($otherProduct){
//            if(!$otherProduct){
//                return response()->json(['success' => false, 'message' => 'Product Not Found !']);
//            }
//            $product = [
//                'serial' => $otherProduct->new_serial_no,
//                'name' => $otherProduct->name,
//                'product_id' => $otherProduct->id,
//                'date' => Carbon::parse($otherProduct->date)->format('d-m-Y'),
//                'warranty' => $otherProduct->service_warranty,
//            ];
//
//            return response()->json(['success' => true, 'product' => $product]);
//        }



    }
    public function searchReServiceProduct(Request $request)
    {

        $serviceProduct = ServiceOrder::where('serial',$request->serviceSerial)->latest()->first();


        if($serviceProduct){
            if(!$serviceProduct){
                return response()->json(['success' => false, 'message' => 'Product Not Found !']);
            }

            $product = [
                'serial' => $serviceProduct->serial,
                'name' => $serviceProduct->product_name ?? '',
                'date' => Carbon::parse($serviceProduct->date)->format('d-m-Y'),
                'warranty' => $serviceProduct->service_warranty ?? '',
            ];

            return response()->json(['success' => true, 'product' => $product]);
        }



    }
    public function serviceProductDetails(Request $request) {

        $product = Product::with('unit')->where('id', $request->serviceId)->first();



        $inventory = InventoryLog::where('product_id', $request->serviceId)
            ->where('purchase_order_id', $product->purchase_order_id)
            ->where('quantity', '>', 0)
            ->first();


        return response()->json([
            'inventory'=>$inventory,
            'product'=>$product,
        ]);
    }

    public function serviceReceiptAll(ServiceOrder $order)
    {
        return view('service.receipt.all_details', compact('order'));
    }

    public function serviceMakeReceipt(Request $request){

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
            $order = ServiceOrder::find($request->order);
            $rules['amount'] = 'required|numeric|min:0|max:'.$order->due;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }


        $order = ServiceOrder::where('id',$request->order)->first();
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
            $receiptPayment->service_order_id = $order->id;
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
            $receiptPayment->service_order_id = $order->id;
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

        return response()->json(['success' => true, 'message' => 'Receipt has been completed.', 'redirect_url' => route('service_order.receipt')]);
    }
}
