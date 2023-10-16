<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucher extends Model
{
    use HasFactory;

    protected $fillable = [];


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


    public function journalVoucherDebitDetails()
    {
        return $this->hasMany(JournalVoucherDetail::class)
            ->where('type',1);
    }
    public function journalVoucherCreditDetails()
    {
        return $this->hasMany(JournalVoucherDetail::class)
            ->where('type',2);
    }

    public function files()
    {
        return $this->hasMany(ReceiptPaymentFile::class)
            ->where('journal_voucher_id','!=','');
    }


    public function accountHead()
    {
        return $this->belongsTo(AccountHead::class);
    }
}
