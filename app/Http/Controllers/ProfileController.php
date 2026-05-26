<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'          => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
        ]);

        $user->update([
            'name'           => trim($validated['first_name'].' '.$validated['last_name']),
            'email'          => $validated['email'],
            'phone'          => $validated['phone'],
            'license_number' => $validated['license_number'],
            'specialization' => $validated['specialization'],
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }
}
