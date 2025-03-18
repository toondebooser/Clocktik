<?php

use App\Http\Controllers\AddCustomTimesheetController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConfirmAction;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeleteTimesheetController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyListController;
use App\Http\Controllers\MyWorkersController;
use App\Http\Controllers\PdfExportController;
use App\Http\Controllers\SpecialsController;
use App\Http\Controllers\TimeclockController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\UpdateTimesheetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersheetsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

Auth::routes(['verify' => true]);

// Public Routes
Route::group([], function () {
    Route::get('/', [HomeController::class, 'show'])->name('home');
    Route::get('/login', [HomeController::class, 'login'])->name('login')->middleware('notSigned');
    Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
    Route::post('/authentication', [HomeController::class, 'authentication'])->name('authentication');
});

// User Registration Routes
Route::group([], function () {
    Route::get('/registration-form', [UserController::class, 'registrationForm'])->name('registration-form');
    Route::post('/user-registration', [UserController::class, 'registrate'])->name('registrate');
});

// Worker Routes
Route::middleware('worker')->group(function () {
    // Authenticated and Verified Routes
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::match(['get', 'post'], '/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard');
        Route::get('/my-profile', [UsersheetsController::class, 'myProfile'])->name('myProfile');
    });

    // Timeclock Routes with Confirmation
    Route::middleware(['auth', 'confirm.action'])->group(function () {
        Route::get('/dashboard-start', [TimeclockController::class, 'startWorking'])->name('start');
        Route::get('/dashboard-break', [TimeclockController::class, 'break'])->name('break');
        Route::get('/dashboard-stop-break', [TimeclockController::class, 'stopBreak'])->name('stopBreak');
        Route::get('/dashboard-stop', [TimeclockController::class, 'stop'])->name('stop');
    });
});

// Admin Routes
Route::middleware(['admin', 'auth'])->group(function () {
    Route::match(['get', 'post'], '/new-timesheet-form', [AddCustomTimesheetController::class, 'customTimesheetForm'])->name('timesheetForm');
    Route::match(['get', 'post'], '/add-new-timesheet', [TimesheetController::class, 'addNewTimesheet'])->name('newTimesheet');
    Route::get('/get-List/{type?}/{company_code?}', [MyListController::class, 'fetchList'])->name('myList');
    // Route::get('/forWorker', [SpecialsController::class, 'forWorker'])->name('forWorker');
    Route::match(['get', 'post'], '/update-timesheet/{id}/{timesheet}/{type?}', [UpdateTimesheetController::class, 'updateForm'])->name('update');
    Route::post('/update-worker-timesheet', [UpdateTimesheetController::class, 'updateTimesheet'])->name('updateTimesheet');
    Route::match(['get', 'post'], '/specials', [SpecialsController::class, 'specials'])->name('specials');
    Route::post('/setSpecial', [TimesheetController::class, 'setSpecial'])->name('setSpecial');
    Route::get('/export-pdf', [PdfExportController::class, 'exportPdf'])->name('exportPdf');
    Route::match(['get', 'post'], '/delete-timesheet/{workerId?}/{deleteSheet?}/{date?}', [DeleteTimesheetController::class, 'deleteTimesheet'])->name('delete');
});

// Company Routes
Route::middleware('god')->group( function () {
    Route::get('/add-company', [CompanyController::class, function() { return view('addCompany'); }])
        ->name('addCompany');
    Route::post('/registrate-company', [CompanyController::class, 'registrateCompany'])->name('registrateCompany');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/my-profile-post', [UsersheetsController::class, 'myProfile'])->name('getData');
    Route::get('/make-timesheet/{id}', [TimesheetController::class, 'makeTimesheet'])->name('makeTimesheet');
});

// Email Verification Routes
Route::group(['middleware' => 'auth'], function () {
    Route::get('/email/verify', function () {
        return view('verify-email');
    })->name('verification.notice');

    Route::post('/email/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'A new verification link has been sent to your email address.');
    })->middleware('throttle:6,1')->name('verification.resend');
});

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);
    if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }
    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }
    return redirect('/login')->with('verified', true);
})->middleware(['signed'])->name('verification.verify');

// Password Reset Routes
Route::middleware('guest')->group(function () {
    Route::view('/forgot-password', 'forgot-password')->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'updatePassword'])->name('password.update');
});

// Miscellaneous Routes
Route::post('/confirm-action', [ConfirmAction::class, 'confirmAction']);