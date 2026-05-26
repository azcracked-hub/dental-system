<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientDashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StaffDashboardController;
use App\Livewire\Admin\AppointmentsIndex as AdminAppointmentsIndex;
use App\Livewire\Admin\BillingIndex as AdminBillingIndex;
use App\Livewire\Admin\ClinicalNotesIndex as AdminClinicalNotesIndex;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\PatientShow as AdminPatientShow;
use App\Livewire\Admin\PatientsIndex as AdminPatientsIndex;
use App\Livewire\Admin\SystemAdminIndex as AdminSystemAdminIndex;
use App\Livewire\Patient\Dashboard as PatientDashboard;
use App\Livewire\Staff\Dashboard as StaffDashboard;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect('/login'));

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| Dashboard (MAIN ENTRY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = Auth::user();

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
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
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

    // Appointments
    Route::get('/appointments', AdminAppointmentsIndex::class)->name('appointments.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
    Route::post('/appointments/{id}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Clinical Notes
    Route::get('/clinical-notes', AdminClinicalNotesIndex::class)->name('clinical-notes.index');

    // Billing
    Route::get('/billing', AdminBillingIndex::class)->name('billing.index');
    Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
    Route::patch('/billing/{id}/mark-paid', [BillingController::class, 'markPaid'])->name('billing.markPaid');
    Route::delete('/billing/{id}', [BillingController::class, 'destroy'])->name('billing.destroy');

    // Patients
    Route::get('/patients', AdminPatientsIndex::class)->name('patients.index');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', AdminPatientShow::class)->name('patients.show');
    Route::patch('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');

    // System Admin
    Route::get('/system-admin', AdminSystemAdminIndex::class)->name('system-admin.index');

    // Profile
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {

    Route::get('/dashboard',                  PatientDashboard::class)->name('dashboard');
    Route::get('/appointments/book',          [PatientDashboardController::class, 'book'])->name('appointments.book');
    Route::post('/appointments',              [PatientDashboardController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{id}/cancel', [PatientDashboardController::class, 'cancel'])->name('appointments.cancel');
});

Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {

    // Dashboard UI
    Route::get('/dashboard', StaffDashboard::class)->name('dashboard');

    // Appointments
    Route::get('/appointments', [StaffDashboardController::class, 'appointments'])->name('appointments.index');
    Route::put('/appointments/{id}/cancel', [StaffDashboardController::class, 'cancelAppointment'])->name('appointments.cancel');

    // Patients
    Route::get('/patients', [StaffDashboardController::class, 'patients'])->name('patients.index');

    // Billing
    Route::get('/billing', [StaffDashboardController::class, 'billing'])->name('billing.index');
});
