<?php

namespace App\Rules;

use App\Services\SchedulingService;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidBookableDate implements DataAwareRule, ValidationRule
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
        if (! $doctorId || ! $value) {
            return;
        }

        $scheduling = app(SchedulingService::class);
        $reason = $scheduling->getDayBlockReason($doctorId, (string) $value);

        if ($reason) {
            $fail($reason);
        }
    }
}
