<?php

namespace App\Http\Controllers;

use App\Models\ConfigProduct;
use App\Models\ConfigProductDetails;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ManufacturerConfigController extends Controller
{
    public function datatable() {
        $query = ConfigProduct::select('config_products.*')
            ->with('finishedGoods','configProductDetails');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function (ConfigProduct $configProduct) {
                $btn = '';
                $btn .= ' <a href="' . route('manufacture_template.edit', ['configProduct' => $configProduct->id]) . '" class="btn btn-dark bg-gradient-dark btn-sm"><i class="fa fa-edit"></i></a> ';
                $btn .= ' <a data-id="'.$configProduct->id.'" class="btn btn-danger bg-gradient-danger btn-sm btn-delete"><i class="fa fa-trash"></i></a> ';
                return $btn;
            })
            ->addColumn('product_name', function (ConfigProduct $configProduct) {
                return $configProduct->finishedGoods->name ?? '';
            })
            ->addColumn('finished_goods_template_items', function (ConfigProduct $configProduct) {
                $codes = '<ol style="text-align: left;">';
                foreach ($configProduct->configProductDetails as $configProductDetail) {
                    $codes .= '<li>';
                    $codes .= ($configProductDetail->product->name ?? '');
                    $codes .= ' - Quantity: '.$configProductDetail->quantity.' ';
                    $codes .= ($configProductDetail->product->unit->name ?? '');
                    $codes .= ', Loss Quantity: '.$configProductDetail->loss_quantity_percent.'%';
                    $codes .='</li>';
                }
                $codes .= '</ol>';

                return $codes;
            })

            ->rawColumns(['action','finished_goods_template_items'])
            ->toJson();
    }
    public function index()
    {
        return view('manufacture_template.all');
    }
    public function create()
    {
        $products = Product::where('product_type',1)->get();
        return view('manufacture_template.add',compact('products'));
    }
    public function store(Request $request)
    {
        $rules = [
            'finished_goods' => 'required',
            'product.*' => 'required',
            'quantity.*' => 'required|numeric|min:.1',
            'loss_quantity_percent.*' => 'required|numeric|min:0',
        ];

        $request->validate($rules);

        $configProduct = new ConfigProduct();
        $configProduct->product_id = $request->finished_goods;
        $configProduct->extra_cost = $request->extra_cost;
        $configProduct->save();

        $counter = 0;
        foreach ($request->product as $reqProduct) {
            $configItem = new ConfigProductDetails();
            $configItem->config_product_id = $configProduct->id;
            //$configItem->extra_cost = $request->extra_cost;
            $configItem->product_id = $request->product[$counter];
            $configItem->quantity = $request->quantity[$counter];
            $configItem->loss_quantity_percent = $request->loss_quantity_percent[$counter];
            $configItem->save();

            $counter++;
        }

        return redirect()->route('manufacture_template')->with('message', 'Manufacture template successfully created');
    }
    public function edit(ConfigProduct $configProduct)
    {
        $products = Product::where('product_type',1)->get();
        return view('manufacture_template.edit',compact('configProduct','products'));
    }

    public function update(ConfigProduct $configProduct,Request $request)
    {

        $rules = [
            'finished_goods' => 'required',
            'product.*' => 'required',
            'quantity.*' => 'required|numeric|min:.1',
            'loss_quantity_percent.*' => 'required|numeric|min:0',
        ];
        $request->validate($rules);

        $configProduct->product_id = $request->finished_goods;
        $configProduct->extra_cost = $request->extra_cost;
        $configProduct->save();

        ConfigProductDetails::where('config_product_id',$configProduct->id)->delete();

        $counter = 0;
        foreach ($request->product as $reqProduct) {

            $configItem = new ConfigProductDetails();
            $configItem->config_product_id = $configProduct->id;
            $configItem->product_id = $request->product[$counter];
            $configItem->quantity = $request->quantity[$counter];
            $configItem->loss_quantity_percent = $request->loss_quantity_percent[$counter];
            //$configItem->extra_cost = $request->extra_cost;
            $configItem->save();
            $counter++;
        }

        return redirect()->route('manufacture_template')->with('message', 'Manufacture template successfully updated');
    }

    public function delete(Request $request)
    {

        ConfigProductDetails::where('config_product_id',$request->id)->delete();
        ConfigProduct::find($request->id)->delete();

        return response()->json(['success' => true, 'message' => 'Successfully Deleted.']);
    }

    public function getProductDetails(Request $request){
        $product = Product::with('unit')
            ->where('id',$request->productId)
            ->first();
        $inventory=Inventory::where('product_id',$request->productId)->first();
        $data = [
            'product' => $product,
            'inventory' => $inventory,
        ];
        return response()->json($data);
    }

    public function getTemplateDetails(Request $request)
    {

        $template = ConfigProduct::with('configProductDetails')
            ->where('id',$request->templateId)
            ->first();
        $inventory = Inventory::where('product_id',$template->product_id)->first();

        $lastPrice = 0;
        if ($inventory){
            $lastPrice = $inventory->unit_price;
        }
        $extra_cost = $template->extra_cost?? 0;


        $manufactureTemplateHtml = view('layouts.partial.__manufacture_template',compact('template'))->render();

        return response()->json([
            'html'=>$manufactureTemplateHtml,
            'last_price'=>$lastPrice,
            'extra_cost'=>$extra_cost,
        ]);
    }
}
