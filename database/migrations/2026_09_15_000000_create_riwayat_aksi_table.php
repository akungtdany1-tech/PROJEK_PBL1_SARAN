<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_aksi', function (Blueprint $table) {
            $table->bigIncrements('id_riwayat');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('role', 20)->nullable();
            $table->string('aksi', 40);
            $table->unsignedBigInteger('id_transaksi')->nullable();
            $table->string('label', 100);
            $table->integer('total')->default(0);
            $table->date('tanggal_transaksi')->nullable();
            $table->json('detail')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_aksi');
    }
};