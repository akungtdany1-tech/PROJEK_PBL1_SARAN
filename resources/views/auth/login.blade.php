<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @php
        $landing    = \App\Models\LandingPage::first();
        $titleColor = $landing->login_text_color ?? '#2d6a4f';
        $bgColor    = $landing->login_bg_color ?? '#2d6a4f';
        $fontFamily = $landing->login_font_family ?? "'Plus Jakarta Sans', sans-serif";
        $title      = $landing->login_title ?? 'Sarana Agro Makmur Sukses';
        $subtitle   = $landing->login_subtitle ?? 'SISTEM MANAJEMEN TOKO & GUDANG';

        $logoPath = ($landing && $landing->login_logo_path)
            ? asset('storage/' . $landing->login_logo_path)
            : asset('images/logotoko.png');

        $heroImage = ($landing && $landing->login_hero_image)
            ? asset('storage/' . $landing->login_hero_image)
            : null;
    @endphp
    <link rel="icon" type="image/png" href="{{ $logoPath }}">

    <style>
        * { font-family: {!! $fontFamily !!}; }
        body { margin: 0; background: #f0f2f1; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 md:p-8">

    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row min-h-[540px]">

        {{-- ═══ KIRI: HERO PANEL ═══ --}}
        <div class="relative w-full md:w-[52%] flex-shrink-0 overflow-hidden">
            @if($heroImage)
                {{-- Ada hero image: fill seluruh panel --}}
                <img src="{{ $heroImage }}" alt="Hero" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/25"></div>
                <div class="relative z-10 flex flex-col justify-end h-full p-8 md:p-10">
                    <h1 class="text-2xl md:text-3xl font-extrabold text-white leading-tight">{{ $title }}</h1>
                    <p class="text-white/70 text-sm mt-2 tracking-wide uppercase">{{ $subtitle }}</p>
                    <div class="w-12 h-1 rounded-full mt-4 bg-white/60"></div>
                </div>
            @else
                {{-- Tidak ada hero: logo besar di tengah --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center p-8 md:p-10" style="background-color: {{ $bgColor }};">
                    <img src="{{ $logoPath }}" alt="Logo" class="w-32 h-32 md:w-40 md:h-40 object-contain rounded-2xl bg-white/10 p-4">
                    <h1 class="text-2xl md:text-3xl font-extrabold text-white leading-tight mt-8 text-center">{{ $title }}</h1>
                    <p class="text-white/60 text-sm mt-2 tracking-wide uppercase text-center">{{ $subtitle }}</p>
                    <div class="w-12 h-1 rounded-full mt-4 bg-white/40"></div>
                </div>
            @endif
        </div>

        {{-- ═══ KANAN: FORM LOGIN ═══ --}}
        <div class="w-full md:w-[48%] flex items-center justify-center p-8 md:p-12">
            <div class="w-full max-w-sm">

                {{-- Header --}}
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">
                        Masuk ke <span style="color: {{ $titleColor }}">{{ $title }}</span>
                    </h2>
                    <p class="text-gray-400 text-sm mt-2">Masukkan kredensial Anda untuk mengakses sistem.</p>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input id="email" type="email" name="email" required
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 outline-none transition focus:border-gray-400 focus:bg-white">
                        @error('email')
                            <span class="block text-xs text-red-500 mt-1.5">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <input id="password" :type="show ? 'text' : 'password'" name="password" required
                                   placeholder="Masukkan password"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 outline-none transition focus:border-gray-400 focus:bg-white pr-12">
                            <button type="button" @click="show = !show"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <span class="block text-xs text-red-500 mt-1.5">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Ingat saya + Lupa password --}}
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                   class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500">
                            <span class="text-sm text-gray-500">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-gray-400 hover:text-gray-700 transition-colors">
                            Lupa password?
                        </a>
                    </div>

                    {{-- Tombol Masuk --}}
                    <button type="submit"
                            class="w-full py-3.5 rounded-full text-sm font-bold text-white transition"
                            style="background-color: {{ $bgColor }};"
                            onmouseover="this.style.filter='brightness(0.88)'" onmouseout="this.style.filter='none'">
                        Masuk
                    </button>
                </form>

            </div>
        </div>

    </div>

</body>
</html>
