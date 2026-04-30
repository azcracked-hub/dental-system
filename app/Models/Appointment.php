<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model {
    protected $fillable = [
    'patients_id',
    'doctor_id',
    'service_id',
    'date',
    'time',
    'status',
    'notes'
];

    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient::class, 'patients_id');
    }

    public function billing() {
        return $this->hasOne(Billing::class);
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
