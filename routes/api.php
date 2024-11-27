<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\CompanyController;
use App\Http\Middleware\IsManager;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\isSuperAdmin;


Route::post('/auth/login', [AuthController::class, 'login']);
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/employee', [EmployeeController::class, 'allEmployees']);
    Route::get('/employee/{id}', [EmployeeController::class, 'detailEmployee']);
});

Route::group(['middleware' => ['auth:api', IsManager::class]], function () {
    Route::get('/manager', [ManagerController::class, 'allManagers']);
    Route::get('/manager/{id}', [ManagerController::class, 'detailManager']);

    Route::get('/profile', [ManagerController::class, 'profile']);
    Route::put('/profile', [ManagerController::class, 'updateProfile']);

    Route::post('/employee', [EmployeeController::class, 'createEmployee']);
    Route::put('/employee/{id}', [EmployeeController::class, 'updateEmployee']);
    Route::delete('/employee/{id}', [EmployeeController::class, 'deleteEmployee']);
});

Route::group(['middleware' => ['auth:api', isSuperAdmin::class]], function () {
    Route::post('/company', [CompanyController::class, 'createCompany']);
});