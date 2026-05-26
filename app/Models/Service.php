<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_minutes'
    ];

    // optional: if you want reverse relation
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
