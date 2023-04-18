<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\UserController;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;



Route::get('/', [HomeController::class, 'show'])->name('login');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
Route::get('/new-user', [UserController::class, 'newUser'])->name('newUser');
Route::post('/user-validation', [UserController::class, 'registrate'])->name('registrate');
Route::post('/authentication',[HomeController::class, 'authentication'])->name('authentication');
Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard')->middleware('auth');
Route::get('/dashboard-start', [DashboardController::class, 'startWorking'])->name('start')->middleware('auth');
// Route::get('/dashboard-break', [DashboardController::class, 'break'])->name('break')->middleware('auth');
// Route::get('/dashboard-stop', [DashboardController::class, 'stop'])->name('stop')->middleware('auth');