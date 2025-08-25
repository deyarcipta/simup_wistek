<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminKelolaUserController;
use App\Http\Controllers\Admin\AdminProdukJasaController;
use App\Http\Controllers\Admin\AdminStokBarangController;
use App\Http\Controllers\Admin\AdminTransaksiController;
use App\Http\Controllers\Admin\AdminGajiKaryawanController;
use App\Http\Controllers\Admin\AdminPengeluaranLainController;
use App\Http\Controllers\Admin\AdminLaporanController;
use App\Http\Controllers\Admin\AdminPiutangController;
use App\Http\Controllers\Admin\AdminPengaturanController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminKelolaMemberController;
use App\Http\Controllers\Admin\AdminPencairanSaldoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProdukJasaController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\Member\MemberProfileController;
use App\Models\StokBarang;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Profile
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::post('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');

        // Kelola User
        Route::resource('kelola-user', AdminKelolaUserController::class)->except(['show']);

        // Produk Jasa
        Route::get('produk-jasa/data/{id}', [AdminProdukJasaController::class, 'getData'])->name('produk-jasa.data');
        Route::resource('produk-jasa', AdminProdukJasaController::class)
        ->names('admin.produk-jasa')
        ->except(['show']);

        // Stok Barang
        Route::get('stok-barang/data/{id}', [AdminStokBarangController::class, 'getData']);
        Route::resource('stok-barang', AdminStokBarangController::class)->except(['show']);
        Route::get('/get-stok-barang/{id}', function ($id) {
            return response()->json(StokBarang::find($id));
        });

        // Transaksi
        Route::get('transaksi', [AdminTransaksiController::class, 'index'])->name('transaksi.index');
        Route::post('transaksi', [AdminTransaksiController::class, 'store'])->name('transaksi.store');
        Route::delete('transaksi/{id}', [AdminTransaksiController::class, 'destroy'])->name('transaksi.destroy');
        Route::get('transaksi/download', [AdminTransaksiController::class, 'download'])->name('transaksi.download');

        Route::get('rekap-transaksi', [AdminTransaksiController::class, 'rekap'])->name('transaksi.rekap');
        Route::get('transaksi/rekap/download', [AdminTransaksiController::class, 'downloadRekap'])->name('transaksi.rekap.download');

        // Gaji Karyawan & Pengeluaran Lain
        Route::get('gaji-karyawan/data/{id}', [AdminGajiKaryawanController::class, 'getData']);
        Route::resource('gaji-karyawan', AdminGajiKaryawanController::class);
        Route::get('pengeluaran-lain/data/{id}', [AdminPengeluaranLainController::class, 'getData']);
        Route::resource('pengeluaran-lain', AdminPengeluaranLainController::class);

        // Laporan
        Route::get('laporan/buku-besar', [AdminLaporanController::class, 'bukuBesar'])->name('laporan.buku-besar');
        Route::get('laporan/download', [AdminLaporanController::class, 'downloadBukuBesar'])->name('laporan.buku-besar.download');

        Route::get('laporan/shu', [AdminLaporanController::class, 'sisaHasilUsaha'])->name('laporan.shu');
        Route::get('laporan/shu/download', [AdminLaporanController::class, 'downloadSisaHasilUsaha'])->name('laporan.shu.download');

        Route::get('/laporan/piutang', [AdminPiutangController::class, 'index'])->name('laporan.piutang');
        Route::post('/laporan/piutang', [AdminPiutangController::class, 'store'])->name('piutang.store');
        Route::put('/piutang/{piutang}', [AdminPiutangController::class, 'update'])->name('piutang.update');
        Route::delete('/piutang/{piutang}', [AdminPiutangController::class, 'destroy'])->name('piutang.destroy');

        // kelola Member
        Route::resource('data-member', AdminKelolaMemberController::class);
        Route::resource('pencairan', AdminPencairanSaldoController::class);

        // Pengaturan
        Route::get('pengaturan', [AdminPengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('pengaturan', [AdminPengaturanController::class, 'update'])->name('pengaturan.update');
    });

/*
|--------------------------------------------------------------------------
| Operator Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:operator'])
    ->prefix('operator')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('operator.dashboard');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('operator.profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('operator.profile.update');

        // Produk Jasa
        Route::get('produk-jasa/data/{id}', [ProdukJasaController::class, 'getData'])->name('produk-jasa.data');
        Route::resource('produk-jasa', ProdukJasaController::class)->names('operator.produk-jasa')->except(['show']);

        // Transaksi
        Route::get('transaksi', [TransaksiController::class, 'index'])->name('operator.transaksi.index');
        Route::post('transaksi', [TransaksiController::class, 'store'])->name('operator.transaksi.store');
        Route::delete('transaksi/{id}', [TransaksiController::class, 'destroy'])->name('operator.transaksi.destroy');
    });

Route::middleware(['auth', 'role:member'])
    ->prefix('member')
    ->group(function () {
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])
        ->name('member.dashboard');

        // Profile
        Route::get('/profile', [MemberProfileController::class, 'edit'])->name('member.profile.edit');
        Route::post('/profile', [MemberProfileController::class, 'update'])->name('member.profile.update');
    }); 