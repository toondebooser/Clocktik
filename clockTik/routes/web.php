<?php

use App\Http\Controllers\AddCustomTimesheetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyWorkersController;
use App\Http\Controllers\SpecialsController;
use App\Http\Controllers\TimeclockController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersheetsController;
use App\Http\Controllers\WorkersController;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', [HomeController::class, 'show'])->name('home');
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
Route::get('/registration-form', [UserController::class, 'registrationForm'])->name('registration-form');
Route::post('/user-registration', [UserController::class, 'registrate'])->name('registrate');
Route::post('/authentication', [HomeController::class, 'authentication'])->name('authentication');
Route::match(['get', 'post'],'/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard')->middleware('worker');
Route::get('/dashboard-start', [TimeclockController::class, 'startWorking'])->name('start')->middleware('worker');
Route::get('/dashboard-break', [TimeclockController::class, 'break'])->name('break')->middleware('worker');
Route::get('/dashboard-stop-break', [TimeclockController::class, 'stopBreak'])->name('stopBreak')->middleware('worker');
Route::get('/dashboard-stop', [TimeclockController::class, 'stop'])->name('stop')->middleware('worker');
Route::get('/my-profile', [UsersheetsController::class, 'myProfile'])->name('myProfile')->middleware('worker');
Route::post('/my-profile-post', [UsersheetsController::class, 'myProfile'])->name('getData')->middleware('auth');
Route::get('/make-timesheet/{id}', [TimesheetController::class, 'makeTimesheet'])->name('makeTimesheet')->middleware('auth');
Route::post('/new-timesheet-form', [AddCustomTimesheetController::class, 'customTimesheetForm'])->name('timesheetForm')->middleware('admin');
Route::post('/add-new-timesheet', [TimesheetController::class, 'addNewTimesheet'])->name('newTimesheet')->middleware('admin');
Route::get('/my-workers', [MyWorkersController::class, 'fetchworkers'])->name('myWorkers')->middleware('admin');
Route::get('/forWorker', [SpecialsController::class, 'forWorker'])->name('forWorker')->middleware('admin');
Route::match(['get', 'post'], '/specials', [SpecialsController::class, 'specials'])->name('specials')->middleware('admin');
Route::post('/setSpecial', [TimesheetController::class, 'setSpecial'])->name('setSpecial')->middleware('admin');