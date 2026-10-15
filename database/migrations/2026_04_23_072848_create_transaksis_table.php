<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->bigIncrements('id_transaksi');
            $table->enum('jenis', ['penjualan', 'pembelian']); // ← kolom baru
            $table->date('tanggal');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->unsignedBigInteger('id_pelanggan')->nullable();
            $table->unsignedBigInteger('id_suplier')->nullable();   // ← kolom baru
            $table->unsignedBigInteger('id_akun')->default(0);
            $table->integer('subtotal')->default(0);
            $table->integer('total_diskon')->default(0);
            $table->integer('total')->default(0);
            $table->integer('bayar')->default(0);
            $table->integer('kembalian')->default(0);
            $table->string('metode_pembayaran')->nullable();
            $table->text('keterangan')->nullable();                 // ← kolom baru
            $table->text('catatan')->nullable();
            $table->timestamps();           

            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan')->onDelete('set null');
            $table->foreign('id_suplier')->references('id_suplier')->on('suplier')->onDelete('set null');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};