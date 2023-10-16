<?php

namespace App\Models;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function getProjectExpenseAttribute(){
        $projectExpense = ReceiptPayment::where('project_id',$this->id)
            ->where('transaction_type',2)
            ->sum('amount');
        return $projectExpense;
    }

    public function getProjectIncomeAttribute(){
        $projectIncome = ReceiptPayment::where('project_id',$this->id)
            ->where('transaction_type',1)
            ->sum('amount');
        return $projectIncome;
    }
}
