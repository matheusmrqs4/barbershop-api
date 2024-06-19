<?php

use App\Http\Controllers\API\Auth\BarberShop\BarberShopController;
use App\Http\Controllers\API\Auth\BarberShop\BarberShopProfileController;
use App\Http\Controllers\API\Auth\User\UserController;
use App\Http\Controllers\API\Auth\User\UserProfileController;
use App\Http\Controllers\API\Entity\AppointmentController;
use App\Http\Controllers\API\Entity\BarberController;
use App\Http\Controllers\API\Entity\DashboardController;
use App\Http\Controllers\API\Entity\EvaluationController;
use App\Http\Controllers\API\Entity\ScheduleController;
use App\Http\Controllers\API\Entity\ServiceController;
use Illuminate\Support\Facades\Route;

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
    Route::middleware('user.validate.register')->post('register', [UserController::class, 'register']);
    Route::middleware('user.validate.login')->post('login', [UserController::class, 'login']);
    Route::middleware('auth:api')->post('refresh', [UserController::class, 'refresh']);
    Route::middleware('auth:api')->post('logout', [UserController::class, 'logout']);
    Route::middleware('auth:api')->get('appointments', [UserController::class, 'userAppointments']);
    Route::middleware('auth:api')->get('profile', [UserProfileController::class, 'profile']);
    Route::middleware('auth:api')->put('update-profile', [UserProfileController::class, 'updateProfile']);
});

Route::prefix('barber-shop')->group(function () {
    Route::middleware('barbershop.validate.register')->post('register', [BarberShopController::class, 'register']);
    Route::middleware('barbershop.validate.login')->post('login', [BarberShopController::class, 'login']);
    Route::middleware('auth:barber_shop')->post('refresh', [BarberShopController::class, 'refresh']);
    Route::middleware('auth:barber_shop')->post('logout', [BarberShopController::class, 'logout']);
    Route::middleware('auth:barber_shop')->get('profile', [BarberShopProfileController::class, 'profile']);
    Route::middleware('auth:barber_shop')->put('update-profile', [BarberShopProfileController::class, 'updateProfile']);
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

Route::middleware('auth:api')->post('appointment/{appointment}/evaluate', [EvaluationController::class, 'store']);
Route::middleware('auth:api')->get('evaluation/{evaluation}', [EvaluationController::class, 'show']);
