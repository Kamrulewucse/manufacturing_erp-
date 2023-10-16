<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucherDetail extends Model
{
    protected $fillable = [];

    public function accountHead()
    {
        return $this->belongsTo(AccountHead::class);
    }
}
