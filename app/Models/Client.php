<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function clients() {
        return $this->hasMany(AccountHead::class,'client_id');
    }
    public function suppliers() {
        return $this->hasMany(AccountHead::class,'client_id');
    }

    public function payments() {
        return $this->hasMany(PurchaseOrder::class,'client_id','id')
            ->with('purchaseOrder');
    }

    public function getPaid() {
        $total_paid = PurchaseOrder::where('supplier_id',$this->id)->sum('paid');
        return $total_paid;
    }

    public function getTotalAttribute() {
        //modified by Hasan
        $orderTotal = PurchaseOrder::where('supplier_id', $this->id)->sum('total');
        $opening_due = Client::where('id', $this->id)->first()->opening_due;
        return $orderTotal+$opening_due;
    }

    public function getPaidAttribute() {
        //modified by Hasan
        $orderPaidTotal = PurchaseOrder::where('supplier_id', $this->id)->sum('paid');
        $opening_paid = Client::where('id', $this->id)->first()->opening_paid;
        return $orderPaidTotal+$opening_paid;
    }

    public function getDueAttribute() {
        //modified by Hasan
        $orderPaidTotal = PurchaseOrder::where('supplier_id',$this->id)->sum('paid');
        $opening_paid = Client::where('id', $this->id)->first()->opening_paid;
        $total_paid = $orderPaidTotal+$opening_paid;
        $orderTotal = PurchaseOrder::where('supplier_id', $this->id)->sum('total');
        $opening_due = Client::where('id', $this->id)->first()->opening_due;
        $total_amount = $orderTotal+$opening_due;
        $total_due = $total_amount - $total_paid;
        //modified by Hasan

        return $total_due;
    }

    // Customer Part

    public function getTotalsAttribute() {
        //modified by Hasan
        $orderTotal = SalesOrder::where('client_id', $this->id)->sum('total');
        $opening_due = Client::where('id', $this->id)->first()->opening_due;
        return $orderTotal + $opening_due;
    }

    public function getPaidsAttribute() {
        //modified by Hasan
        $orderPaidTotal = SalesOrder::where('client_id', $this->id)->sum('paid');
        $opening_paid = Client::where('id', $this->id)->first()->opening_paid;
        return $orderPaidTotal+$opening_paid;
    }

    public function getDuesAttribute() {
        //modified by Hasan
        $orderPaidTotal = SalesOrder::where('client_id', $this->id)->sum('paid');
        $opening_paid = Client::where('id', $this->id)->first()->opening_paid;
        $total_paid = $orderPaidTotal+$opening_paid;
        $orderTotal = SalesOrder::where('client_id', $this->id)->sum('total');
        $opening_due = Client::where('id', $this->id)->first()->opening_due;
        $total_amount = $orderTotal + $opening_due;
        $total_due = $total_amount - $total_paid;

        return $total_due;
    }

}
