<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\WithAlerts;
use App\Models\Billing;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class BillingIndex extends Component
{
    use WithAlerts;
    use WithPagination;

    public bool $showMarkPaidModal = false;
    public ?int $markPaidBillingId = null;
    public string $payment_method = 'cash';

    public function openMarkPaidModal(int $billingId): void
    {
        $this->resetValidation();
        $this->markPaidBillingId = $billingId;
        $this->payment_method = 'cash';
        $this->showMarkPaidModal = true;
    }

    public function closeMarkPaidModal(): void
    {
        $this->showMarkPaidModal = false;
        $this->reset(['markPaidBillingId', 'payment_method']);
        $this->payment_method = 'cash';
    }

    public function markPaid(): void
    {
        $validated = $this->validate([
            'markPaidBillingId' => 'required|exists:billings,id',
            'payment_method' => 'required|in:cash,gcash,bank_transfer',
        ]);

        $billing = Billing::findOrFail($validated['markPaidBillingId']);
        $billing->update([
            'status' => 'paid',
            'payment_method' => $validated['payment_method'],
        ]);

        $this->closeMarkPaidModal();
        $this->alertSuccess('Marked as paid.');
    }

    public function deleteBilling(int $id): void
    {
        Billing::findOrFail($id)->delete();
        $this->alertSuccess('Billing record deleted.');
        $this->resetPage();
    }

    public function render()
    {
        $billings = Billing::with(['patient', 'appointment.service'])
            ->latest()
            ->paginate(15);

        return view('livewire.admin.billing-index', [
            'billings' => $billings,
            'totalCollected' => Billing::where('status', 'paid')->sum('amount'),
            'pendingPayment' => Billing::where('status', 'unpaid')->sum('amount'),
        ]);
    }
}
