<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInventoryLog extends Model
{
    protected $guarded = [];

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function product() {
        return $this->belongsTo(PurchaseProduct::class, 'purchase_product_id', 'id');
    }

//    public function order() {
//        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'id');
//    }

    public function purchaseOrder() {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function productType(){
        return $this->belongsTo(ProductType::class,'product_type_id','id');
    }
}
