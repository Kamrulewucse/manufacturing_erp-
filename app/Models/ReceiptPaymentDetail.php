<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptPaymentDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function accountHead()
    {
        return $this->belongsTo(AccountHead::class);
    }
    public function parentDeductionAccountHead()
    {
        return $this->belongsTo(AccountHead::class,'parent_deduction_account_head_id','id');
    }
    public function vatAccountHead()
    {
        return $this->belongsTo(AccountHead::class,'vat_account_head_id','id');
    }
    public function aitAccountHead()
    {
        return $this->belongsTo(AccountHead::class,'ait_account_head_id','id');
    }
}
