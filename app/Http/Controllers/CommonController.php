<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Client;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\PurchaseInventory;
use App\Models\PurchaseOrderProduct;
use App\Models\SalesOrderProduct;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;


class CommonController extends Controller
{
    public function payeeJson(Request $request)
    {
        if (!$request->searchTerm) {
            $accountHeads = AccountHead::where(function($query) {
                $query->where('account_head_type_id', 5)
                    ->orWhereNull('account_head_type_id');
            })
                ->whereNotNull('client_id')
                ->orWhereNotNull('employee_id')
                ->limit(20)
                ->get();
        } else {
            $accountHeads = AccountHead::whereNotNull('client_id')
                ->orWhereNotNull('employee_id')
                ->where('name', 'like','%' . $request->searchTerm.'%')
                ->limit(50)->get();
        }

        $data = array();

        foreach ($accountHeads as $accountHead) {
            $data[] = [
                'id' => $accountHead->id,
                'text' =>('Name:'.(employeeClientInfo($accountHead->id)->name ?? '').'|ID No:'.(employeeClientInfo($accountHead->id)->id_no ?? '')),
            ];
        }

        echo json_encode($data);
    }

    public function payeeJson1(Request $request)
    {
        if (!$request->searchTerm) {
            $accountHeads = AccountHead::where(function($query) {
                $query->where('account_head_type_id',4)
                    ->orWhereNull('account_head_type_id');
            })
                ->whereNotNull('client_id')
                ->orWhereNotNull('employee_id')
                ->limit(20)
                ->get();
        } else {
            $accountHeads = AccountHead::whereNotNull('client_id')
                ->orWhereNotNull('employee_id')
                ->where('name', 'like','%' . $request->searchTerm.'%')
                ->limit(50)->get();
        }

        $data = array();

        foreach ($accountHeads as $accountHead) {
            $data[] = [
                'id' => $accountHead->id,
                'text' =>('Name:'.(employeeClientInfo($accountHead->id)->name ?? '').'|ID No:'.(employeeClientInfo($accountHead->id)->id_no ?? '')),
            ];
        }

        echo json_encode($data);
    }

    public function getProduct(Request $request)
    {
        $product = Product::where('category_id', $request->categoryID)
            ->where('product_type', 2)
            ->where('status', 1)
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($product);
    }
    public function getSubCategory(Request $request)
    {
        $subcategory = ProductSubCategory::where('product_category_id', $request->categoryID)
            ->where('status', 1)
            ->orderBy('name')
            ->get()->toArray();

        return response()->json($subcategory);
    }

    public function getStock(Request $request)
    {
        $inventory = Inventory::where('product_category_id', $request->categoryId)
            ->where('product_sub_category_id', $request->subCategoryId)
            ->where('product_id', $request->productId)
            ->where('color_id', $request->colorId)
            ->where('size_id', $request->sizeId)
            ->where('warehouse_id', $request->warehouseId)
            ->first();

        //dd($request->all());

        return response()->json($inventory);
    }
    public function getUnit(Request $request)
    {
        $product =Product::where('id', $request->productID)->first();

        $unit = Unit::where('id',$product->unit_id)->first();

        $lastPurchasePrice = PurchaseOrderProduct::where('product_id', $request->productID)->latest()->first();

        return response()->json([
            'unit'=>$unit,
            'lastPurchasePrice'=>$lastPurchasePrice,
        ]);
    }

    public function getEmployeeDetails(Request $request) {
        $employee = Employee::where('id', $request->employeeId)->with('department', 'designation')->first();

        return response()->json($employee);
    }

    public function getCustomerDetails(Request $request) {
        $customer = Client::where('id', $request->customerID)->first();

        return response()->json($customer);
    }

    public function getField(Request $request)
    {
        $fields = Designation::where('department_id', $request->divisionId)->get();
        return response($fields);
    }

    public function getFieldEdit(Request $request)
    {
        $fields = Designation::where('department_id', $request->departmentId)->get();
        return response($fields);
    }

    public function accountHeadCodeJson(Request $request)
    {
        if (!$request->searchTerm) {
            $accountHeads = AccountHead::where('status', 1)
                ->orderBy('id')
                ->limit(10)
                ->get();
        } else {
            $accountHeads = AccountHead::where('status', 1)
                ->where('account_code', 'like','%' . $request->searchTerm.'%')
                ->orWhere('name', 'like','%'.$request->searchTerm.'%')
                ->orderBy('account_code','asc')
                ->limit(50)
                ->get();
        }

        $data = array();

        foreach ($accountHeads as $accountHead) {
            $data[] = [
                'id' => $accountHead->id,
                'text' =>$accountHead->name.'|Code:'.$accountHead->account_code,
            ];
        }

        echo json_encode($data);
    }
    public function saleAccountHeadCodeJson(Request $request)
    {
        if (!$request->searchTerm) {
            $accountHeads = AccountHead::where('status', 1)->whereIn('account_head_type_id',[1,5])
                ->orderBy('id')
                ->limit(10)
                ->get();
        } else {
            $accountHeads = AccountHead::where('status', 1)
                ->whereIn('account_head_type_id',[1,5])
                ->where('account_code', 'like','%' . $request->searchTerm.'%')
                ->orWhere('name', 'like','%'.$request->searchTerm.'%')
                ->orderBy('account_code','asc')
                ->limit(50)
                ->get();
        }

        $data = array();

        foreach ($accountHeads as $accountHead) {
            $data[] = [
                'id' => $accountHead->id,
                'text' =>$accountHead->name.'|Code:'.$accountHead->account_code,
            ];
        }

        echo json_encode($data);
    }

    public function productJson(Request $request)
    {
        if (!$request->searchTerm) {
            $products = Product::where('product_type', 2)
                ->where('status', 1)
                ->where('quantity','>', 0)
                ->orderBy('name')
                ->limit(20)
                ->get();
        } else {
            $products = Product::where('product_type', 2)
                ->where('name', 'like','%'.$request->searchTerm.'%')
                ->where('quantity','>', 0)
                ->orderBy('name')
                ->limit(20)
                ->get();

        }
        $data = array();

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->id,
                'text' =>$product->name.' - '.$product->unit->name ?? '',
            ];
        }

        echo json_encode($data);
    }
    public function finishProductJson(Request $request)
    {
        if (!$request->searchTerm) {
            $products = Product::where('product_type', 1)
                ->where('status', 1)
                ->orderBy('name')
                ->limit(20)
                ->get();
        } else {

            $products = Product::where('product_type', 1)
                ->where('status', 1)
                ->where('name', 'like','%'.$request->searchTerm.'%')
                ->orderBy('name')
                ->limit(20)
                ->get();

        }
        $data = array();

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->id,
                'text' =>$product->name,
            ];
        }

        echo json_encode($data);
    }

    public function saleProductJson(Request $request)
    {
        if (!$request->searchTerm) {
            $products = Inventory::where('product_type',3)
                ->where('quantity', '>', 0)
                ->where('serial','!=',null)
                ->limit(20)
                ->get();
        } else {

            $products =Inventory::where('product_type',3)
                ->where('quantity', '>', 0)
                ->where('serial','!=',null)
                ->whereHas('product', function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->searchTerm . '%');
                })
                ->orWhere('serial', 'like','%'.$request->searchTerm.'%')
                ->limit(20)
                ->get();

        }
        $data = array();

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->id,
                'text' =>$product->product->name.' - '.$product->serial ?? '',
            ];
        }

        echo json_encode($data);
    }

    public function productStatus(){
       return view('product_status');
    }

    public function productStatusDetails(Request $request){

        $saleProduct = SalesOrderProduct::where('serial', $request->serial)->first();
        $finishProduct = Inventory::where('serial', $request->serial)->first();

//        dd($finishProduct);
            if ($request->serial == '') {
                return response()->json(['success' => false, 'message' => 'Search field empty !']);
            } if (!$finishProduct ) {
                return response()->json(['success' => false, 'message' => 'Product Not Found !']);
            }

            if($saleProduct && $finishProduct->quantity==0){
                $product = [
                    'serial' => $saleProduct->serial,
                    'name' => $saleProduct->product->name,
                    'customer' => $saleProduct->client->name,
                    'date' => Carbon::parse($saleProduct->date)->format('d-m-Y'),
                    'warranty' => $saleProduct->warranty,
                    'sale_id' => $saleProduct->sales_order_id,
                    'manufacture_date' => Carbon::parse($finishProduct->created_at)->format('d-m-Y'),
                ];
                return response()->json(['success' => true, 'product' => $product]);
            }

                if($finishProduct){
                $product = [
                    'serial' => $finishProduct->serial,
                    'name' => $finishProduct->product->name,
                    'customer' => '',
                    'date' => '',
                    'warranty' =>  $finishProduct->product->warranty ?? '',
                    'sale_id' => '',
                    'manufacture_date' => Carbon::parse($finishProduct->created_at)->format('d-m-Y'),
                ];
                return response()->json(['success' => true, 'product' => $product]);
            }
    }



}
