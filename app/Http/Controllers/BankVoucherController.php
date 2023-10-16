<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Client;
use App\Models\Project;
use App\Models\ReceiptPayment;
use App\Models\ReceiptPaymentDetail;
use App\Models\ReceiptPaymentFile;
use App\Models\TaxSection;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use SakibRahaman\DecimalToWords\DecimalToWords;
use Yajra\DataTables\Facades\DataTables;

class BankVoucherController extends Controller
{
    public function datatable()
    {
        $query = ReceiptPayment::select('receipt_payments.*')
            ->where('receipt_payments.transaction_type', 2)
            ->where('receipt_payments.payment_type', 1)
            ->with('paymentAccountHead', 'payeeDepositorAccountHead','receiptPaymentDetails');
//            ->where('receipt_payments.sister_concern_id', auth()->user()->sister_concern_id);

        return DataTables::eloquent($query)
            ->addColumn('action', function (ReceiptPayment $receiptPayment) {
                $btn = '';
                $btn .= ' <a target="_blank" href="' . route('bank_voucher_print', ['receiptPayment' => $receiptPayment->id]) . '" class="btn btn-dark btn-sm"><i class="fa fa-print"></i></a> ';
                $btn .= ' <a href="' . route('bank_voucher_details', ['receiptPayment' => $receiptPayment->id]) . '" class="btn btn-dark btn-sm"><i class="fa fa-info-circle"></i></a> ';
                if ($receiptPayment->is_delete == 0)
                    $btn .= ' <a href="' . route('bank_voucher.edit', ['receiptPayment' => $receiptPayment->id]) . '" class="btn btn-dark btn-sm"><i class="fa fa-edit"></i></a> ';

                return $btn;
            })
            ->addColumn('payee_depositor_account_head_name', function (ReceiptPayment $receiptPayment) {
                return $receiptPayment->payeeDepositorAccountHead->name ?? '';
            })
            ->addColumn('payment_account_name', function (ReceiptPayment $receiptPayment) {
                return $receiptPayment->paymentAccountHead->name ?? '';
            })
            ->addColumn('expenses_code', function (ReceiptPayment $receiptPayment) {

                $codes = '<ul style="text-align: left;">';
                foreach ($receiptPayment->receiptPaymentDetails as $receiptPaymentDetails) {
                    $codes .= '<li>' . ($receiptPaymentDetails->accountHead->account_code ?? '') . '</li>';
                }
                $codes .= '</ul>';

                return $codes;
            })
            ->addColumn('net_total', function (ReceiptPayment $receiptPayment) {
                return number_format($receiptPayment->receiptPaymentDetails->sum('net_total') - $receiptPayment->receiptPaymentOtherDetails->sum('other_amount'), 2);
            })
            ->editColumn('date', function (ReceiptPayment $receiptPayment) {
                return Carbon::parse($receiptPayment->date)->format('d-m-Y');
            })
            ->rawColumns(['action', 'expenses_code'])
            ->toJson();
    }

    public function index()
    {
        return view('accounts.bank_voucher.all');
    }

    public function create()
    {
        $taxSections = TaxSection::orderBy('sort')->get();
        $projects = Project::get();
        return view('accounts.bank_voucher.add', compact('taxSections','projects'));
    }


    public function createPost(Request $request)
    {
        $rules = [
            'client_type' => 'required',
            'financial_year' => 'required',
            'date' => 'required',
            'bank_account_code' => 'required',
            'cheque_no' => 'required',
            'e_tin' => 'nullable|max:255',
            'designation' => 'nullable|max:255',
            'address' => 'nullable|max:255',
            'mobile_no' => 'nullable|digits:11',
            'email' => 'nullable|email',
            'account_head_code.*' => 'required',
            'amount.*' => 'required|numeric',
            'vat_base_amount.*' => 'nullable|numeric',
            'vat_rate.*' => 'nullable|numeric',
            'vat_amount.*' => 'nullable|numeric',
            'ait_base_amount.*' => 'nullable|numeric',
            'ait_rate.*' => 'nullable|numeric',
            'ait_amount.*' => 'nullable|numeric',
            'notes' => 'nullable|max:255',
            'project'=>'nullable',
        ];

        if ($request->client_type == 1) {
            $rules['payee'] = 'required';

        } else {
            $rules['payee_name'] =  'required';
        }

        if ($request->other_account_head_code) {
            $rules['parent_account_head_code.*'] = 'required';
            $rules['other_account_head_code.*'] = 'required';
            $rules['other_amount.*'] = 'required|numeric';
        }

        if ($request->ait_amount) {
            $counter = 0;
            $aitAmountTotal = 0;
            foreach ($request->ait_amount as $reqAit) {
                $aitAmountTotal += $request->ait_amount[$counter];
                $counter++;
            }
            if ($aitAmountTotal > 0) {
                $rules['nature_of_organization'] = 'required';
            }
        }


        $request->validate($rules);

        $counter = 0;
        $totalOtherAmount = 0;

        $parentAccountHeadsId = [];
        if ($request->other_account_head_code) {

            foreach ($request->other_account_head_code as $reqOtherAccountHeadCode) {
                array_push($parentAccountHeadsId, $request->parent_account_head_code[$counter]);


                $totalOtherAmount += $request->other_amount[$counter];
                $counter++;
            }
        }
        $counter = 0;
        $totalAmount = 0;
        $totalVatAmount = 0;
        $totalAitAmount = 0;
        $grandTotal = 0;

        if ($request->account_head_code) {
            $accountHeadsId = [];
            foreach ($request->account_head_code as $reqAccountHeadCode) {

                array_push($accountHeadsId, $reqAccountHeadCode);

                $totalAmount += $request->amount[$counter];
                $totalVatAmount += $request->vat_amount[$counter] ?? 0;
                $totalAitAmount += $request->ait_amount[$counter] ?? 0;
                $counter++;
            }
            $grandTotal = $totalAmount - ($totalOtherAmount + $totalVatAmount + $totalAitAmount);

        } else {
            return redirect()
                ->route('accounts.bank_voucher.create')
                ->withInput()
                ->with('error', 'Account Head is empty !');
        }

        $compareDeductionParentHeads = array_intersect($accountHeadsId, $parentAccountHeadsId);
        if (count($parentAccountHeadsId) != count($compareDeductionParentHeads)) {
            return redirect()
                ->route('accounts.bank_voucher.create')
                ->withInput()
                ->with('error', 'Deduction parent account head  and payment account head mismatch!');
        }


        //create dynamic voucher no process start
        $transactionType = 2;
        $financialYear = $request->financial_year;
        $bankAccountId = $request->bank_account_code;
        $cashId = null;
        $voucherNo = generateVoucherReceiptNo($financialYear, $bankAccountId, $cashId, $transactionType);
        //create dynamic voucher no process end
        $receiptPaymentNoExplode = explode("-", $voucherNo);

        $receiptPaymentNoSl = $receiptPaymentNoExplode[1];

        $receiptPayment = new ReceiptPayment();
//        $receiptPayment->sister_concern_id = auth()->user()->sister_concern_id;
        $receiptPayment->receipt_payment_no = $voucherNo;
        $receiptPayment->financial_year = financialYear($request->financial_year);
        $receiptPayment->project_id = $request->project;
        $receiptPayment->date = Carbon::parse($request->date)->format('Y-m-d');
        $receiptPayment->transaction_type = 2;
        $receiptPayment->payment_type = 1;
        $bankAccount = AccountHead::where('id', $request->bank_account_code)
            ->first();
        $receiptPayment->payment_account_head_id = $request->bank_account_code;
        $receiptPayment->cheque_no = $request->cheque_no;
        $receiptPayment->cheque_date = Carbon::parse($request->date)->format('Y-m-d');

        if ($request->client_type == 1) {
            $accountHead = AccountHead::find($request->payee);
        } else {

            $maxClientId = Client::max('id_no');
            if (!$maxClientId) {
                $maxClientId = 100;
            }
            $client = new Client();
//            $client->sister_concern_id = auth()->user()->sister_concern_id;
            $client->type = 3;//Third Party
            $client->id_no = $maxClientId + 1;
            $client->name = $request->payee_name;
            $client->designation = $request->designation;
            $client->address = $request->address;
            $client->email = $request->email;
            $client->mobile = $request->mobile_no;
            $client->status = 1;
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

        $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
        $receiptPayment->e_tin = $request->e_tin;
        $receiptPayment->tax_section_id = $request->nature_of_organization;

        $receiptPayment->notes = $request->notes;
        $receiptPayment->amount =  $grandTotal;
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
        $log->account_head_id = $receiptPayment->payment_account_head_id;
        $log->tax_section_id = $request->nature_of_organization;
        $log->amount = $grandTotal;
        $log->project_id = $request->project;
        $log->notes = $receiptPayment->notes;
        $log->save();

        $counter = 0;
        foreach ($request->account_head_code as $reqAccountHeadCode) {

            $accountHead = AccountHead::where('id', $request->account_head_code[$counter])
                ->first();

            $receiptPaymentDetail = new ReceiptPaymentDetail();
            $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
            $receiptPaymentDetail->account_head_id = $request->account_head_code[$counter];
            $receiptPaymentDetail->amount = $request->amount[$counter];

            $vatAccountHead = AccountHead::where('id', 3)
                ->first();
            if ($request->vat_rate[$counter] != '') {
                $receiptPaymentDetail->vat_account_head_id = $vatAccountHead->id;
                $receiptPaymentDetail->vat_base_amount = $request->vat_base_amount[$counter] ?? 0;
                $receiptPaymentDetail->vat_rate = $request->vat_rate[$counter] ?? 0;
                $receiptPaymentDetail->vat_amount = $request->vat_amount[$counter] ?? 0;
            }

            $aitAccountHead = AccountHead::where('id', 4)
                ->first();
            if ($request->ait_rate[$counter] != '') {
                $receiptPaymentDetail->ait_account_head_id = $aitAccountHead->id;
                $receiptPaymentDetail->ait_base_amount = $request->ait_base_amount[$counter] ?? 0;
                $receiptPaymentDetail->ait_rate = $request->ait_rate[$counter] ?? 0;
                $receiptPaymentDetail->ait_amount = $request->ait_amount[$counter] ?? 0;
            }
            $subTotalDetail = $request->amount[$counter] - (($request->vat_amount[$counter] ?? 0) + $request->ait_amount[$counter] ?? 0);
            $receiptPaymentDetail->net_total = $subTotalDetail;
            $receiptPaymentDetail->save();

            //Account Head Amount ->debit
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
            $log->payment_account_head_id = $request->bank_account_code;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
            $log->account_head_id = $request->account_head_code[$counter];
            $log->amount = $request->amount[$counter] - ($request->vat_amount[$counter] + $request->ait_amount[$counter]);
            $log->notes = $request->notes;
            $log->save();

            if ($request->vat_rate[$counter] > 0) {
                //credit
                $log = new TransactionLog();
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->payment_account_head_id = $request->bank_account_code;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 4;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $vatAccountHead->id;
                $log->amount = $request->vat_amount[$counter];
                $log->notes = $request->notes;
                $log->save();
                //debit
                $log = new TransactionLog();
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->payment_account_head_id = $request->bank_account_code;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 44;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $request->account_head_code[$counter];
                $log->amount = $request->vat_amount[$counter];
                $log->notes = $request->notes;
                $log->save();
            }

            if ($request->ait_rate[$counter] > 0) {
                //credit
                $log = new TransactionLog();
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->payment_account_head_id = $request->bank_account_code;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 5;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $aitAccountHead->id;
                $log->amount = $request->ait_amount[$counter];
                $log->notes = $request->notes;
                $log->tax_section_id = $request->nature_of_organization;
                $log->save();
                //debit
                $log = new TransactionLog();
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->payment_account_head_id = $request->bank_account_code;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);;
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 55;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $request->account_head_code[$counter];
                $log->amount = $request->ait_amount[$counter];
                $log->notes = $request->notes;
                $log->save();
            }

            $counter++;
        }

        if ($request->other_account_head_code) {
            $counter = 0;
            foreach ($request->other_account_head_code as $reqOtherAccountHeadCode) {

                $otherAccountHead = AccountHead::where('id', $request->other_account_head_code[$counter])
                    ->first();

                $receiptPaymentDetail = new ReceiptPaymentDetail();
                $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
                $receiptPaymentDetail->other_head = 1;
                $receiptPaymentDetail->parent_deduction_account_head_id = $request->parent_account_head_code[$counter];
                $receiptPaymentDetail->account_head_id = $request->other_account_head_code[$counter];
                $receiptPaymentDetail->other_amount = $request->other_amount[$counter];
                $receiptPaymentDetail->save();


                // Other Head Amount credit
                $log = new TransactionLog();
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->other_head = 1;
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 17;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $request->other_account_head_code[$counter];
                $log->amount = $request->other_amount[$counter];
                $log->notes = $request->notes;
                $log->save();

                // Other Head Amount debit
                $log = new TransactionLog();
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->other_head = 1;
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 34;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $request->parent_account_head_code[$counter];
                $log->amount = $request->other_amount[$counter];
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
                $receiptPaymentFile->receipt_payment_id = $receiptPayment->id;
                $receiptPaymentFile->file = $path;
                $receiptPaymentFile->save();
            }
        }

        return redirect()->route('bank_voucher_details', ['receiptPayment' => $receiptPayment->id])
            ->with('message', 'Bank Voucher(VB) created');
    }

    public function edit(ReceiptPayment $receiptPayment)
    {

        $yearGet = financialYearToYear($receiptPayment->financial_year);
        $voucherExplode = voucherExplode($receiptPayment->receipt_payment_no);

        $fiscalYear = explode('-', $receiptPayment->financial_year)[0];
        $taxSections = TaxSection::orderBy('sort')->get();
        return view('accounts.bank_voucher.edit', compact('receiptPayment', 'yearGet', 'voucherExplode', 'fiscalYear', 'taxSections'));
    }

    public function editPost(ReceiptPayment $receiptPayment, Request $request)
    {

        $rules = [
            'client_type' => 'required',
            'bank_account_code' => 'required',
            'date' => 'required',
            'cheque_no' => 'required',
            'e_tin' => 'nullable|max:255',
            'designation' => 'nullable|max:255',
            'address' => 'nullable|max:255',
            'mobile_no' => 'nullable|digits:11',
            'email' => 'nullable|email',
            'account_head_code.*' => 'required',
            'amount.*' => 'required|numeric',
            'vat_base_amount.*' => 'nullable|numeric',
            'vat_rate.*' => 'nullable|numeric',
            'vat_amount.*' => 'nullable|numeric',
            'ait_base_amount.*' => 'nullable|numeric',
            'ait_rate.*' => 'nullable|numeric',
            'ait_amount.*' => 'nullable|numeric',
            'notes' => 'nullable|max:255',
        ];

        if ($request->client_type == 1) {
            $rules['payee'] = 'required';

        } else {
            $rules['payee_name'] = 'required';
        }

        if ($request->other_account_head_code) {
            $rules['parent_account_head_code.*'] = 'required';
            $rules['other_account_head_code.*'] = 'required';
            $rules['other_amount.*'] = 'required|numeric';
        }
        if ($request->ait_amount) {
            $counter = 0;
            $aitAmountTotal = 0;
            foreach ($request->ait_amount as $reqAit) {
                $aitAmountTotal += $request->ait_amount[$counter];
                $counter++;
            }
            if ($aitAmountTotal > 0) {
                $rules['nature_of_organization'] = 'required';
            }
        }

        $request->validate($rules);

        $counter = 0;
        $totalOtherAmount = 0;

        $parentAccountHeadsId = [];
        if ($request->other_account_head_code) {
            foreach ($request->other_account_head_code as $reqOtherAccountHeadCode) {
                array_push($parentAccountHeadsId, $request->parent_account_head_code[$counter]);
                $totalOtherAmount += $request->other_amount[$counter];
                $counter++;
            }
        }
        $counter = 0;
        $totalAmount = 0;
        $totalVatAmount = 0;
        $totalAitAmount = 0;

        $accountHeadsId = [];
        if ($request->account_head_code) {
            foreach ($request->account_head_code as $reqAccountHeadCode) {
                array_push($accountHeadsId, $reqAccountHeadCode);
                $totalAmount += $request->amount[$counter];
                $totalVatAmount += $request->vat_amount[$counter] ?? 0;
                $totalAitAmount += $request->ait_amount[$counter] ?? 0;

                $counter++;
            }
            $grandTotal = $totalAmount - ($totalOtherAmount + $totalVatAmount + $totalAitAmount);

        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Account Head is empty !');
        }

        $compareDeductionParentHeads = array_intersect($accountHeadsId, $parentAccountHeadsId);
        if (count($parentAccountHeadsId) != count($compareDeductionParentHeads)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Deduction parent account head  and payment account head mismatch!');
        }


        //create dynamic voucher no process start
        $transactionType = 2;
        $financialYear = $request->financial_year;
        $bankAccountId = $request->bank_account_code;
        $cashId = null;
        //$voucherNo = generateVoucherReceiptNo($financialYear, $bankAccountId, $cashId, $transactionType);

        //create dynamic voucher no process end


        $receiptPayment->date = Carbon::parse($request->date)->format('Y-m-d');
        $receiptPayment->transaction_type = 2;
        $receiptPayment->payment_type = 1;
        $receiptPayment->cheque_no = $request->cheque_no;
        $receiptPayment->cheque_date = Carbon::parse($request->date)->format('Y-m-d');


        if ($request->client_type == 1) {
            $accountHead = AccountHead::find($request->payee);
        } else {
            $maxClientId = Client::max('id_no');
            if (!$maxClientId) {
                $maxClientId = 100;
            }
            $client = new Client();
            $client->type = 2;//supplier
            $client->id_no = $maxClientId + 1;
            $client->name = $request->payee_name;
            $client->designation = $request->designation;
            $client->address = $request->address;
            $client->email = $request->email;
            $client->mobile = $request->mobile_no;
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
        $receiptPayment->payee_depositor_account_head_id = $accountHead->id;
        $receiptPayment->e_tin = $request->e_tin;
        $receiptPayment->tax_section_id = $request->nature_of_organization;
        $receiptPayment->notes = $request->notes;
        $receiptPayment->save();

        //


        $voucherNo = $receiptPayment->receipt_payment_no;
        $receiptPaymentNoExplode = explode("-", $voucherNo);

        $receiptPaymentNoSl = $receiptPaymentNoExplode[1];

        $request->financial_year = financialYearToYear($request->financial_year);

        ReceiptPaymentDetail::where('receipt_payment_id', $receiptPayment->id)->delete();
        TransactionLog::where('receipt_payment_id', $receiptPayment->id)->delete();

        //Bank Credit
        $log = new TransactionLog();
        $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
        $log->payee_depositor_account_head_id = $accountHead->id;
        $log->tax_section_id = $request->nature_of_organization;
        $log->receipt_payment_no = $receiptPayment->receipt_payment_no;
        $log->receipt_payment_sl = $receiptPaymentNoSl;
        $log->financial_year = $receiptPayment->financial_year;
        $log->date = $receiptPayment->date;
        $log->receipt_payment_id = $receiptPayment->id;
        $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
        $log->cheque_no = $request->cheque_no;
        $log->transaction_type = 11;//Bank Credit
        $log->payment_type = 1;
        $log->account_head_id = $receiptPayment->payment_account_head_id;
        $log->amount = $grandTotal;
        $log->notes = $receiptPayment->notes;
        $log->save();

        $counter = 0;
        foreach ($request->account_head_code as $reqAccountHeadCode) {

            $accountHead = AccountHead::where('id', $request->account_head_code[$counter])
                ->first();

            $receiptPaymentDetail = new ReceiptPaymentDetail();
            $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
            $receiptPaymentDetail->account_head_id = $request->account_head_code[$counter];
            $receiptPaymentDetail->amount = $request->amount[$counter];

            $vatAccountHead = AccountHead::where('id', 3)
                ->first();
            if ($request->vat_rate[$counter] != '') {
                $receiptPaymentDetail->vat_account_head_id = $vatAccountHead->id;
                $receiptPaymentDetail->vat_base_amount = $request->vat_base_amount[$counter] ?? 0;
                $receiptPaymentDetail->vat_rate = $request->vat_rate[$counter] ?? 0;
                $receiptPaymentDetail->vat_amount = $request->vat_amount[$counter] ?? 0;
            }
            $aitAccountHead = AccountHead::where('id',4)
                ->first();
            if ($request->ait_rate[$counter] != '') {
                $receiptPaymentDetail->ait_account_head_id = $aitAccountHead->id;
                $receiptPaymentDetail->ait_base_amount = $request->ait_base_amount[$counter] ?? 0;
                $receiptPaymentDetail->ait_rate = $request->ait_rate[$counter] ?? 0;
                $receiptPaymentDetail->ait_amount = $request->ait_amount[$counter] ?? 0;
            }
            $subTotalDetail = $request->amount[$counter] - (($request->vat_amount[$counter] ?? 0) + $request->ait_amount[$counter] ?? 0);
            $receiptPaymentDetail->net_total = $subTotalDetail;
            $receiptPaymentDetail->save();

            //Payment Head Amount
            $log = new TransactionLog();
            $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
            $log->payee_depositor_account_head_id = $accountHead->id;
            $log->tax_section_id = $request->nature_of_organization;
            $log->receipt_payment_no = $voucherNo;
            $log->receipt_payment_sl = $receiptPaymentNoSl;
            $log->financial_year = financialYear($request->financial_year);
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_id = $receiptPayment->id;
            $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
            $log->transaction_type = 2;
            $log->payment_type = 1;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
            $log->account_head_id = $request->account_head_code[$counter];
            $log->amount = $request->amount[$counter] - ($request->vat_amount[$counter] + $request->ait_amount[$counter]);
            $log->notes = $request->notes;

            $log->save();

            if ($request->vat_rate[$counter] > 0) {
                //credit
                $log = new TransactionLog();
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->tax_section_id = $request->nature_of_organization;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 4;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $vatAccountHead->id;
                $log->amount = $request->vat_amount[$counter];
                $log->notes = $request->notes;
                $log->save();
                //debit
                $log = new TransactionLog();
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->tax_section_id = $request->nature_of_organization;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 44;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $request->account_head_code[$counter];
                $log->amount = $request->vat_amount[$counter];
                $log->notes = $request->notes;
                $log->save();

            }

            if ($request->ait_rate[$counter] > 0) {
                // credit
                $log = new TransactionLog();
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->tax_section_id = $request->nature_of_organization;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 5;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $aitAccountHead->id;
                $log->amount = $request->ait_amount[$counter];
                $log->notes = $request->notes;
                $log->save();

                // debit
                $log = new TransactionLog();
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->tax_section_id = $request->nature_of_organization;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->reconciliation = $request->reconciliation ?? 0;
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 55;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $request->account_head_code[$counter];
                $log->amount = $request->ait_amount[$counter];
                $log->notes = $request->notes;
                $log->save();
            }

            $counter++;
        }

        if ($request->other_account_head_code) {
            $counter = 0;
            foreach ($request->other_account_head_code as $reqOtherAccountHeadCode) {

                $otherAccountHead = AccountHead::where('id', $request->other_account_head_code[$counter])
                    ->first();

                $receiptPaymentDetail = new ReceiptPaymentDetail();
                $receiptPaymentDetail->receipt_payment_id = $receiptPayment->id;
                $receiptPaymentDetail->other_head = 1;
                $receiptPaymentDetail->parent_deduction_account_head_id = $request->parent_account_head_code[$counter];
                $receiptPaymentDetail->account_head_id = $request->other_account_head_code[$counter];
                $receiptPaymentDetail->other_amount = $request->other_amount[$counter];
                $receiptPaymentDetail->save();


                // Other Head Amount credit
                $log = new TransactionLog();
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->other_head = 1;
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 17;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $request->other_account_head_code[$counter];
                $log->amount = $request->other_amount[$counter];
                $log->notes = $request->notes;
                $log->save();

                // Other Head Amount debit
                $log = new TransactionLog();
                $log->payment_account_head_id = $receiptPayment->payment_account_head_id;
                $log->payee_depositor_account_head_id = $accountHead->id;
                $log->receipt_payment_no = $voucherNo;
                $log->receipt_payment_sl = $receiptPaymentNoSl;
                $log->financial_year = financialYear($request->financial_year);
                $log->other_head = 1;
                $log->date = Carbon::parse($request->date)->format('Y-m-d');
                $log->receipt_payment_id = $receiptPayment->id;
                $log->receipt_payment_detail_id = $receiptPaymentDetail->id;
                $log->transaction_type = 34;
                $log->payment_type = 1;
                $log->cheque_no = $request->cheque_no;
                $log->cheque_date = Carbon::parse($request->date)->format('Y-m-d');
                $log->account_head_id = $request->parent_account_head_code[$counter];
                $log->amount = $request->other_amount[$counter];
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
                $receiptPaymentFile->receipt_payment_id = $receiptPayment->id;
                $receiptPaymentFile->file = $path;
                $receiptPaymentFile->save();

            }
        }

        return redirect()->route('bank_voucher_details', ['receiptPayment' => $receiptPayment->id])
            ->with('message', 'Bank Voucher(VB) Updated');

    }

    public function details(ReceiptPayment $receiptPayment)
    {
        $inWordAmount = new DecimalToWords();
        return view('accounts.bank_voucher.details', compact('receiptPayment','inWordAmount'));
    }

    public function print(ReceiptPayment $receiptPayment)
    {
        $inWordAmount = new DecimalToWords();
        return view('accounts.bank_voucher.print', compact('receiptPayment','inWordAmount'));
    }

    public function rangePrint(Request $request)
    {
        $selectBank = AccountHead::find($request->bank_account_code);
        if (!$selectBank) {
            abort('404', 'Bank Account not found!');
        }

        $from = 'BV-' . $request->from . '-' . $selectBank->account_code;
        $to = 'BV-' . $request->to . '-' . $selectBank->account_code;

        $voucherLists = [];
        for ($i = $request->from; $i <= $request->to; $i++) {
            array_push($voucherLists, 'BV-' . $i . '-' . $selectBank->account_code);
        }

        $receiptPayments = ReceiptPayment::where('transaction_type', 2)
            ->where('payment_type', 1)
            ->where('payment_account_head_id', $request->bank_account_code)
            ->whereIn('receipt_payment_no', $voucherLists)
            ->orderBy('id')
            ->get();

        if (count($receiptPayments) <= 0)
            return redirect()->back()->with('error', 'Opps... Somethings wrong!');

        $inWordAmount = new DecimalToWords();

        return view('accounts.bank_voucher.range_print', compact('receiptPayments',
            'from', 'to','inWordAmount'));
    }
}
