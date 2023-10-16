<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderProduct extends Model
{
    protected $guarded = [];

    public function productType() {
        return $this->belongsTo(ProductType::class,'product_type_id','id');
    }

    public function category() {
        return $this->belongsTo(ProductCategory::class,'product_category_id');
    }
    public function subCategory() {
        return $this->belongsTo(ProductSubCategory::class,'product_sub_category_id');
    }
    public function size() {
        return $this->belongsTo(Size::class,'size_id');
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function purchaseOrder() {
        return $this->belongsTo(PurchaseOrder::class,'purchase_order_id','id');
    }

}
