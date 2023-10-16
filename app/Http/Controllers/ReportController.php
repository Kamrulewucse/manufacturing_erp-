<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\PurchaseProduct;
use App\Models\ReceiptPayment;
use App\Models\SalesOrder;
use App\Models\ServiceOrder;
use App\Models\Supplier;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SakibRahaman\DecimalToWords\DecimalToWords;

class ReportController extends Controller
{
    public function ledger(Request $request)
    {
        $accountHeads = AccountHead::orderBy('account_code')->get();

        $query = AccountHead::query();

        if ($request->search != '' && $request->account_head != ''){
            $query->where('id',$request->account_head);
        }

        $accountHeadsSearch = $query->orderBy('account_code')
            ->get();

        $in_word = new DecimalToWords();

        $currentMonth = date('m');
        if ($currentMonth < 7) {
            $currentYear = date('Y') - 1;
            $currentDate = date('01-07-' . $currentYear);
        } else {
            $currentDate = date('01-07-Y');
        }


        $startDate = date('Y-m-d', strtotime($request->start_date));
        $endDate = date('Y-m-d', strtotime($request->end_date));

        $month = strtotime($startDate);
        $end = strtotime($endDate);
        $monthsArray = [];
        while($month <= $end)
        {
            $monthGenerate = date('Y-m', $month);
            array_push($monthsArray,$monthGenerate);
            $month = strtotime("+1 month", $month);

        }


        return view('report.ledger', compact('accountHeadsSearch','accountHeads','monthsArray','currentDate','in_word'));
    }

    public function trailBalance(Request $request)
    {
        $accountHeads = AccountHead::orderBy('account_code')->get();

        $query = AccountHead::query();
        if ($request->search != '' && $request->account_head != ''){
            $query->where('id',$request->account_head);
        }
        $accountHeadsSearch = $query->orderBy('account_code')
            ->get();


        $in_word = new DecimalToWords();

        $currentMonth = date('m');
        if ($currentMonth < 7) {
            $currentYear = date('Y') - 1;
            $currentDate = date('01-07-' . $currentYear);
        } else {
            $currentDate = date('01-07-Y');
        }


        return view('report.trail_balance', compact('currentDate',
            'accountHeads','in_word',
            'accountHeadsSearch'));
    }

    public function receivePayment(Request $request){

        $receipts = [];
        $payments = [];

        if ( $request->start!='' && $request->end!='') {
            $receipts = ReceiptPayment::where('transaction_type',1)
                ->whereBetween('date', [$request->start, $request->end])
                ->get();

//            dd($receipts);

            $payments = ReceiptPayment::where('transaction_type',2)
                ->whereBetween('date', [$request->start, $request->end])
                ->get();
        }

        return view('report.receive_payment', compact('receipts','payments'));
    }

    public function supplierReport(Request $request){
        $suppliers = Client::where('type',2)->orderBy('name')->get();
        $appends = [];
        $query = PurchaseOrder::query();

        if ($request->supplier && $request->supplier != '') {
            $query->where('supplier_id', $request->supplier);
            $appends['supplier'] = $request->supplier;
        }

        $query->selectRaw('supplier_id, SUM(total) as total_sum,SUM(due) as due_sum,
                       SUM(paid) as paid_sum')
            ->groupBy('supplier_id');

        $orders = $query->get();


        return view('report.supplier_ledger',compact('suppliers','orders','appends'));
    }

    public function purchaseReport(Request $request){
        $suppliers = Client::where('type',2)->orderBy('name')->get();
        $appends = [];
        $query = PurchaseOrder::with('supplier');

        $start = date('Y-m-d', strtotime($request->start));
        $end = date('Y-m-d', strtotime($request->end));

        if ($request->start && $request->end) {
            $query->whereBetween('date', [$start, $end]);
            $appends['date'] = $request->date;
        }

        if ($request->supplier && $request->supplier != '') {
            $query->where('supplier_id', $request->supplier);
            $appends['supplier'] = $request->supplier;
        }

        $currentMonth = date('m');
        if ($currentMonth < 7) {
            $currentYear = date('Y') - 1;
            $currentDate = date('01-08-' . $currentYear);
        } else {
            $currentDate = date('01-08-Y');
        }

        $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        $data = [
            'total' => $query->sum('total'),
            'due' => $query->sum('due'),
            'paid' => $query->sum('paid'),
        ];

        $orders = $query->paginate(10);


        return view('report.purchase_report', compact('orders', 'suppliers',
            'appends','currentDate'))->with($data);
    }

    public function customerReport(Request $request){
        $customers = Client::where('type',1)->orderBy('name')->get();
        $appends = [];
        $query = SalesOrder::query();

        if ($request->customer && $request->customer != '') {
            $query->where('client_id', $request->customer);
            $appends['customer'] = $request->customer;
        }
        $query->selectRaw('client_id, SUM(total) as total_sum,SUM(due) as due_sum,
                       SUM(paid) as paid_sum')
            ->groupBy('client_id');

        $orders = $query->get();
        return view('report.customer_report',compact('customers','orders','appends'));
    }
    public function salesReport(Request $request){
        $customers = Client::where('type',1)->orderBy('name')->get();
        $appends = [];
        $query = SalesOrder::query();

        $start = date('Y-m-d', strtotime($request->start));
        $end = date('Y-m-d', strtotime($request->end));

        if ($request->start && $request->end) {
            $query->whereBetween('date', [$start, $end]);
            $appends['date'] = $request->date;
        }

        if ($request->customer && $request->customer != '') {
            $query->where('client_id', $request->customer);
            $appends['customer'] = $request->customer;
        }

        $currentMonth = date('m');
        if ($currentMonth < 7) {
            $currentYear = date('Y') - 1;
            $currentDate = date('01-08-' . $currentYear);
        } else {
            $currentDate = date('01-08-Y');
        }

        $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        $data = [
            'total' => $query->sum('total'),
            'due' => $query->sum('due'),
            'paid' => $query->sum('paid'),
        ];

        $orders = $query->paginate(10);

        return view('report.sales_report',compact('customers','orders','appends','currentDate'));
    }
    public function saleTransactionReport(Request $request){
        $customers = Client::where('type',1)->orderBy('name')->get();
        $appends = [];
        $query = SalesOrder::query();

        $start = date('Y-m-d', strtotime($request->start));
        $end = date('Y-m-d', strtotime($request->end));

        if ($request->start && $request->end) {
            $query->whereBetween('date', [$start, $end]);
            $appends['date'] = $request->date;
        }

        if ($request->customer && $request->customer != '') {
            $query->where('client_id', $request->customer);
            $appends['customer'] = $request->customer;
        }

        $currentMonth = date('m');
        if ($currentMonth < 7) {
            $currentYear = date('Y') - 1;
            $currentDate = date('01-08-' . $currentYear);
        } else {
            $currentDate = date('01-08-Y');
        }

        $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        $data = [
            'total' => $query->sum('total'),
            'due' => $query->sum('due'),
            'paid' => $query->sum('paid'),
        ];

        $orders = $query->paginate(10);

        return view('report.sale_transaction_report',compact('customers','orders','appends','currentDate'));
    }
    public function productSerial(Request $request){
        $appends = [];
        $query = Inventory::with('product','inventoryLog')->where('product_type', 3);

        $start = $request->input('start');
        $end = $request->input('end');

        if ($request->has('start') && $request->has('end')) {
            $start = $request->input('start');
            $end = $request->input('end');

            $query->whereBetween('serial', [$start, $end]);
            $appends['start'] = $start;
            $appends['end'] = $end;
        }

        $query->orderBy('serial')->orderBy('created_at', 'desc');
        $serials = $query->get();

        $existingSerials = $serials->pluck('serial')->toArray();

        $allSerials = range($start, $end);

        $missingSerials = array_diff($allSerials, $existingSerials);


        return view('report.product_serial', compact('serials','missingSerials', 'appends'));
    }

    public function serviceReport(Request $request){
        $customers = Client::where('type',1)->orderBy('name')->get();
        $appends = [];
        $query = ServiceOrder::query();

        $start = date('Y-m-d', strtotime($request->start));
        $end = date('Y-m-d', strtotime($request->end));

        if ($request->start && $request->end) {
            $query->whereBetween('date', [$start, $end]);
            $appends['date'] = $request->date;
        }

        if ($request->customer && $request->customer != '') {
            $query->where('client_id', $request->customer);
            $appends['customer'] = $request->customer;
        }

        $currentMonth = date('m');
        if ($currentMonth < 7) {
            $currentYear = date('Y') - 1;
            $currentDate = date('01-08-' . $currentYear);
        } else {
            $currentDate = date('01-08-Y');
        }

        $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        $data = [
            'total' => $query->sum('total'),
            'due' => $query->sum('due'),
            'paid' => $query->sum('paid'),
        ];

        $orders = $query->paginate(10);

        return view('report.service_report',compact('customers','orders','appends','currentDate'));
    }



//    public function projectReport(Request $request){
//
//        //$stakeholdersId = TransactionLog::where('stakeholder_id','!=',null)->groupBy('stakeholder_id')->pluck('stakeholder_id');
//        if (Auth::user()->role == 3 && Auth::user()->admin_status == 1) {
//            $projects = Project::where('status', 1)->get();
//        }else{
//            $projects = Project::where('id',Auth::user()->project_id)->where('status', 1)->get();
//        }
//        $stakeholders = [];
//        $start = null;
//        $end = null;
//        $incomes = null;
//        $expenses = null;
//        $totalDuration = null;
//        $project = null;
//        $otherIncomes = [];
//        $otherExpenses = [];
//        $supplierPayments = [];
//        $supplierDetails = [];
//        $datas = [
//            'project' => '',
//        ];
//        // $query = TransactionLog::where('transaction_type',1)->where('stakeholder_id','!=',null);
//
//
//        if ($request->project && $request->project != ''){
//            $stakeholderIds=ProjectWiseStakeholder::where('project_id',$request->project)->pluck('stakeholder_id');
//            //dd($stakeholderIds);
//            $stakeholders = Stakeholder::whereIn('id',$stakeholderIds)->get();
//            $stakeHolderPayments = StakeholderPayment::whereIn('stakeholder_id',$stakeholderIds)->get();
//            //dd($stakeHolderPayments);
//            $project= Project::find($request->project);
//            $incomes= TransactionLog::where('project_id',$request->project)->where('transaction_type',1)->sum('amount');
//            $expenses= TransactionLog::where('project_id',$request->project)->where('transaction_type',2)->sum('amount');
//            $start= $project->duration_start;
//            $end= $project->duration_end;
//            $totalDuration = $project->total_duration;
//            $otherIncomes = TransactionLog::where('project_id',$request->project)
//                ->where('transaction_type',1)
//                ->where('project_payment_type',3)->get();
//            $otherExpenses = TransactionLog::where('project_id',$request->project)
//                ->where('transaction_type',2)
//                ->where('project_payment_type',3)->get();
//            $supplierPayments= PurchasePayment::where('project_id',$request->project)->get();
//            $supplierDetails= Supplier::all();
//            $datas=[
//                'project' => $project,
//            ];
//        }
//
//        //dd($project);
//
//        return view('report.project_report',compact('projects','supplierPayments','otherIncomes','incomes','start','end',
//            'otherExpenses','totalDuration','expenses','project','datas','stakeholders','supplierDetails'));
//    }
//
//    public function progressReport(){
//        if (Auth::user()->role == 3 && Auth::user()->admin_status == 1) {
//            $projects = Project::where('status', 1)->get();
//        }else{
//            $projects = Project::where('id',Auth::user()->project_id)->where('status', 1)->get();
//        }
//
//        return view('report.progress_report',compact('projects'));
//    }
//
//    public function bankStatement(Request $request) {
//        $banks = Bank::where('status', 1)->orderBy('name')->get();
//
//        $result = null;
//        $metaData = null;
//        if ($request->bank && $request->branch && $request->account && $request->start && $request->end) {
//            $bankAccount = BankAccount::where('id', $request->account)->first();
//            $bank = Bank::where('id', $request->bank)->first();
//            $branch = Branch::where('id', $request->branch)->first();
//
//            $metaData = [
//                'name' => $bank->name,
//                'branch' => $branch->name,
//                'account' => $bankAccount->account_no,
//                'start_date' => $request->start,
//                'end_date' => $request->end,
//            ];
//
//            $result = collect();
//
//            $initialBalance = $bankAccount->opening_balance;
//
//            $previousDay = date('Y-m-d', strtotime('-1 day', strtotime($request->start)));
//
//            $totalIncome = TransactionLog::where('transaction_type', 1)
//                ->where('bank_account_id', $request->account)
//                ->whereDate('date', '<=', $previousDay)
//                ->sum('amount');
//
//            $totalExpense = TransactionLog::where('transaction_type', 2)
//                ->where('bank_account_id', $request->account)
//                ->whereDate('date', '<=', $previousDay)
//                ->sum('amount');
//
//            $openingBalance = $initialBalance + $totalIncome - $totalExpense;
//
//            $result->push(['date' => $request->start_date, 'particular' => 'Opening Balance', 'debit' => '', 'credit' => '', 'balance' => $openingBalance]);
//
//            $transactionLogs = TransactionLog::where('bank_account_id', $request->account)
//                ->whereBetween('date', [$request->start, $request->end])
//                ->get();
//
//            $balance = $openingBalance;
//            $totalDebit = 0;
//            $totalCredit = 0;
//            foreach ($transactionLogs as $log) {
//                if ($log->transaction_type == 1) {
//                    // Income
//                    $balance += $log->amount;
//                    $totalDebit += $log->amount;
//                    $result->push(['date' => $log->date, 'particular' => $log->particular, 'debit' => $log->amount, 'credit' => '', 'balance' => $balance]);
//                } else {
//                    $balance -= $log->amount;
//                    $totalCredit += $log->amount;
//                    $result->push(['date' => $log->date, 'particular' => $log->particular, 'debit' => '', 'credit' => $log->amount, 'balance' => $balance]);
//                }
//            }
//
//            $metaData['total_debit'] = $totalDebit;
//            $metaData['total_credit'] = $totalCredit;
//
//        }
//
//        return view('report.bank_statement', compact('banks', 'result', 'metaData'));
//    }
//
//    public function cashStatement(Request $request) {
//        $result = null;
//        $metaData = null;
//        if ($request->start && $request->end) {
//            $cashAccount = Cash::first();
//
//            $metaData = [
//                'start_date' => $request->start,
//                'end_date' => $request->end,
//            ];
//
//            $result = collect();
//
//            $initialBalance = $cashAccount->opening_balance;
//
//            $previousDay = date('Y-m-d', strtotime('-1 day', strtotime($request->start)));
//
//            if (Auth::user()->company_branch_id == 0) {
//
//                $totalIncome = TransactionLog::where('transaction_type', 1)
//                    ->where('transaction_method', 1)
//                    ->whereDate('date', '<=', $previousDay)
//                    ->orderBy('date')
//                    ->sum('amount');
//
//                $totalExpense = TransactionLog::where('transaction_type', 2)
//                    ->where('transaction_method', 1)
//                    ->whereDate('date', '<=', $previousDay)
//                    ->orderBy('date')
//                    ->sum('amount');
//            }else{
//                $totalIncome = TransactionLog::where('transaction_type', 1)
//                    ->where('transaction_method', 1)
//                    ->where('company_branch_id', Auth::user()->company_branch_id)
//                    ->whereDate('date', '<=', $previousDay)
//                    ->orderBy('date')
//                    ->sum('amount');
//
//                $totalExpense = TransactionLog::where('transaction_type', 2)
//                    ->where('transaction_method', 1)
//                    ->where('company_branch_id', Auth::user()->company_branch_id)
//                    ->whereDate('date', '<=', $previousDay)
//                    ->orderBy('date')
//                    ->sum('amount');
//            }
//
//            $openingBalance = $initialBalance + $totalIncome - $totalExpense;
//
//            $result->push(['date' => $request->start_date, 'particular' => 'Opening Balance', 'debit' => '', 'credit' => '', 'balance' => $openingBalance]);
//
//            if (Auth::user()->company_branch_id == 0) {
//                $transactionLogs = TransactionLog::whereBetween('date', [$request->start, $request->end])
//                    ->where('transaction_method', 1)
//                    ->get();
//            }else{
//                $transactionLogs = TransactionLog::whereBetween('date', [$request->start, $request->end])
//                    ->where('transaction_method', 1)
//                    ->where('company_branch_id', Auth::user()->company_branch_id)
//                    ->get();
//            }
//
//            $balance = $openingBalance;
//            $totalDebit = 0;
//            $totalCredit = 0;
//            foreach ($transactionLogs as $log) {
//                if ($log->transaction_type == 1) {
//                    // Income
//                    $balance += $log->amount;
//                    $totalDebit += $log->amount;
//                    $result->push(['date' => $log->date, 'particular' => $log->particular, 'debit' => $log->amount, 'credit' => '', 'balance' => $balance]);
//                } else {
//                    $balance -= $log->amount;
//                    $totalCredit += $log->amount;
//                    $result->push(['date' => $log->date, 'particular' => $log->particular, 'debit' => '', 'credit' => $log->amount, 'balance' => $balance]);
//                }
//            }
//
//            $metaData['total_debit'] = $totalDebit;
//            $metaData['total_credit'] = $totalCredit;
//
//        }
//
//        return view('report.cash_statement', compact( 'result', 'metaData'));
//    }
//
////    public function allReceivePayment(Request $request) {
////        $segments = ProductSegment::where('project_id',Auth::user()->project_id)
////                ->where('status',1)
////                ->get();
////        $incomes = null;
////        $expenses = null;
////
////
////        if ($request->start && $request->end && $request->segment != 0) {
////            $incomes = TransactionLog::whereIn('transaction_type', [1])
////                ->where('project_id',Auth::user()->project_id)
////                ->whereIn('product_segment_id',[$request->segment])
////                ->with('transaction')
////                ->whereBetween('date', [$request->start, $request->end])
////                ->get();
////
////            $expenses = TransactionLog::whereIn('transaction_type', [2])
////                ->where('project_id',Auth::user()->project_id)
////                ->whereIn('product_segment_id',[$request->segment])
////                ->with('transaction')
////                ->whereBetween('date', [$request->start, $request->end])
////                ->get();
////
////        }else if($request->start && $request->end ){
////            $incomes = TransactionLog::whereIn('transaction_type', [1])
////                ->where('project_id',Auth::user()->project_id)
////                ->with('transaction')
////                ->whereBetween('date', [$request->start, $request->end])
////                ->get();
////            $expenses = TransactionLog::whereIn('transaction_type', [2])
////                ->where('project_id',Auth::user()->project_id)
////                ->with('transaction')
////                ->whereBetween('date', [$request->start, $request->end])
////                ->get();
////        }
////
////        return view('report.all_receive_payment', compact('incomes', 'expenses','segments'));
////    }
//
//    public function allReceivePayment(Request $request) {
//
//        $segments = ProductSegment::where('project_id',Auth::user()->project_id)
//            ->where('status',1)
//            ->get();
//        $incomes = null;
//        $expenses = null;
//
//
//        if ($request->start && $request->end && $request->segment != 0) {
//            $incomes = TransactionLog::whereIn('transaction_type', [1])
//                ->where('project_id',Auth::user()->project_id)
//                ->whereIn('product_segment_id',[$request->segment])
//                ->with('transaction')
//                ->whereBetween('date', [$request->start, $request->end])
//                ->get();
//
//            $expenses = TransactionLog::whereIn('transaction_type', [2])
//                ->where('project_id',Auth::user()->project_id)
//                ->whereIn('product_segment_id',[$request->segment])
//                ->with('transaction')
//                ->whereBetween('date', [$request->start, $request->end])
//                ->get();
//
//        }else if ($request->start == '' && $request->end =='' && $request->segment != 0 ) {
//            $incomes = TransactionLog::whereIn('transaction_type', [1])
//                ->where('project_id', Auth::user()->project_id)
//                ->whereIn('product_segment_id', [$request->segment])
//                ->with('transaction')
//                ->get();
//            $expenses = TransactionLog::whereIn('transaction_type', [2])
//                ->where('project_id', Auth::user()->project_id)
//                ->whereIn('product_segment_id', [$request->segment])
//                ->with('transaction')
//                ->get();
//
//        }else if ($request->start == '' && $request->end =='' && $request->segment == 0) {
//
//            $incomes = TransactionLog::whereIn('transaction_type', [1])
//                ->where('project_id', Auth::user()->project_id)
//                ->with('transaction')
//                ->get();
//            $expenses = TransactionLog::whereIn('transaction_type', [2])
//                ->where('project_id', Auth::user()->project_id)
//                ->with('transaction')
//                ->get();
//
//
//        }else if($request->start && $request->end ){
//            $incomes = TransactionLog::whereIn('transaction_type', [1])
//                ->where('project_id',Auth::user()->project_id)
//                ->with('transaction')
//                ->whereBetween('date', [$request->start, $request->end])
//                ->get();
//            $expenses = TransactionLog::whereIn('transaction_type', [2])
//                ->where('project_id',Auth::user()->project_id)
//                ->with('transaction')
//                ->whereBetween('date', [$request->start, $request->end])
//                ->get();
//        }
//        if ($request->all() == null){
//            //dd('jmh');
//            $incomes = [];
//            $expenses = [];
//        }
//
//        return view('report.all_receive_payment', compact('incomes', 'expenses','segments'));
//    }
}
