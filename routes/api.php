<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/login', [AuthController::class, 'auth'])->name('auth_personal_token');

//Route::fallback(function (){
//    return response()->json([
//        'code' => 404,
//        'success' => false,
//        'data' => null,
//        'errors' => [
//            'message' => 'API endpoint not found'
//        ],
//    ], 404);
//});
