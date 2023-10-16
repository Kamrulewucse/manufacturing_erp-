<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Client;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetail;
use App\Models\ReceiptPayment;
use App\Models\ReceiptPaymentDetail;
use App\Models\TransactionLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller{
    public function index() {
        return view('sale.booking.all');
    }

    public function add() {
        $customers = Client::where('type', 1)->where('status', 1)->orderBy('name')->get();
        $technicians = User::where('role', 2)->orderBy('name')->get();
        return view('sale.booking.add',compact('customers','technicians'));
    }

    public function addPost(Request $request) {
//        return($request->all());
        $rules = [
            'customer' => 'required',
            'technician' => 'required',
            'date' => 'required|date',
            'delivery_date' => 'required|date',
            'warning_date' => 'nullable|date',
            'product.*' => 'required',
            'quantity.*' => 'required',
            'advance_amount' => 'required',
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

        $accountHead = AccountHead::where('client_id',$request->customer)->first();

        $booking = new Booking();
        $booking->customer_id = $request->customer;
        $booking->technician_id = json_encode($request->technician);
        $booking->advance_amount = $request->advance_amount;
        $booking->total_amount = $request->advance_amount;
        $booking->quantity = 0;
        $booking->date = Carbon::parse($request->date);
        $booking->delivery_date = Carbon::parse($request->delivery_date);
        $booking->warning_date = Carbon::parse($request->warning_date);
        $booking->note = $request->note;
        $booking->save();
        $booking->order_no = str_pad($booking->id, 8, 0, STR_PAD_LEFT);
        $booking->save();

        $counter = 0;
        $quantityTotal = 0;
        foreach ($request->product as $reqProduct) {
            for ($i = 1; $i <= $request->quantity[$counter] ; $i++) {
                $bookingDetail = new BookingDetail();
                $bookingDetail->booking_id = $booking->id;
                $bookingDetail->product_id = $request->product[$counter];
                $bookingDetail->quantity = 1;
                $bookingDetail->date = Carbon::parse($request->date);
                $bookingDetail->save();
            }

            $quantityTotal += $request->quantity[$counter];

            $counter++;
        }
        $booking->quantity = $quantityTotal;
        $booking->save();


        if($request->advance_amount>0) {
            // Booking advance amount Journal

            $request['financial_year'] = convertDateToFiscalYear($request->date);
            $financialYear = financialYear($request->financial_year);

            $journalVoucherCheck = JournalVoucher::where('financial_year', $financialYear)
                ->orderBy('id', 'desc')->first();

            if ($journalVoucherCheck) {
                $getJVLastNo = explode("-", $journalVoucherCheck->jv_no);
                $jvNo = 'JV-' . ($getJVLastNo[1] + 1);
            } else {
                $jvNo = 'JV-1';
            }

            $journalVoucher = new JournalVoucher();
            $journalVoucher->jv_no = $jvNo;
            $journalVoucher->financial_year = financialYear($request->financial_year);
            $journalVoucher->date = Carbon::parse($request->date)->format('Y-m-d');
            $journalVoucher->booking_order_id = $booking->id;
            $journalVoucher->payee_depositor_account_head_id = $accountHead->id;
            $journalVoucher->notes = $request->note;
            $journalVoucher->save();

            //Debit->customer
            $detail = new JournalVoucherDetail();
            $detail->type = 1;
            $detail->journal_voucher_id = $journalVoucher->id;
            $detail->account_head_id = $accountHead->id;
            $detail->amount = $booking->advance_amount;
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
            $log->amount =$booking->advance_amount;
            $log->notes = $request->note;
            $log->save();

            //credit->sales->Finish Product
            $bookingAccountHead = AccountHead::where('id', 134)->first();
            $detail = new JournalVoucherDetail();
            $detail->type = 2;
            $detail->journal_voucher_id = $journalVoucher->id;
            $detail->account_head_id = $bookingAccountHead->id;
            $detail->amount = $booking->advance_amount;
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
            $log->account_head_id = $bookingAccountHead->id;
            $log->amount = $booking->advance_amount;
            $log->notes = $request->note;
            $log->save();

                //create dynamic voucher no process start
                if ($request->payment_type == 2) {

                    $transactionType = 1;
                    $financialYear = $request->financial_year;
                    $cashAccountId = null;
                    $cashId = $request->account;
                    $voucherNo = generateVoucherReceiptNo($financialYear, $cashAccountId, $cashId, $transactionType);

                    $receiptPaymentNoExplode = explode("-", $voucherNo);
                    $receiptPaymentNoSl = $receiptPaymentNoExplode[1];

                    $receiptPayment = new ReceiptPayment();
                    $receiptPayment->receipt_payment_no = $voucherNo;
                    $receiptPayment->financial_year = financialYear($request->financial_year);
                    $receiptPayment->date = Carbon::parse($request->date)->format('Y-m-d');
                    $receiptPayment->transaction_type = 1;
                    $receiptPayment->payment_type = 2;
                    $receiptPayment->payment_account_head_id = $request->account;
                    $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
                    $receiptPayment->booking_order_id = $booking->id;
                    $receiptPayment->amount = $booking->advance_amount;;
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
                    $log->amount =$booking->advance_amount;
                    $log->notes = $receiptPayment->notes;
                    $log->save();

                    $receiptPaymentDetail = new ReceiptPaymentDetail();
                    $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
                    $receiptPaymentDetail->account_head_id = $request->account;
                    $receiptPaymentDetail->amount = $booking->advance_amount;
                    $receiptPaymentDetail->net_total = $booking->advance_amount;
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
                    $log->amount =$booking->advance_amount;
                    $log->notes = $request->note;
                    $log->save();

                } else if ($request->payment_type == 1) {
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
                    $receiptPayment->booking_order_id = $booking->id;
                    $receiptPayment->amount = $booking->advance_amount;
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
                    $log->amount = $booking->advance_amount;
                    $log->notes = $receiptPayment->notes;
                    $log->save();

                    $receiptPaymentDetail = new ReceiptPaymentDetail();
                    $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
                    $receiptPaymentDetail->account_head_id = $request->account;
                    $receiptPaymentDetail->amount = $booking->advance_amount;
                    $receiptPaymentDetail->net_total = $booking->advance_amount;
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
                    $log->amount = $booking->advance_amount;
                    $log->notes = $request->note;
                    $log->save();
                }

        }

        return redirect()->route('booking')->with('message', 'Booking successfully.');
    }

    public function edit(Booking $booking) {
        $customers = Client::where('type', 1)->where('status', 1)->orderBy('name')->get();
        $technicians = User::where('role', 2)->orderBy('name')->get();
        return view('sale.booking.edit',compact('customers','technicians','booking'));
    }

    public function editPost(Booking $booking, Request $request) {
        $rules = [
            'customer' => 'required',
            'technician' => 'required',
            'date' => 'required|date',
            'delivery_date' => 'required|date',
            'product.*' => 'required',
            'quantity.*' => 'required',
            'advance_amount.*' => 'nullable',
        ];

        $request->validate($rules);

        $booking->customer_id = $request->customer;
        $booking->technician_id = json_encode($request->technician);
        $booking->advance_amount = $request->advance_amount ?? 0;
        $booking->quantity = 0;
        $booking->date = Carbon::parse($request->date);
        $booking->delivery_date = Carbon::parse($request->delivery_date);
        $booking->save();
        BookingDetail::where('booking_id',$booking->id)->delete();

        $counter = 0;
        $quantityTotal = 0;
        foreach ($request->product as $reqProduct) {
            for ($i = 1; $i <= $request->quantity[$counter] ; $i++) {
                $bookingDetail = new BookingDetail();
                $bookingDetail->booking_id = $booking->id;
                $bookingDetail->product_id = $request->product[$counter];
                $bookingDetail->quantity = 1;
                $bookingDetail->date = Carbon::parse($request->date);
                $bookingDetail->save();
            }
            $quantityTotal += $request->quantity[$counter];

            $counter++;
        }
        $booking->quantity = $quantityTotal;
        $booking->save();

        return redirect()->route('booking')->with('message', 'Booking edit successfully.');
    }

    public function datatable() {

        $query = Booking::with('customer','technician')->orderBy('created_at', 'desc');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Booking $booking) {
                $btn = ' <a href="'.route('booking_details',['booking'=>$booking->id]).'" class="btn btn-dark btn-sm"><i class="fa fa-info-circle"></i></a>';
                $btn .= ' <a data-id="'.$booking->id.'" class="btn btn-danger bg-gradient-danger btn-sm btn-delete"><i class="fa fa-trash"></i></a> ';
                if($booking->status !=3){
                    if($booking->status == 0){
                        $btn .= ' <a href="' . route('booking.edit', ['booking' => $booking->id]) . '" class="btn btn-info btn-sm btn-edit">Edit</a> ';
                        $btn .= ' <a data-id="'.$booking->id.'" class="btn btn-primary btn-sm btn-accept">Accept</a> ';
                    } else
                        $btn .= ' <a href="'.route('assign_order_process',['booking'=>$booking->id]).'" data-id="'.$booking->id.'" class="btn btn-info btn-sm">Order Process</i></a> ';
                    $btn .=' <a data-id="'. $booking->id .'"data-date="'. $booking->delivery_date .'" class="btn btn-warning btn-sm btn-change-date">Date Change</a>';

                }
          return $btn;


            })
            ->addColumn('customer', function(Booking $booking) {
                return $booking->customer->name ?? '';
            })
            ->addColumn('technician', function(Booking $booking) {
                $technicianIds = json_decode($booking->technician_id);

                if (!empty($technicianIds)) {
                    $technicianNames = [];
                    foreach ($technicianIds as $technicianId) {
                        $technician = User::find($technicianId);
                        if ($technician) {
                            $technicianNames[] = $technician->name;
                        }
                    }
                    return implode(', ', $technicianNames);
                }
            })

            ->editColumn('date', function(Booking $booking) {
                return Carbon::parse($booking->date)->format('d-m-Y');
            })
            ->editColumn('delivery_date', function(Booking $booking) {
                return Carbon::parse($booking->delivery_date)->format('d-m-Y');
            })
            ->addColumn('status', function(Booking $booking) {
                if ($booking->status == 0)
                    return '<span class="badge badge-warning">Pending</span>';
                else if ($booking->status == 1)
                    return '<span class="badge badge-danger">Processing</span>';
                else if ($booking->status == 2)
                    return '<span class="badge badge-info">Ready For Stock</span>';
                else if ($booking->status == 3)
                    return '<span class="badge badge-success">Complete</span>';
            })

            ->rawColumns(['action','status','booking'])

            ->toJson();

    }
    public function bookingDetails(Booking $booking) {
        return view('sale.booking.booking_details', compact('booking'));
    }

    public function bookingReceiptPrint(Booking $booking)
    {
        return view('sale.booking.booking_receipt_print', compact('booking'));
    }

    public function assignSaleReceipt()
    {
        return view('sale.booking.assign_all');
    }
    public function assignSaleReceiptDatatable() {

        $user = User::where('role', 2)->where('id',auth()->user()->id)->first();

        $query = Booking::with('technician','customer')->whereRaw("JSON_CONTAINS(technician_id, '\"$user->id\"')")->orderBy('created_at', 'desc');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Booking $booking) {
                $btn = ' <a href="'.route('booking_details',['booking'=>$booking->id]).'" class="btn btn-dark btn-sm"><i class="fa fa-info-circle"></i></a>';
                if($booking->status != 3){
                    if($booking->status == 0)
                        $btn .= ' <a data-id="'.$booking->id.'" class="btn btn-primary btn-sm btn-accept">Accept</i></a> ';
                    else
                        $btn .= ' <a href="'.route('assign_order_process',['booking'=>$booking->id]).'" data-id="'.$booking->id.'" class="btn btn-info btn-sm">Order Process</i></a> ';
                }

                return $btn;
            })
            ->addColumn('customer', function(Booking $booking) {
                return $booking->customer->name ?? '';
            })
            ->addColumn('technician', function(Booking $booking) {
                return $booking->technician->name ?? '';
            })
            ->editColumn('date', function(Booking $booking) {
                return Carbon::parse($booking->date)->format('d-m-Y');
            })
            ->editColumn('delivery_date', function(Booking $booking) {
                return Carbon::parse($booking->delivery_date)->format('d-m-Y');
            })
            ->addColumn('status', function(Booking $booking) {
                if ($booking->status == 0)
                    return '<span class="badge badge-warning">Pending</span>';
                else if ($booking->status == 1)
                    return '<span class="badge badge-danger">Processing</span>';
                else if ($booking->status == 2)
                    return '<span class="badge badge-info">Partially Complete </span>';
                else if ($booking->status == 3)
                    return '<span class="badge badge-success">Complete</span>';
            })

            ->rawColumns(['action','status'])
            ->toJson();
    }


    public function acceptAssignOrder(Request $request)
    {
        Booking::where('id',$request->id)->update(['status'=>1]);

        BookingDetail::where('booking_id',$request->id)->update(['status'=>1]);

        return response()->json(['success' => true, 'message' => 'Accept Order Successfully.']);
    }
    public function completeAssignOrder(Request $request)
    {
        BookingDetail::where('id',$request->id)->update([
            'status'=>2,
            'remake_request'=>0,
            ]);
        $bookingDetail = BookingDetail::where('id',$request->id)->first();
        Booking::where('id',$bookingDetail->booking_id)->update(['remake_request'=>0]);

        return response()->json(['success' => true, 'message' => 'Complete product Successfully.']);
    }
    public function cancelAssignOrder(Request $request)
    {
        BookingDetail::where('id',$request->id)->update([
            'status'=>4,
            'remake_request'=>0,
            ]);
        $bookingDetail = BookingDetail::where('id',$request->id)->first();

        $booking = Booking::find($bookingDetail->booking_id);
        if ($booking) {
            $booking->increment('cancel_quantity');
            $booking->update([
                'status' => 2,
                'remake_request' => 0,
            ]);
        }
        if($booking->quantity <=($booking->delivery_quantity+$booking->cancel_quantity)){
            $booking->status= 3;
            $booking->save();
        }

        return response()->json(['success' => true, 'message' => 'Cancel product Successfully.']);
    }
    public function stockAssignOrder(Request $request)
    {
        BookingDetail::where('id',$request->id)->update(['status'=>3]);
        $bookingDetail = BookingDetail::where('id',$request->id)->first();
        Booking::where('id',$bookingDetail->booking_id)->update(['status'=>2]);

        return response()->json(['success' => true, 'message' => 'Stock Order Successfully.']);
    }
    public function remakeAssignOrder(Request $request)
    {
        BookingDetail::where('id',$request->id)->update([
            'remake_request'=>1,
            'status'=>1,
            ]);
        $bookingDetail = BookingDetail::where('id',$request->id)->first();
        Booking::where('id',$bookingDetail->booking_id)->update(['remake_request'=>1]);
        return response()->json(['success' => true, 'message' => 'Remake Order Successfully.']);
    }

    public function deliveryDateChange(Request $request){
        $rules = [
            'booking_id' => 'required',
            'date' => 'required',

        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        Booking::where('id', $request->booking_id)->update([
            'delivery_date' => Carbon::parse($request->date),
        ]);

        return response()->json(['success' => true, 'message' => 'Update Delivery Date Successfully !.']);

    }
    public function assignOrderProcess(Booking $booking) {
        return view('sale.booking.assign_order_process', compact('booking'));
    }

    public function delete(Request $request)
    {

        BookingDetail::where('booking_id',$request->id)->delete();
        Booking::find($request->id)->delete();

        return response()->json(['success' => true, 'message' => 'Successfully Deleted.']);
    }
}
