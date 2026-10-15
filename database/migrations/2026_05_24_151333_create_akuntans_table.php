<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akun', function (Blueprint $table) {
            $table->bigIncrements('id_akun');
            $table->string('kode_akun', 20)->unique();
            $table->string('nama_akun', 150);
            $table->enum('pos_saldo', ['debet', 'kredit']);
            $table->enum('pos_laporan', ['neraca', 'laba_rugi']);
            $table->enum('kategori_neraca', [
                'aktiva_lancar',
                'aktiva_tetap',
                'kewajiban_lancar',
                'kewajiban_jangka_panjang',
                'modal'
            ])->nullable();
            $table->enum('kategori_laba_rugi', [
                'pendapatan',
                'beban_pokok',
                'beban_operasional',
                'pendapatan_lain',
                'beban_lain'
            ])->nullable();
            $table->boolean('flag_transaksi_offline')->default(false);
            $table->boolean('flag_transaksi_online')->default(false);
            $table->boolean('flag_kas_offline')->default(false);
            $table->boolean('flag_kas_online')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akun');
    }
};