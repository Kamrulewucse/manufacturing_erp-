<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function client() {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function products() {
        return $this->hasMany(ServiceOrderDetails::class);
    }
    public function product() {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function payments() {
        return $this->hasMany(ReceiptPayment::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function journalVoucher()
    {
        return $this->hasOne(JournalVoucher::class,'service_order_id','id');
    }
}
