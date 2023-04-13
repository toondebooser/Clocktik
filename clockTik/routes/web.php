<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;



Route::get('/', [HomeController::class, 'show'])->name('home');
Route::get('/new-user', [UserController::class, 'newUser'])->name('newUser');