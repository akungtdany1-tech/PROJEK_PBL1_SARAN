<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'judul_h1', 
        'judul_highlight', 
        'deskripsi', 
        'login_logo_path',
        'login_hero_image',
        'login_icon_path',
        'login_bg_color', 
        'login_text_color', 
        'login_title', 
        'login_subtitle', 
        'login_font_family'
    ];

    public function slides()
    {
        return $this->hasMany(LandingSlide::class)->orderBy('order');
    }
}