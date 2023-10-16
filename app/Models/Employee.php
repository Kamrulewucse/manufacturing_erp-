<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $dates = ['dob', 'joining_date', 'confirmation_date'];

    public function department() {
        return $this->belongsTo(Department::class);
    }
    public function designation() {
        return $this->belongsTo(Designation::class);
    }
    public function designations() {
        return $this->belongsTo(Designation::class, 'department_id');
    }
    public function designationLogs() {
        return $this->hasMany(DesignationLog::class)->orderBy('date', 'desc')->orderBy('created_at', 'desc');
    }
    public function salaryChangeLog() {
        return $this->hasMany(SalaryChangeLog::class)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');
    }
    public function leaves() {
        return $this->hasMany(Leave::class);
    }
    public function section(){
        return $this->belongsTo(Section::class);
    }
    public function cardType(){
        return $this->belongsTo(CardType::class);
    }
    public function bloodGroup(){
        return $this->belongsTo(BloodGroup::class);
    }
    public function field(){
        return $this->belongsTo(Designation::class,'field_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'client_id');
    }
}
