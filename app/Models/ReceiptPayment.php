<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function vats()
    {
        return $this->hasMany(ReceiptPaymentDetail::class)
            ->where('vat_amount','>',0);
    }
    public function aits()
    {
        return $this->hasMany(ReceiptPaymentDetail::class)
            ->where('ait_amount','>',0);
    }
    public function paymentAccountHead()
    {
        return $this->belongsTo(AccountHead::class,'payment_account_head_id','id');
    }

    public function parentDeductionAccountHead()
    {
        return $this->belongsTo(AccountHead::class,'parent_deduction_account_head_id','id');
    }
    public function payeeDepositorAccountHead()
    {
        return $this->belongsTo(AccountHead::class,'payee_depositor_account_head_id','id');
    }
    public function taxSetion()
    {
        return $this->belongsTo(TaxSection::class,'tax_section_id','id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class,'project_id','id');
    }

    public function files()
    {
        return $this->hasMany(ReceiptPaymentFile::class);
    }


    public function aitAccountHead($id)
    {
        return TransactionLog::where('receipt_payment_id',$id)
            ->where('transaction_type',5)
            ->where('account_head_id',2741)->get();

    }

    public function accountHead()
    {
        return $this->belongsTo(AccountHead::class);
    }
    public function receiptPaymentDetail()
    {
        return $this->hasOne(ReceiptPaymentDetail::class)
            ->where('other_head',0);
    }

    public function receiptPaymentDetails()
    {
        return $this->hasMany(ReceiptPaymentDetail::class)
            ->where('other_head',0);
    }
    public function receiptPaymentOtherDetails()
    {
        return $this->hasMany(ReceiptPaymentDetail::class)
            ->where('other_head',1);
    }
}
