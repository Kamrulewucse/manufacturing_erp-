<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function supplier(){
        return $this->belongsTo(Client::class)->where('type',2);
    }
    public function finishGoodsId(){
        return $this->belongsTo(FinishedGoods::class,'finished_goods_id');
    }

    public function purchaseOrder(){
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function saleOrder(){
        return $this->belongsTo(SalesOrder::class,'sales_order_id','id');
    }
}
