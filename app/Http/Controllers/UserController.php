<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Stakeholder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index() {
        $users = User::orderBy('name')->whereIn('role',[1,2])->get();
        return view('administrator.user.all', compact('users'));
    }

    public function add() {
        return view('administrator.user.add');
    }

    public function addPost(Request $request) {

        $request->validate([
//            'company_type' => 'required',
            'role' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = new User();
//        $user->company_type = $request->company_type;
        $user->name = $request->name;
        $user->username = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->password = bcrypt($request->password);
        $user->status = 1;
        $user->save();

        //$user->syncPermissions($request->permission);

        return redirect()->route('user.all')->with('message', 'User add successfully.');
    }

    public function edit(User $user) {
        return view('administrator.user.edit', compact('user'));
    }

    public function editPost(User $user, Request $request) {

        $request->validate([
//            'company_type' => 'required',
            'role' => 'required',
            'name' => 'required|string|max:255',
            'email' => [
                'required','string','email','max:255',
                Rule::unique('users')
                    ->ignore($user)
                    ->where('email',$request->email)
            ],
        ]);

//        $user->company_type = $request->company_type;
        $user->name = $request->name;
        $user->username = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();
//        $user->syncPermissions($request->permission);

        return redirect()->route('user.all')->with('message', 'User edit successfully.');
    }

    public function accessPermissionRole(Request $request)
    {
        $rules = [
            'role' => 'required',
        ];

        if ($request->role == 1 || $request->role == 2){
            $rules['project'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $user = User::where('project_id',$request->project)
            ->where('role',2)->first();

        $admin = User::where('id', Auth::id())->where('admin_status',1)->first();

        $permissions = Permission::select('name')->pluck('name')->toArray();


        if ($user && $admin) {
            $admin->project_id = $user->project_id;
            $admin->role = $user->role;
            $admin->save();
            $admin->syncPermissions($permissions);
        }elseif ($admin){
            $admin->project_id = null;
            $admin->role = 3;
            $admin->save();
            $permissions = null;
            $admin->syncPermissions($permissions);

        }
        return response()->json(['success' => true, 'message' => 'Access Permission Changed']);

    }
}
