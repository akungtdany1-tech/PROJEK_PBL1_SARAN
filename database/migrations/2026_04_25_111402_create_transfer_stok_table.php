<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_stok', function (Blueprint $table) {
            $table->bigIncrements('id_transfer');
            $table->unsignedBigInteger('id_produk');
            $table->integer('jumlah_dus');
            $table->integer('jumlah_pcs');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_produk')
                ->references('id_produk')
                ->on('produk')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_stok');
    }
};