@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-8 flex items-center gap-4">
        <div class="p-3 bg-[#2d6a4f] rounded-2xl shadow-lg shadow-green-100">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Profil Pemilik</h2>
            <p class="text-gray-500 text-sm">Update informasi akun utama Sarana Agro Makmur</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-[#2d6a4f] text-[#2d6a4f] px-4 py-3 rounded-r-2xl flex items-center gap-3">
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="max-w-3xl bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('pengaturan.pemilik.update') }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 ml-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#2d6a4f] outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 ml-1">Email (hanya @gmail.com)</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required pattern="^[a-zA-Z0-9._%+\-]+@gmail\.com$" title="Harus @gmail.com"
                           class="w-full px-5 py-3 bg-gray-50 border @error('email') border-red-300 @else border-gray-200 @enderror rounded-2xl focus:ring-2 focus:ring-[#2d6a4f] outline-none transition-all">
                    @error('email')<p class="text-xs text-red-600 ml-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 ml-1">Password Baru</label>
                    <input type="password" name="password" class="w-full px-5 py-3 bg-gray-50 border @error('password') border-red-300 @else border-gray-200 @enderror rounded-2xl focus:ring-2 focus:ring-[#2d6a4f] outline-none" placeholder="••••••••">
                    @error('password')<p class="text-xs text-red-600 ml-1">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 ml-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#2d6a4f] outline-none" placeholder="••••••••">
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="w-full md:w-auto px-12 py-4 bg-[#2d6a4f] text-white font-bold rounded-2xl shadow-xl transition-all">
                    SIMPAN PERUBAHAN
                </button>
            </div>
        </form>
    </div>
</div>
@endsection