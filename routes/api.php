<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\RegisterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group whichS
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:api')->get('user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::get('/category', [CategoryController::class, 'index']);
Route::get('/category/{slug}', [CategoryController::class, 'show']);
Route::get('/categoryHome', [CategoryController::class, 'categoryHome']);

Route::get('/campaign', [CampaignController::class, 'index']);
Route::get('/campaign/{slug}', [CampaignController::class, 'show']);

Route::get('/profile', [ProfileController::class, 'index'])->middleware('auth:api');
Route::post('/profile', [ProfileController::class, 'update'])->middleware('auth:api');
Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->middleware('auth:api');

Route::get('/slider', [SliderController::class, 'index']);

/**
 * Api Donation
 */
Route::get('/donation', [DonationPayController::class, 'index'])->middleware('auth:api');
Route::post('/donation', [DonationPayController::class, 'store'])->middleware('auth:api');

Route::post('/donation/notification', [DonationPayController::class, 'notificationHandler']);

// Forgot password
Route::post('donatur/forgot-password', [ForgotPasswordController::class, 'sendResetPasswordEmail']);

// Reset password
// Route::post('donatur/reset-password/{token}', [DonaturController::class, 'resetPassword']);
Route::post('donatur/reset-password/{token}', [ForgotPasswordController::class, 'resetPassword'])->withoutMiddleware(['auth:api']);

