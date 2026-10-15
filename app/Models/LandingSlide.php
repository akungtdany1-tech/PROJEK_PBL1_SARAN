<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSlide extends Model
{
    protected $fillable = ['landing_page_id', 'path', 'type', 'order'];

    // Relasi balik ke Landing Page utama
    public function landingPage()
    {
        return $this->belongsTo(LandingPage::class);
    }
}