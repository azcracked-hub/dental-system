<?php

namespace App\Livewire\Concerns;

trait WithAlerts
{
    protected function alertSuccess(string $message): void
    {
        session()->flash('success', $message);
        $this->dispatch('notify', type: 'success', message: $message);
    }

    protected function alertError(string $message): void
    {
        session()->flash('error', $message);
        $this->dispatch('notify', type: 'error', message: $message);
    }

    protected function alertWarning(string $message): void
    {
        session()->flash('warning', $message);
        $this->dispatch('notify', type: 'warning', message: $message);
    }
}
