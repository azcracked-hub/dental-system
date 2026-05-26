<?php

namespace App\Rules;

use App\Models\Appointment;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class NoDoubleBooking implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $doctorId = $this->data['doctor_id'] ?? null;
        $time = $this->data['time'] ?? null;

        if (! $doctorId || ! $time || ! $value) {
            return;
        }

        $normalizedTime = strlen($time) === 5 ? $time.':00' : $time;

        $exists = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('date', $value)
            ->where(function ($query) use ($time, $normalizedTime) {
                $query->where('time', $time)
                    ->orWhere('time', $normalizedTime)
                    ->orWhere('time', 'like', substr((string) $time, 0, 5).'%');
            })
            ->whereNotIn('status', ['canceled'])
            ->exists();

        if ($exists) {
            $fail('This doctor already has an appointment at the selected date and time.');
        }
    }
}
