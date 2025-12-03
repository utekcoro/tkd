<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApprovalStockController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FakturController;
use App\Http\Controllers\PackingListController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\UserController;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/login-proses', [AuthController::class, 'login_proses'])->name('login-proses');

Route::middleware(['auth'])->group(function () {
    // Route untuk memilih toko - bisa diakses oleh semua user yang login
    Route::get('/branch/select', [BranchController::class, 'select'])->name('branch.select');
    Route::post('/branch/choose', [BranchController::class, 'choose'])->name('branch.choose');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Route yang hanya bisa diakses oleh admin
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
        Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::post('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');

        // Route untuk branch management - hanya untuk admin
        Route::post('/branch', [BranchController::class, 'store'])->name('branch.store');
        Route::put('/branch/{branch}', [BranchController::class, 'update'])->name('branch.update');
        Route::delete('/branch/{branch}', [BranchController::class, 'destroy'])->name('branch.destroy');
    });

    Route::middleware(['role:toko,super_admin'])->group(function () {
        Route::resource('barcode', BarcodeController::class);
        Route::post('/barcode/update-from-csv', [BarcodeController::class, 'updateFromCSV'])->name('barcode.updateFromCSV');

        Route::get('/approval-stock', [ApprovalStockController::class, 'index'])->name('approval_stock.index');
        Route::get('/approval-stock/update', [ApprovalStockController::class, 'updateFromBarcodes'])->name('approval-stock.update');

        Route::resource('barang-masuk', BarangMasukController::class);
        Route::get('/barang-masuk/detail/{id}', [BarangMasukController::class, 'show'])->name('barang_masuk_detail');
    });

    Route::get('/profile', [UserController::class, 'editProfile'])->name('user.profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');

    Route::resource('packing-list', PackingListController::class);
    Route::get('/packing-list/detail/{id}', [PackingListController::class, 'show'])->name('packing_list_detail');

    Route::get('/faktur', [FakturController::class, 'index'])->name('faktur.index');
    Route::get('/faktur/{no_billing}', [FakturController::class, 'show'])->name('faktur.show');

    Route::get('/surat-jalan', [SuratJalanController::class, 'index'])->name('surat_jalan.index');
    Route::get('/surat-jalan/{no_billing}', [SuratJalanController::class, 'show'])->name('surat_jalan.show');
});
