<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceTransfer extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $dates = ['date','source_cheque_date','target_bank_cheque_date'];



    public function sourceAccountHead()
    {
        return $this->belongsTo(AccountHead::class,'source_account_head_id','id');
    }
    public function targetAccountHead()
    {
        return $this->belongsTo(AccountHead::class,'target_account_head_id','id');
    }
    public function sourceCash()
    {
        return $this->belongsTo(Cash::class,'source_cash_id','id');
    }
    public function targetCash()
    {
        return $this->belongsTo(Cash::class,'target_cash_id','id');
    }
}
