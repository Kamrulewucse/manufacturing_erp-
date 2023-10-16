<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\ConfigProduct;
use App\Models\FinishedGoods;
use App\Models\FinishedGoodsRowMaterial;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetail;
use App\Models\Product;
use App\Models\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ManufacturerController extends Controller
{

    public function datatable()
    {
        $query = FinishedGoods::select('finished_goods.*')
            ->with('product','user','journalVoucher');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function (FinishedGoods $finishedGoods) {
                $btn = ' <a href="' . route('manufacture.edit', ['finishedGoods' => $finishedGoods->id]) . '" class="btn btn-dark btn-sm"><i class="fa fa-edit"></i></a> ';
                if ($finishedGoods->journalVoucher)
                    $btn .= ' <a target="_blank" href="'.route('journal_voucher_details',['journalVoucher'=>$finishedGoods->journalVoucher->id]).'" class="btn btn-dark btn-sm"><i class="fa fa-info-circle"></i> JV</a>';


                $btn .= ' <a href="'.route('finished_goods_details',['finishedGoods'=>$finishedGoods->id]).'" class="btn btn-dark btn-sm"><i class="fa fa-info-circle"></i></a>';
                $btn .= ' <a href="'.route('add_serial',['finishedGoods'=>$finishedGoods->id]).'" class="btn btn-info btn-sm">Add Serial</a>';
//                $btn .= ' <a data-id="' . $finishedGoods->id . '" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash"></i></a> ';
                return $btn;
            })
            ->addColumn('product_name', function (FinishedGoods $finishedGoods) {
                return $finishedGoods->product->name ?? '';
            })
            ->editColumn('quantity', function (FinishedGoods $finishedGoods) {
                return number_format($finishedGoods->quantity, 2);
            })
            ->editColumn('unit_price', function (FinishedGoods $finishedGoods) {
                return number_format($finishedGoods->unit_price, 2);
            })
            ->editColumn('selling_price', function (FinishedGoods $finishedGoods) {
                return number_format($finishedGoods->selling_price, 2);
            })
            ->editColumn('date', function (FinishedGoods $finishedGoods) {
                return $finishedGoods->date ? Carbon::parse($finishedGoods->date)->format('d-m-Y') : '';
            })

            ->rawColumns(['action'])
            ->toJson();
    }

    public function index()
    {
        return view('manufacture.all');
    }
    public function details(FinishedGoods $finishedGoods)
    {

        return view('manufacture.finished_goods_details',compact('finishedGoods'));
    }
    public function consumption(Request $request)
    {
        $rowMaterials = [];
        if ($request->start_date != '' && $request->end_date){
            $rowMaterials = FinishedGoodsRowMaterial::whereBetween('date', [Carbon::parse($request->start_date)->format('Y-m-d'), Carbon::parse($request->end_date)->format('Y-m-d')])
                ->get();
        }
        return view('manufacture.consumption',compact('rowMaterials'));
    }


    public function create()
    {
        $configProducts = ConfigProduct::with('finishedGoods')->get();
        $products = Product::where('product_type',1)->where('status',1)->get();
        return view('manufacture.add', compact('configProducts','products'));
    }



    public function store(Request $request) {
        $request->validate([
            'finished_goods' => 'required',
            'template_product' => 'required',
            'finished_goods_quantity' => 'required|numeric|min:1',
            'finished_goods_unit_price' => 'required|numeric|min:1',
            'date' => 'required|date',
            'quantity.*' => 'required|numeric|min:.1',
            'loss_quantity_percent.*' => 'required|numeric|min:0',
        ]);

        $counter = 0;
        foreach ($request->product_id as $reqProduct) {
            $product = Product::find($reqProduct);
            $inventory = Inventory::where('id',$request->inventory_id[$counter] ?? null)
                ->first();

            if ($inventory) {
                if ($inventory->quantity < $request->quantity[$counter]) {
                    return redirect()->back()->withInput()
                        ->with('error', "$product->name consumption quantity is  grater than stock quantity $inventory->quantity");
                }
            } else {
                return redirect()->back()->withInput()
                    ->with('error', "$product->name out of stock");
            }

            $counter++;
        }

        $counterOne = 0;
        foreach ($request->product_id as $reqProduct) {
            $product = Product::find($reqProduct);
            $inventoryLog=InventoryLog::where('product_id', $product->id)->where('purchase_order_id','>',$product->purchase_order_id)->first();
            $totalQuantity = $request->finished_goods_quantity * $request->quantity[$counterOne];
            if ($inventoryLog){
                if($product->quantity<$totalQuantity){
                    $product->update([
                        'quantity' => $product->quantity + $inventoryLog->quantity,
                    ]);
                }
            }

            $product->decrement('quantity',$totalQuantity);
            $counterOne++;
        }

        $templateConfigProduct = ConfigProduct::where('id',$request->template_product)->first();
        $templateFinishedGoods = Product::where('id',$request->finished_goods)->first();

        $finishedGoods = new FinishedGoods();
        $finishedGoods->name = $templateFinishedGoods->name;
        $finishedGoods->config_product_id = $templateConfigProduct->id;
        $finishedGoods->product_id = $templateFinishedGoods->id;
        $finishedGoods->quantity = $request->finished_goods_quantity;
        $finishedGoods->total = 0;
        $finishedGoods->extra_cost = 0;
        $finishedGoods->unit_price = 0;
        $finishedGoods->selling_price = $request->finished_goods_unit_price;
        $finishedGoods->date = Carbon::parse($request->date);
        $finishedGoods->save();

        $maxCode = AccountHead::max('account_code');
        if ($maxCode) {
            $maxCode += 1;
        } else {
            $maxCode = 10001;
        }

        $accountHead = new AccountHead();
        $accountHead->product_id = $finishedGoods->id;
        $accountHead->account_code = $maxCode;
        $accountHead->name = $finishedGoods->name;
        $accountHead->account_head_type_id = 5;//Income
        $accountHead->save();


        $counter = 0;
        $consumptionUnitPrice = 0;
        $totalExtra = 0;
        if ($request->product_id) {
            foreach ($request->product_id as $reqProduct) {
                $product = Product::find($reqProduct);
                $inventoryRowMaterial = Inventory::where('id',$request->inventory_id[$counter])
                    ->first();
                $totalConsumptionQty = $request->finished_goods_quantity * $request->quantity[$counter];
                $totalConsumptionLossQty = ($totalConsumptionQty / 100) * $request->loss_quantity_percent[$counter];

                $rowMaterial = new FinishedGoodsRowMaterial();
                $rowMaterial->finished_goods_id = $finishedGoods->id;
                $rowMaterial->inventory_id = $request->inventory_id[$counter];
                $rowMaterial->product_id = $request->product_id[$counter];
                $rowMaterial->per_unit_quantity = $request->quantity[$counter];
                $rowMaterial->consumption_quantity = $totalConsumptionQty;
                $rowMaterial->consumption_unit_price = $product->unit_price;
                $rowMaterial->remain_quantity = $inventoryRowMaterial->quantity;
                $rowMaterial->consumption_loss_quantity_percent = $request->loss_quantity_percent[$counter];
                $rowMaterial->consumption_loss_quantity = $totalConsumptionLossQty;
                $rowMaterial->date = Carbon::parse($request->date);
                $rowMaterial->save();

                //row material Inventory log
                $rowLog = InventoryLog::create([
                    'finished_goods_id' => $finishedGoods->id,
                    'finished_goods_row_material_id' => $rowMaterial->id,
                    'type' => 4,//Manufacturer Row Material Consumption
                    'product_id' => $request->product_id[$counter],
                    'product_category_id' => $product->category_id,
                    'product_type' => 1,
                    'date' => Carbon::parse($request->date)->format('Y-m-d'),
                    'quantity' => $rowMaterial->consumption_quantity,
                    'consumption_loss_quantity'=> $rowMaterial->consumption_loss_quantity,
                    'unit_price' => $product->unit_price,
                    'selling_price' => $inventoryRowMaterial->selling_price,
                    'avg_unit_price' => $inventoryRowMaterial->avg_unit_price,
                    'total' => $finishedGoods->quantity * $product->unit_price,
                    'note' => 'Manufacturer Row Material Consumption',
                ]);

                $rowLog->update([
                    'inventory_id' => $inventoryRowMaterial->id,
                    'serial' => str_pad($inventoryRowMaterial->id, 8, 0, STR_PAD_LEFT),
                ]);
                $inventoryRowMaterial->decrement('quantity',$totalConsumptionQty);

                $consumptionUnitPrice += $totalConsumptionQty * $product->unit_price;
                $counter++;
            }
        }
        $finishedGoods->unit_price = ($consumptionUnitPrice + $templateConfigProduct->extra_cost * $request->finished_goods_quantity) / $request->finished_goods_quantity;
        $finishedGoods->total = $consumptionUnitPrice + $totalExtra;
        $finishedGoods->save();

        // Inventory
        $inventory = Inventory::where('product_id',$templateFinishedGoods->id)->where('product_type',1)
            ->first();

        if ($inventory){
            $totalPrice = $inventory->total + ($finishedGoods->quantity * $finishedGoods->unit_price);
            $totalQuantity = $inventory->quantity + $finishedGoods->quantity;
            $inventory->update([
                'quantity' => $inventory->quantity + $finishedGoods->quantity,
                'unit_price' => $finishedGoods->unit_price,
                'selling_price' => $finishedGoods->selling_price,
                'total' => ($inventory->quantity + $finishedGoods->quantity) * $finishedGoods->unit_price,
            ]);
        }else{
            $inventory = Inventory::create([
                'product_id' => $templateFinishedGoods->id,
                'product_type' => 1,
                'quantity' => $finishedGoods->quantity,
                'finish_goods_id' => $finishedGoods->id,
                'unit_price' => $finishedGoods->unit_price,
                'selling_price' => $finishedGoods->selling_price,
                'avg_unit_price' => $finishedGoods->unit_price,
                'total' => $finishedGoods->quantity * $finishedGoods->unit_price,
            ]);
        }

        // Inventory Log
        $log = InventoryLog::create([
            'finished_goods_id' => $templateFinishedGoods->id,
            'type' => 3,//finished goods
            'product_id' =>$templateFinishedGoods->id,
            'product_type' => 1,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'quantity' => $finishedGoods->quantity,
            'unit_price' => $finishedGoods->unit_price,
            'selling_price' => $finishedGoods->selling_price,
            'total' => $finishedGoods->quantity * $finishedGoods->unit_price,
            'note' => 'Finished Goods Product',

        ]);

        $log->update([
            'inventory_id' => $inventory->id,
            'serial' => str_pad($inventory->id, 8, 0, STR_PAD_LEFT),
        ]);

        $inventory->update([
            'serial' => str_pad($inventory->id, 8, 0, STR_PAD_LEFT),
        ]);
        for ($i = 1; $i <= $request->finished_goods_quantity ; $i++) {
            Inventory::create([
                'product_id' => $templateFinishedGoods->id,
                'product_category_id' => $templateFinishedGoods->category_id,
                'finish_goods_id' => $finishedGoods->id,
                'product_type' => 3, //Sale Goods
                'quantity' => 1,
                'selling_price' => $request->finished_goods_unit_price,
                'unit_price' => $finishedGoods->unit_price,
                'avg_unit_price' => $finishedGoods->unit_price,
                'total' =>  $finishedGoods->unit_price,
            ]);

            // Inventory Log
            $saleLog = InventoryLog::create([
                'product_id' => $templateFinishedGoods->id,

                'type' => 1,
                'product_type' => 3, //Sale Goods
                'quantity' => 1,
                'date' => Carbon::parse($request->date)->format('Y-m-d'),
                'unit_price' => $finishedGoods->unit_price,
                'selling_price' => $request->finished_goods_unit_price,
                'total' => $finishedGoods->unit_price,
                'note' => 'Ready for Sale',

            ]);

            $saleLog->update([
                'inventory_id' => $inventory->id,
                'serial' => str_pad($inventory->id, 8, 0, STR_PAD_LEFT),
            ]);

            if($i == $request->finished_goods_quantity){
                break;
            }
        }

        //Create Journal
        $request['financial_year'] = convertDateToFiscalYear($request->date);

        $financialYear = financialYear($request->financial_year);

        $journalVoucherCheck = JournalVoucher::where('financial_year',$financialYear)
            ->orderBy('id','desc')->first();

        if ($journalVoucherCheck){
            $getJVLastNo = explode("-",$journalVoucherCheck->jv_no);
            $jvNo = 'JV-'.($getJVLastNo[1]+1);
        }else{
            $jvNo = 'JV-1';
        }
        $payeeDepositor = AccountHead::where('id',92)->first();

        $journalVoucher = new JournalVoucher();
        $journalVoucher->jv_no = $jvNo;
        $journalVoucher->financial_year = financialYear($request->financial_year);
        $journalVoucher->date = Carbon::parse($request->date)->format('Y-m-d');
        $journalVoucher->finished_goods_id = $finishedGoods->id;
        $journalVoucher->payee_depositor_account_head_id = $payeeDepositor->id;
        $journalVoucher->notes = 'Finished Goods';
        $journalVoucher->save();

        $accountHeadDebit = AccountHead::where('id',92)->first();

        $detail = new JournalVoucherDetail();
        $detail->type = 1;
        $detail->journal_voucher_id = $journalVoucher->id;
        $detail->account_head_id = $accountHeadDebit->id;
        $detail->amount = $finishedGoods->unit_price * $finishedGoods->quantity;
        $detail->save();

        //debit
        $log = new TransactionLog();
        $log->payee_depositor_account_head_id = $payeeDepositor->id;
        $log->receipt_payment_no = $jvNo;
        $log->jv_no = $jvNo;
        $log->date = Carbon::parse($request->date)->format('Y-m-d');
        $log->financial_year = financialYear($request->financial_year);
        $log->jv_type = 1;
        $log->journal_voucher_id = $journalVoucher->id;
        $log->journal_voucher_detail_id = $detail->id;
        $log->transaction_type = 8;//debit
        $log->account_head_id = $accountHeadDebit->id;
        $log->amount = $finishedGoods->unit_price * $finishedGoods->quantity;
        $log->notes = 'Finished Goods';
        $log->save();

        $consumptionTotal = 0;
        foreach ($finishedGoods->finishedGoodsRowMaterials as $finishedGoodsRowMaterial){
            $consumptionTotal += $finishedGoodsRowMaterial->consumption_unit_price * $finishedGoodsRowMaterial->consumption_quantity;
        }
            $accountHeadCredit = AccountHead::where('id',91)->first();

            $detail = new JournalVoucherDetail();
            $detail->type = 2;
            $detail->journal_voucher_id = $journalVoucher->id;
            $detail->account_head_id = $accountHeadCredit->id;
            $detail->amount =$consumptionTotal+($request->finished_goods_quantity*$request->extra_cost);
            $detail->save();

            //Credit
            $log = new TransactionLog();
            $log->payee_depositor_account_head_id = $payeeDepositor->id;
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_no = $jvNo;
            $log->jv_no = $jvNo;
            $log->financial_year = financialYear($request->financial_year);
            $log->jv_type = 2;
            $log->journal_voucher_id = $journalVoucher->id;
            $log->journal_voucher_detail_id = $detail->id;
            $log->transaction_type = 9;//credit
            $log->account_head_id = $accountHeadCredit->id;
            $log->amount =  $detail->amount;
            $log->notes = 'Row Material Consumption';
            $log->save();


        $countertwo = 0;
        foreach ($request->product_id as $reqProduct) {
            $product = Product::find($reqProduct);
            $inventoryLog=InventoryLog::where('product_id', $product->id)->where('purchase_order_id','>',$product->purchase_order_id)->first();
            $totalQuantity = $request->finished_goods_quantity*$request->quantity[$countertwo];
            if ($inventoryLog){
                if($product->quantity<$totalQuantity){
                    $product->update([
                        'unit_price' => $inventoryLog->unit_price,
                        'purchase_order_id' => $inventoryLog->purchase_order_id,
                    ]);
                }
            }

            $countertwo++;
        }

        return redirect()->route('add_serial',['finishedGoods'=>$finishedGoods->id])
            ->with('message','Manufacturing successfully');
    }

    public function edit(FinishedGoods $finishedGoods)
    {
        $configProducts = ConfigProduct::with('finishedGoods')->get();
        $products = Product::where('product_type',1)->where('status',1)->get();

        return view('manufacture.edit', compact('configProducts','finishedGoods','products'));
    }
    public function update(FinishedGoods $finishedGoods,Request $request)
    {
        $request->validate([
            'finished_goods' => 'required',
            'finished_goods_quantity' => 'required|numeric|min:1',
            'finished_goods_unit_price' => 'required|numeric|min:1',
            'date' => 'required|date',
            'quantity.*' => 'required|numeric|min:.1',
            'loss_quantity_percent.*' => 'required|numeric|min:0',
        ]);

        $counter = 0;
        foreach ($request->product_id as $reqProduct) {
            $product = Product::find($reqProduct);
            $inventoryCheck = Inventory::where('id',$request->inventory_id[$counter] ?? '')
                ->first();
            if ($inventoryCheck) {
                if (($inventoryCheck->quantity + $request->remaining_quantity[$counter]) < $request->quantity[$counter]) {
                    return redirect()->back()->withInput()
                        ->with('error', "$product->name consumption quantity is $request->quantity[$counter] grater than stock quantity $inventoryCheck->quantity");
                }
            } else {
                return redirect()->back()->withInput()
                    ->with('error', "$product->name out of stock");
            }

            $counter++;
        }

        //dd('ok');
        $templateFinishedGoods = ConfigProduct::where('id',$request->finished_goods)->first();


        $finishedGoods->name = $templateFinishedGoods->category->name.'-M-' . str_pad($templateFinishedGoods->id, 8, 0, STR_PAD_LEFT);
        $finishedGoods->config_product_id = $templateFinishedGoods->id;
        $finishedGoods->category_id = $templateFinishedGoods->category_id;
        $finishedGoods->quantity = $request->finished_goods_quantity;
        $finishedGoods->unit_price = 0;
        $finishedGoods->selling_price = $request->finished_goods_unit_price;
        $finishedGoods->date = Carbon::parse($request->date);
        $finishedGoods->save();

        $counter = 0;
        $consumptionUnitPrice = 0;
        if ($request->product_id) {
            InventoryLog::where('finished_goods_id',$finishedGoods->id)->delete();
            $product = Product::find($reqProduct);
            $rowMaterials = FinishedGoodsRowMaterial::where('finished_goods_id',$finishedGoods->id)->get();
            foreach ($rowMaterials as $rowMaterial){
                $inventoryRowMaterialUpdate = Inventory::where('id',$request->inventory_id[$counter])
                    ->first();
                if ($inventoryRowMaterialUpdate){
                    $inventoryRowMaterialUpdate->increment('quantity',$rowMaterial->consumption_quantity);
                }
            }
            FinishedGoodsRowMaterial::where('finished_goods_id',$finishedGoods->id)->delete();

            foreach ($request->product_id as $reqProduct) {
                $inventoryRowMaterial = Inventory::where('id',$request->inventory_id[$counter])
                    ->first();

                $totalConsumptionQty = $request->finished_goods_quantity * $request->quantity[$counter];
                $totalConsumptionLossQty = ($totalConsumptionQty / 100) * $request->loss_quantity_percent[$counter];


                $rowMaterial = new FinishedGoodsRowMaterial();
                $rowMaterial->finished_goods_id = $finishedGoods->id;
                $rowMaterial->inventory_id = $request->inventory_id[$counter];
                $rowMaterial->product_id = $request->product_id[$counter];
                $rowMaterial->per_unit_quantity = $request->quantity[$counter];
                $rowMaterial->consumption_quantity = $totalConsumptionQty;
                $rowMaterial->consumption_unit_price = $product->unit_price;
                $rowMaterial->remain_quantity = $inventoryRowMaterial->quantity;
                $rowMaterial->consumption_loss_quantity_percent = $request->loss_quantity_percent[$counter];
                $rowMaterial->consumption_loss_quantity = $totalConsumptionLossQty;
                $rowMaterial->date = Carbon::parse($request->date);
                $rowMaterial->save();

                //row material Inventory log
                $rowLog = InventoryLog::create([
                    'finished_goods_id' => $finishedGoods->id,
                    'finished_goods_row_material_id' => $rowMaterial->id,
                    'type' => 4,//Manufacturer Row Material Consumption
                    'product_id' => $request->product_id[$counter],
                    'product_category_id' => $product->category_id,
                    'product_type' => 1,
                    'date' => Carbon::parse($request->date)->format('Y-m-d'),
                    'quantity' => $rowMaterial->consumption_quantity,
                    'consumption_loss_quantity'=> $rowMaterial->consumption_loss_quantity,
                    'unit_price' => $product->unit_price,
                    'selling_price' => $inventoryRowMaterial->selling_price,
                    'avg_unit_price' => $inventoryRowMaterial->avg_unit_price,
                    'total' => $finishedGoods->quantity * $product->unit_price,
                    'note' => 'Manufacturer Row Material Consumption',
                ]);
                $rowLog->update([
                    'inventory_id' => $inventoryRowMaterial->id,
                    'serial' => str_pad($inventoryRowMaterial->id, 8, 0, STR_PAD_LEFT),
                ]);
                $inventoryRowMaterial->decrement('quantity',$totalConsumptionQty);

                $consumptionUnitPrice += $totalConsumptionQty * $product->unit_price;
                $counter++;
            }
        }
        $finishedGoods->unit_price = $consumptionUnitPrice / $request->finished_goods_quantity;
        $finishedGoods->save();

        // Inventory
        $inventory = Inventory::where('product_id',$finishedGoods->product_id)
            ->first();

        if ($inventory){
            $totalPrice = $inventory->total + ($finishedGoods->quantity * $finishedGoods->unit_price);
            $totalQuantity = $inventory->quantity + $finishedGoods->quantity;
            $avgPrice = $totalPrice / $totalQuantity;
            $inventory->update([
                'quantity' => $inventory->quantity + $finishedGoods->quantity,
                'unit_price' => $finishedGoods->unit_price,
                'selling_price' => $finishedGoods->selling_price,
                'avg_unit_price' => $avgPrice,
                'total' => ($inventory->quantity + $finishedGoods->quantity) * $finishedGoods->unit_price,
            ]);

        }else{
            $avgPrice = $finishedGoods->unit_price;
            $inventory = Inventory::create([
                'product_id' => $finishedGoods->id,
                'product_type' => 1,
                'quantity' => $finishedGoods->quantity,
                'unit_price' => $finishedGoods->unit_price,
                'selling_price' => $finishedGoods->selling_price,
                'avg_unit_price' => $finishedGoods->unit_price,
                'total' => $finishedGoods->quantity * $finishedGoods->unit_price,
            ]);
        }


        // Inventory Log
        $log = InventoryLog::create([
            'finished_goods_id' => $finishedGoods->id,
            'type' => 3,//finished goods
            'product_id' => $finishedGoods->id,
            'product_type' => 1,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'quantity' => $finishedGoods->quantity,
            'unit_price' => $finishedGoods->unit_price,
            'selling_price' => $finishedGoods->selling_price,
            'avg_unit_price' => $avgPrice,
            'total' => $finishedGoods->quantity * $finishedGoods->unit_price,
            'note' => 'Finished Goods Product',

        ]);

        $log->update([
            'inventory_id' => $inventory->id,
            'serial' => str_pad($inventory->id, 8, 0, STR_PAD_LEFT),
        ]);

        $inventory->update([
            'serial' => str_pad($inventory->id, 8, 0, STR_PAD_LEFT),
        ]);

        //Update Journal
        $request['financial_year'] = convertDateToFiscalYear($request->date);

        $financialYear = financialYear($request->financial_year);

        $journalVoucher = JournalVoucher::where('finished_goods_id',$finishedGoods->id)->first();
        JournalVoucherDetail::where('journal_voucher_id',$journalVoucher->id)->delete();
        TransactionLog::where('journal_voucher_id',$journalVoucher->id)->delete();

        $jvNo = $journalVoucher->jv_no;

        $payeeDepositor = AccountHead::where('product_id',$finishedGoods->product_id)->first();

        $journalVoucher->jv_no = $jvNo;
        $journalVoucher->financial_year = financialYear($request->financial_year);
        $journalVoucher->date = Carbon::parse($request->date)->format('Y-m-d');
        $journalVoucher->finished_goods_id = $finishedGoods->id;
        $journalVoucher->payee_depositor_account_head_id = $payeeDepositor->id;
        $journalVoucher->notes = 'Finished Goods';
        $journalVoucher->save();

        $accountHeadDebit = AccountHead::where('product_id',$finishedGoods->product_id)->first();

        $detail = new JournalVoucherDetail();
        $detail->type = 1;
        $detail->journal_voucher_id = $journalVoucher->id;
        $detail->account_head_id = $accountHeadDebit->id;
        $detail->amount = $finishedGoods->unit_price * $finishedGoods->quantity;
        $detail->save();

        //debit
        $log = new TransactionLog();
        $log->payee_depositor_account_head_id = $payeeDepositor->id;
        $log->receipt_payment_no = $jvNo;
        $log->jv_no = $jvNo;
        $log->date = Carbon::parse($request->date)->format('Y-m-d');
        $log->financial_year = financialYear($request->financial_year);
        $log->jv_type = 1;
        $log->journal_voucher_id = $journalVoucher->id;
        $log->journal_voucher_detail_id = $detail->id;
        $log->transaction_type = 8;//debit
        $log->account_head_id = $accountHeadDebit->id;
        $log->amount = $finishedGoods->unit_price * $finishedGoods->quantity;
        $log->notes = 'Finished Goods';
        $log->save();

        foreach ($finishedGoods->finishedGoodsRowMaterials as $finishedGoodsRowMaterial){
            $accountHeadCredit = AccountHead::where('product_id',$finishedGoodsRowMaterial->product_id)->first();

            $detail = new JournalVoucherDetail();
            $detail->type = 2;
            $detail->journal_voucher_id = $journalVoucher->id;
            $detail->account_head_id = $accountHeadCredit->id;
            $detail->amount = $finishedGoodsRowMaterial->consumption_unit_price * $finishedGoodsRowMaterial->consumption_quantity;
            $detail->save();

            //Credit
            $log = new TransactionLog();
            $log->payee_depositor_account_head_id = $payeeDepositor->id;
            $log->date = Carbon::parse($request->date)->format('Y-m-d');
            $log->receipt_payment_no = $jvNo;
            $log->jv_no = $jvNo;
            $log->financial_year = financialYear($request->financial_year);
            $log->jv_type = 2;
            $log->journal_voucher_id = $journalVoucher->id;
            $log->journal_voucher_detail_id = $detail->id;
            $log->transaction_type = 9;//credit
            $log->account_head_id = $accountHeadCredit->id;
            $log->amount =  $detail->amount;
            $log->notes = 'Row Material Consumption';
            $log->save();
        }


        return redirect()->route('finished_goods')
            ->with('message','Manufacturing updated successfully');

    }

    public function delete(Request $request)
    {


        $finishedGoods = FinishedGoods::where('id',$request->id)->first();

        $items = FinishedGoodsRowMaterial::where('finished_goods_id',$finishedGoods->id)->get();

        foreach ($items as $item){
            $inventory = Inventory::where('id',$item->inventory_id)->first();
            if ($inventory){
                if ($inventory->quantity < $item->consumption_quantity){
                    return response()->json(['success' => false, 'message' => 'Row product '.$item->product->name.' stock qty:'.$inventory->quantity.' < consumption qty:'.$item->consumption_quantity]);
                }
            }else{
                return response()->json(['success' => false, 'message' => $item->product->name.' stock out']);

            }

        }

        $finishedGoodsInventory = Inventory::where('id',$finishedGoods->product_id)->first();
        if ($finishedGoodsInventory){
            if ($finishedGoods->quantity > $finishedGoodsInventory->quantity){
                return response()->json(['success' => false, 'message' => 'Finished goods  '.$finishedGoods->product->name.' stock qty:'.$finishedGoodsInventory->quantity.' < finished goods qty:'.$finishedGoods->quantity]);
            }
        }else{
            return response()->json(['success' => false, 'message' => $finishedGoods->product->name.' stock out']);

        }


        $journalVoucher = JournalVoucher::where('finished_goods_id',$finishedGoods->id)->first();
        JournalVoucherDetail::where('journal_voucher_id',$journalVoucher->id)->delete();
        TransactionLog::where('journal_voucher_id',$journalVoucher->id)->delete();
        InventoryLog::where('finished_goods_id',$finishedGoods->id)->delete();
        FinishedGoodsRowMaterial::where('finished_goods_id',$finishedGoods->id)->delete();

        $finishedGoods->delete();
        return response()->json(['success' => true, 'message' => 'Successfully Deleted.']);
    }

    public function inventory() {
        return view('manufacture.inventory.all');
    }

    public function inventoryDatatable() {

        $query = Inventory::with('product')->where('product_type', 1);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('product', function(Inventory $inventory) {
                return $inventory->product->name??'';
            })
            ->addColumn('product_type', function(Inventory $inventory) {
                if ($inventory->product_type == 1)
                    return 'Finish Good';
                else if($inventory->product_type == 2)
                    return 'Row Material';
            })

//            ->addColumn('action', function (Inventory $inventory) {
//                return '<a href="' . route('stock.details', ['product' => $inventory->product_id, 'color' => $inventory->color_id, 'size' => $inventory->size_id]) . '" class="btn btn-primary btn-sm">Details</a>';
//            })
            ->addColumn('action', function (Inventory $inventory) {
//                $btn = '<a href="' . route('stock.details', ['product' => $inventory->product_id, 'color' => $inventory->color_id, 'size' => $inventory->size_id]).'" class="btn btn-primary btn-sm">Details</a>';;
                $btn = '<a href="' . route('finish_stock.details', ['product' => $inventory->product_id, 'color' => $inventory->color_id, 'size' => $inventory->size_id]).'" class="btn btn-primary btn-sm">Details</a>';;
                return $btn;
            })

            ->editColumn('quantity', function(Inventory $inventory) {
                return number_format($inventory->quantity, 2);
            })
            ->editColumn('total', function(Inventory $inventory) {
                return number_format($inventory->total, 2);
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function stock() {
        return view('manufacture.inventory.stock');
    }

    public function stockDatatable() {
        $query = Inventory::with('product')->where('product_type',3)->where('quantity', '>',0);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('product', function(Inventory $inventory) {
                return $inventory->product->name??'';
            })
            ->addColumn('product_type', function(Inventory $inventory) {
                if ($inventory->product_type == 1)
                    return 'Finish Good';
                else if($inventory->product_type == 2)
                    return 'Row Material';
            })
            ->addColumn('action', function (Inventory $inventory) {
                return ' <a data-id="' . $inventory->id . '" class="btn btn-primary btn-sm btn-add">Add</a>';
            })
            ->editColumn('quantity', function(Inventory $inventory) {
                return number_format($inventory->quantity, 2);
            })
            ->editColumn('total', function(Inventory $inventory) {
                return number_format($inventory->total, 2);
            })
            ->rawColumns(['action'])
            ->toJson();
    }

//    public function stockDetails(Product $product) {
//        return view('manufacture.inventory.stock_details', compact('product'));
//    }
    public function finishStockDetails(Product $product) {
        $inventories = Inventory::where('product_type',3)->where('quantity', '>',0)->where('product_id',$product->id)->orderBy('serial')->get();
        return view('manufacture.inventory.finish_stock_details', compact('product','inventories'));
    }



    public function inventoryDetailsDatatable() {

        $query = InventoryLog::where('product_id', request('product_id'))
            ->with('product', 'supplier', 'finishGoodsId');



        return DataTables::eloquent($query)
            ->editColumn('date', function(InventoryLog $log) {
                return $log->date;
            })
            ->editColumn('type', function(InventoryLog $log) {
                if ($log->type == 1)
                    return '<span class="badge badge-success">In</span>';
                elseif ($log->type == 2)
                    return '<span class="badge badge-danger">Out</span>';
                else
                    return '';
            })
            ->editColumn('quantity', function(InventoryLog $log) {
                return number_format($log->quantity, 2);
            })
            ->editColumn('selling_price', function(InventoryLog $log) {
                return number_format($log->selling_price, 2);
            })
            ->editColumn('total', function(InventoryLog $log) {
                return number_format($log->total, 2);
            })
            ->editColumn('unit_price', function(InventoryLog $log) {
                if ($log->unit_price)
                    return '৳'.number_format($log->unit_price, 2);
                else
                    return '';
            })
            ->editColumn('supplier', function(InventoryLog $log) {
                if ($log->supplier)
                    return $log->supplier->name??'';
                else
                    return '';
            })
            ->editColumn('finishGoodsId', function(InventoryLog $log) {
                if ($log->finishGoodsId)
                    return '<a href="'.route('finished_goods_details', ['finishedGoods' => $log->finishGoodsId->id]).'">Order</a>';
                else
                    return '';
            })
            ->orderColumn('date', function ($query, $order) {
                $query->orderBy('date', $order)->orderBy('created_at', 'desc');
            })
            ->rawColumns(['type', 'order'])
            ->filter(function ($query) {
                if (request()->has('date') && request('date') != '') {
                    $dates = explode(' - ', request('date'));
                    if (count($dates) == 2) {
                        $query->where('date', '>=', $dates[0]);
                        $query->where('date', '<=', $dates[1]);
                    }
                }

                if (request()->has('type') && request('type') != '') {
                    $query->where('type', request('type'));
                }
            })
            ->rawColumns(['action','finishGoodsId','type'])
            ->toJson();
    }

    public function addSerial(FinishedGoods $finishedGoods){

        $inventories = Inventory::where('product_type',3)->where('finish_goods_id',$finishedGoods->id)->get();

        return view('manufacture.inventory.add_serial',compact('finishedGoods','inventories'));
    }

    public function updateSerial(Request $request){
        $rules = [
            'product_id' => 'required',
            'serial' =>  [
                'required',
                Rule::unique('inventories')
                    ->ignore($request->product_id)
                    ->where('serial', $request->serial)
            ],
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        Inventory::where('id', $request->product_id)->update([
            'serial' => $request->serial,
        ]);

        return response()->json(['success' => true, 'message' => 'Update Serial Successfully !.']);

    }
}
