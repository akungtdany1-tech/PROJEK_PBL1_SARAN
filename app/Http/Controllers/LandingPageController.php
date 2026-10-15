<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandingPage;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    public function index()
    {
        // Pake firstOrCreate biar kalau DB fresh, minimal ada 1 baris data biar gak error
        $data = LandingPage::first() ?? LandingPage::create([
            'judul_h1' => 'Solusi Belanja',
            'judul_highlight' => 'Lengkap'
        ]);

        return view('Pengaturan.landing', compact('data'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'login_bg_color' => 'nullable|string|max:10',
            'login_text_color' => 'nullable|string|max:10',
            'login_title' => 'nullable|string|max:255',
            'login_subtitle' => 'nullable|string|max:255',
            'login_font_family' => 'nullable|string',
            'login_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'login_hero' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'login_icon' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
        ]);

        $landing = LandingPage::first() ?? new LandingPage();
        $landing->fill($request->only([
            'login_bg_color', 'login_text_color',
            'login_title', 'login_subtitle', 'login_font_family'
        ]));
        $landing->save();

        if ($request->hasFile('login_logo')) {
            if ($landing->login_logo_path) Storage::disk('public')->delete($landing->login_logo_path);
            $landing->login_logo_path = $request->file('login_logo')->store('landing/login', 'public');
        }

        if ($request->hasFile('login_hero')) {
            if ($landing->login_hero_image) Storage::disk('public')->delete($landing->login_hero_image);
            $landing->login_hero_image = $request->file('login_hero')->store('landing/login', 'public');
        }

        if ($request->hasFile('login_icon')) {
            if ($landing->login_icon_path) Storage::disk('public')->delete($landing->login_icon_path);
            $landing->login_icon_path = $request->file('login_icon')->store('landing/login', 'public');
        }

        $landing->save();
        return back()->with('success', 'Pengaturan diperbarui!');
    }
}