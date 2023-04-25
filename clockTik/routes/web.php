<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\UserController;
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
Route::get('/dashboard-start', [DashboardController::class, 'startWorking'])->name('start')->middleware('auth');
Route::get('/dashboard-break', [DashboardController::class, 'break'])->name('break')->middleware('auth');
Route::get('/dashboard-stop-break', [DashboardController::class, 'stopBreak'])->name('stopBreak');
Route::get('/dashboard-stop', [DashboardController::class, 'stop'])->name('stop')->middleware('auth');
Route::get('/my-profile', [DashboardController::class, 'myProfile'])->name('myProfile')->middleware('auth');
Route::post('/my-profile', [DashboardController::class, 'myProfile'])->name('postDate')->middleware(('auth'));
