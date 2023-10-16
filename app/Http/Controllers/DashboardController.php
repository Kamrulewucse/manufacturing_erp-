<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\TransactionLog;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index() {

        $consultancyProjects = Project::where('status', 1)->get();

        $todaySale = SalesOrder::whereDate('date', date('Y-m-d'))->sum('total');
        $todayReturn = SalesOrder::whereDate('date', date('Y-m-d'))->sum('return_amount');
        $todayCashSale = SalesOrder::whereDate('date', date('Y-m-d'))->sum('paid');
        $todayDue = SalesOrder::whereDate('date', date('Y-m-d'))->sum('due');
        $totalSale = SalesOrder::sum('total');
        $totalReturn = SalesOrder::sum('return_amount');

        $todayExpense = TransactionLog::whereDate('date', date('Y-m-d'))
            ->whereIn('transaction_type', [2])->sum('amount');
        $totalExpense = TransactionLog::whereIn('transaction_type', [2])->sum('amount');
        $todayPurchaseReceipt = PurchaseOrder::whereDate('date', date('Y-m-d'))
            ->with('supplier')
            ->orderBy('created_at', 'desc')->paginate(10);
        $todayPurchaseReceipt->setPageName('purchase_receipt');
        $todaySaleReceipt = SalesOrder::whereDate('date', date('Y-m-d'))
            ->with('client')
            ->orderBy('created_at', 'desc')->paginate(10);

        $todaySaleReceipt->setPageName('sale_receipt');
        $accountHeads = AccountHead::whereIn('id',[4,5])->get();
        $data = [
            'consultancyProjects' => $consultancyProjects,
            'todayDue' => $todayDue,
            'totalSale' => $totalSale,
            'todayReturn' => $todayReturn,
            'totalReturn' => $totalReturn,
            'todayCashSale' => $todayCashSale,
            'todaySale' => $todaySale,
            'todayExpense' => $todayExpense,
            'totalExpense' => $totalExpense,
            'todayPurchaseReceipt' => $todayPurchaseReceipt,
            'todaySaleReceipt' => $todaySaleReceipt,
            'accountHeads' => $accountHeads,

        ];
        return view('dashboard', $data);
    }

//   public function index1()
//    {
//        return view('dashboard');
//    }

}
