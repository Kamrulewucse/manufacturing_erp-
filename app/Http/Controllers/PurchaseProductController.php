<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInventory;
use App\Models\PurchaseInventoryLog;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use App\Models\ProductType;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\PurchaseProduct;
use App\Models\SalePriceSetting;
use Intervention\Image\Facades\Image;

class PurchaseProductController extends Controller
{
    public function index() {
        $products = PurchaseProduct::all();

        // dd($products);
        return view('purchase.product.all', compact('products'));
    }

    public function add() {
        $categories = ProductCategory::where('status',1)->get();
        $productTypes = ProductType::where('status',1)->get();
        return view('purchase.product.add',compact('categories','productTypes'));
    }

    public function addPost(Request $request) {
        $request->validate([
            'product_type' => 'required',
            'category' => 'required',
            'subcategory' => 'required',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_image' => 'required|mimes:jpg,jpeg,png',
            'dimension' => 'required|max:255',
            'capacity' => 'required|max:255',
            'opening_product' => 'required|numeric|min:0',
            'status' => 'required'
        ]);

        $productImagePath= null;

        if ($request->product_image) {

            // Upload Image
            $file = $request->file('product_image');

            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();

            $destinationPath = 'public/uploads/product_image';

            $file->move($destinationPath, $filename);
            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            $img->save(public_path('uploads/product_image/'.$filename), 70);
            $productImagePath = 'uploads/product_image/'.$filename;
        }

        $product = new PurchaseProduct();
        $product->product_type = $request->product_type;
        $product->product_category_id = $request->category;
        $product->product_sub_category_id = $request->subcategory;
        $product->name = $request->name;
        $product->code = $request->code;
        $product->capacity = $request->capacity;
        $product->dimension = $request->dimension;
        $product->product_image = $productImagePath;
        $product->opening_product = $request->opening_product;
        $product->description = $request->description;
        $product->status = $request->status;
        $product->save();

        return redirect()->route('purchase_product')->with('message', 'Purchase product add successfully.');
    }

    public function edit(PurchaseProduct $product) {
        $categories = ProductCategory::where('status',1)->get();
        $productTypes = ProductType::where('status',1)->get();
        return view('purchase.product.edit', compact( 'product','categories','productTypes'));
    }

    public function editPost(PurchaseProduct $product, Request $request) {
        $request->validate([
            'product_type' => 'required',
            'category' => 'required',
            'subcategory' => 'required',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'product_image' => 'nullable|mimes:jpg,jpeg,png',
            'dimension' => 'required|max:255',
            'capacity' => 'required|max:255',
            'opening_product' => 'required|numeric|min:0',
            'status' => 'required'
        ]);

        if ($request->product_image) {

            unlink('public/'.$product->product_image);

            // Upload Image
            $file = $request->file('product_image');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/product_image';
            $file->move($destinationPath, $filename);
            // Thumbs
            $img = Image::make($destinationPath.'/'.$filename);
            //$img->resize(370, 181);
            $img->save(public_path('uploads/product_image/'.$filename), 70);
            $productImagePath = 'uploads/product_image/'.$filename;
            $product->product_image = $productImagePath;
        }

        $product->product_type = $request->product_type;
        $product->product_category_id = $request->category;
        $product->product_sub_category_id = $request->subcategory;
        $product->name = $request->name;
        $product->code = $request->code;
        $product->capacity = $request->capacity;
        $product->dimension = $request->dimension;
        $product->opening_product = $request->opening_product;
        $product->description = $request->description;
        $product->status = $request->status;
        $product->save();

        return redirect()->route('purchase_product')->with('message', 'Purchase product edit successfully.');
    }


    public function salePriceSettingAll(){
        $salePriceSettings = SalePriceSetting::all();
        return view('sale_price_setting.all', compact('salePriceSettings'));
    }

    public function salePriceSettingAdd(){
        $categories = ProductCategory::where('status',1)->get();
        return view('sale_price_setting.add',compact('categories'));
    }

    public function salePriceSettingAddPost(Request $request){
        $request->validate([
            'category' => 'required',
            'subcategory' => 'required',
            'product' => 'required',
            'customer_selling_price' => 'required|numeric|min:0',
            'dealer_selling_price' => 'required|numeric|min:0',
            'distributor_selling_price' => 'required|numeric|min:0',
            'status' => 'required'
        ]);

        $salePriceSetting = new SalePriceSetting();
        $salePriceSetting->product_category_id = $request->category;
        $salePriceSetting->product_sub_category_id = $request->subcategory;
        $salePriceSetting->purchase_product_id = $request->product;
        $salePriceSetting->customer_selling_price = $request->customer_selling_price;
        $salePriceSetting->dealer_selling_price = $request->dealer_selling_price;
        $salePriceSetting->distributor_selling_price = $request->distributor_selling_price;
        $salePriceSetting->status = $request->status;
        $salePriceSetting->save();

        return redirect()->route('sale_price_setting')->with('message', 'Sale Price Setting add successfully.');
    }
    public function salePriceSettingEdit(SalePriceSetting $salePriceSetting) {
        $categories = ProductCategory::where('status',1)->get();
        return view('sale_price_setting.edit', compact( 'salePriceSetting','categories'));
    }

    public function salePriceSettingEditPost(SalePriceSetting $salePriceSetting, Request $request) {
        $request->validate([
            'category' => 'required',
            'subcategory' => 'required',
            'product' => 'required',
            'customer_selling_price' => 'required|numeric|min:0',
            'dealer_selling_price' => 'required|numeric|min:0',
            'distributor_selling_price' => 'required|numeric|min:0',
            'status' => 'required'
        ]);

        $salePriceSetting->product_category_id = $request->category;
        $salePriceSetting->product_sub_category_id = $request->subcategory;
        $salePriceSetting->purchase_product_id = $request->product;
        $salePriceSetting->customer_selling_price = $request->customer_selling_price;
        $salePriceSetting->dealer_selling_price = $request->dealer_selling_price;
        $salePriceSetting->distributor_selling_price = $request->distributor_selling_price;
        $salePriceSetting->status = $request->status;
        $salePriceSetting->save();

        return redirect()->route('sale_price_setting')->with('message', 'Sale Price Setting edit successfully.');
    }
}
