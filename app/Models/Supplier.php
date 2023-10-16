<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $gurded = [];

    public function getDueAttribute() {
        return PurchaseOrder::where('supplier_id', $this->id)->sum('due');
    }

    public function getPaidAttribute() {
        return PurchaseOrder::where('supplier_id', $this->id)->sum('paid');
    }

    public function getTotalAttribute() {
        return PurchaseOrder::where('supplier_id', $this->id)->sum('total');
    }
}
