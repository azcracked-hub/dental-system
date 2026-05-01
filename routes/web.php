<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClinicalNoteController;
use App\Http\Controllers\SystemAdminController;
use App\Http\Controllers\PatientDashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StaffDashboardController;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect('/login'));

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard (MAIN ENTRY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->get('/dashboard', function () {

    if (!session()->has('login_web_' . Auth::id())) {
        Auth::logout();
        return redirect('/login');
    }

    $user = Auth::user();

    return match ($user->role) {
        'admin', 'doctor' => redirect()->route('admin.dashboard'),
        'staff' => redirect()->route('staff.dashboard'),
        'patient' => redirect()->route('patient.dashboard'),
        default => redirect('/login'),
    };
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Appointments
    Route::get('/appointments',                [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments',               [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{id}/status',  [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
    Route::post('/appointments/{id}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::delete('/appointments/{id}',        [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Clinical Notes
    Route::get('/clinical-notes', [ClinicalNoteController::class, 'index'])->name('clinical-notes.index');

    // Billing
    Route::get('/billing',                  [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing',                 [BillingController::class, 'store'])->name('billing.store');
    Route::patch('/billing/{id}/mark-paid', [BillingController::class, 'markPaid'])->name('billing.markPaid');
    Route::delete('/billing/{id}',          [BillingController::class, 'destroy'])->name('billing.destroy');

    // Patients
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::patch('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');

    // System Admin
    Route::get('/system-admin', [SystemAdminController::class, 'index'])->name('system-admin.index');

    // Profile
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {

    Route::get('/dashboard',                  [PatientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments/book',          [PatientDashboardController::class, 'book'])->name('appointments.book');
    Route::post('/appointments',              [PatientDashboardController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{id}/cancel', [PatientDashboardController::class, 'cancel'])->name('appointments.cancel');
});

Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {

    // Dashboard UI
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    // Appointments
    Route::get('/appointments', [StaffDashboardController::class, 'appointments'])->name('appointments.index');
    Route::put('/appointments/{id}/cancel', [StaffDashboardController::class, 'cancelAppointment'])->name('appointments.cancel');

    // Patients
    Route::get('/patients', [StaffDashboardController::class, 'patients'])->name('patients.index');

    // Billing
    Route::get('/billing', [StaffDashboardController::class, 'billing'])->name('billing.index');
});
