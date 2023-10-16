<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SalesOrder extends Model
{
   protected $guarded =[];

    public function journalVoucher()
    {
        return $this->hasOne(JournalVoucher::class,'sales_order_id','id');
    }

    public function products() {
        return $this->hasMany(SalesOrderProduct::class);
    }

    public function payments() {
        return $this->hasMany(ReceiptPayment::class);
    }
    public function client() {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
