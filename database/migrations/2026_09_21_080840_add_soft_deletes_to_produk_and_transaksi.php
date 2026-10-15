<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Soft delete untuk tabel produk
        Schema::table('produk', function (Blueprint $table) {
            $table->softDeletes(); // tambah kolom deleted_at
        });

        // Soft delete untuk tabel transaksi
        Schema::table('transaksi', function (Blueprint $table) {
            $table->softDeletes(); // tambah kolom deleted_at
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
