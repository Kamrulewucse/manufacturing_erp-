<?php

namespace App\Http\Controllers;

use App\Enumeration\Role;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class DesignationController extends Controller
{
    public function datatable() {
        $query = Designation::with('department');
        return DataTables::eloquent($query)
            ->addColumn('action', function(Designation $designation) {
                return '<a href="'.route('designation.edit',['designation'=>$designation->id]).'" class="btn btn-success btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
            })
            ->addColumn('status', function(Designation $designation) {
                if ($designation->status == 1)
                    return '<span class="badge badge-success">Active</span>';
                else
                    return '<span class="badge badge-danger">Inactive</span>';
            })
            ->addColumn('department', function(Designation $designation) {
                return $designation->department->name ?? '';

            })
            ->rawColumns(['action','status'])
            ->toJson();
    }

    public function index() {

        $fields = Designation::all();

//        foreach ($fields as $field){
//            $user = new User();
//            $user->role = Role::$FIELD;
//            $user->field_id = $field->id;
//            $user->name = $field->name;
//            $user->username = $field->name;
//            $user->plain_password = 123456;
//            $user->password = bcrypt(123456);
//            $user->save();
//        }
       // dd('ok');

        return view('designation.all');
    }

    public function add() {
        $divisions = Department::where('status',1)->get();
        return view('designation.add',compact('divisions'));
    }

    public function addPost(Request $request) {

        $rules = [
            'name' => 'required|string|max:255|unique:fields',
            'division' => 'required',
            'status' => 'required',
        ];
        $request->validate($rules);

        $field = new Designation();
        $field->department_id = $request->division;
        $field->name = $request->name;
        $field->status = $request->status;
        $field->save();

        return redirect()->route('designation')->with('message', 'Designation add successfully.');
    }

    public function edit(Designation $designation) {
        $divisions = Department::where('status',1)->get();
        return view('designation.edit', compact('designation','divisions'));
    }

    public function editPost(Designation $designation, Request $request) {

        $rules = [
            'name' =>  [
                'required','max:255',
                Rule::unique('fields')
                    ->ignore($designation)
            ],
            'status' => 'required',
            'division' => 'required',
        ];
        $request->validate($rules);

        $designation->department_id = $request->division;
        $designation->name = $request->name;
        $designation->status = $request->status;
        $designation->save();

        return redirect()->route('designation')->with('message', 'Designation edit successfully.');
    }
}
