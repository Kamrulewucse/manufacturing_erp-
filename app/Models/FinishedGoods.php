<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishedGoods extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function journalVoucher()
    {
        return $this->hasOne(JournalVoucher::class,'finished_goods_id','id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function finishedGoodsRowMaterials()
    {
        return $this->hasMany(FinishedGoodsRowMaterial::class,'finished_goods_id','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
