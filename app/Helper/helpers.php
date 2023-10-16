<?php



use App\Models\AccountHead;
use App\Models\Client;
use App\Models\ReceiptPaymentDetail;
use App\Models\TransactionLog;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;


if (! function_exists('financialYear')) {
    function financialYear($year)
    {

        $start = $year;
        $end = ($year + 1);
        return $financialYear = $start.'-'.$end;

    }
}
if (! function_exists('generateVoucherReceiptNo')) {
    function generateVoucherReceiptNo($financialYear,$bankAccountId = null,$cashId = null,$transactionType)
    {
        $financialYear = financialYear($financialYear);
        $query = TransactionLog::whereNull('jv_type')
            ->where('financial_year',$financialYear);
        $vR = '';
        if ($transactionType == 2){
            if ($bankAccountId){
                $query->whereIn('transaction_type',[$transactionType,15]);
                $query->where('payment_account_head_id',$bankAccountId);
                $bankAccount = AccountHead::where('id',$bankAccountId)->first();
                $vR = 'BV-'.'1-'.$bankAccount->account_code;
            }
            if ($cashId){
                $query->where('transaction_type',$transactionType);
                $query->where('payment_account_head_id',$cashId);
                $cashAccount = AccountHead::where('id',$cashId)->first();
                $vR = 'CV-'.'1-'.$cashAccount->account_code;
            }
        }else{
            if ($bankAccountId){
                $query->whereIn('transaction_type',[$transactionType,16]);
                $query->where('payment_account_head_id',$bankAccountId);
                $bankAccount = AccountHead::where('id',$bankAccountId)->first();
                $vR = 'BMR-'.'1-'.$bankAccount->account_code;

            }
            if ($cashId){

                $query->whereIn('transaction_type',[$transactionType,16]);
                $query->where('payment_account_head_id',$cashId);
                $cashAccount = AccountHead::where('id',$cashId)->first();
                $vR = 'CMR-'.'1-'.$cashAccount->account_code;
            }
        }

        $maxVoucherReceiptNo = $query->max('receipt_payment_sl');
        $receiptPayment = $query->orderBy('receipt_payment_sl','desc')
            ->first();

        if ($receiptPayment)
            $receiptPaymentNo = $maxVoucherReceiptNo;


        if ($transactionType == 2){
            if ($receiptPayment){
                if ($cashId){
                    $vR = 'CV-'.($receiptPaymentNo + 1).'-'.$cashAccount->account_code;
                }
                if ($bankAccountId){
                    $vR = 'BV-'.($receiptPaymentNo+1).'-'.$bankAccount->account_code;
                }
            }
        }else{
            if ($receiptPayment){
                if ($cashId){
                    $vR = 'CMR-'.($receiptPaymentNo+1).'-'.$cashAccount->account_code;
                }
                if ($bankAccountId){

                    $vR = 'BMR-'.($receiptPaymentNo+1).'-'.$bankAccount->account_code;
                }
            }
        }
        return $vR;

    }
}
if (! function_exists('financialYearToYear')) {
    function financialYearToYear($financialYear)
    {

        $financialYear = explode("-",$financialYear);

        if ($financialYear)
            return $financialYear[0];

        return null;

    }
}
if (! function_exists('employeeClientInfo')) {
    function employeeClientInfo($id)
    {
        $accountHead = AccountHead::where('id',$id)->first();
        if($accountHead){
            if ($accountHead->client_id){
                return Client::find($accountHead->client_id) ?? null;
            }elseif($accountHead->employee_id){
                return Employee::find($accountHead->employee_id) ?? null;
            }else{
                return null;
            }

        }else{
            return null;
        }

    }
    if (! function_exists('voucherExplode')) {
        function voucherExplode($receipt_payment_no)
        {

            $receipt_payment_no = explode("-",$receipt_payment_no);

            if ($receipt_payment_no)
                return $receipt_payment_no;

            return null;

        }
    }
    if (! function_exists('previousLedger')) {
        function previousLedger($start_date,$end_date,$account_head_id)
        {

            $fromCostCenter = null;
            $toCostCenter = null;
            $start = date('Y-m-d', strtotime($start_date));
            $end = date('Y-m-d', strtotime($end_date));



            $query = TransactionLog::query();

            $previousDay = date('Y-m-d', strtotime('-1 day', strtotime($start)));
            $incomeExpenses = $query->where('account_head_id',$account_head_id)
                ->whereDate('date', '<=', $previousDay)
                ->with('receiptPayment')
                ->get();

//            dd($incomeExpenses);

            $debitTotal = 0;
            $creditTotal = 0;
            $accountOpeningHead = AccountHead::where('id',$account_head_id)->first();

            if ($accountOpeningHead){
                if ($accountOpeningHead->account_head_type_id == 1){
                    $debitTotal = $accountOpeningHead->opening_balance;
                }elseif ($accountOpeningHead->account_head_type_id == 2 || $accountOpeningHead->account_head_type_id == 3){
                    $creditTotal = $accountOpeningHead->opening_balance;
                }
            }

            foreach($incomeExpenses as $incomeExpense) {
                if (in_array($incomeExpense->transaction_type, [2,44,55,8,13,14,16,18]))
                    $debitTotal += $incomeExpense->amount;

                if (in_array($incomeExpense->transaction_type, [1,4,5,9,11,12,15,17]))
                    $creditTotal += $incomeExpense->amount;

                if ($incomeExpense->transaction_type == 2){
                    if (otherDeductLastItemCheck($incomeExpense->receipt_payment_id, $incomeExpense->receipt_payment_detail_id)) {
                        if ($incomeExpense->receiptPayment) {
                            foreach ($incomeExpense->receiptPayment->receiptPaymentOtherDetails as $receiptPaymentOtherDetail) {
                                $debitTotal += $receiptPaymentOtherDetail->other_amount;
                            }
                        }
                    }
                }

            }

            return [
                'debitOpening'=>$debitTotal,
                'creditOpening'=>$creditTotal,
                'accountOpeningHead'=>$accountOpeningHead,
            ];
        }
    }
    if (! function_exists('ledger')) {
        function ledger($start_date,$end_date,$account_head_id)
        {


            $fromCostCenter = null;
            $toCostCenter = null;
            $start = date('Y-m-d', strtotime($start_date));
            $end = date('Y-m-d', strtotime($end_date));


            return TransactionLog::where('account_head_id',$account_head_id)
                ->whereBetween('date', [$start, $end])
                ->with('receiptPayment')
                ->orderBy('date')
                ->orderBy('receipt_payment_no')
                ->get();
        }
    }

//    if (! function_exists('previousLedger')) {
//        function previousLedger($start_date,$end_date,$account_head_id)
//        {
//
//            $start = date('Y-m-d', strtotime($start_date));
//            $end = date('Y-m-d', strtotime($end_date));
//
//            $previousDay = date('Y-m-d', strtotime('-1 day', strtotime($start)));
//
//            $debitTotal = 0;
//            $creditTotal = 0;
//            $accountOpeningHead = AccountHead::where('id',$account_head_id)->first();
//
//            if ($accountOpeningHead){
//                if ($accountOpeningHead->account_head_type_id == 1){
//                    $debitTotal = $accountOpeningHead->opening_balance;
//                }elseif ($accountOpeningHead->account_head_type_id == 2 || $accountOpeningHead->type_id == 3){
//                    $creditTotal = $accountOpeningHead->opening_balance;
//                }
//            }
//
//            $debitTotal += TransactionLog::where('account_head_id',$account_head_id)
//                ->where('transaction_type',1)
//                ->whereDate('date', '<=', $previousDay)
//                ->sum('amount');
//
//            $creditTotal += TransactionLog::where('account_head_id',$account_head_id)
//                ->where('transaction_type',2)
//                ->whereDate('date', '<=', $previousDay)
//                ->sum('amount');
//
//            return [
//                'debitOpening'=>$debitTotal,
//                'creditOpening'=>$creditTotal,
//                'accountOpeningHead'=>$accountOpeningHead,
//            ];
//        }
//    }
//    if (! function_exists('ledger')) {
//        function ledger($start_date,$end_date,$account_head_id)
//        {
//            $start = date('Y-m-d', strtotime($start_date));
//            $end = date('Y-m-d', strtotime($end_date));
//
//            return TransactionLog::where('account_head_id',$account_head_id)
//                ->whereBetween('date', [$start, $end])
//                ->orderBy('date')
//                ->orderBy('receipt_payment_no')
//                ->get();
//
//        }
//    }
    if (! function_exists('otherDeductLastItemCheck')) {
        function otherDeductLastItemCheck($id,$detailId)
        {
            $check = ReceiptPaymentDetail::where('receipt_payment_id',$id)
                ->where('other_head',0)
                ->orderBy('id','desc')
                ->first();
            if ($check){
                if ($check->id == $detailId)
                    return true;
                else
                    return false;
            }else{
                return false;
            }


        }
    }
    if (! function_exists('otherDeductLastItemCheckDetail')) {
        function otherDeductLastItemCheckDetail($id)
        {
            $check = ReceiptPaymentDetail::with('accountHead')
                ->where('receipt_payment_id',$id)
                ->where('other_head',0)
                ->orderBy('id','desc')
                ->first();

            if ($check){
                return $check;
            }else{
                return false;
            }


        }
    }

    if (! function_exists('previousAccountTrailDebitCredit')) {
        function previousAccountTrailDebitCredit($start_date,$end_date,$account_head_code)
        {

            $start = date('Y-m-d', strtotime($start_date));
            $end = date('Y-m-d', strtotime($end_date));
            $previousDay = date('Y-m-d', strtotime('-1 day', strtotime($start)));
            $debitTotal = 0;
            $creditTotal = 0;
            $accountOpeningHead = AccountHead::select('id','account_head_type_id','opening_balance')
                ->where('id',$account_head_code)
                ->first();

            if ($accountOpeningHead){
                if ($accountOpeningHead->account_head_type_id == 1){
                    $debitTotal += $accountOpeningHead->opening_balance;
                }elseif ($accountOpeningHead->account_head_type_id == 2 || $accountOpeningHead->account_head_type_id == 3){
                    $creditTotal += $accountOpeningHead->opening_balance;
                }
            }

            $debitTotal += TransactionLog::select('account_head_id','date','transaction_type','amount')
                ->where('account_head_id',$account_head_code)
                ->where('date', '<=', $previousDay)
                ->whereIn('transaction_type',[2,44,55, 8,13,14,16,18])
                ->sum('amount');
            $creditTotal += TransactionLog::select('account_head_id','date','transaction_type','amount')
                ->where('account_head_id',$account_head_code)
                ->where('date', '<=', $previousDay)
                ->whereIn('transaction_type',[1,4,5, 9, 11, 12, 15,17])
                ->sum('amount');

            return [
                'debitOpeningTotal'=>$debitTotal,
                'creditOpeningTotal'=>$creditTotal,
            ];
        }
    }
    if (! function_exists('accountTrailDebitCredit')) {
        function accountTrailDebitCredit($start_date,$end_date,$account_head_code)
        {

            $start = date('Y-m-d', strtotime($start_date));
            $end = date('Y-m-d', strtotime($end_date));

            return [
                'debitTotal'=>TransactionLog::select('account_head_id','date','transaction_type','amount')
                    ->where('account_head_id',$account_head_code)
                    ->whereBetween('date', [$start, $end])
                    ->whereIn('transaction_type',[2,44,55, 8, 13, 14, 16,18])
                    ->sum('amount'),
                'creditTotal'=>TransactionLog::select('account_head_id','date','transaction_type','amount')
                    ->where('account_head_id',$account_head_code)
                    ->whereBetween('date', [$start, $end])
                    ->whereIn('transaction_type',[1,4,5, 9, 11, 12, 15,17])
                    ->sum('amount'),
            ];
        }
    }


    function convertDateToFiscalYear($date)
    {
        $currentMonth = Carbon::parse($date)->format('m');
        $currentYearInit = Carbon::parse($date)->format('Y');
        if ($currentMonth < 7) {
            $currentYear = date($currentYearInit) - 1;
            return date($currentYear);
        } else {
            return date($currentYearInit);
        }
    }
}
