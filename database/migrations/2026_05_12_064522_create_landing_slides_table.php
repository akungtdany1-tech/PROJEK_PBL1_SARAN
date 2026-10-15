<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    // 1. Tabel Utama Landing Page
    Schema::create('landing_pages', function (Blueprint $table) {
        $table->id();
        $table->string('judul_h1')->nullable();
        $table->string('judul_highlight')->nullable();
        $table->text('deskripsi')->nullable();
        
        // Asset Login Page
        $table->string('login_logo_path')->nullable(); // Logo Besar Kiri
        $table->string('login_icon_path')->nullable(); // Icon Kecil Samping Teks
        $table->string('login_title')->nullable();     // Judul Brand (SAM)
        $table->string('login_subtitle')->nullable();  // Slogan (Sistem Manajemen...)
        $table->string('login_bg_color')->default('#2d6a4f'); // Warna Panel Kiri
        $table->string('login_text_color')->default('#2d6a4f'); // Warna Teks & Tombol
        $table->string('login_font_family')->default("'Plus Jakarta Sans', sans-serif");
        
        $table->timestamps();
    });

    // 2. Tabel Slides
    Schema::create('landing_slides', function (Blueprint $table) {
        $table->id();
        $table->foreignId('landing_page_id')->constrained('landing_pages')->onDelete('cascade');
        $table->string('path');
        $table->enum('type', ['image', 'video']);
        $table->integer('order')->default(0);
        $table->timestamps();
    });
}
};