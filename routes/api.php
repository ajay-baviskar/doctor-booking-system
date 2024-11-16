<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\API\Doctor\AppointmentController as DoctorAppointmentController;

// Authentication
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Authenticated Routes
Route::middleware(['auth.jwt'])->group(function () {
    // Patient Routes
    Route::prefix('patient')->group(function () {
        Route::post('appointments', [PatientAppointmentController::class, 'create']);
        Route::put('appointments/{id}', [PatientAppointmentController::class, 'updateStatus']);
        Route::get('appointments', [PatientAppointmentController::class, 'index']);
    });

    // Doctor Routes
    Route::prefix('doctor')->group(function () {
        Route::get('appointments', [DoctorAppointmentController::class, 'index']);
        Route::put('appointments/{id}', [DoctorAppointmentController::class, 'updateStatus']);
    });
});
