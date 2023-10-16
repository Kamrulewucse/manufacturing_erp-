<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function user(){

        return $this->belongsTo(User::class,'user_id');
    }
    public function getDueAttribute() {
        $query = SalesOrder::where('customer_id', $this->id)
            ->where('return_status',0);
        return $query->sum('due');
    }

    public function getPaidAttribute() {
        $query = SalesOrder::where('customer_id', $this->id)
            ->where('return_status',0);
        return $query->sum('paid');
    }

    public function getTotalAttribute() {
        $query = SalesOrder::where('customer_id', $this->id);
        return $query->sum('total');
    }

//    public function getRefundAttribute() {
//        $query = SalesOrder::where('customer_id', $this->id);
//        return $query->sum('refund');
//    }
}
