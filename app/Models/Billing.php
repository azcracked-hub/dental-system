<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model {
    protected $fillable = [
    'appointment_id',
    'patients_id',
    'amount',
    'status',
    'description',
    'due_date',
    'payment_method'
];

    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient::class, 'patients_id');
    }

    public function appointment()
    {
        return $this->belongsTo(\App\Models\Appointment::class);
    }
}
