<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->bigIncrements('id_detail_transaksi');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_produk');
            $table->enum('tipe', ['eceran', 'grosir'])->nullable();         // untuk penjualan
            $table->enum('tipe_stok', ['gudang', 'toko'])->nullable();      // untuk pembelian
            $table->integer('jumlah');
            $table->integer('harga')->default(0);                           // harga jual
            $table->integer('harga_beli')->default(0);                      // ← kolom baru (pembelian)
            $table->integer('nominal_diskon')->default(0);
            $table->integer('subtotal')->default(0);
            $table->timestamps();

            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};