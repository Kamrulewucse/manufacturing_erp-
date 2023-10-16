<?php

namespace App\Http\Controllers;

use App\Enumeration\Role;
use App\Models\BloodGroup;
use App\Models\CardType;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Field;
use App\Models\Section;
use App\Models\DesignationLog;
use App\Models\SalaryChangeLog;
use App\Models\EmployeeTarget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;
use File;

class ClientController extends Controller
{
    public function datatableEmployee() {
        $query = Employee::with('user','department','field','designation','cardType','bloodGroup')
            ->orderBy('sort','ASC');

        if (auth()->user()->role == Role::$PRINT)
            $query->where('field_id',auth()->user()->field_id);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function (Employee $client) {
                $btn = '<div class="btn-group">
                        <button type="button" class="btn btn-default btn-flat"><i class="fa fa-ellipsis-v"></i></button>
                        <button type="button" class="btn btn-default btn-flat dropdown-toggle dropdown-icon" data-toggle="dropdown">
                        <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu custom-datatable-menu-list" role="menu">';
                            if (auth()->user()->role != Role::$PRINT)
                                $btn .='<a href="'.route('employee.edit',['client'=>$client->id]).'" class="dropdown-item"><i class="fa fa-edit"></i> Edit</a>';
                                $btn .=' <a href="'.route('employee_details',['client'=>$client->id]).'" class="dropdown-item"><i class="fa fa-info-circle"></i> Details</a>';
                                $btn .=' <a target="_blank" href="'.route('employee_card_print',['client'=>$client->id]).'" class="dropdown-item"><i class="fa fa-info-circle"></i> Front </a>';
                                $btn .=' <a target="_blank" href="'.route('employee_card_back_side_print',['client'=>$client->id]).'" class="dropdown-item"><i class="fa fa-info-circle"></i> Rear</a>';
                                $btn .= '<a class="dropdown-item btn-employee-target" role="button" data-id="' . $client->id . '"><i class="fa fa-info-circle"></i> Target </a> ';
                                $btn .= '<a class="dropdown-item btn-change-designation" role="button" data-id="' . $client->id . '"><i class="fa fa-info-circle"></i> Change Designation</a> ';

                        $btn .='</div></div>';
                return $btn;
            })
            ->addColumn('department', function(Employee $client) {
                return $client->department->name ?? '';
            })
            ->addColumn('field', function(Employee $client) {
                return $client->field->name ?? '';
            })
            ->addColumn('designation', function(Employee $client) {
                return $client->designation->name ?? '';
            })
            ->addColumn('card_type', function(Employee $client) {
                return $client->cardType->name ?? '';
            })
            ->addColumn('blood_group', function(Employee $client) {
                return $client->bloodGroup->name ?? '';
            })
            ->rawColumns(['action'])
            ->toJson();
    }
    public function indexEmployee() {
        $departments = Department::where('status', 1)->orderBy('name')->get();

        return view('employee.all', compact('departments'));
    }

    public function addEmployee() {
        $divisions = Department::where('status',1)->get();
        $designations = Designation::where('status',1)->get();
        $bloodGroups = BloodGroup::where('status',1)->get();
        $cardTypes = CardType::where('status',1)->get();
        $sections = Section::where('status',1)->get();

        $sortId = Employee::max('sort') + 1;


        $count = Employee::count();
        $employeeId = str_pad($count+1, 4, '0', STR_PAD_LEFT);


        // dd($employeeId);

        return view('employee.add',compact('sections','divisions','designations','bloodGroups','cardTypes','employeeId','sortId'));
    }

    public function addPostEmployee(Request $request) {


        $request->validate([
             'card_type' => 'required',
             'division' => 'required',
             'designation' => 'required',
             'name' => 'required',
             'employee_type' => 'required',
             'mobile_no' => 'required',
             'gross_salary' => 'required',
            'email' => 'required|email|unique:users',
        ]);

        $nidPhotoPath = null;
        if ($request->nid_photo){
            // Upload Image
            $file = $request->file('nid_photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/nid_photo';
            $file->move($destinationPath, $filename);

            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            $img->resize(192,192);
            $img->save(public_path('uploads/nid_photo/'.$filename));
            $nidPhotoPath = 'uploads/nid_photo/'.$filename;
        }

        $signaturePhotoPath = null;
        if ($request->signature_photo){
            // Upload Image
            $file = $request->file('signature_photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/signature_photo';
            $file->move($destinationPath, $filename);

            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            $img->resize(192,192);
            $img->save(public_path('uploads/signature_photo/'.$filename));
            $signaturePhotoPath = 'uploads/signature_photo/'.$filename;
        }

        $employeePhotoPath = null;
        if ($request->employee_photo){
            // Upload Image
            $file = $request->file('employee_photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/employee_photo';
            $file->move($destinationPath, $filename);

            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            $img->resize(192,192);
            $img->save(public_path('uploads/employee_photo/'.$filename));
            $employeePhotoPath = 'uploads/employee_photo/'.$filename;
        }

        $client = new Employee();
        // $client->user_id = $user->id;
        $client->card_type_id = $request->card_type;
        $client->department_id = $request->division;
        $client->section_id = $request->section;
        $client->blood_group_id = $request->blood_group;
        $client->id_no = $request->id_no;
        $client->designation_id= $request->designation;
        $client->name = $request->name;
        $client->email = $request->email;
        $client->date_of_birth = $request->date_of_birth;
        $client->joining_date = $request->joining_date;
        $client->confirmation_date = $request->confirmation_date;
        $client->education_qualification = $request->education_qualification;
        $client->employee_type = $request->employee_type;
        $client->reporting_to = $request->reporting_to;
        $client->gender = $request->gender;
        $client->marital_status = $request->marital_status;
        $client->father_name = $request->father_name;
        $client->mother_name = $request->mother_name;
        $client->current_address = $request->current_address;
        $client->permanent_address = $request->permanent_address;
        $client->religion = $request->religion;
        $client->cv = $request->cv;
        $client->mobile_no = $request->mobile_no;
        $client->nid = $request->nid;
        $client->alternative_mobile = $request->alternative_mobile;
        $client->date_of_birth = $request->date_of_birth ?? null;
        $client->issue_date = $request->issue_date ?? null;
        $client->expire_date = $request->expire_date ?? null;
        $client->nid_photo = $nidPhotoPath;
        $client->signature_photo = $signaturePhotoPath;
        $client->employee_photo = $employeePhotoPath;

        $client->previous_salary = $request->previous_salary ? $request->previous_salary : 0;
        $client->gross_salary = $request->gross_salary;
        $client->bank_name = $request->bank_name;
        $client->bank_branch = $request->bank_branch;
        $client->bank_account = $request->bank_account;

        $client->medical = round($request->gross_salary * .04);
        $client->travel = round($request->gross_salary * .12);
        $client->house_rent = round($request->gross_salary * .24);
        $client->basic_salary = round($request->gross_salary * .60);
        $client->tax = 0;
        $client->others_deduct =0;
        $client->remarks = $request->remarks;
        $client->sort = $request->sort;
        $client->save();

        $designationLog = new DesignationLog();
        $designationLog->employee_id = $client->id;
        $designationLog->department_id = $request->division;
        $designationLog->designation_id = $request->field;
        $designationLog->date = date('Y-m-d');
        $designationLog->save();

        $salaryChangeLog = new SalaryChangeLog();
        $salaryChangeLog->employee_id = $client->id;
        $salaryChangeLog->date = date('Y-m-d');
        $salaryChangeLog->basic_salary = round($request->gross_salary * .60);
        $salaryChangeLog->house_rent = round($request->gross_salary * .24);
        $salaryChangeLog->travel = round($request->gross_salary * .12);
        $salaryChangeLog->medical = round($request->gross_salary * .04);
        $salaryChangeLog->tax = 0;
        $salaryChangeLog->others_deduct = 0;
        $salaryChangeLog->gross_salary = round($request->gross_salary);
        $salaryChangeLog->type = 5;
        $salaryChangeLog->save();

        $user = new User();
        $user->client_id = $client->id;
        $user->field_id = $request->field;
        $user->role = Role::$EMPLOYEE;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->mobile_no = $request->mobile_no;
        $user->password = bcrypt($request->password);
        $user->plain_password = 123456;
        $user->status = 1;
        $user->save();

        return redirect()->route('employee')->with('message', 'Employee add successfully.');
    }

    public function editEmployee(Employee $client) {
        $divisions = Department::where('status',1)->get();
        $designations = Designation::where('status',1)->get();
        $bloodGroups = BloodGroup::where('status',1)->get();
        $cardTypes = CardType::where('status',1)->get();
        $sections = Section::where('status',1)->get();
        $maxSort = Employee::Max('sort');

        return view('employee.edit', compact('sections','client','designations','divisions','bloodGroups','cardTypes','maxSort'));
    }

    public function editPostEmployee(Employee $client, Request $request) {

        $request->validate([
            'card_type' => 'required',
            'division' => 'required',
            'designation' => 'required',
            'name' => 'required',
            'employee_type' => 'required',
            'mobile_no' => 'required',
            'gross_salary' => 'required',
        ]);


        if ($request->nid_photo){
            if(File::exists(public_path($client->nid_photo))){
                File::delete(public_path($client->nid_photo));
            }
            // Upload Image
            $file = $request->file('nid_photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/nid_photo';
            $file->move($destinationPath, $filename);

            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            $img->resize(192,192);
            $img->save(public_path('uploads/nid_photo/'.$filename));
            $client->nid_photo = 'uploads/nid_photo/'.$filename;
        }

        if ($request->signature_photo){
            if(File::exists(public_path($client->signature_photo))){
                File::delete(public_path($client->signature_photo));
            }

            // Upload Image
            $file = $request->file('signature_photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/signature_photo';
            $file->move($destinationPath, $filename);

            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            $img->resize(192,192);
            $img->save(public_path('uploads/signature_photo/'.$filename));
            $client->signature_photo = 'uploads/signature_photo/'.$filename;
        }

        if ($request->employee_photo){
            if(File::exists(public_path($client->employee_photo))){
                File::delete(public_path($client->employee_photo));
            }

            // Upload Image
            $file = $request->file('employee_photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/employee_photo';
            $file->move($destinationPath, $filename);

            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            $img->resize(192,192);
            $img->save(public_path('uploads/employee_photo/'.$filename));
            $client->employee_photo = 'uploads/employee_photo/'.$filename;
        }


        $client->card_type_id = $request->card_type;
        $client->department_id = $request->division;
        $client->section_id = $request->section;
        $client->blood_group_id = $request->blood_group;
        $client->id_no = $request->id_no;
        $client->designation_id= $request->designation;
        $client->name = $request->name;
        $client->date_of_birth = $request->date_of_birth;
        $client->joining_date = $request->joining_date;
        $client->confirmation_date = $request->confirmation_date;
        $client->education_qualification = $request->education_qualification;
        $client->employee_type = $request->employee_type;
        $client->reporting_to = $request->reporting_to;
        $client->gender = $request->gender;
        $client->marital_status = $request->marital_status;
        $client->father_name = $request->father_name;
        $client->mother_name = $request->mother_name;
        $client->current_address = $request->current_address;
        $client->permanent_address = $request->permanent_address;
        $client->religion = $request->religion;
        $client->cv = $request->cv;
        $client->mobile_no = $request->mobile_no;
        $client->nid = $request->nid;
        $client->alternative_mobile = $request->alternative_mobile;
        $client->date_of_birth = $request->date_of_birth ?? null;
        $client->issue_date = $request->issue_date ?? null;
        $client->expire_date = $request->expire_date ?? null;
        $client->previous_salary = $request->previous_salary ? $request->previous_salary : 0;
        $client->gross_salary = $request->gross_salary;
        $client->bank_name = $request->bank_name;
        $client->bank_branch = $request->bank_branch;
        $client->bank_account = $request->bank_account;

        $client->medical = round($request->gross_salary * .04);
        $client->travel = round($request->gross_salary * .12);
        $client->house_rent = round($request->gross_salary * .24);
        $client->basic_salary = round($request->gross_salary * .60);
        $client->tax = 0;
        $client->others_deduct =0;
        $client->remarks = $request->remarks;
        $client->sort = $request->sort;
        $client->save();

        $user = User::where('client_id',$client->id)->first();
        $user->name = $request->name;
        $user->mobile_no = $request->mobile_no;
        $user->plain_password = 123456;
        $user->password = bcrypt(123456);
        $user->status = 1;
        $user->save();

        return redirect()->route('employee')->with('message', 'Employee edit successfully.');
    }

    public function employeeDetails(Employee $client){
//        $leaves = Leave::where('employee_id', $client->id)
//            ->where('year', date('Y'))
//            ->orderBy('created_at', 'desc')
//            ->get();

        return view('employee.details',compact('client'));
    }


    public function employeeCardPrintAll(){
        $clients = Employee::where('id_no','!=',null)->get();
        return view('employee.all_card_print',compact('clients'));
    }
    public function employeeCardPrint(Employee $client){

        return view('employee.card_print',compact('client'));
    }
    public function employeeCardBackSidePrint(Employee $client){

        return view('employee.back_side_card_print',compact('client'));
    }
    public function employeeCardBackSidePrintAll(){
        $clients = Employee::where('id_no','!=',null)->get();
        return view('employee.back_side_card_print_all',compact('clients'));
    }
    public function employeePasswordChange(){

        return view('employee.password_change');
    }

    public function employeePasswordChangePost(Request $request)
    {
        $request->validate([
            'old_password'=>'required|min:6',
            'new_password' => 'required|min:6|same:password_confirmation',
            'password_confirmation' => 'required|min:6',
        ]);
        $user = auth()->user();

        if (Hash::check($request->old_password, $user->password)) {
            $user->fill([
                'password' => bcrypt($request->new_password),
                'plain_password' => $request->new_password,
            ])->save();

            return redirect()->route('employee_profile_password_change')
                ->with('message','Password changed successful');

        }

        return redirect()->route('employee_profile_password_change')
            ->with('error','Old Password does not match');
    }
    public function employeeProfile(){
        $client = auth()->user()->client;
        return view('employee.profile',compact('client'));
    }
    public function employeeProfileEdit(){
        $client = auth()->user()->client;
        $divisions = Department::where('status',1)->get();
        $designations = Designation::where('status',1)->get();
        $bloodGroups = BloodGroup::where('status',1)->get();
        $cardTypes = CardType::where('status',1)->get();
        $sections = Section::where('status',1)->get();
        return view('employee.profile_edit',compact('client',
        'divisions','bloodGroups','designations','cardTypes',
        'sections'));
    }

    public function employeeProfileEditPost(Request $request)
    {
        $client = auth()->user()->client;

        $request->validate([
            'id_no' =>  [
                'required','max:50',
                Rule::unique('employees')
                    ->ignore($client)
            ],
            'nid' =>  [
                'nullable','max:50',
                Rule::unique('employees')
                    ->ignore($client)

            ],
            'mobile_no' =>  [
                'required','digits:11',
                Rule::unique('employees')
                    ->ignore($client)

            ],
            'card_type' => 'required',
            'field' => 'required',
            'section' => 'nullable',
            'division' => 'required',
            'designation' => 'required',
            'blood_group' => 'required',
            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'current_address' => 'nullable|string|max:255',
            'permanent_address' => 'nullable|string|max:255',
            'alternative_mobile' => 'nullable|numeric|digits:11',
            'date_of_birth' => 'nullable|date',
            'nid_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'signature_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'employee_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'remarks' => 'nullable',
        ]);

        if ($request->nid_photo){
            if(File::exists(public_path($client->nid_photo))){
                File::delete(public_path($client->nid_photo));
            }
            // Upload Image
            $file = $request->file('nid_photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/nid_photo';
            $file->move($destinationPath, $filename);

            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            $img->resize(192,192);
            $img->save(public_path('uploads/nid_photo/'.$filename));
            $client->nid_photo = 'uploads/nid_photo/'.$filename;
        }

        if ($request->signature_photo){
            if(File::exists(public_path($client->signature_photo))){
                File::delete(public_path($client->signature_photo));
            }

            // Upload Image
            $file = $request->file('signature_photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/signature_photo';
            $file->move($destinationPath, $filename);

            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            $img->resize(192,192);
            $img->save(public_path('uploads/signature_photo/'.$filename));
            $client->signature_photo = 'uploads/signature_photo/'.$filename;
        }

        if ($request->employee_photo){
            if(File::exists(public_path($client->employee_photo))){
                File::delete(public_path($client->employee_photo));
            }

            // Upload Image
            $file = $request->file('employee_photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads/employee_photo';
            $file->move($destinationPath, $filename);

            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            $img->resize(192,192);
            $img->save(public_path('uploads/employee_photo/'.$filename));
            $client->employee_photo = 'uploads/employee_photo/'.$filename;
        }

        $user = auth()->user();
        $user->name = $request->name;
        $user->save();

        $client->field_id = $request->field;
        $client->section_id = $request->section;
        $client->department_id = $request->division;
        $client->designation_id = $request->designation;
        $client->blood_group_id = $request->blood_group;
        $client->card_type_id = $request->card_type;
        $client->id_no = $request->id_no;
        $client->name = $request->name;
        $client->father_name = $request->father_name;
        $client->mother_name = $request->mother_name;
        $client->current_address = $request->current_address;
        $client->permanent_address = $request->permanent_address;
        $client->mobile_no = $request->mobile_no;
        $client->nid = $request->nid;
        $client->alternative_mobile = $request->alternative_mobile;
        $client->date_of_birth = $request->date_of_birth ?? null;
        $client->remarks = $request->remarks;
        $client->save();

        return redirect()->route('employee_profile')->with('message', 'Employee Profile edit successfully.');

    }

    public function employeeProfileCardPrint(){

        $client = auth()->user()->client;

        return view('employee.profile_card_print',compact('client'));
    }
    public function employeeProfileCardBackSidePrint(){
        $client = auth()->user()->client;
        return view('employee.profile_back_side_card_print',compact('client'));
    }

    public function getEmployeeDetails(Request $request) {
        $employee = Employee::where('id', $request->employeeId)->with('department', 'designation')->first();

        return response()->json($employee);
    }

    public function get_employee_target(Request $request) {
        $employee_target = EmployeeTarget::where([
            'employee_id'=> $request->employee_id,
            'month'=> $request->month,
            'year'=> $request->year,
        ])->first();
        if($employee_target){
            return $employee_target->amount;
        }else{
            return 0;
        }
    }

    public function employeeTargetUpdate(Request $request) {
        $employee_target = EmployeeTarget::where([
            'employee_id'=> $request->employee_id,
            'month'=> $request->month,
            'year'=> $request->year,
        ])->first();
        if($employee_target){
            $employee_target->amount = $request->amount;
            $employee_target->save();
        }else{
            $data = $request->all();
            EmployeeTarget::create($data);
        }
        return "Employee target amount updated successfully done.";
    }

    public function getEmployeeDesignation(Request $request) {
        $designations = Designation::where('department_id', $request->departmentId)
            ->where('status', 1)
            ->orderBy('name')->get()->toArray();

        return response()->json($designations);
    }

    public function employeeDesignationUpdate(Request $request) {
        $rules = [
            'department' => 'required',
            'designation' => 'required',
            'date' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $employee = Employee::find($request->id);
        $employee->department_id = $request->department;
        $employee->field_id = $request->designation;
        $employee->save();

        $log = new DesignationLog();
        $log->employee_id = $employee->id;
        $log->department_id = $request->department;
        $log->designation_id = $request->designation;
        $log->date = $request->date;
        $log->save();

        return response()->json(['success' => true, 'message' => 'Update has been completed.']);
    }

    public function employeeList(Request $request) {
        if ($request->category != '') {
            $employees = Employee::with('designation', 'department')
                ->where('category_id', $request->category)
                ->get();
        } else {
            $employees = Employee::with('designation', 'department')->get();
        }

//        dd($employees);
        return view('employee.employee_list', compact('employees'));
    }

}
