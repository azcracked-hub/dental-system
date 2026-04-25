<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model {
    protected $fillable = ['appointment_id', 'patients_id', 'amount', 'status', 'description', 'due_date'];

    public function patient() {
        return $this->belongsTo(Patient::class, 'patients_id');
    }

    public function appointment() {
        return $this->belongsTo(Appointment::class);
    }
}
