<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model {
    protected $fillable = ['name', 'email', 'phone', 'address'];

    public function appointments() {
        return $this->hasMany(Appointment::class, 'patients_id');
    }

    public function billings() {
        return $this->hasMany(Billing::class, 'patients_id');
    }
}
