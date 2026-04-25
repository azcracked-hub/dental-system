<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model {
    protected $fillable = ['patients_id', 'date', 'time', 'service', 'status', 'notes'];

    public function patient() {
        return $this->belongsTo(Patient::class, 'patients_id');
    }

    public function billing() {
        return $this->hasOne(Billing::class);
    }
}
