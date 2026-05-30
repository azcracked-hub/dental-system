<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicHoliday extends Model
{
    protected $fillable = ['date', 'name'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }
}
