<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Appointment;
use App\Services\BillingService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $billings = Billing::with(['patient', 'appointment.service'])
            ->latest()
            ->get();

        return view('admin.billing.index', compact('billings'));
    }

    public function store(Request $request, BillingService $billingService)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $appointment = Appointment::with(['patient', 'service', 'services'])
            ->findOrFail($request->appointment_id);

        try {
            $result = $billingService->createFromAppointment(
                $appointment,
                $request->filled('amount') ? (float) $request->amount : null
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($result['existed']) {
            return back()->with('warning', 'Billing already exists for this appointment.');
        }

        return back()->with('success', 'Billing record created.');
    }

    public function markPaid(Request $request, int $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,gcash,bank_transfer',
        ]);

        $billing = Billing::findOrFail($id);

        $billing->update([
            'status' => 'paid',
            'payment_method' => $request->payment_method,
        ]);

        return back()->with('success', 'Marked as paid.');
    }

    public function destroy(int $id)
    {
        Billing::findOrFail($id)->delete();

        return back()->with('success', 'Billing record deleted.');
    }
}
