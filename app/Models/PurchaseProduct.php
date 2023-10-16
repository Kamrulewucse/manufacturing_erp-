<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{
    protected $guarded = [];

    public function productType()
    {
        return $this->belongsTo(ProductType::class,'product_type_id','id');
    }
    public function category()
    {
        return $this->belongsTo(ProductCategory::class,'product_category_id','id');
    }
    public function subcategory()
    {
        return $this->belongsTo(ProductSubCategory::class,'product_sub_category_id','id');
    }
}
