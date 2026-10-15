<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->bigIncrements('id_jurnal');
            $table->string('no_jurnal', 30)->unique();
            $table->date('tanggal');
            $table->string('ref_type')->nullable(); // e.g. 'penjualan', 'pembelian', 'beban', null (manual)
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('total')->default(0);
            $table->unsignedBigInteger('id_user')->nullable();
            $table->timestamps();

            $table->index(['ref_type', 'ref_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};
