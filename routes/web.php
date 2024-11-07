<?php

use App\Http\Controllers\AddCustomTimesheetController;
use App\Http\Controllers\ConfirmAction;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeleteTimesheetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyWorkersController;
use App\Http\Controllers\PdfExportController;
use App\Http\Controllers\SpecialsController;
use App\Http\Controllers\TimeclockController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\UpdateTimesheetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersheetsController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Auth::routes(['verify' => true]);
Route::get('/', [HomeController::class, 'show'])->name('home');
Route::get('/login', [HomeController::class, 'login'])->name('login')->middleware('notSigned');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
Route::get('/registration-form', [UserController::class, 'registrationForm'])->name('registration-form');
Route::post('/user-registration', [UserController::class, 'registrate'])->name('registrate');
Route::post('/authentication', [HomeController::class, 'authentication'])->name('authentication');
Route::match(['get', 'post'], '/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard')->middleware('worker','verified');
Route::get('/dashboard-start', [TimeclockController::class, 'startWorking'])->name('start')->middleware('worker', 'auth', 'confirm.action');
Route::get('/dashboard-break', [TimeclockController::class, 'break'])->name('break')->middleware('worker', 'auth', 'confirm.action');
Route::get('/dashboard-stop-break', [TimeclockController::class, 'stopBreak'])->name('stopBreak')->middleware('worker', 'confirm.action');
Route::get('/dashboard-stop', [TimeclockController::class, 'stop'])->name('stop')->middleware('worker', 'confirm.action');
Route::get('/my-profile', [UsersheetsController::class, 'myProfile'])->name('myProfile')->middleware('worker', 'auth','verified');
Route::match(['get', 'post'], '/my-profile-post', [UsersheetsController::class, 'myProfile'])->name('getData')->middleware('auth');
Route::get('/make-timesheet/{id}', [TimesheetController::class, 'makeTimesheet'])->name('makeTimesheet')->middleware('auth');
Route::match(['get', 'post'], '/new-timesheet-form', [AddCustomTimesheetController::class, 'customTimesheetForm'])->name('timesheetForm')->middleware('admin', 'auth');
Route::match(['get', 'post'], '/add-new-timesheet', [TimesheetController::class, 'addNewTimesheet'])->name('newTimesheet')->middleware('admin', 'auth');
Route::get('/my-workers', [MyWorkersController::class, 'fetchWorkers'])->name('myWorkers')->middleware('admin', 'auth');
Route::get('/forWorker', [SpecialsController::class, 'forWorker'])->name('forWorker')->middleware('admin', 'auth');
Route::match(['get', 'post'], '/update-timesheet/{id}/{timesheet}', [UpdateTimesheetController::class, 'updateForm'])->name('update')->middleware('admin', 'auth');
Route::post('/update-worker-timesheet', [UpdateTimesheetController::class, 'updateTimesheet'])->name('updateTimesheet')->middleware('admin', 'auth');
Route::match(['get', 'post'], '/specials', [SpecialsController::class, 'specials'])->name('specials')->middleware('admin', 'auth');
Route::post('/setSpecial', [TimesheetController::class, 'setSpecial'])->name('setSpecial')->middleware('admin', 'auth');
Route::get('/export-pdf', [PdfExportController::class, 'exportPdf'])->name('exportPdf')->middleware('admin', 'auth');
Route::post('/delete-timesheet', [DeleteTimesheetController::class, 'deleteTimesheet'])->name('delete')->middleware('admin');
Route::post('/confirm-action', [ConfirmAction::class, 'confirmAction']);

Route::get('/email/verify', function () {
    return view('verify-email');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
 
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'A new verification link has been sent to your email address.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');
