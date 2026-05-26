<?php

namespace App\Models;

use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'patients_id',
        'doctor_id',
        'service_id',
        'date',
        'time',
        'status',
        'notes',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patients_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /** Primary / legacy single service column */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'appointment_service')->withTimestamps();
    }

    public function billing(): HasOne
    {
        return $this->hasOne(Billing::class);
    }

    public function serviceNames(): string
    {
        if ($this->relationLoaded('services') && $this->services->isNotEmpty()) {
            return $this->services->pluck('name')->join(', ');
        }

        return $this->service?->name ?? 'No Service';
    }

    public function totalServicePrice(): float
    {
        if ($this->relationLoaded('services') && $this->services->isNotEmpty()) {
            return (float) $this->services->sum('price');
        }

        return (float) ($this->service?->price ?? 0);
    }

    public function totalServiceDuration(): int
    {
        if ($this->relationLoaded('services') && $this->services->isNotEmpty()) {
            return (int) $this->services->sum('duration_minutes');
        }

        return (int) ($this->service?->duration_minutes ?? 0);
    }
}
