<?php

namespace App\Http\Controllers;

use App\Models\MobileBanking;
use Illuminate\Http\Request;

class MobileBankController extends Controller
{
    public function index() {
        $mobileBanks = MobileBanking::all();

        return view('bank_n_account.mobile_bank.all', compact('mobileBanks'));
    }

    public function add() {
        return view('bank_n_account.mobile_bank.add');
    }

    public function addPost(Request $request) {

        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:255',
            'opening_balance' => 'nullable|numeric|min:0',
            'status' => 'required'
        ]);

        $mobileBank = new MobileBanking();
        $mobileBank->name = $request->name;
        $mobileBank->number = $request->number;
        $mobileBank->opening_balance = $request->opening_balance;
        $mobileBank->amount = $request->opening_balance;
        $mobileBank->status = $request->status;
        $mobileBank->save();

        return redirect()->route('mobile_bank')->with('message', 'Mobile bank add successfully.');
    }

    public function edit(MobileBanking $mobileBank) {

        return view('bank_n_account.mobile_bank.edit', compact('mobileBank'));
    }

    public function editPost(MobileBanking $mobileBank, Request $request) {
//        dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $mobileBank->name = $request->name;
        $mobileBank->number = $request->number;
        $mobileBank->status = $request->status;
        $mobileBank->save();

        return redirect()->route('mobile_bank')->with('message', 'Mobile bank edit successfully.');
    }
}
