<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrderDetails extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class,'row_product_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function serviceOrder()
    {
        return $this->belongsTo(ServiceOrder::class,'service_order_id','id');
    }
}
