<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInventory extends Model
{
    protected $guarded = [];

    public function product(){
        return $this->belongsTo(PurchaseOrderProduct::class,'purchase_product_id','id');
    }
    public function category(){
        return $this->belongsTo(ProductCategory::class,'product_category_id','id');
    }
    public function subcategory(){
        return $this->belongsTo(ProductSubCategory::class,'product_sub_category_id','id');
    }
    public function technician(){
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function productType(){
        return $this->belongsTo(ProductType::class,'product_type_id','id');
    }
}
