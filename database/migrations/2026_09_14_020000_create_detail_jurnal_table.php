<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_jurnal', function (Blueprint $table) {
            $table->bigIncrements('id_detail_jurnal');
            $table->unsignedBigInteger('id_jurnal');
            $table->unsignedBigInteger('id_akun');
            $table->integer('debet')->default(0);
            $table->integer('kredit')->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_jurnal')->references('id_jurnal')->on('jurnal')->onDelete('cascade');
            $table->foreign('id_akun')->references('id_akun')->on('akun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_jurnal');
    }
};
