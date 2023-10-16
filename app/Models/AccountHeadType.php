<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountHeadType extends Model
{
    protected $guarded = [];

    public function accountSubHeadType()
    {
        return $this->hasMany(AccountHeadSubType::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
