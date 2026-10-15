<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Password Baru - Sarana Agro Makmur</title>
    @php
        $landing = \App\Models\LandingPage::first();
        $logoPath = ($landing && $landing->login_logo_path)
            ? asset('storage/' . $landing->login_logo_path)
            : asset('images/logotoko.png');
    @endphp
    <link rel="icon" type="image/png" href="{{ $logoPath }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f0f2f1] font-sans antialiased flex items-center justify-center min-h-screen p-6">

    <div class="max-w-md w-full bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        
        {{-- Logo & Judul --}}
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <img src="{{ $logoPath }}" alt="Logo" class="w-16 h-16 object-contain">
            </div>
            <h2 class="text-2xl font-bold text-[#2d6a4f]">Sarana Agro Makmur
            </h2>
            <p class="text-gray-500 text-sm mt-2">
                Masukkan password baru 
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email Address (Readonly lebih aman) --}}
            <div class="space-y-2">
                <label class="block text-gray-700 font-bold ml-1 text-sm">Email</label>
                <input type="email" name="email" value="{{ old('email', $request->email) }}" required readonly
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg outline-none text-gray-500 cursor-not-allowed">
                @if($errors->has('email'))
                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $errors->first('email') }}</p>
                @endif
            </div>

            {{-- New Password --}}
            <div class="space-y-2" x-data="{ show: false }">
                <label class="block text-gray-700 font-bold ml-1 text-sm">Password Baru</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                        placeholder="Min. 8 karakter"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#2d6a4f] outline-none text-gray-800 pr-12">
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#2d6a4f]">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                    </button>
                </div>
                @if($errors->has('password'))
                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $errors->first('password') }}</p>
                @endif
            </div>

            {{-- Confirm Password --}}
            <div class="space-y-2">
                <label class="block text-gray-700 font-bold ml-1 text-sm">Ulangi Password Baru</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password"
                    placeholder="Pastikan sama dengan di atas"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#2d6a4f] outline-none text-gray-800">
                @if($errors->has('password_confirmation'))
                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $errors->first('password_confirmation') }}</p>
                @endif
            </div>

            <button type="submit" class="w-full bg-[#2d6a4f] hover:bg-[#1b4332] text-white font-bold py-3 rounded-lg transition mt-2">
                UPDATE PASSWORD
            </button>
        </form>

    </div>

</body>
</html>