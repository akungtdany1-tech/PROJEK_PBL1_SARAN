<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beban_operasional', function (Blueprint $table) {
            $table->bigIncrements('id_beban');
            $table->date('tanggal');
            $table->unsignedBigInteger('id_akun');
            $table->string('nama_beban', 150);
            $table->integer('jumlah');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();

            $table->foreign('id_akun')->references('id_akun')->on('akun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beban_operasional');
    }
};
