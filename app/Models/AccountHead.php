<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountHead extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function typeName()
    {
        return $this->belongsTo(AccountHeadType::class,'account_head_type_id','id');
    }
    public function subType()
    {
        return $this->belongsTo(AccountHeadSubType::class,'account_head_sub_type_id','id');
    }

}
