<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicalNote extends Model
{
    protected $fillable = [
        'appointment_id',
        'notes',
    ];

    // Clinical note belongs to appointment
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
