<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('kategori', function (Blueprint $table) {
        $table->bigIncrements('id_kategori');
        $table->string('nama_kategori');
        $table->string('status')->default('active'); // Tambahkan ini
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};