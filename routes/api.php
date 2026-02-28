<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentNotificationController;

Route::post('/appointments/notify-tomorrow', [AppointmentNotificationController::class, 'notifyTomorrowAppointments']);
