<?php

namespace App\Http\Controllers;

use App\Model\Cash;
use App\Model\EmployeeSalaryAdvance;
use App\Model\Holiday;
use App\Model\Leave;
use App\Model\MobileBanking;
use App\Model\SalaryChangeLog;
use App\Models\AccountHead;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\ReceiptPayment;
use App\Models\ReceiptPaymentDetail;
use App\Models\Salary;
use App\Models\SalaryProcess;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use SakibRahaman\DecimalToWords\DecimalToWords;

class PayrollController extends Controller
{
    public function salaryIndex() {
        //$banks = Bank::where('status', 1)->orderBy('name')->get();
        return view('payroll.salary_update.all');
    }

    public function salaryUpdatePost(Request $request) {
        $rules = [
            'tax' => 'required|numeric|min:0',
            'others_deduct' => 'nullable|numeric',
            'gross_salary' => 'required|numeric|min:0',
            'date' => 'required|date',
            'type' => 'nullable',
        ];

        if ($request->type==5) {
            $rules = [
            'bonus_salary' => 'required|numeric|min:0',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $others_deduct=0;
        if ($request->others_deduct) {
            $others_deduct= $request->others_deduct;
        }

        $employee = Employee::find($request->id);
        $employee->medical = round($request->gross_salary * .04);
        $employee->travel = round($request->gross_salary * .12);
        $employee->house_rent = round($request->gross_salary * .24);
        $employee->basic_salary = round($request->gross_salary * .60);
        $employee->tax = $request->tax;
        $employee->others_deduct =$others_deduct;
        $employee->gross_salary =$request->gross_salary;
        if ($request->type==6){
            $employee->bonus = $request->bonus_salary;
        }
        $employee->save();

        if ($request->type) {

            $salaryChangeLog = new SalaryChangeLog();
            $salaryChangeLog->employee_id = $employee->id;
            $salaryChangeLog->date = $request->date;
            $salaryChangeLog->type = $request->type;
            $salaryChangeLog->basic_salary = round($request->gross_salary * .60);
            $salaryChangeLog->house_rent = round($request->gross_salary * .24);
            $salaryChangeLog->travel = round($request->gross_salary * .12);
            $salaryChangeLog->medical = round($request->gross_salary * .04);
            $salaryChangeLog->tax = $request->tax;
            $salaryChangeLog->others_deduct = $request->others_deduct;
            $salaryChangeLog->gross_salary = $request->gross_salary;

            if ($request->type==6){
                $salaryChangeLog->bonus = $request->bonus_salary;
            }
            $salaryChangeLog->save();
        }


        return response()->json(['success' => true, 'message' => 'Updates has been completed.']);
    }
    public function employeeSalaryAdvancePost(Request $request) {
        $rules = [
            'year' => 'required',
            'month' => 'required',
            'payment_type' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required',
            'date' => 'required|date',
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
            }elseif ($request->payment_type == 3){
                $mobileBank = MobileBanking::first();

                if ($request->amount > $mobileBank->amount)
                    $validator->errors()->add('amount', 'Insufficient balance.');
            }
            else {
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
//        dd($request->all());

        if ($request->payment_type == 1 || $request->payment_type == 3) {
            //code here
            $salaryAdvanceLog = new EmployeeSalaryAdvance();

            $salaryAdvanceLog->employee_id = $request->id;
            $salaryAdvanceLog->year = $request->year;
            $salaryAdvanceLog->month = $request->month;
            $salaryAdvanceLog->date = $request->date;
            $salaryAdvanceLog->type = $request->type;
            $salaryAdvanceLog->advance = $request->amount;
            $salaryAdvanceLog->save();

            if ($request->payment_type == 1)
                Cash::first()->decrement('amount', $request->amount);
            else
                MobileBanking::first()->decrement('amount', $request->amount);

            $log = new TransactionLog();

            $log->date = $request->date;
            $log->particular = 'Advance salary to '.$salaryAdvanceLog->employee->name.' for employee id'.$salaryAdvanceLog->employee->employee_id;
            $log->transaction_type = 2;
            $log->transaction_method = $request->payment_type;
            $log->account_head_type_id = 5;
            $log->account_head_sub_type_id = 5;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->employee_salary_advance_id = $salaryAdvanceLog->id;
            $log->save();

        } else {
            $image = 'img/no_image.png';

            if ($request->cheque_image) {
                // Upload Image
                $file = $request->file('cheque_image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'public/uploads/advance_payment_cheque';
                $file->move($destinationPath, $filename);

                $image = 'uploads/advance_payment_cheque/'.$filename;
            }

            //code here
            $salaryAdvanceLog = new EmployeeSalaryAdvance();
            $salaryAdvanceLog->employee_id = $request->id;
            $salaryAdvanceLog->year = $request->year;
            $salaryAdvanceLog->month = $request->month;
            $salaryAdvanceLog->date = $request->date;
            $salaryAdvanceLog->type = $request->type;
            $salaryAdvanceLog->advance = $request->amount;
            $salaryAdvanceLog->save();

            BankAccount::find($request->account)->decrement('balance', $request->amount);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Advance salary to '.$salaryAdvanceLog->employee->name.' for employee id'.$salaryAdvanceLog->employee->employee_id;
            $log->transaction_type = 2;
            $log->transaction_method = 2;
            $log->account_head_type_id = 5;
            $log->account_head_sub_type_id = 5;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->cheque_no = $request->cheque_no;
            $log->cheque_image = $image;
            $log->amount = $request->amount;
            $log->note = $request->note;
            $log->employee_salary_advance_id = $salaryAdvanceLog->id;
            $log->save();
        }

        return response()->json(['id'=>$salaryAdvanceLog->id,'success' => true, 'message' => 'Salary advance has been completed.','redirect_url' => route('employee.salary_advance_receipt', ['employeeSalaryAdvance' => $salaryAdvanceLog->id])]);
    }

    public function employeeSalaryAdvanceReceipt(EmployeeSalaryAdvance $employeeSalaryAdvance){
        $employeeSalaryAdvance->amount_in_word = DecimalToWords::convert($employeeSalaryAdvance->advance,'Taka',
            'Poisa');


        return view('payroll.salary_update.salary_advance_receipt', compact('employeeSalaryAdvance'));

    }


    public function salaryProcessIndex() {
        $banks = Bank::where('status', 1)->orderBy('name')->get();
        $employees = Employee::get();
        return view('payroll.salary_process.index',compact('banks','employees'));
    }

    public function salaryProcessPost(Request $request) {

//        $rules = [
//            'tax' => 'required|numeric|min:0',
//            'others_deduct' => 'nullable|numeric',
//            'gross_salary' => 'required|numeric|min:0',
//            'date' => 'required|date',
//            'type' => 'nullable',
//        ];
//
//        if ($request->type==5) {
//            $rules = [
//                'bonus_salary' => 'required|numeric|min:0',
//            ];
//        }
//
//        $validator = Validator::make($request->all(), $rules);
//
//        if ($validator->fails()) {
//            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
//        }

        $totalSalary=0;

        if ($request->employee){
            $employee = Employee::where('id',$request->employee)->first();

            $salary = Salary::where('month',$request->month)
                ->where('year',$request->year)
                ->where('employee_id',$employee->id)
                ->first();

            if ($salary) {
                return redirect()->route('payroll.salary_process.index')->with('error', 'This Employee Salary All Ready Processed');
            }

            $absent_count=EmployeeAttendance::where('employee_id',$employee->id)
                ->where('present_or_absent',0)
                ->whereYear('date', '=', date('Y'))
                ->whereMonth('date', '=', $request->month)
                ->count();
            $late_count=EmployeeAttendance::where('employee_id',$employee->id)
                ->where('present_or_absent',1)
                ->where('late',1)
                ->whereYear('date',date('Y'))
                ->whereMonth('date',$request->month)
                ->count();
            $working_days=cal_days_in_month(CAL_GREGORIAN,$request->month,date('Y'));


            $late=(int)($late_count/3);


            $per_day_salary=$employee->gross_salary/$working_days;

            $deduct_absent_salary=$absent_count+$late*$per_day_salary;

            $totalSalary+=$employee->gross_salary-$deduct_absent_salary-$employee->others_deduct;

            // $totalSalary = Employee::sum('gross_salary');

            $bankAccount = BankAccount::find($request->account);

            if ($totalSalary > $bankAccount->balance) {
                return redirect()->route('payroll.salary_process.index')->with('error', 'Insufficient Balance.');
            }

            $salaryProcess = new SalaryProcess();
            $salaryProcess->date = $request->date;
            $salaryProcess->month = $request->month;
            $salaryProcess->year = $request->year;
            $salaryProcess->bank_id = $request->bank;
            $salaryProcess->branch_id = $request->branch;
            $salaryProcess->bank_account_id = $request->account;
            $salaryProcess->total = $totalSalary;
            $salaryProcess->status = 1; //1= For Single Salary
            $salaryProcess->save();

            $employee = Employee::where('id',$employee->id)->first();

            $absent_count=EmployeeAttendance::where('employee_id',$employee->id)
                ->where('present_or_absent',0)
                ->whereYear('date',date('Y'))
                ->whereMonth('date',$request->month)
                ->count();

            $late_count=EmployeeAttendance::where('employee_id',$employee->id)
                ->where('present_or_absent',1)
                ->where('late',1)
                ->whereYear('date',date('Y'))
                ->whereMonth('date',$request->month)
                ->count();

            $working_days=cal_days_in_month(CAL_GREGORIAN,$request->month,date('Y'));

            $per_day_salary=$employee->gross_salary/$working_days;

            $late=(int)($late_count/3);

            $deduct_absent_salary=$absent_count+$late*$per_day_salary;

            $salary = new Salary();
            $salary->salary_process_id = $salaryProcess->id;
            $salary->employee_id = $employee->id;
            $salary->date = $request->date;
            $salary->month = $request->month;
            $salary->year = $request->year;
            $salary->basic_salary = $employee->basic_salary;
            $salary->house_rent = $employee->house_rent;
            $salary->travel = $employee->travel;
            $salary->medical = $employee->medical;
            $salary->tax = $employee->tax;
            $salary->others_deduct = $employee->others_deduct;
            $salary->absent_deduct = $deduct_absent_salary;
            $salary->gross_salary = $employee->gross_salary;
            $salary->save();


            BankAccount::find($request->account)->decrement('balance', $totalSalary);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Salary';
            $log->transaction_type = 2;
            $log->transaction_method = 2;
            $log->account_head_type_id = 5;
            $log->account_head_sub_type_id = 5;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->amount = $totalSalary;
            $log->salary_process_id = $salaryProcess->id;
            $log->save();
        }else{

            $salaryProdess = SalaryProcess::where('month',$request->month)
                ->where('year',$request->year)
                ->where('status',2)
                ->first();

            if ($salaryProdess) {
                return redirect()->route('payroll.salary_process.index')->with('error', 'This Month Salary All Ready Processed');
            }

            $employeeId = Salary::where('month',$request->month)
                ->where('year',$request->year)
                ->get()
                ->pluck('employee_id');



            $employees = Employee::whereNotIn('id',$employeeId)
                ->get();

            if (count($employees) > 0){
                foreach ($employees as $employee){

                    $absent_count=EmployeeAttendance::where('employee_id',$employee->id)
                        ->where('present_or_absent',0)
                        ->whereYear('date', '=', date('Y'))
                        ->whereMonth('date', '=', $request->month)
                        ->count();
                    $late_count=EmployeeAttendance::where('employee_id',$employee->id)
                        ->where('present_or_absent',1)
                        ->where('late',1)
                        ->whereYear('date',date('Y'))
                        ->whereMonth('date',$request->month)
                        ->count();
                    $working_days=cal_days_in_month(CAL_GREGORIAN,$request->month,date('Y'));


                    $late=(int)($late_count/3);


                    $per_day_salary=$employee->gross_salary/$working_days;

                    $deduct_absent_salary=$absent_count+$late*$per_day_salary;

                    $totalSalary+=$employee->gross_salary-$deduct_absent_salary-$employee->others_deduct;
                }

                // $totalSalary = Employee::sum('gross_salary');

                $bankAccount = BankAccount::find($request->account);

                if ($totalSalary > $bankAccount->balance) {
                    return redirect()->route('payroll.salary_process.index')->with('error', 'Insufficient Balance.');
                }
            }else{
                return redirect()->route('payroll.salary_process.index')->with('error', 'No Employee Available For Salary');
            }

            $salaryProcess = new SalaryProcess();
            $salaryProcess->date = $request->date;
            $salaryProcess->month = $request->month;
            $salaryProcess->year = $request->year;
            $salaryProcess->bank_id = $request->bank;
            $salaryProcess->branch_id = $request->branch;
            $salaryProcess->bank_account_id = $request->account;
            $salaryProcess->total = $totalSalary;
            $salaryProcess->status = 2; //1= For Multiple Salary
            $salaryProcess->save();

            $employees = Employee::whereNotIn('id',$employeeId)->get();

            foreach ($employees as $employee) {

                $absent_count=EmployeeAttendance::where('employee_id',$employee->id)
                    ->where('present_or_absent',0)
                    ->whereYear('date',date('Y'))
                    ->whereMonth('date',$request->month)
                    ->count();
                $late_count=EmployeeAttendance::where('employee_id',$employee->id)
                    ->where('present_or_absent',1)
                    ->where('late',1)
                    ->whereYear('date',date('Y'))
                    ->whereMonth('date',$request->month)
                    ->count();
                $working_days=cal_days_in_month(CAL_GREGORIAN,$request->month,date('Y'));

                $per_day_salary=$employee->gross_salary/$working_days;

                $late=(int)($late_count/3);

                $deduct_absent_salary=$absent_count+$late*$per_day_salary;

                $salary = new Salary();
                $salary->salary_process_id = $salaryProcess->id;
                $salary->employee_id = $employee->id;
                $salary->date = $request->date;
                $salary->month = $request->month;
                $salary->year = $request->year;
                $salary->basic_salary = $employee->basic_salary;
                $salary->house_rent = $employee->house_rent;
                $salary->travel = $employee->travel;
                $salary->medical = $employee->medical;
                $salary->tax = $employee->tax;
                $salary->others_deduct = $employee->others_deduct;
                $salary->absent_deduct = $deduct_absent_salary;
                $salary->gross_salary = $employee->gross_salary;
                $salary->save();
            }

            BankAccount::find($request->account)->decrement('balance', $totalSalary);

            $log = new TransactionLog();
            $log->date = $request->date;
            $log->particular = 'Salary';
            $log->transaction_type = 2;
            $log->transaction_method = 2;
            $log->account_head_type_id = 5;
            $log->account_head_sub_type_id = 5;
            $log->bank_id = $request->bank;
            $log->branch_id = $request->branch;
            $log->bank_account_id = $request->account;
            $log->amount = $totalSalary;
            $log->salary_process_id = $salaryProcess->id;
            $log->save();
        }
        return redirect()->route('payroll.salary_process.index')->with('message', 'Salary process successful.');
    }

    public function leaveAll() {
        $leaves = Leave::orderBy('employee_id')
            ->with('employee')
            ->get();

        return view('payroll.leave.all', compact('leaves'));
    }

    public function leaveIndex() {
        $employees = Employee::orderBy('employee_id')->get();

        return view('payroll.leave.index', compact('employees'));
    }


    public function leavePost(Request $request) {
        $request->validate([
            'employee' => 'required',
            'from' => 'required|date',
            'to' => 'required|date',
            'note' => 'nullable|max:255',
            'type' => 'required'
        ]);

        $fromObj = new Carbon($request->from);
        $toObj = new Carbon($request->to);
        $totalDays = $fromObj->diffInDays($toObj) + 1;

        $leave = new Leave();
        $leave->employee_id = $request->employee;
        $leave->type = $request->type;
        $leave->year = $toObj->format('Y');
        $leave->from = $request->from;
        $leave->to = $request->to;
        $leave->total_days = $totalDays;
        $leave->note = $request->note;
        $leave->save();

        return redirect()->route('payroll.leave.all')->with('message', 'Leave add successful.');
    }


    public function holidayIndex() {

        return view('payroll.holiday.index');
    }

    public function holidayAdd()
    {
        return view('payroll.holiday.add');
    }

    public function holidayPost(Request $request) {
        $request->validate([
            'name' => 'required',
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $fromObj = new Carbon($request->from);
        $toObj = new Carbon($request->to);
        $totalDays = $fromObj->diffInDays($toObj) + 1;

        $holiday = new Holiday();
        $holiday->name = $request->name;
        $holiday->year = $toObj->format('Y');
        $holiday->from = $request->from;
        $holiday->to = $request->to;
        $holiday->total_days = $totalDays;
        $holiday->save();

        return redirect()->route('payroll.holiday.index')->with('message', 'Holiday add successful.');
    }

    public function holidayEdit(Holiday $holiday)
    {
        return view('payroll.holiday.edit',compact('holiday'));
    }

    public function holidayEditPost(Holiday $holiday,Request $request)
    {
        $request->validate([
            'name' => 'required',
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $fromObj = new Carbon($request->from);
        $toObj = new Carbon($request->to);
        $totalDays = $fromObj->diffInDays($toObj) + 1;

        $holiday->name = $request->name;
        $holiday->year = $toObj->format('Y');
        $holiday->from = $request->from;
        $holiday->to = $request->to;
        $holiday->total_days = $totalDays;
        $holiday->save();

        return redirect()->route('payroll.holiday.index')->with('message', 'Holiday update successful.');
    }

    public function holidayDatatable()
    {
        $query=Holiday::query();
        return DataTables::eloquent($query)
            ->editColumn('from', function(Holiday $holiday) {
                return $holiday->from->format('j F, Y');
            })
            ->editColumn('to', function(Holiday $holiday) {
                return $holiday->to->format('j F, Y');
            })

            ->addColumn('action', function(Holiday $holiday) {
                return '<a href="'.route('payroll.holiday_edit', ['holiday' => $holiday->id]).'" class="btn btn-primary btn-sm">Edit</a>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function salaryUpdateDatatable() {
        $query = Employee::with('department', 'designation');

        return DataTables::eloquent($query)
            ->addColumn('department', function(Employee $employee) {
                return $employee->department->name??'';
            })
            ->addColumn('designation', function(Employee $employee) {
                return $employee->designation->name??'';
            })
            ->addColumn('action', function(Employee $employee) {
                return ' <a class="btn btn-info btn-sm btn-update" role="button" data-id="'.$employee->id.'">Update</a>';

                //<a class="btn btn-info btn-sm btn-advance" role="button" data-id="'.$employee->id.'">Advance Salary</a>
                //<a href="'.route('employee.details',['employee'=>$employee->id]).'" class="btn btn-info btn-sm">Logs</a>
            })
            ->editColumn('photo', function(Employee $employee) {
                return '<img src="'.asset($employee->photo).'" height="50px">';
            })
            ->editColumn('employee_type', function(Employee $employee) {
                if ($employee->employee_type == 1)
                    return '<span class="label label-success">Permanent</span>';
                else
                    return '<span class="label label-warning">Temporary</span>';
            })

            ->editColumn('gross_salary', function(Employee $employee) {
                return ' '.number_format($employee->gross_salary, 2);
            })
            ->rawColumns(['action', 'photo', 'employee_type'])
            ->toJson();
    }

    public function salarySheetReport(Request $request)
    {

        $salaries = [];
        $working_days = '';

        if ($request->month != '' && $request->year != '') {

            $working_days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);

            $salaries = Salary::with('employee')->where('year', $request->year)->where('month', $request->month)->get();

            foreach ($salaries as $salary) {
                $absent = EmployeeAttendance::select(DB::raw('count(*) as absent_count'))
                    ->where('employee_id', $salary->employee_id)
                    ->whereYear('date', $request->year)
                    ->whereMonth('date', $request->month)
                    ->where('present_or_absent', 0)
                    ->first();
                $late = EmployeeAttendance::select(DB::raw('count(*) as late_count'))
                    ->where('employee_id', $salary->employee_id)
                    ->whereYear('date', $request->year)
                    ->whereMonth('date', $request->month)
                    ->where('late', 1)
                    ->first();

                $late2 = (int)($late->late_count / 3);

                $salary->absent = $absent->absent_count;
                $salary->late = $late2;
            }

            //dd($salaries);
        }

        $salaryDates = SalaryProcess::select('year')->distinct()->get();


        return view('payroll.salary_report.salary_report', compact('salaries', 'working_days', 'salaryDates'));

//        return view('payroll.salary_report.salary_report',compact('salaryDates'));

    }
}
