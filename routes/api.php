<?php

use App\Http\Controllers\AlatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\PemakaianLabController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum']], function () {
    Route::get('user', [AuthController::class, 'indexUser']);
    Route::post('status/{nipOrUsername}', [AuthController::class, 'changeStatusUser']);
    Route::post('newFCM', [AuthController::class, 'makeFCM']);
    Route::get('fcm/{idUser}', [AuthController::class, 'getUserToken']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('userStatus', [AuthController::class, 'getUserStatus']);
});

// Route::group(['prefix' => 'auth', 'middleware' => ['auth:sanctum']], function () {
//     Route::post('logout', [AuthController::class, 'logout']);
//     Route::get('user', [AuthController::class, 'getUser']);
//     Route::get('user/{id}', [AuthController::class, 'getUserById']);
//     Route::get('pp/{filename}', [AuthController::class, 'getPP']);
//     Route::post('profile', [AuthController::class, 'update']);
// });

Route::group(['prefix' => 'lab', 'middleware' => ['auth:sanctum']], function () {
    Route::get('index', [LabController::class, 'index']);
    Route::post('add', [LabController::class, 'store']);
    Route::post('edit/{id}', [LabController::class, 'update']);
    Route::delete('delete/{id}', [LabController::class, 'destroy']);
});

Route::group(['prefix' => 'jadwal', 'middleware' => ['auth:sanctum']], function () {
    Route::get('index', [JadwalController::class, 'index']);
    Route::get('all', [JadwalController::class, 'indexAll']);
    Route::post('add', [JadwalController::class, 'store']);
    Route::post('edit/{id}', [JadwalController::class, 'update']);
    Route::delete('delete/{id}', [JadwalController::class, 'destroy']);
});

Route::group(['prefix' => 'pemakaian', 'middleware' => ['auth:sanctum']], function () {
    Route::get('index', [PemakaianLabController::class, 'index']);
    Route::get('indexByLab/{id}', [PemakaianLabController::class, 'indexByLab']);
    Route::get('indexByUser', [PemakaianLabController::class, 'indexByUser']);
    Route::get('show/{id}', [PemakaianLabController::class, 'show']);
    Route::post('status/{id}', [PemakaianLabController::class, 'changeStatus']);
    Route::post('add', [PemakaianLabController::class, 'store']);
    Route::post('edit/{id}', [PemakaianLabController::class, 'update']);
    Route::delete('delete/{id}', [PemakaianLabController::class, 'destroy']);
});

Route::group(['prefix' => 'alat', 'middleware' => ['auth:sanctum']], function () {
    Route::get('index/{idLab}', [AlatController::class, 'index']);
    Route::post('add', [AlatController::class, 'store']);
    Route::post('edit/{id}', [AlatController::class, 'update']);
    Route::delete('delete/{id}', [AlatController::class, 'destroy']);
});

Route::group(['prefix' => 'peminjaman', 'middleware' => ['auth:sanctum']], function () {
    Route::get('index', [PeminjamanController::class, 'index']);
    Route::get('indexByLab/{plp}', [PeminjamanController::class, 'indexByLab']);
    Route::get('show/{id}', [PeminjamanController::class, 'show']);
    Route::get('user', [PeminjamanController::class, 'indexByUser']);
    Route::get('detail', [PeminjamanController::class, 'indexDetail']);
    Route::get('history', [PeminjamanController::class, 'historyByUser']);
    Route::post('add', [PeminjamanController::class, 'store']);
    Route::post('status/{id}', [PeminjamanController::class, 'changeStatus']);
});