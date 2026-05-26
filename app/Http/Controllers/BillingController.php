<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Appointment;
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
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $existing = Billing::where('appointment_id', $request->appointment_id)->first();

        if ($existing) {
            return back()->with('success', 'Billing already exists for this appointment.');
        }

        $appointment = Appointment::with(['patient', 'service'])->findOrFail($request->appointment_id);

        Billing::create([
            'appointment_id' => $appointment->id,
            'patients_id'    => $appointment->patients_id,
            'amount'         => $request->amount ?? $appointment->service?->price ?? 0,
            'status'         => 'unpaid',
            'description'    => $appointment->service?->name ?? 'Appointment',
        ]);

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
