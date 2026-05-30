<?php

namespace App\Rules;

use App\Services\SchedulingService;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidBookableTime implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $doctorId = (int) ($this->data['doctor_id'] ?? 0);
        $date = $this->data['date'] ?? null;

        if (! $doctorId || ! $date || ! $value) {
            return;
        }

        $scheduling = app(SchedulingService::class);

        if (! $scheduling->isSlotAvailable($doctorId, (string) $date, (string) $value)) {
            $fail('The selected time is not available. Please choose another slot.');
        }
    }
}
