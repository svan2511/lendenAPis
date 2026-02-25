<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::post('register',    [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);
Route::post('otp/verify',  [AuthController::class, 'verifyOtp']);


Route::middleware('auth:api')->group(function () {
    Route::post('/onboarding', [OnboardingController::class, 'store']);
    Route::get('/onboarding', [OnboardingController::class, 'show']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard-data', [DashboardController::class, 'getData']);
    Route::post('create-customer', [CustomerController::class, 'store']);
    Route::get('customers', [CustomerController::class, 'index']);
    Route::get('customers/{customer}', [CustomerController::class, 'show']);
    Route::post('create-product', [ProductController::class, 'store']);
    Route::get('products', [ProductController::class, 'index']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);
    Route::put('products/{product}', [ProductController::class, 'update']); 
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('/bills', [BillController::class, 'store']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'store']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/bills/{bill}', [BillController::class, 'markFullPaid']);
    Route::apiResource('expenses', ExpenseController::class);
    Route::get('expenses/summary/monthly', [ExpenseController::class, 'monthlySummary']);
    Route::get('reports/monthly-pl', [BillController::class, 'getProfitOrLoss']);
    
});

 Route::get('test-api', [TestController::class, 'dummyUsers']);