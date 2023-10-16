<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function datatable() {
        $query = Department::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Department $department) {
                return '<a href="'.route('department.edit',['department'=>$department->id]).'" class="btn btn-success btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
//                        <a href="'.route('department.delete',['department'=>$department->id]).'" class="btn btn-danger btn-sm btn-edit" id="data-id"><i class="fa fa-trash"></i></a>';
            })
            ->addColumn('name', function(Department $department) {
                return $department->name ?? '';
            })
            ->addColumn('status', function(Department $department) {
                if ($department->status == 1)
                return '<span class="badge badge-success">Active</span>';
                else
                    return '<span class="badge badge-danger">Inactive</span>';
            })
            ->rawColumns(['action','status'])
            ->toJson();
    }
    public function department() {
        return view('department.all');
    }

    public function departmentAdd() {
        return view('department.add');
    }

    public function departmentAddPost(Request $request) {

        $rules = [
            'name' => 'required|string|max:255|unique:departments',
            'status' => 'required',
        ];
        $request->validate($rules);

        $department = new Department();
        $department->name = $request->name;
        $department->status = $request->status;
        $department->save();

        return redirect()->route('department')->with('message', 'Department add successfully.');
    }

    public function departmentEdit(Department $department) {
        return view('department.edit', compact('department'));
    }

    public function departmentEditPost(Department $department, Request $request) {

        $rules = [
            'name' =>  [
                'required','max:255',
                Rule::unique('departments')
                    ->ignore($department)
            ],
            'status' => 'required',
        ];
        $request->validate($rules);

        $department->name = $request->name;
        $department->status = $request->status;
        $department->save();

        return redirect()->route('department')->with('message', 'Department edit successfully.');
    }

    public function departmentDelete(Department $department){
        $department = Department::find($department->id);
        $department->delete();
        return redirect()->route('department')->with('message', 'Department delete successfully.');
    }
}
