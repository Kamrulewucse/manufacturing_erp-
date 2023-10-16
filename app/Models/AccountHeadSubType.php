<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountHeadSubType extends Model
{
    protected $guarded = [];

    public function accountHeadType() {
        return $this->belongsTo(AccountHeadType::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
