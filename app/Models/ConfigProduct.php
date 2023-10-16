<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function finishedGoods()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }
    public function configProductDetails()
    {
        return $this->hasMany(ConfigProductDetails::class,'config_product_id','id')
            ->with('product');
    }
}
