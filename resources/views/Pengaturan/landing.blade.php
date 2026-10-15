@extends('layouts.app')

@section('content')
<div class="p-8">
    {{-- HEADER --}}
    <div class="mb-10">
        <h2 class="text-3xl font-black text-[#2d6a4f] tracking-tight">Kustomisasi Visual</h2>
        <p class="text-gray-400 text-sm font-medium italic">Kelola branding toko anda.</p>
    </div>

    {{-- VISUAL LOGIN --}}
    <div class="space-y-10">
        <form action="{{ route('pengaturan.landing.update') }}" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-[2.5rem] border shadow-sm">
            @csrf
            <h4 class="font-black text-gray-800 uppercase tracking-tighter mb-6">Pengaturan Visual Login</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- KOLOM KIRI --}}
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-bold uppercase text-gray-400">Judul Login</label>
                        <input type="text" name="login_title" value="{{ $data->login_title ?? '' }}" class="w-full p-4 bg-gray-50 rounded-2xl border outline-none">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-gray-400">Subtitle</label>
                        <input type="text" name="login_subtitle" value="{{ $data->login_subtitle ?? '' }}" class="w-full p-4 bg-gray-50 rounded-2xl border outline-none">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-gray-400">Font</label>
                        <select name="login_font_family" class="w-full p-4 bg-gray-50 rounded-2xl border outline-none text-sm">
                            @php
                                $fonts = [
                                    "'Plus Jakarta Sans', sans-serif" => 'Plus Jakarta Sans (Default)',
                                    "'Inter', sans-serif"             => 'Inter',
                                    "'Poppins', sans-serif"           => 'Poppins',
                                    "'Montserrat', sans-serif"        => 'Montserrat',
                                    "'Nunito', sans-serif"            => 'Nunito',
                                    "'DM Sans', sans-serif"           => 'DM Sans',
                                ];
                            @endphp
                            @foreach($fonts as $value => $label)
                                <option value="{{ $value }}" {{ ($data->login_font_family ?? "'Plus Jakarta Sans', sans-serif") == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-bold uppercase text-gray-400">Warna Panel Kiri</label>
                        <input type="color" name="login_bg_color" value="{{ $data->login_bg_color ?? '#2d6a4f' }}" class="w-full h-14 p-1 bg-gray-50 rounded-2xl border outline-none">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-gray-400">Warna Teks & Tombol</label>
                        <input type="color" name="login_text_color" value="{{ $data->login_text_color ?? '#2d6a4f' }}" class="w-full h-14 p-1 bg-gray-50 rounded-2xl border outline-none">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-gray-400">Logo Besar (Panel Kiri)</label>
                        <input type="file" name="login_logo" class="w-full p-3 bg-gray-50 rounded-2xl border text-xs">
                        @if($data->login_logo_path)
                            <img src="{{ asset('storage/' . $data->login_logo_path) }}" class="mt-2 h-12 object-contain rounded-xl border p-1">
                        @endif
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-gray-400">Foto Hero (Panel Kiri Login)</label>
                        <input type="file" name="login_hero" accept="image/*" class="w-full p-3 bg-gray-50 rounded-2xl border text-xs">
                        <p class="text-[10px] text-gray-400 mt-1">Foto latar belakang panel kiri login. Jika kosong, menggunakan warna solid.</p>
                        @if($data->login_hero_image)
                            <img src="{{ asset('storage/' . $data->login_hero_image) }}" class="mt-2 h-20 w-full object-cover rounded-xl border">
                        @endif
                    </div>

                </div>
            </div>
            <button type="submit" class="mt-8 bg-blue-600 text-white px-10 py-3 rounded-xl font-bold">Simpan Visual Login</button>
        </form>
    </div>
</div>
@endsection