<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleProductReturnOrder extends Model
{
    protected $guarded =[];

    public function journalVoucher()
    {
        return $this->hasOne(JournalVoucher::class,'sale_product_return_order_id','id');
    }

    public function products() {
        return $this->hasMany(InventoryLog::class,'sale_product_return_order_id','id');
    }

    public function payments() {
        return $this->hasMany(ReceiptPayment::class,'sale_product_return_order_id','id');
    }
    public function client() {
        return $this->belongsTo(Client::class,'customer_id','id');
    }
}
