<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPage;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        LandingPage::updateOrCreate(
            ['id' => 1],
            [
                'judul_h1' => 'Solusi Belanja',
                'judul_highlight' => 'Lengkap',
                'deskripsi' => 'Temukan segala kebutuhan Anda dalam satu tempat. Kami hadir dengan sistem manajemen modern.',
                'login_bg_color' => '#2d6a4f',
                'login_text_color' => '#2d6a4f',
                'login_title' => 'Sarana Agro Makmur',
                'login_subtitle' => 'SISTEM MANAJEMEN TOKO',
                'login_font_family' => "'Plus Jakarta Sans', sans-serif",
            ]
        );
    }
}