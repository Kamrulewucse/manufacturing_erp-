<?php

namespace App\Http\Controllers;

use App\Enumeration\Role;
use App\Models\AccountHead;
use App\Models\BaseClient;
use App\Models\Client;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index()
    {
        return view('sale.customer.all');
    }

    public function add()
    {
        return view('sale.customer.add');
    }

    public function addPost(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_no' => 'required|string|digits:11',
            'address' => 'nullable|string|max:255',
            'status' => 'required',
            'email' => 'nullable|string|email|max:255',
        ]);

        $customer = new Client();
        $maxClientId = Client::max('id_no');
        if (!$maxClientId) {
            $maxClientId = 100;
        }
        $customer->id_no = $maxClientId + 1;
        $customer->name = $request->name;
        $customer->type = 1;
        $customer->mobile = $request->mobile_no;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->opening_due = $request->opening_due;
        $customer->status = $request->status;
        $customer->save();

        $maxCode = AccountHead::max('account_code');
        if ($maxCode) {
            $maxCode += 1;
        } else {
            $maxCode = 10001;
        }

        $accountHead = new AccountHead();
        $accountHead->client_id = $customer->id;
        $accountHead->account_code = $maxCode;
        $accountHead->name = $customer->name;
        $accountHead->account_head_type_id = 1; //Asset
        $accountHead->opening_balance = $request->opening_due;//added by Hasan
        $accountHead->save();

        return redirect()->route('customer')->with('message', 'Customer add successfully.');
    }

    public function edit(Client $customer)
    {
        return view('sale.customer.edit', compact('customer'));
    }

    public function editPost(Client $customer, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_no' => 'required|string|digits:11',
            'address' => 'nullable|string|max:255',
            'customer_image' => 'nullable|mimes:jpg,jpeg,png',
            'status' => 'required',
            'email' => 'nullable|string|email|max:255',
        ]);

        $customer->name = $request->name;
        $customer->mobile = $request->mobile_no;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->opening_due = $request->opening_due;
        $customer->is_opening_show = 0;
        $customer->status = $request->status;
        $customer->save();
        //Start. added by Hasan
        $accountHead = AccountHead::where('client_id',$customer->id)->first();
        $accountHead->opening_balance = $request->opening_due;
        $accountHead->save();
        //End. added by Hasan

        return redirect()->route('customer')->with('message', 'Customer edit successfully.');
    }

    public function datatable()
    {

        $query = Client::where('type', 1);

        return DataTables::eloquent($query)
            ->addColumn('action', function (Client $customer) {
                return '<a class="btn btn-info btn-sm" href="' . route('customer.edit', ['customer' => $customer->id]) . '"> Edit';
            })
            ->addColumn('status', function (Client $customer) {
                if ($customer->status == 1) {
                    return '<span class="text-success">Active</span>';
                } else {
                    return '<span class="text-danger">Inactive</span>';
                }
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }
}
