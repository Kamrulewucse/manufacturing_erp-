<?php

namespace App\Http\Controllers;


use App\Models\Acc;
use App\Models\AccountHead;
use App\Models\AccountHeadType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class AccountHeadController extends Controller
{
    public function datatable() {
        $query = AccountHead::with('typeName','subType');
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(AccountHead $accountHead) {
                return '<a href="'.route('account_head.edit',['accountHead'=>$accountHead->id]).'" class="btn btn-success btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
//                        <a role="button" data-id="'.$accountHead->id.'" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></a>';
            })
            ->addColumn('type_name', function(AccountHead $accountHead) {
                return $accountHead->typeName->name ?? '';
            })
            ->addColumn('sub_type', function(AccountHead $accountHead) {
                return $accountHead->subType->name ?? '';
            })
            ->editColumn('opening_balance', function(AccountHead $accountHead) {
                return number_format($accountHead->opening_balance,2);
            })
            ->rawColumns(['action'])
            ->toJson();
    }
    public function accountHead() {
        $product = AccountHead::where('product_id','!=','')->get();

        foreach ($product as $record) {
            $record->delete();
        }

        //dd($product);

        return view('accounts.account_head.all');
    }
    public function accountHeadDelete(Request $request)
    {
        $accountHead = AccountHead::find($request->id);

//        $logCheck = TransactionLog::where('account_head_id',$accountHead->id)
//            ->first();
//
//        if ($logCheck){
//            return response()->json(['success' => false, 'message' => "Can't Delete,It's have logs also."]);
//        }
        $accountHead->delete();
        return response()->json(['success' => true, 'message' => 'Successfully Deleted.']);


    }
    public function accountHeadAdd() {
        $types = AccountHeadType::all();
        $maxCode = AccountHead::max('account_code');
        if ($maxCode){
            $maxCode +=1;
        }else{
            $maxCode = 10001;
        }
        return view('accounts.account_head.add',compact('types','maxCode'));
    }

    public function accountHeadAddPost(Request $request) {

        $rules = [
            'name' =>[
                'required','max:255',
                Rule::unique('account_heads')
            ],
            'account_code' =>[
                'required','max:255',
                Rule::unique('account_heads')
            ],
            'type' => 'required|integer',
            'opening_balance' => 'required|numeric|min:0',
        ];

        if ($request->type == 1){
            $rules['sub_type'] = 'required';
        }
        $request->validate($rules);
        $maxCode = AccountHead::max('account_code');
        if ($maxCode){
            $maxCode +=1;
        }else{
            $maxCode = 10001;
        }

        $request['account_code'] = $maxCode;


        $accountHead = new AccountHead();
        $accountHead->account_code = $request->account_code;
        $accountHead->name = $request->name;
        $accountHead->account_head_type_id = $request->type;
        $accountHead->account_head_sub_type_id = $request->sub_type;
        $accountHead->opening_balance = $request->opening_balance;
        $accountHead->save();

        return redirect()->route('account_head')->with('message', 'Account head add successfully.');
    }

    public function accountHeadEdit(AccountHead $accountHead) {
        $types = AccountHeadType::all();
        return view('accounts.account_head.edit', compact('accountHead','types'));
    }

    public function accountHeadEditPost(AccountHead $accountHead, Request $request) {

        $rules = [
            'name' =>[
                'required','max:255',
                Rule::unique('account_heads')
                    ->ignore($accountHead)
            ],
            'account_code' =>[
                'required','max:255',
                Rule::unique('account_heads')
                    ->ignore($accountHead)
            ],
            'opening_balance' => 'required|numeric|min:0',
            'type' => 'required|integer',
        ];

        if ($request->type == 1){
            $rules['sub_type'] = 'required';
        }
        $request->validate($rules);


        $accountHead->account_code = $request->account_code;
        $accountHead->name = $request->name;
        $accountHead->account_head_type_id = $request->type;
        $accountHead->account_head_sub_type_id = $request->sub_type;
        $accountHead->opening_balance = $request->opening_balance;
        $accountHead->save();
        return redirect()->route('account_head')->with('message', 'Account head edit successfully.');
    }
}
