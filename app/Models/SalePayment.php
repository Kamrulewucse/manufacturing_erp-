<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    protected $guarded = [];

    public function salesOrder() {
        return $this->belongsTo(SalesOrder::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function transactionLog(){
        return $this->hasOne(TransactionLog::class,'sale_payment_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function bank() {
        return $this->belongsTo(Bank::class,'bank_id','id');
    }

    public function account() {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'id');
    }

    public function mobileBank() {
        return $this->belongsTo(MobileBanking::class);
    }
}
