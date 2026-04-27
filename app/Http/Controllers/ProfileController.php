<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ProfileController extends Controller
{
    public function update(Request $request)
{
    $user = Auth::user();
    $name = trim($request->first_name . ' ' . $request->last_name);

    $user->update([
        'name'           => $name,
        'email'          => $request->email,
        'phone'          => $request->phone,
        'license_number' => $request->license_number,
        'specialization' => $request->specialization,
    ]);

    return back()->with('success', 'Profile updated successfully.');
}
}
