<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'capacity', 'is_active'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
