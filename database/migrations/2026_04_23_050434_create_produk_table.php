<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->bigIncrements('id_produk');

            $table->string('kode_produk', 50)->unique();
            $table->string('nama_produk', 150)->unique();

            $table->unsignedBigInteger('id_kategori');

            $table->integer('harga_satuan');
            $table->integer('harga_grosir')->nullable();
            $table->integer('minimal_grosir')->nullable();

            $table->integer('stok_gudang')->default(0);
            $table->integer('stok_toko')->default(0);
            $table->integer('isi_per_dus')->default(1);
            

            $table->enum('satuan', ['pcs', 'box', 'kg', 'liter'])->nullable();
            $table->timestamps();

            $table->foreign('id_kategori')
                  ->references('id_kategori')
                  ->on('kategori')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};