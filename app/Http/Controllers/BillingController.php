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

        $appointment = Appointment::with(['patient', 'service'])->findOrFail($request->appointment_id);

        Billing::create([
            'appointment_id' => $appointment->id,
            'patients_id'    => $appointment->patients_id,
            'amount'         => $request->amount ?? $appointment->service->price,
            'status'         => 'unpaid',
            'description'    => $appointment->service->name,
        ]);

        return back()->with('success', 'Billing record created.');
    }

    public function markPaid(Request $request, int $id)
    {
        $billing = Billing::findOrFail($id);
        $billing->update([
            'status'         => 'paid',
            'payment_method' => $request->payment_method ?? 'cash',
        ]);

        return back()->with('success', 'Marked as paid.');
    }

    public function destroy(int $id)
    {
        Billing::findOrFail($id)->delete();
        return back()->with('success', 'Billing record deleted.');
    }
}
