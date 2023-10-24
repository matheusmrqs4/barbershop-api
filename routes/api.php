<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\User\UserController;
use App\Http\Controllers\Auth\User\UserRegisterController;
use App\Http\Controllers\Auth\BarberShop\BarberShopController;
use App\Http\Controllers\Auth\BarberShop\BarberShopRegisterController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('user')->group(function () {
    Route::middleware('user.validate.register')->post('register', [UserRegisterController::class, 'register']);
    Route::middleware('user.validate.login')->post('login', [UserController::class, 'login']);
    Route::middleware('auth:api')->get('me', [UserController::class, 'me']);
    Route::middleware('auth:api')->post('refresh', [UserController::class, 'refresh']);
    Route::middleware('auth:api')->post('logout', [UserController::class, 'logout']);
    Route::middleware('auth:api')->get('appointments', [UserController::class, 'userAppointments']);
});

Route::prefix('barber-shop')->group(function () {
    Route::middleware('barbershop.validate.register')->post('register', [BarberShopRegisterController::class, 'register']);
    Route::middleware('barbershop.validate.login')->post('login', [BarberShopController::class, 'login']);
    Route::middleware('auth:barber_shop')->get('me', [BarberShopController::class, 'me']);
    Route::middleware('auth:barber_shop')->post('refresh', [BarberShopController::class, 'refresh']);
    Route::middleware('auth:barber_shop')->post('logout', [BarberShopController::class, 'logout']);
    Route::middleware('auth:barber_shop')->get('dashboard', [DashboardController::class, 'index']);
});

Route::middleware('auth:barber_shop')->apiResource('barber', BarberController::class)->except(['index', 'show']);
Route::get('barber', [BarberController::class, 'index']);
Route::get('barber/{barber}', [BarberController::class, 'show']);

Route::middleware('auth:barber_shop')->apiResource('service', ServiceController::class)->except(['index', 'show']);
Route::get('service', [ServiceController::class, 'index']);
Route::get('service/{service}', [ServiceController::class, 'show']);

Route::middleware('auth:barber_shop')->apiResource('schedule', ScheduleController::class)->except(['index', 'show']);
Route::get('schedule', [ScheduleController::class, 'index']);
Route::get('schedule/{schedule}', [ScheduleController::class, 'show']);

Route::middleware('auth:api')->post('create-appointment', [AppointmentController::class, 'createAppointment']);
Route::middleware('auth:api')->get('show-appointment/{appointment}', [AppointmentController::class, 'showAppointment']);
