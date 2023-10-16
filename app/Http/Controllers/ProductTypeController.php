<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function index() {
        $productTypes = ProductType::get();
        return view('administrator.product_type.all', compact('productTypes'));
    }

    public function add() {
        return view('administrator.product_type.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $productType = new ProductType();
        $productType->name = $request->name;
        $productType->status = $request->status;
        $productType->save();

        return redirect()->route('product_type')->with('message', 'Product type add successfully.');
    }

    public function edit(ProductType $productType) {
        return view('administrator.product_type.edit', compact('productType'));
    }

    public function editPost(ProductType $productType, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $productType->name = $request->name;
        $productType->status = $request->status;
        $productType->save();

        return redirect()->route('product_type')->with('message', 'Product type edit successfully.');
    }
}
