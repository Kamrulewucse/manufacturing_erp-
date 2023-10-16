<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $guarded = [];

    public function products() {
        return $this->hasMany(PurchaseOrderProduct::class,'purchase_order_id');
    }

    public function supplier() {
        return $this->belongsTo(Client::class);
    }
    public function brand() {
        return $this->belongsTo(Brand::class);
    }
    public function quantity(){
        return $this->hasMany(PurchaseOrderProduct::class)->sum('quantity');
    }
    public function payments() {
        return $this->hasMany(ReceiptPayment::class,'purchase_order_id','id');
    }

    public function getPaymentTypeAttribute() {
        return PurchasePayment::where('purchase_order_id', $this->id)->first();
    }

    public function productType() {
        return $this->belongsTo(ProductType::class,'product_type_id','id');
    }
    public function journalVoucher()
    {
        return $this->hasOne(JournalVoucher::class,'purchase_order_id','id');
    }
}
