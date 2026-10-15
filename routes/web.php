<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    ProfileController, ProdukController, KategoriController,
    PelangganController, TransaksiController, SuplierController,
    LaporanController, DashboardController,
    PengaturanController, DiskonController, LandingPageController,
    AkuntansiController, LaporanKeuanganController, RiwayatController
};

// Internal: tanpa halaman welcome/landing — root langsung arahkan ke login,
// kalau sudah login lempar ke dashboard.
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// --- 1. ROUTE LOGIN UMUM ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- 2. ROUTE AKSES BERSAMA (Pemilik & Kasir) ---
Route::middleware(['auth', 'role:pemilik,kasir,pemilik2'])->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');
});

// --- 3. ROUTE PENJUALAN (Kasir + Pemilik) ---
Route::middleware(['auth', 'role:kasir,pemilik,pemilik2'])->group(function () {
    Route::get('/transaksi/stok-terkini', [TransaksiController::class, 'stokTerkini'])->name('transaksi.stokTerkini');
    Route::resource('transaksi', TransaksiController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('/transaksi/{id}/print', [TransaksiController::class, 'printStruk'])->name('transaksi.print');
});

// --- 4. ROUTE KHUSUS PEMILIK ---
Route::middleware(['auth', 'role:pemilik,pemilik2'])->group(function () {
    // Sampah routes (kategori, produk, diskon, pelanggan, suplier)
    Route::get('/kategori/sampah', [KategoriController::class, 'sampah'])->name('kategori.sampah');
    Route::post('/kategori/sampah/{id}/pulihkan', [KategoriController::class, 'pulihkan'])->name('kategori.pulihkan');
    Route::delete('/kategori/sampah/{id}/hapus', [KategoriController::class, 'hapusPermanen'])->name('kategori.hapusPermanen');

    Route::get('/produk/sampah', [ProdukController::class, 'sampah'])->name('produk.sampah');
    Route::post('/produk/sampah/{id}/pulihkan', [ProdukController::class, 'pulihkan'])->name('produk.pulihkan');
    Route::delete('/produk/sampah/{id}/hapus', [ProdukController::class, 'hapusPermanen'])->name('produk.hapusPermanen');

    Route::get('/diskon/sampah', [DiskonController::class, 'sampah'])->name('diskon.sampah');
    Route::post('/diskon/sampah/{id}/pulihkan', [DiskonController::class, 'pulihkan'])->name('diskon.pulihkan');
    Route::delete('/diskon/sampah/{id}/hapus', [DiskonController::class, 'hapusPermanen'])->name('diskon.hapusPermanen');

    Route::get('/pelanggan/sampah', [PelangganController::class, 'sampah'])->name('pelanggan.sampah');
    Route::post('/pelanggan/sampah/{id}/pulihkan', [PelangganController::class, 'pulihkan'])->name('pelanggan.pulihkan');
    Route::delete('/pelanggan/sampah/{id}/hapus', [PelangganController::class, 'hapusPermanen'])->name('pelanggan.hapusPermanen');

    // Sampah Transaksi
    Route::get('/transaksi/sampah', [TransaksiController::class, 'sampah'])->name('transaksi.sampah');
    Route::post('/transaksi/sampah/{id}/pulihkan', [TransaksiController::class, 'pulihkan'])->name('transaksi.pulihkan');
    Route::delete('/transaksi/sampah/{id}/hapus', [TransaksiController::class, 'hapusPermanen'])->name('transaksi.hapusPermanen');

    Route::resource('pelanggan', PelangganController::class);
    Route::resource('produk', ProdukController::class);
    Route::resource('kategori', KategoriController::class)->except(['create', 'show']);
    Route::resource('diskon', DiskonController::class);

    // Pembelian
    Route::get('/pembelian', [TransaksiController::class, 'indexPembelian'])->name('pembelian.index');
    Route::get('/pembelian/create', [TransaksiController::class, 'createPembelian'])->name('pembelian.create');
    Route::post('/pembelian', [TransaksiController::class, 'storePembelian'])->name('pembelian.store');
    Route::get('/pembelian/{id}', [TransaksiController::class, 'showPembelian'])->name('pembelian.show');
    Route::delete('/pembelian/{id}', [TransaksiController::class, 'destroyPembelian'])->name('pembelian.destroy');

    // Pengaturan
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/landing', [LandingPageController::class, 'index'])->name('landing.index');
        Route::post('/landing/update', [LandingPageController::class, 'update'])->name('landing.update');

        Route::get('/pemilik', [PengaturanController::class, 'pemilikIndex'])->name('pemilik');
        Route::put('/pemilik/update', [PengaturanController::class, 'pemilikUpdate'])->name('pemilik.update');

        Route::get('/kasir', [PengaturanController::class, 'kasir'])->name('kasir');
        Route::post('/kasir', [PengaturanController::class, 'kasirStore'])->name('kasir.store');
        Route::get('/kasir/{id}/edit', [PengaturanController::class, 'kasirEdit'])->name('kasir.edit');
        Route::put('/kasir/{id}', [PengaturanController::class, 'kasirUpdate'])->name('kasir.update');
        Route::delete('/kasir/{id}', [PengaturanController::class, 'kasirDestroy'])->name('kasir.destroy');

        Route::get('/suplier/sampah', [SuplierController::class, 'sampah'])->name('suplier.sampah');
        Route::post('/suplier/sampah/{id}/pulihkan', [SuplierController::class, 'pulihkan'])->name('suplier.pulihkan');
        Route::delete('/suplier/sampah/{id}/hapus', [SuplierController::class, 'hapusPermanen'])->name('suplier.hapusPermanen');

        Route::get('/suplier', [SuplierController::class, 'index'])->name('suplier');
        Route::post('/suplier', [SuplierController::class, 'store'])->name('suplier.store');
        Route::get('/suplier/{id}/edit', [SuplierController::class, 'edit'])->name('suplier.edit');
        Route::put('/suplier/{id}', [SuplierController::class, 'update'])->name('suplier.update');
        Route::delete('/suplier/{id}', [SuplierController::class, 'destroy'])->name('suplier.destroy');
    });

    Route::post('/produk/{id}/transfer-stok', [ProdukController::class, 'transferStok'])->name('produk.transferStok');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
});

require __DIR__.'/auth.php';
