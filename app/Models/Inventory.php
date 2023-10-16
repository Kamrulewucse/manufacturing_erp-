<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function product(){
        return $this->belongsTo(Product::class,'product_id', 'id');
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
    public function color() {
        return $this->belongsTo(Color::class,'color_id');
    }
    public function dieNo() {
        return $this->belongsTo(DieNo::class,'die_no_id');
    }
    public function inventoryLog(){
        return $this->hasOne(InventoryLog::class,'inventory_id', 'id');
    }
}
