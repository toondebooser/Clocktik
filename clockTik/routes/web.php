<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyWorkersController;
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
Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard')->middleware('auth');
Route::get('/dashboard-start', [TimeclockController::class, 'startWorking'])->name('start')->middleware('auth');
Route::get('/dashboard-break', [TimeclockController::class, 'break'])->name('break')->middleware('auth');
Route::get('/dashboard-stop-break', [TimeclockController::class, 'stopBreak'])->name('stopBreak');
Route::get('/dashboard-stop', [TimeclockController::class, 'stop'])->name('stop')->middleware('auth');
Route::get('/my-profile', [UsersheetsController::class, 'myProfile'])->name('myProfile')->middleware('auth');
Route::post('/my-profile', [UsersheetsController::class, 'myProfile'])->name('getData')->middleware('auth');
Route::get('/make-timesheet', [TimesheetController::class, 'makeTimesheet']) -> name('makeTimesheet')->middleware('auth');
Route::get('/my-workers', [MyWorkersController::class, 'fetchworkers'])->name('myWorkers')->middleware('admin');
Route::post('/worker-profile', [WorkersController::class, 'workersProfile'])->name('workerProfile')->middleware('admin');