<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sarana Agro Makmur') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $landing    = \App\Models\LandingPage::first() ?? new \App\Models\LandingPage;
        $brandColor = $landing->login_text_color ?? '#2d6a4f';
        $title      = $landing->login_title ?? 'Sarana Agro Makmur';
        $logoPath   = ($landing->login_logo_path)
            ? asset('storage/' . $landing->login_logo_path)
            : asset('images/logotoko.png');
    @endphp
    <link rel="icon" type="image/png" href="{{ $logoPath }}">
    <style>
    [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">

<div class="flex h-screen overflow-hidden"
     x-data="{
        sidebarOpen: false,
        openOperasional: {{ request()->routeIs('transaksi.*') || request()->routeIs('pembelian.*') || request()->routeIs('laporan.*') ? 'true' : 'false' }},
        openPengaturan: {{ request()->routeIs('landing.*') || request()->is('pengaturan/pemilik') ? 'true' : 'false' }}
     }">

    {{-- Backdrop (mobile) --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak x-transition.opacity
         class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

    {{-- SIDEBAR --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-72 lg:static lg:w-64 bg-white flex flex-col justify-between border-r border-gray-200
                  shadow-xl lg:shadow-none transition-transform duration-200 ease-in-out lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex-1 overflow-y-auto">
            {{-- LOGO HEADER SIDEBAR --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200">
                <img src="{{ $logoPath }}" alt="Logo" class="w-10 h-10 object-contain">
                <div>
                    <p class="font-bold text-[{{ $brandColor }}] text-sm leading-tight">{{ $title }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                </div>
            </div>

            <nav class="mt-4 px-4 space-y-1 pb-4">

                {{-- MENU UMUM --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                   {{ request()->routeIs('dashboard') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                {{-- MENU KHUSUS PEMILIK --}}
                @if(auth()->user()->role === 'pemilik')

                    {{-- MASTER DATA --}}
                    <div class="pt-4 pb-1 px-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Master Data</p>
                    </div>

                    <a href="{{ route('pelanggan.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                       {{ request()->routeIs('pelanggan.*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Pelanggan
                    </a>

                    <a href="{{ route('produk.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                       {{ request()->routeIs('produk.*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Produk
                    </a>

                    <a href="{{ route('kategori.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                       {{ request()->routeIs('kategori.*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M13 7h.01M13 11h.01M13 15h.01M17 7h.01M17 11h.01M17 15h.01M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Kategori
                    </a>

                    <a href="{{ route('diskon.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                       {{ request()->routeIs('diskon.*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Diskon
                    </a>

                    <a href="{{ route('pengaturan.suplier') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                       {{ request()->is('pengaturan/suplier*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Suplier
                    </a>

                    {{-- OPERASIONAL (accordion) --}}
                    <div class="pt-4">
                        <button @click="openOperasional = !openOperasional"
                                class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                                {{ request()->routeIs('transaksi.*') || request()->routeIs('pembelian.*') || request()->routeIs('laporan.*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <span>Operasional</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="openOperasional ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openOperasional" x-cloak x-transition class="ml-4 mt-1 space-y-1 border-l-2 border-gray-100 pl-3">
                            <a href="{{ route('transaksi.index') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                               {{ request()->routeIs('transaksi.*') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Transaksi
                            </a>
                            <a href="{{ route('pembelian.index') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                               {{ request()->routeIs('pembelian.*') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                Pembelian
                            </a>
                            <a href="{{ route('laporan.index') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                               {{ request()->routeIs('laporan.*') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m-9-3h12a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                                </svg>
                                Laporan
                            </a>
                        </div>
                    </div>

                    {{-- ponytail: AKUNTANSI disabled — restore: uncomment blok ini + routes akuntansi di web.php + JurnalService calls di TransaksiController --}}
                    {{--
                    <div class="pt-2">
                        <button @click="openAkuntansi = !openAkuntansi"
                                class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                                {{ request()->is('akuntansi/*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                <span>Akuntansi</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="openAkuntansi ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openAkuntansi" x-cloak x-transition class="ml-4 mt-1 space-y-1 border-l-2 border-gray-100 pl-3">
                            <a href="{{ route('akuntansi.coa') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                               {{ request()->routeIs('akuntansi.coa') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10"/>
                                </svg>
                                Chart of Accounts
                            </a>
                            <a href="{{ route('akuntansi.jurnal') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                               {{ request()->routeIs('akuntansi.jurnal') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3-7 3V5z"/>
                                </svg>
                                Jurnal Umum
                            </a>
                            <a href="{{ route('akuntansi.buku-besar') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                               {{ request()->routeIs('akuntansi.buku-besar') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16m-9-13h3m-3 4h3"/>
                                </svg>
                                Buku Besar
                            </a>
                            <a href="{{ route('akuntansi.beban') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                               {{ request()->routeIs('akuntansi.beban') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Beban Operasional
                            </a>
                            <a href="{{ route('akuntansi.laba-rugi') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                               {{ request()->routeIs('akuntansi.laba-rugi') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                Laba Rugi
                            </a>
                            <a href="{{ route('akuntansi.neraca') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                               {{ request()->routeIs('akuntansi.neraca') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 12h18M3 18h12"/>
                                </svg>
                                Neraca
                            </a>
                            <a href="{{ route('akuntansi.arus-kas') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                               {{ request()->routeIs('akuntansi.arus-kas') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                                Arus Kas
                            </a>
                        </div>
                    </div>
                    --}}

                    {{-- KEAMANAN --}}
                    <div class="pt-4 pb-1 px-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Keamanan</p>
                    </div>

                    <a href="{{ route('riwayat.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                       {{ request()->routeIs('riwayat.*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Riwayat Aktivitas
                    </a>

                    {{-- PENGATURAN (accordion) --}}
                    <button @click="openPengaturan = !openPengaturan"
                            class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                            {{ request()->routeIs('landing.*') || request()->is('pengaturan/pemilik') || request()->is('pengaturan/kasir*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Pengaturan</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="openPengaturan ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="openPengaturan" x-cloak x-transition class="ml-4 mt-1 space-y-1 border-l-2 border-gray-100 pl-3">
                        <a href="{{ route('pengaturan.kasir') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                           {{ request()->is('pengaturan/kasir*') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Kasir
                        </a>
                        <a href="{{ route('pengaturan.landing.index') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                           {{ request()->routeIs('landing.*') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                            Landing Page
                        </a>
                        <a href="{{ route('pengaturan.pemilik') }}"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-bold transition
                           {{ request()->is('pengaturan/pemilik') ? 'text-[#2d6a4f] bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Akun Pemilik
                        </a>
                    </div>

                @endif

                {{-- MENU KHUSUS KASIR & PEMILIK2 --}}
                @if(in_array(auth()->user()->role, ['kasir', 'pemilik2']))
                    <div class="pt-4 pb-1 px-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Operasional</p>
                    </div>
                    <a href="{{ route('transaksi.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                       {{ request()->routeIs('transaksi.*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Transaksi
                    </a>

                    <a href="{{ route('laporan.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                       {{ request()->routeIs('laporan.*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m-9-3h12a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                        </svg>
                        Laporan
                    </a>

                    @if(auth()->user()->role !== 'kasir')
                        <a href="{{ route('riwayat.index') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg font-bold text-sm transition
                           {{ request()->routeIs('riwayat.*') ? 'bg-[#2d6a4f] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Riwayat Aktivitas
                        </a>
                    @endif
                @endif

            </nav>
        </div>

        {{-- Profil & Logout --}}
        <div class="px-4 py-4 border-t border-gray-200 flex-shrink-0">
            <div class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                <div class="w-9 h-9 rounded-full bg-[#2d6a4f] flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 hover:bg-red-50 rounded-lg transition group">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- CONTENT AREA --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center gap-3">
            <button type="button" @click="sidebarOpen = !sidebarOpen"
                    class="p-2 -ml-1 lg:hidden rounded-lg hover:bg-gray-100 text-gray-600" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            @isset($header)
                <div class="font-bold text-lg sm:text-xl text-gray-800">
                    {{ $header }}
                </div>
            @endisset
        </header>
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50">
            @if(isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>