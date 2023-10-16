<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
   protected $guarded = [];

    public function receiptPayment()
    {
        return $this->belongsTo(ReceiptPayment::class, 'receipt_payment_id');
    }

    public function accountHead()
    {
        return $this->belongsTo(AccountHead::class, 'account_head_type_id');
    }


}
