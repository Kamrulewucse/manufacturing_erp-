<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderProduct extends Model
{
    protected $guarded = [];

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class,'product_category_id');
    }

    public function productSubCategory()
    {
        return $this->belongsTo(ProductSubCategory::class,'product_sub_category_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
    public function color()
    {
        return $this->belongsTo(Color::class,'color_id');
    }
    public function size()
    {
        return $this->belongsTo(Size::class,'size_id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function saleOrder()
    {
        return $this->belongsTo(SalesOrder::class,'sales_order_id','id');
    }
}


