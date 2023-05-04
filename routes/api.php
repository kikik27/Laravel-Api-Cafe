<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\DashboardController;

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


Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => ['jwt.verify']], function () {

    Route::post('/me', [UserController::class, 'getAuthenticatedUser']);
    Route::post('/ubah/profile', [UserController::class, 'UbahProfile']);

    Route::get('/meja/get', [MejaController::class, 'get']);
    Route::get('/meja/semua', [MejaController::class, 'semua']);
    Route::get('/meja/terpakai', [MejaController::class, 'terpakai']);
    Route::get('/meja/tersedia', [MejaController::class, 'tersedia']);

    Route::get('/menu/get', [MenuController::class, 'get']);
    Route::get('/menu/semua', [MenuController::class, 'semua']);
    Route::get('/menu/id_menu/{id_menu}', [MenuController::class, 'id']);
    Route::get('/menu/makanan', [MenuController::class, 'makanan']);
    Route::get('/menu/minuman', [MenuController::class, 'minuman']);
    Route::get('/menu', [MenuController::class, 'get']);

    Route::get('/meja/tersedia', [MejaController::class, 'tersedia']);

    Route::get('/dashboard/CountAll', [DashboardController::class, 'CountAll']);
    Route::get('/dashboard/MenuTerlaris', [DashboardController::class, 'MenuTerlaris']);
    Route::get('/dashboard/get/pendapatan', [DashboardController::class, 'getTotalPendapatanPerBulan']);

    Route::get('/pegawai/role/kasir', [UserController::class, 'kasir']);

    Route::get('/transaksi/get/detail/{id}', [TransaksiController::class, 'getDetail']);
});

Route::group(['middleware' => ['jwt.verify', 'Role:admin']], function () {

    Route::post('/menu/add', [MenuController::class, 'add']);
    Route::delete('/menu/delete/{id_menu}', [MenuController::class, 'delete']);
    Route::post('/menu/update/{id_menu}', [MenuController::class, 'update']);

    Route::get('/pegawai/get', [UserController::class, 'get']);
    Route::get('/pegawai/semua', [UserController::class, 'semua']);
    Route::get('/pegawai/role/manager', [UserController::class, 'manager']);
    Route::post('/pegawai/register', [UserController::class, 'register']);
    Route::post('/pegawai/update/{id}', [UserController::class, 'update']);
    Route::delete('/pegawai/delete/{id}', [UserController::class, 'delete']);

    Route::post('/meja/add', [MejaController::class, 'add']);
    Route::put('/meja/pakai/{id_meja}', [MejaController::class, 'pakai']);
    Route::put('/meja/selesai/{id_meja}', [MejaController::class, 'selesai']);

});


Route::group(['middleware' => ['jwt.verify', 'Role:kasir']], function () {

    Route::post('/transaksi/baru', [TransaksiController::class, 'transaksi']);
    Route::post('/transaksi/detail', [TransaksiController::class, 'detail']);
    Route::post('/transaksi/bayar/{id}', [TransaksiController::class, 'bayar']);
    Route::get('/transaksi/get/bill', [TransaksiController::class, 'bill']);

});

Route::group(['middleware' => ['jwt.verify', 'Role:manager']], function () {

        Route::get('/transaksi/get', [TransaksiController::class, 'Get']);
        
});

