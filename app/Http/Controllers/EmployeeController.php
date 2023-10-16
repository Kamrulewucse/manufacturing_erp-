<?php

namespace App\Http\Controllers;

use App\Enumeration\Role;
use App\Models\BaseEmployee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmployeeCash;
use App\Models\EmployeeType;
use App\Models\SalaryChangeLog;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\Facades\Image;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function index() {
        $employees = Employee::where('status',1)->get();
        return view('administrator.employee.employee.all', compact('employees'));
    }

    public function datatableEmployee() {
        $query = Employee::with('user','department','designation','designations')
                 ->orderBy('sort','ASC');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function (Employee $employee) {
                $btn = '<div class="btn-group">
                        <button type="button" class="btn btn-default btn-flat"><i class="fa fa-ellipsis-v"></i></button>
                        <button type="button" class="btn btn-default btn-flat dropdown-toggle dropdown-icon" data-toggle="dropdown">
                        <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu custom-datatable-menu-list" style="left: -83px !important;" role="menu">';

                $btn .='<a href="'.route('employee.edit',['employee'=>$employee->id]).'" class="dropdown-item"><i class="fa fa-edit"></i> Edit</a>';
                $btn .=' <a href="'.route('employee_details',['employee'=>$employee->id]).'" class="dropdown-item"><i class="fa fa-info-circle"></i> Details</a>';
                $btn .= '<a class="dropdown-item btn-change-designation" role="button" data-id="' . $employee->id . '"><i class="fa fa-info-circle"></i> Change Designation</a>';

                $btn .='</div></div>';
                return $btn;
            })
            ->addColumn('department', function(Employee $employee) {
                return $employee->department->name ?? '';
            })
            ->addColumn('designation', function(Employee $employee) {
                return $employee->designation->name ?? '';
            })
            ->addColumn('status', function(Employee $employee) {
                if ($employee->status == 1)
                    return '<span class="badge badge-success">Active</span>';
                else
                    return '<span class="badge badge-danger">Inactive</span>';
            })
            ->rawColumns(['action','status'])
            ->toJson();
    }

    public function add() {
        $count = Employee::count();
        $employeeId = str_pad($count+1, 4, '0', STR_PAD_LEFT);
        $divisions = Department::where('status',1)->get();
        $designations = Designation::where('status',1)->get();
//        $types = EmployeeType::where('status', 1)->get();

        return view('administrator.employee.employee.add',compact('divisions','designations','employeeId'));
    }

    public function addPost(Request $request) {
        $rules= [
            'employee_type' => 'nullable|string|max:255',
            'division' => 'required',
            'designation' => 'required',
            'name' => 'required|string|max:255',
            'id_no' => 'required',
            'current_address' => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'education_qualification' => 'required|string|max:255',
            'reporting_to' => 'required|string|max:255',
            'nid' => 'required|numeric',
            'marital_status' => 'required',
            'gender' => 'required',
//            'cv' => 'nullable|pdf',
            'previous_salary' => 'nullable|numeric',
            'gross_salary' => 'nullable|numeric',
            'bank_account' => 'nullable|numeric',
            'joining_date' => 'nullable|date',
            'confirmation_date' => 'nullable|date',
            'dob' => 'nullable|date',
            'mobile' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'employee_image' => 'nullable|mimes:jpg,jpeg,png',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => ['nullable', 'confirmed',Password::defaults()],
            'status' => 'required',
        ];
        $request->validate($rules);

        $employeeImagePath= null;
        if ($request->employee_image) {
            // Upload Image
            $file = $request->file('employee_image');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/employee';
            $file->move($destinationPath, $filename);
            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            $img->save(public_path('uploads/employee/'.$filename), 70);
            $employeeImagePath = 'uploads/employee/'.$filename;
        }

        $emp_cv = NULL;
        if($request->cv){
            // Upload Employee Image
            $file = $request->file('cv');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/employee';
            $file->move($destinationPath, $filename);
            $emp_cv = 'uploads/employee/'.$filename;
        }

        $employee = new Employee();
        $employee->employee_type_id = $request->employee_type;
        $employee->department_id = $request->division;
        $employee->designation_id= $request->designation;
        $employee->name = $request->name;
        $employee->id_no = $request->id_no;
        $employee->mobile = $request->mobile;
        $employee->father_name = $request->father_name;
        $employee->mother_name = $request->mother_name;
        $employee->previous_salary = $request->previous_salary;
        $employee->gross_salary = $request->gross_salary;
        $employee->current_address = $request->current_address;
        $employee->opening_balance = $request->opening_balance;
        $employee->bank_account = $request->bank_account;
        $employee->permanent_address = $request->permanent_address;
        $employee->education_qualification = $request->education_qualification;
        $employee->nid = $request->nid;
        $employee->reporting_to = $request->reporting_to;
        $employee->marital_status = $request->marital_status;
        $employee->joining_date = $request->joining_date ? Carbon::parse($request->joining_date) : NULL;
        $employee->confirmation_date = $request->confirmation_date ? Carbon::parse($request->confirmation_date) : NULL;
        $employee->date_of_birth = $request->dob ? Carbon::parse($request->dob) : NULL;
        $employee->gender = $request->gender;
        $employee->email = $request->email;
        $employee->employee_photo = $employeeImagePath;
        $employee->cv = $emp_cv;
        $employee->status = $request->status;
        $employee->save();

        if ($request->gross_salary) {
            $salaryChangeLog = new SalaryChangeLog();
            $salaryChangeLog->employee_id = $employee->id;
            $salaryChangeLog->date = $request->confirmation_date ? Carbon::parse($request->confirmation_date) : NULL;
            $salaryChangeLog->type = 1;
            $salaryChangeLog->basic_salary = round($request->gross_salary * .60);
            $salaryChangeLog->house_rent = round($request->gross_salary * .24);
            $salaryChangeLog->travel = round($request->gross_salary * .12);
            $salaryChangeLog->medical = round($request->gross_salary * .04);
            $salaryChangeLog->tax = $request->tax?? 0.00;
            $salaryChangeLog->others_deduct = $request->others_deduct?? 0.00;
            $salaryChangeLog->gross_salary = $request->gross_salary?? 0.00;
            $salaryChangeLog->save();
        }

        $user = new User();
        $user->employee_id = $employee->id;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = Role::$EMPLOYEE;
        $user->password = bcrypt($request->password);
        $user->status = $request->status;
        $user->save();

        return redirect()->route('employee')->with('message', 'Employee add successfully.');
    }

    public function edit(Employee $employee) {
        $count = Employee::count();
        $employeeId = str_pad($count+1, 4, '0', STR_PAD_LEFT);
        $divisions = Department::where('status',1)->get();
        $designations = Designation::where('status',1)->get();
//        $zones = Zone::where('status',1)->get();
        return view('administrator.employee.employee.edit', compact('employee','divisions','designations'));
    }

    public function editPost(Employee $employee, Request $request) {
//        dd($request->all());

//        $user = User::where('id',$employee->user_id)->first();

        $rules = [
//            'employee_code' => 'required|string|max:255',
            'division' => 'required',
            'designation' => 'required',
            'employee_type' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'employee_image' => 'nullable|mimes:jpg,jpeg,png',
            'status' => 'required',
//            'username' => 'required|string|max:255',
//            'password' => 'required|string|min:8|confirmed',
            'email' => 'nullable|string|email|max:255',
//            Rule::unique('users')
//                ->ignore($user)
//                ->where('email',$request->email)

        ];
        if ($request->employee_type == 2) {
            $rules['zone'] = 'required';
        }

        $request->validate($rules);

//        $user->name = $request->name;
//        $user->username = $request->username;
//        $user->email = $request->email;
//
//        if ($request->password) {
//            $user->password = bcrypt($request->password);
//        }
//        $user->save();

        if ($request->employee_image) {

            unlink('public/'.$employee->employee_image);

            // Upload Image
            $file = $request->file('employee_image');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee_image';
            $file->move($destinationPath, $filename);
            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            //$img->resize(370, 181);
            $img->save(public_path('uploads/employee_image/'.$filename), 70);
            $employeeImagePath = 'uploads/employee_image/'.$filename;
            $employee->employee_image = $employeeImagePath;
        }

//        $employee->employee_code = $request->employee_code;
        $employee->department_id = $request->division;
        $employee->designation_id= $request->designation;
        $employee->employee_type_id = $request->employee_type;
//        $employee->zone_id = $request->zone??'';
        $employee->name = $request->name;
        $employee->mobile = $request->mobile;
        $employee->address = $request->address;
        $employee->email = $request->email;
        $employee->status = $request->status;
        $employee->save();

        return redirect()->route('employee')->with('message', 'Employee edit successfully.');
    }

    public function employeeList(Request $request) {
//        if ($request->category != '') {
//            $employees = Employee::with('designation', 'department')
//                ->where('category_id', $request->category)
//                ->get();
//        } else {
//            $employees = Employee::with('designation', 'department')->get();
//        }

        $employees = Employee::with('designation', 'department')->get();

//        dd($employees);
        return view('employee.employee_list', compact('employees'));
    }
}
