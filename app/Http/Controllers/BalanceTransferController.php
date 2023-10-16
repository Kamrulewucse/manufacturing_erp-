<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\BalanceTransfer;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;
use Yajra\DataTables\Facades\DataTables;

class BalanceTransferController extends Controller
{
    public function balanceTransferIndex()
    {
        return view('accounts.balance_transfer.all');
    }
    public function voucherDetails(BalanceTransfer $balanceTransfer)
    {
        $balanceTransfer->amount_in_word = DecimalToWords::convert($balanceTransfer->amount,'Taka',
            'Poisa');

        return view('accounts.balance_transfer.voucher_details',compact('balanceTransfer'));
    }
    public function voucherPrint(BalanceTransfer $balanceTransfer)
    {

        $balanceTransfer->amount_in_word = DecimalToWords::convert($balanceTransfer->amount,'Taka',
            'Poisa');

        return view('accounts.balance_transfer.voucher_print',compact('balanceTransfer'));
    }

    public function receiptDetails(BalanceTransfer $balanceTransfer)
    {
        $balanceTransfer->amount_in_word = DecimalToWords::convert($balanceTransfer->amount,'Taka',
            'Poisa');

        return view('accounts.balance_transfer.receipt_details',compact('balanceTransfer'));
    }
    public function receiptPrint(BalanceTransfer $balanceTransfer)
    {
        $balanceTransfer->amount_in_word = DecimalToWords::convert($balanceTransfer->amount,'Taka',
            'Poisa');

        return view('accounts.balance_transfer.receipt_print',compact('balanceTransfer'));
    }

    public function balanceTransferDatatable()
    {
        $query = BalanceTransfer::select('balance_transfers.*')
            ->with('sourceAccountHead','targetAccountHead');

        return DataTables::eloquent($query)

            ->editColumn('date', function(BalanceTransfer $balanceTransfer) {
                return Carbon::parse($balanceTransfer->date)->format('d-m-Y');
            })
            ->editColumn('amount', function(BalanceTransfer $balanceTransfer) {
                return number_format($balanceTransfer->amount,2);
            })
            ->addColumn('source_account_head', function(BalanceTransfer $balanceTransfer) {
                return $balanceTransfer->sourceAccountHead->name ?? '';
            })
            ->editColumn('target_account_head', function(BalanceTransfer $balanceTransfer) {
                return $balanceTransfer->targetAccountHead->name ?? '';
            })
            ->editColumn('type', function(BalanceTransfer $balanceTransfer) {
                if ($balanceTransfer->type == 1)
                    return '<span class="badge badge-warning text-white">Bank To Cash</span>';
                elseif ($balanceTransfer->type == 2)
                    return '<span class="badge badge-success">Cash To Bank</span>';
                elseif ($balanceTransfer->type == 3)
                    return '<span class="badge badge-info">Bank To Bank</span>';
                else
                    return '<span class="badge badge-danger">Cash To Cash</span>';
            })
            ->addColumn('action', function(BalanceTransfer $balanceTransfer) {
                $btn = '';
                if ($balanceTransfer->type == 1)
                    $btn .= ' <a href="'.route('balance_transfer_voucher_details',['balanceTransfer'=>$balanceTransfer->id]).'" class="btn btn-dark btn-sm">Voucher </a> <a href="'.route('balance_transfer_receipt_details',['balanceTransfer'=>$balanceTransfer->id]).'" class="btn btn-dark btn-sm"> Receipt</a>';
                elseif($balanceTransfer->type == 3)
                    $btn .= ' <a href="'.route('balance_transfer_voucher_details',['balanceTransfer'=>$balanceTransfer->id]).'" class="btn btn-dark btn-sm">Voucher </a> <a href="'.route('balance_transfer_receipt_details',['balanceTransfer'=>$balanceTransfer->id]).'" class="btn btn-dark btn-sm"> Receipt</a>';

                if ($balanceTransfer->is_delete == 0)
                    $btn .= ' <a href="'.route('balance_transfer.edit',['balanceTransfer'=>$balanceTransfer->id]).'" class="btn btn-dark btn-sm"><i class="fa fa-edit"></i></a>';

                return $btn;
            })
            ->rawColumns(['type','action'])
            ->toJson();
    }
    public function balanceTransferAdd() {

        return view('accounts.balance_transfer.add');
    }

    public function balanceTransferAddPost(Request $request) {

        $messages = [
            'source_bank_account_code.required_if' => 'The source bank account code field is required.',
            'source_cash_account_code.required_if' => 'The source cash account code field is required.',
            'target_bank_account_code.required_if' => 'The target bank account code field is required.',
            'target_cash_account_code.required_if' => 'The target cash account code field is required.',
        ];

        $validator = Validator::make($request->all(), [
            'financial_year' => 'required',
            'type' => 'required|integer|min:1|max:4',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:255',
            'source_cash_account_code' => 'required_if:type,==,2|required_if:type,==,4',
            'target_cash_account_code' => 'required_if:type,==,1|required_if:type,==,4',
            'source_bank_account_code' => 'required_if:type,==,1|required_if:type,==,3',
            'source_bank_account_cheque_no' => 'nullable|string|max:255',
            'target_bank_account_code' => 'required_if:type,==,2|required_if:type,==,3',
            'target_bank_account_cheque_no' => 'nullable|string|max:255',
        ], $messages);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        if ($request->type == 3){

            if ($request->source_bank_account_code == $request->target_bank_account_code){
                return redirect()->route('balance_transfer.add')
                    ->withInput()
                    ->with('error','Source bank account and Target bank account are same !');
            }
        } elseif ($request->type == 4){

            if ($request->source_cash_account_code == $request->target_cash_account_code){
                return redirect()->route('balance_transfer.add')
                    ->withInput()
                    ->with('error','Source cash account and Target cash account are same !');
            }
        }


        $sourceBankAccount = AccountHead::where('id',$request->source_bank_account_code)->first();
        $targetBankAccount = AccountHead::where('id',$request->target_bank_account_code)->first();
        $sourceCashAccount = AccountHead::where('id',$request->source_cash_account_code)->first();
        $targetCashAccount = AccountHead::where('id',$request->target_cash_account_code)->first();


        $voucherNo = '';
        $receiptNo = '';
        if ($request->type == 1) {
            //create dynamic voucher no process start
            $transactionType = 2;
            $cashId = null;
            $financialYear = $request->financial_year;
            $bankAccountId = $request->source_bank_account_code;
            $voucherNo = generateVoucherReceiptNo($financialYear,$bankAccountId,$cashId,$transactionType);
            $transactionType = 1;
            $bankAccountId = null;
            $cashId = $request->target_cash_account_code;
            $receiptNo = generateVoucherReceiptNo($financialYear,$bankAccountId,$cashId,$transactionType);


            //create dynamic voucher no process end
        }else{
            //create dynamic voucher no process start
            $transactionType = 2;
            $financialYear = $request->financial_year;
            $cashId = null;
            $bankAccountId = $request->source_bank_account_code;
            $voucherNo = generateVoucherReceiptNo($financialYear,$bankAccountId,$cashId,$transactionType);
            $transactionType = 1;
            $cashId = null;
            $bankAccountId = $request->target_bank_account_code;
            $receiptNo = generateVoucherReceiptNo($financialYear,$bankAccountId,$cashId,$transactionType);

            //create dynamic voucher no process end
        }

        $paymentNoExplode = explode("-",$voucherNo);
        $receiptExplode = explode("-",$receiptNo);

        $paymentNoSl = $paymentNoExplode[1];
        $receiptNoSl = $receiptExplode[1];

        $transfer = new BalanceTransfer();
//        $transfer->sister_concern_id = auth()->user()->sister_concern_id;
        $transfer->voucher_no = $voucherNo;
        $transfer->receipt_no = $receiptNo;
        $transfer->financial_year = financialYear($request->financial_year);
        $transfer->type = $request->type;

        if (in_array($request->type, [1, 3])){
            $transfer->source_account_head_id = $request->source_bank_account_code;
            $transfer->source_cheque_no = $request->source_bank_cheque_no;
            $transfer->source_cheque_date = $request->source_bank_cheque_date ? Carbon::parse($request->source_bank_cheque_date)->format('Y-m-d') : null;
        }


        if (in_array($request->type, [2, 4])){
            $transfer->source_account_head_id = $request->source_cash_account_code;
        }
        if (in_array($request->type, [2, 3])){
            $transfer->target_account_head_id = $request->target_bank_account_code;
            $transfer->target_cheque_no = $request->target_bank_cheque_no;
            $transfer->target_cheque_date = $request->target_bank_cheque_date ? Carbon::parse($request->target_bank_cheque_date)->format('Y-m-d') : null;
        }

        if (in_array($request->type, [1, 4])){
            $transfer->target_account_head_id = $request->target_cash_account_code;
        }

        $transfer->amount = $request->amount;
        $transfer->date = Carbon::parse($request->date)->format('Y-m-d');
        $transfer->notes = $request->notess;
        $transfer->save();

        if ($request->type == 1) {
            // Bank To Cash

            $log = new TransactionLog();
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_no = $voucherNo;
            $log->receipt_payment_sl = $paymentNoSl;
            $log->financial_year = financialYear($request->financial_year);

            $log->transaction_type = 15;//debit
            $log->payment_type = 1;
            $log->payment_account_head_id = $sourceBankAccount->id;
            $log->cheque_no = $request->source_bank_cheque_no;
            $log->cheque_date = $request->source_bank_cheque_date ? Carbon::parse($request->source_bank_cheque_date)->format('Y-m-d') : null;
            $log->amount = $request->amount;
            $log->notes = $request->notes;
            $log->balance_transfer_id = $transfer->id;
            $log->account_head_id = $sourceBankAccount->id;
            $log->save();

            $log = new TransactionLog();
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_no = $receiptNo;
            $log->receipt_payment_sl = $receiptNoSl;
            $log->financial_year = financialYear($request->financial_year);

            $log->transaction_type = 16;//credit
            $log->payment_type = 2;
            $log->payment_account_head_id = $sourceBankAccount->id;
            $log->amount = $request->amount;
            $log->notes = $request->notes;
            $log->balance_transfer_id = $transfer->id;
            $log->account_head_id = $targetCashAccount->id;
            $log->save();
        }elseif ($request->type == 3) {
            // Bank To Bank

            $log = new TransactionLog();
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_no = $voucherNo;
            $log->receipt_payment_sl = $paymentNoSl;
            $log->financial_year = financialYear($request->financial_year);

            $log->transaction_type = 15;//debit
            $log->payment_type = 2;
            $log->payment_account_head_id = $sourceBankAccount->id;
            $log->cheque_no = $request->source_bank_cheque_no;
            $log->cheque_date = $request->source_bank_cheque_date ? Carbon::parse($request->source_bank_cheque_date)->format('Y-m-d') : null;
            $log->amount = $request->amount;
            $log->notes = $request->notes;
            $log->balance_transfer_id = $transfer->id;
            $log->account_head_id = $sourceBankAccount->id;
            $log->save();

            $log = new TransactionLog();
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_no = $receiptNo;
            $log->receipt_payment_sl = $receiptNoSl;
            $log->financial_year = financialYear($request->financial_year);

            $log->transaction_type = 16;//credit
            $log->payment_type = 2;
            $log->payment_account_head_id = $sourceBankAccount->id;
            $log->cheque_no = $request->target_bank_cheque_no;
            $log->cheque_date = $request->target_bank_cheque_date ? Carbon::parse($request->target_bank_cheque_date)->format('Y-m-d') : null;
            $log->amount = $request->amount;
            $log->notes = $request->notes;
            $log->balance_transfer_id = $transfer->id;
            $log->account_head_id = $targetBankAccount->id;
            $log->save();
        }else{
            return redirect()->route('balance_transfer.add')
                ->withInput()
                ->with('error', 'Balance transfer error.');
        }

        return redirect()->route('balance_transfer')
            ->with('message', 'Balance transfer successful.');
    }

    public function balanceTransferEdit(BalanceTransfer $balanceTransfer) {

        $fiscalYear = explode('-',$balanceTransfer->financial_year)[0];
        return view('accounts.balance_transfer.edit', compact('balanceTransfer','fiscalYear'));
    }
    public function balanceTransferEditPost(BalanceTransfer $balanceTransfer,Request $request) {

//        if ($balanceTransfer->sister_concern_id != auth()->user()->sister_concern_id)
//            abort('404');

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'note' => 'nullable|string|max:255',
            'source_bank_account_cheque_no' => 'nullable|string|max:255',
            'source_bank_account_cheque_image' => 'nullable|image',
            'target_bank_account_cheque_no' => 'nullable|string|max:255',
            'target_bank_account_cheque_image' => 'nullable|image',
        ]);


        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $sourceBankAccount = AccountHead::where('id',$balanceTransfer->source_account_head_id)->first();
        $targetBankAccount = AccountHead::where('id',$balanceTransfer->target_account_head_id)->first();
        $targetCashAccount = AccountHead::where('id',$balanceTransfer->target_account_head_id)->first();

        $transfer = $balanceTransfer;
        $transfer->source_cheque_no = in_array($request->type, [1, 3]) ? $request->source_bank_cheque_no : null;
        $transfer->source_cheque_date = in_array($request->type, [1, 3]) ? ($request->source_bank_cheque_date ? Carbon::parse($request->source_bank_cheque_date)->format('Y-m-d') : null) : null;
        $transfer->target_cheque_no = in_array($request->type, [2, 3]) ? $request->target_bank_cheque_no : null;
        $transfer->target_cheque_date = in_array($request->type, [2, 3]) ? ($request->target_bank_cheque_date ? Carbon::parse($request->target_bank_cheque_date)->format('Y-m-d') : null) : null;


        $transfer->amount = $request->amount;
        $transfer->date = Carbon::parse($request->date)->format('Y-m-d');
        $transfer->notes = $request->notes;
        $transfer->save();
        $voucherNo = $transfer->voucher_no;
        $receiptNo = $transfer->receipt_no;

        $paymentNoExplode = explode("-",$voucherNo);
        $receiptExplode = explode("-",$receiptNo);

        $paymentNoSl = $paymentNoExplode[1];
        $receiptNoSl = $receiptExplode[1];

        $request->financial_year = financialYearToYear($request->financial_year);

        TransactionLog::where('balance_transfer_id',$transfer->id)->delete();


        if ($request->type == 1) {
            // Bank To Cash

            $log = new TransactionLog();
//            $log->sister_concern_id = auth()->user()->sister_concern_id;
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_no = $voucherNo;
            $log->receipt_payment_sl = $paymentNoSl;
            $log->financial_year = financialYear($request->financial_year);
            $log->transaction_type = 15;//debit
            $log->payment_type = 1;
            $log->payment_account_head_id = $sourceBankAccount->id;
            $log->cheque_no = $request->source_bank_cheque_no;
            $log->cheque_date = $request->source_bank_cheque_date ? Carbon::parse($request->source_bank_cheque_date)->format('Y-m-d') : null;
            $log->amount = $request->amount;
            $log->notes = $request->notes;
            $log->balance_transfer_id = $transfer->id;
            $log->account_head_id = $sourceBankAccount->id;
            $log->save();

            $log = new TransactionLog();
//            $log->sister_concern_id = auth()->user()->sister_concern_id;
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_no = $receiptNo;
            $log->receipt_payment_sl = $receiptNoSl;
            $log->financial_year = financialYear($request->financial_year);
            $log->transaction_type = 16;//credit
            $log->payment_type = 2;
            $log->payment_account_head_id = $sourceBankAccount->id;
            $log->amount = $request->amount;
            $log->notes = $request->notes;
            $log->balance_transfer_id = $transfer->id;
            $log->account_head_id = $targetCashAccount->id;
            $log->save();
        }elseif ($request->type == 3) {
            // Bank To Bank
            $log = new TransactionLog();
//            $log->sister_concern_id = auth()->user()->sister_concern_id;
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_no = $voucherNo;
            $log->receipt_payment_sl = $paymentNoSl;
            $log->financial_year = financialYear($request->financial_year);

            $log->transaction_type = 15;//debit
            $log->payment_type = 2;
            $log->payment_account_head_id = $sourceBankAccount->id;
            $log->cheque_no = $request->source_bank_cheque_no;
            $log->cheque_date = $request->source_bank_cheque_date ? Carbon::parse($request->source_bank_cheque_date)->format('Y-m-d') : null;
            $log->amount = $request->amount;
            $log->notes = $request->notes;
            $log->balance_transfer_id = $transfer->id;
            $log->account_head_id = $sourceBankAccount->id;
            $log->save();

            $log = new TransactionLog();
//            $log->sister_concern_id = auth()->user()->sister_concern_id;
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_no = $receiptNo;
            $log->receipt_payment_sl = $receiptNoSl;
            $log->financial_year = financialYear($request->financial_year);
            $log->transaction_type = 16;//credit
            $log->payment_type = 2;
            $log->payment_account_head_id = $sourceBankAccount->id;
            $log->cheque_no = $request->target_bank_cheque_no;
            $log->cheque_date = $request->target_bank_cheque_date ? Carbon::parse($request->target_bank_cheque_date)->format('Y-m-d') : null;
            $log->amount = $request->amount;
            $log->notes = $request->notes;
            $log->balance_transfer_id = $transfer->id;
            $log->account_head_id = $targetBankAccount->id;
            $log->save();
        }else{
            return redirect()->route('balance_transfer.add')
                ->withInput()
                ->with('error', 'Balance transfer error.');
        }

        return redirect()->route('balance_transfer')
            ->with('message', 'Balance transfer updated successful.');
    }


}
