<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index() {
        $categories = ProductCategory::all();

        return view('purchase.product_category.all', compact('categories'));
    }

    public function add() {
        return view('purchase.product_category.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $category = new ProductCategory();
        $category->name = $request->name;
        $category->status = $request->status;
        $category->save();

        return redirect()->route('product_category')->with('message', 'Category add successfully.');
    }

    public function edit(ProductCategory $category) {
        return view('purchase.product_category.edit', compact('category'));
    }

    public function editPost(ProductCategory $category, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $category->name = $request->name;
        $category->status = $request->status;
        $category->save();

        return redirect()->route('product_category')->with('message', 'Category edit successfully.');
    }
}
