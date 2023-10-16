<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigProductDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
