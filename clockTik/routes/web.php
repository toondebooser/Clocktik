<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\UserController;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;



Route::get('/', [HomeController::class, 'show'])->name('home');
Route::get('/new-user', [UserController::class, 'newUser'])->name('newUser');
Route::post('/user-validation', [UserController::class, 'registrate'])->name('registrate');
Route::post('/login',[HomeController::class, 'login'])->name('login');
Route::get('/userPage', [DashboardController::class, 'currentUser']);