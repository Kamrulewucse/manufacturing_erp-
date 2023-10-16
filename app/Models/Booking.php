<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Client::class,'customer_id');
    }
    public function technician()
    {
        return $this->belongsTo(User::class,'technician_id');
    }
    public function bookingDetails() {
        return $this->hasMany(BookingDetail::class,'booking_id');
    }
}
