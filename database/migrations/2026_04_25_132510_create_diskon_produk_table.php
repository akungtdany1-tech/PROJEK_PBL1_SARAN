<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diskon_produk', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_diskon');
            $table->unsignedBigInteger('id_produk');
            $table->timestamps();

            $table->foreign('id_diskon')
                  ->references('id_diskon')
                  ->on('diskon')
                  ->onDelete('cascade');

            $table->foreign('id_produk')
                  ->references('id_produk')
                  ->on('produk')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diskon_produk');
    }
};