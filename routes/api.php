<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\EmployeeController;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::controller(EmployeeController::class)->group(function () {
    Route::get('employees', 'index');
    Route::post('employee', 'store');
    Route::get('employee/{id}', 'show');
    Route::put('employee/{id}', 'update');
    Route::delete('employee/{id}', 'destroy');
}); 
