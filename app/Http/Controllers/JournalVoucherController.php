<?php

namespace App\Http\Controllers;

use App\Enumeration\Role;
use App\Models\AccountHead;
use App\Models\Client;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetail;
use App\Models\PurchaseOrder;
use App\Models\ReceiptPaymentFile;
use App\Models\TaxSection;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;
use Yajra\DataTables\Facades\DataTables;

class JournalVoucherController extends Controller
{
    public function datatable(Request $request) {
        $query = JournalVoucher::orderBy('date');
        if (request()->has('start_date') && request('start_date') != '' && request()->has('end_date') && request('end_date') != '') {
            $query->where('date', '>=', Carbon::parse(request('start_date'))->format('Y-m-d'));
            $query->where('date', '<=', Carbon::parse(request('end_date'))->format('Y-m-d'));
        }


        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(JournalVoucher $journalVoucher) {
                $btn = '';
                $btn .= '<a target="_blank" href="'.route('journal_voucher_print',['journalVoucher'=>$journalVoucher->id]).'" class="btn btn-dark btn-sm"><i class="fa fa-print"></i></a> <a href="'.route('journal_voucher_details',['journalVoucher'=>$journalVoucher->id]).'" class="btn btn-dark btn-sm"><i class="fa fa-info-circle"></i></a>';
                $btn .= ' <a href="'.route('journal_voucher.edit',['journalVoucher'=>$journalVoucher->id]).'" class="btn btn-dark btn-sm"><i class="fa fa-edit"></i></a>';
                return $btn;
            })
            ->addColumn('debit_codes', function(JournalVoucher $journalVoucher) {

                $codes = '<ul style="text-align: left;">';
                foreach ($journalVoucher->journalVoucherDebitDetails as $journalVoucherDebitDetail){
                    $codes .= '<li>'.($journalVoucherDebitDetail->accountHead->account_code ?? '').'</li>';
                }
                $codes .= '</ul>';

                return $codes;
            })
            ->addColumn('credit_codes', function(JournalVoucher $journalVoucher) {

                $codes = '<ul style="text-align: left;">';
                foreach ($journalVoucher->journalVoucherCreditDetails as $journalVoucherCreditDetail){
                    $codes .= '<li>'.($journalVoucherCreditDetail->accountHead->account_code ?? '').'</li>';
                }
                $codes .= '</ul>';

                return $codes;
            })
            ->addColumn('payee_depositor_account_head_name', function (JournalVoucher $journalVoucher) {
                return $journalVoucher->payeeDepositorAccountHead->name ?? '';
            })
            ->editColumn('total', function(JournalVoucher $journalVoucher) {
                return number_format($journalVoucher->journalVoucherDebitDetails->sum('amount') + $journalVoucher->journalVoucherCreditDetails->sum('amount'),2);
            })
            ->editColumn('credit_total', function(JournalVoucher $journalVoucher) {
                return number_format($journalVoucher->credit_total,2);
            })
            ->editColumn('date', function(JournalVoucher $journalVoucher) {
                return Carbon::parse($journalVoucher->date)->format('d-m-Y');
            })
            ->rawColumns(['action','reconciliation','debit_codes','credit_codes'])
            ->toJson();
    }

    public function index()
    {
        return view('accounts.journal_voucher.all');
    }
    public function journalDetails(JournalVoucher $journalVoucher){

        $inWordAmount = new DecimalToWords();

        return view('journal_voucher.details',compact('inWordAmount','journalVoucher'));
    }
//    public function details(JournalVoucher $journalVoucher)
//    {
//        $inWordAmount = new DecimalToWords();
//        return view('journal_voucher.details',compact('inWordAmount','journalVoucher'));
//    }

    public function print(JournalVoucher $journalVoucher)
    {
        $inWordAmount = new DecimalToWords();
        return view('accounts.journal_voucher.print',compact('inWordAmount','journalVoucher'));
    }
    public function rangePrint(Request $request)
    {
        $from = 'JV-'.$request->from;
        $to =  'JV-'.$request->to;

        $fromJV = JournalVoucher::where('jv_no',$from)->first();
        $toJV = JournalVoucher::where('jv_no',$to)->first();

        if (!$fromJV){
            for ($i = 1;$i <= $request->to;$i++){
                $from =  'JV-'.$i;
                $fromJV = JournalVoucher::where('jv_no',$from)->first();
                if ($fromJV)
                    break;
            }
        }

        if (!$toJV){
            for ($i = $request->to;$i >= 1;$i--){
                $to =  'JV-'.$i;
                $toJV = JournalVoucher::where('jv_no',$to)->first();
                if ($toJV)
                    break;
            }
        }


        $journalVouchers = JournalVoucher::whereBetween('id',[$fromJV->id ?? null,$toJV->id ?? null])
                ->orderBy('id')
                ->get();


        if (count($journalVouchers) <= 0)
            return redirect()->back()->with('error','Opps... Somethings wrong!');
        $inWordAmount = new DecimalToWords();
        return view('accounts.journal_voucher.range_print',compact('journalVouchers','from','to','inWordAmount'));
    }

    public function create()
    {
        $taxSections = TaxSection::orderBy('sort')->get();
        return view('accounts.journal_voucher.add',compact('taxSections'));
    }


    public function createPost(Request $request)
    {
        $rules = [
            'client_type'=>'required',
            'financial_year'=>'required',
            'date'=>'required|date',
            'e_tin'=>'nullable|max:255',
            'address'=>'nullable|max:255',
            'nature_of_organization'=>'nullable|max:255',
            'designation'=>'nullable|max:255',
            'customer_id'=>'nullable|max:255',
            'account_head_code.*'=>'required',
            'debit_amount.*'=>'required|numeric',
            'notes'=>'nullable|max:255',
        ];


        if ($request->client_type == 1){
            $rules['employee_party'] = 'required';

        }else{
            $rules['name']= 'required|unique:clients|max:255';
        }

        if ($request->other_account_head_code){
            $rules['other_account_head_code.*']='required';
            $rules['credit_amount.*']='required|numeric';
        }

//        foreach ($request->other_account_head_code as $reqOtherAccountHeadCode){
//            //dd();
//            if ($reqOtherAccountHeadCode == 4){
//                $rules['nature_of_organization']='required';
//            }
//        }

        $request->validate($rules);

        $totalCreditAmount = 0;
        $counter = 0;
        if ($request->other_account_head_code) {
            foreach ($request->other_account_head_code as $reqOtherAccountHeadCode) {
                $totalCreditAmount += $request->credit_amount[$counter];
                $counter++;
            }

        }

        $totalDebitAmount = 0;
        $counter = 0;
        if ($request->account_head_code){
            foreach ($request->account_head_code as $reqAccountHeadCode){
                $totalDebitAmount += $request->debit_amount[$counter];
                $counter++;
            }

        }

        if (round($totalCreditAmount,2) != round($totalDebitAmount,2)){

            return redirect()->back()
                ->with('error','Debit and Credit amount is not equal !')
                ->withInput();
        }

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

        if($request->client_type == 1){
            $accountHead = AccountHead::find($request->employee_party);
        }else{
            $client = Client::where('name', $request->name)
                ->first();
            if (!$client){
                $maxClientId = Client::max('id_no');
                $client = new Client();
                $client->type = 2;
                $client->id_no = $maxClientId + 1;
                $client->name = $request->name;
                $client->designation = $request->designation;
                $client->address = $request->address;
                $client->email = $request->email;
                $client->mobile = $request->mobile_no;
                $client->save();
            }
            $maxCode = AccountHead::max('account_code');
            if ($maxCode) {
                $maxCode += 1;
            } else {
                $maxCode = 10001;
            }
            $accountHead = new AccountHead();
            $accountHead->client_id = $client->id;
            $accountHead->account_code = $maxCode;
            $accountHead->name = $client->name;
            $accountHead->account_head_type_id = 2;//Liability
            $accountHead->save();
        }
        $journalVoucher->payee_depositor_account_head_id = $accountHead->id;
        $journalVoucher->e_tin = $request->e_tin;
        $journalVoucher->tax_section_id = $request->nature_of_organization;
        $journalVoucher->notes = $request->notes;
        $journalVoucher->save();

        $counter = 0;
        foreach ($request->account_head_code as $reqAccountHeadCode){


            $detail = new JournalVoucherDetail();
            $detail->type = 1;
            $detail->journal_voucher_id = $journalVoucher->id;
            $detail->account_head_id = $request->account_head_code[$counter];
            $detail->amount = $request->debit_amount[$counter];
            $detail->save();

            //Receipt Head Amount
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
            $log->account_head_id = $request->account_head_code[$counter];
            $log->amount = $request->debit_amount[$counter];
            $log->notes = $request->notes;
            $log->save();

            $counter++;
        }

        if ($request->other_account_head_code){
            $counter = 0;
            foreach ($request->other_account_head_code as $reqOtherAccountHeadCode){


                $detail = new JournalVoucherDetail();
                $detail->type = 2;
                $detail->journal_voucher_id = $journalVoucher->id;
                $detail->account_head_id = $request->other_account_head_code[$counter];
                $detail->amount = $request->credit_amount[$counter];
                $detail->save();

                //Receipt Head Amount
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
                $log->account_head_id = $request->other_account_head_code[$counter];
                $log->amount = $request->credit_amount[$counter];
                $log->notes = $request->notes;
                $log->save();

                $counter++;
            }
        }
        if ($request->supporting_document) {

            foreach ($request->file('supporting_document') as $key => $file) {

                // Upload Image

                $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
                $destinationPath = 'uploads/supporting_document';
                $file->move($destinationPath, $filename);
                $path = 'uploads/supporting_document/' . $filename;

                $receiptPaymentFile = new ReceiptPaymentFile();
                $receiptPaymentFile->journal_voucher_id = $journalVoucher->id;
                $receiptPaymentFile->file = $path;
                $receiptPaymentFile->save();

            }
        }

        return redirect()->route('journal_voucher_details',['journalVoucher'=>$journalVoucher->id])
            ->with('message','Journal Voucher(JV) created');

    }
    public function edit(JournalVoucher $journalVoucher)
    {
        $fiscalYear = explode('-',$journalVoucher->financial_year)[0];
        $taxSections =TaxSection::orderBy('sort')->get();

        return view('accounts.journal_voucher.edit',compact('journalVoucher','fiscalYear',
        'taxSections'));
    }
    public function editPost(JournalVoucher $journalVoucher,Request $request)
    {
        $rules = [
            'client_type'=>'required',
            'date'=>'required|date',
            'e_tin'=>'nullable|max:255',
            'address'=>'nullable|max:255',
            'nature_of_organization'=>'nullable|max:255',
            'designation'=>'nullable|max:255',
            'customer_id'=>'nullable|max:255',
            'account_head_code.*'=>'required',
            'debit_amount.*'=>'required|numeric',
            'notes'=>'nullable|max:255',
        ];

        if ($request->client_type == 1) {
            $rules['payee'] = 'required';

        } else {
            $rules['payee_name'] = 'required';
        }

        if ($request->other_account_head_code){
            $rules['other_account_head_code.*']='required';
            $rules['credit_amount.*']='required|numeric';
        }

        $request->validate($rules);

        $totalCreditAmount = 0;
        $counter = 0;

        if ($request->other_account_head_code) {
            foreach ($request->other_account_head_code as $reqOtherAccountHeadCode) {
                $totalCreditAmount += $request->credit_amount[$counter];
                $counter++;
            }
        }
        $totalDebitAmount = 0;
        $counter = 0;
        if ($request->account_head_code){
            foreach ($request->account_head_code as $reqAccountHeadCode){
                $totalDebitAmount += $request->debit_amount[$counter];
                $counter++;
            }

        }
        if (round($totalCreditAmount,2) != round($totalDebitAmount,2)){
            return redirect()->back()
                ->with('error','Debit and Credit amount is not equal !')
                ->withInput();
        }

        $journalVoucher->date = Carbon::parse($request->date)->format('Y-m-d');

        if ($request->client_type == 1) {
            $accountHead = AccountHead::find($request->payee);
        } else {
            $maxClientId = Client::max('id_no');
            if (!$maxClientId) {
                $maxClientId = 100;
            }
            $client = new Client();
            $client->type = 1;//supplier
            $client->id_no = $maxClientId + 1;
            $client->name = $request->payee_name;
            $client->designation = $request->designation;
            $client->address = $request->address;
            $client->email = $request->email;
            $client->mobile_no = $request->mobile_no;
            $client->save();
            $maxCode = AccountHead::max('account_code');
            if ($maxCode) {
                $maxCode += 1;
            } else {
                $maxCode = 10001;
            }
            $accountHead = new AccountHead();
            $accountHead->client_id = $client->id;
            $accountHead->account_code = $maxCode;
            $accountHead->name = $client->name;
            $accountHead->account_head_type_id = 2;//Liability
            $accountHead->save();
        }
        $journalVoucher->payee_depositor_account_head_id = $accountHead->id;
        $journalVoucher->e_tin = $request->e_tin;
        $journalVoucher->tax_section_id = $request->nature_of_organization;
        $journalVoucher->notes = $request->notes;
        $journalVoucher->save();

        $jvNo = $journalVoucher->jv_no;
        $request->financial_year = financialYearToYear($request->financial_year);

        JournalVoucherDetail::where('journal_voucher_id',$journalVoucher->id)->delete();
        TransactionLog::where('journal_voucher_id',$journalVoucher->id)->delete();


        $counter = 0;
        foreach ($request->account_head_code as $reqAccountHeadCode){

            $detail = new JournalVoucherDetail();
            $detail->type = 1;
            $detail->journal_voucher_id = $journalVoucher->id;
            $detail->account_head_id = $request->account_head_code[$counter];
            $detail->amount = $request->debit_amount[$counter];
            $detail->save();

            //Receipt Head Amount
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
            $log->account_head_id = $request->account_head_code[$counter];
            $log->amount = $request->debit_amount[$counter];
            $log->notes = $request->notes;
            $log->save();

            $counter++;
        }

        if ($request->other_account_head_code){
            $counter = 0;
            foreach ($request->other_account_head_code as $reqOtherAccountHeadCode){


                $detail = new JournalVoucherDetail();
                $detail->type = 2;
                $detail->journal_voucher_id = $journalVoucher->id;
                $detail->account_head_id = $request->other_account_head_code[$counter];
                $detail->amount = $request->credit_amount[$counter];
                $detail->save();

                //Receipt Head Amount
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
                $log->account_head_id = $request->other_account_head_code[$counter];
                $log->amount = $request->credit_amount[$counter];
                $log->notes = $request->notes;
                $log->save();

                $counter++;
            }
        }
        if ($request->supporting_document) {

            foreach ($request->file('supporting_document') as $key => $file) {

                // Upload Image

                $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
                $destinationPath = 'uploads/supporting_document';
                $file->move($destinationPath, $filename);
                $path = 'uploads/supporting_document/' . $filename;

                $receiptPaymentFile = new ReceiptPaymentFile();
                $receiptPaymentFile->journal_voucher_id = $journalVoucher->id;
                $receiptPaymentFile->file = $path;
                $receiptPaymentFile->save();

            }
        }

        return redirect()->route('journal_voucher_details',['journalVoucher'=>$journalVoucher->id])
            ->with('message','Journal Voucher(JV) Updated');

    }

}
