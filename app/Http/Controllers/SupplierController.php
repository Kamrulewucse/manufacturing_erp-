<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Client;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Ramsey\Uuid\Uuid;

class SupplierController extends Controller
{
    public function index() {
        $suppliers = Client::where('type', 2)->get();
        return view('purchase.supplier.all', compact('suppliers'));
    }

    public function add() {
        return view('purchase.supplier.add');
    }

    public function addPost(Request $request) {
            $request->validate([
                'name' => 'required|string|max:255',
                'owner_name' => 'nullable|string|max:255',
                'mobile_no' => 'required|digits:11',
                'email' => 'nullable|email|string|max:255',
                'address' => 'required|string|max:255',
                'opening_due' => 'required|numeric|min:0',
                'status' => 'required'
            ]);

        $supplier = new Client();

        $maxClientId = Client::max('id_no');
        if (!$maxClientId) {
            $maxClientId = 100;
        }

        $supplier->id_no = $maxClientId + 1;
        $supplier->type =  2;
        $supplier->name = $request->name;
        $supplier->owner_name = $request->owner_name;
        $supplier->mobile = $request->mobile_no;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->opening_due = $request->opening_due;
        $supplier->status = $request->status;
        $supplier->save();

        $maxCode = AccountHead::max('account_code');
        if ($maxCode) {
            $maxCode += 1;
        } else {
            $maxCode = 10001;
        }

        $accountHead = new AccountHead();
        $accountHead->client_id = $supplier->id;
        $accountHead->account_code = $maxCode;
        $accountHead->name = $supplier->name;
        $accountHead->account_head_type_id = 2;//LIABILITIES
        $accountHead->opening_balance = $request->opening_due;//added by Hasan
        $accountHead->save();

        return redirect()->route('supplier')->with('message', 'Supplier add successfully.');
    }

    public function edit(Client $supplier) {
        return view('purchase.supplier.edit', compact('supplier'));
    }

    public function editPost(Client $supplier, Request $request) {
            $request->validate([
                'name' => 'required|string|max:255',
                'owner_name' => 'nullable|string|max:255',
                'mobile_no' => 'required|digits:11',
                'email' => 'nullable|email|string|max:255',
                'address' => 'required|string|max:255',
                'opening_due' => 'required|numeric',
                'status' => 'required'
            ]);

            $supplier->name = $request->name;
            $supplier->owner_name = $request->owner_name;
            $supplier->mobile = $request->mobile_no;
            $supplier->email = $request->email;
            $supplier->address = $request->address;
            $supplier->opening_due = $request->opening_due;
            $supplier->status = $request->status;
            $supplier->save();
            //Start. added by Hasan
            $accountHead = AccountHead::where('client_id',$supplier->id)->first();
            $accountHead->opening_balance = $request->opening_due;
            $accountHead->save();
           //End. added by Hasan

        return redirect()->route('supplier')->with('message', 'Supplier edit successfully.');
    }
}
