<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sarana Agro Makmur</title>
    @php
        $landing = \App\Models\LandingPage::first();
        $logoPath = ($landing && $landing->login_logo_path)
            ? asset('storage/' . $landing->login_logo_path)
            : asset('images/logotoko.png');
    @endphp
    <link rel="icon" type="image/png" href="{{ $logoPath }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f0f2f1] font-sans antialiased flex items-center justify-center min-h-screen p-6">

    <div class="max-w-md w-full bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        {{-- Logo & Judul --}}
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <img src="{{ $logoPath }}" alt="Logo" class="w-16 h-16 object-contain">
            </div>
            <h2 class="text-2xl font-bold text-[#2d6a4f]">Lupa Password?</h2>
            <p class="text-gray-500 text-sm mt-2">
                Masukan email anda 
            </p>
        </div>

        {{-- Status Session (Kalau email berhasil dikirim) --}}
        @if (session('status'))
            <div class="mb-6 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm text-center font-medium">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            {{-- Email Address --}}
            <div class="space-y-2">
                <label class="block text-gray-700 font-bold ml-1 text-sm">Email Terdaftar</label>
                <input type="email" name="email" :value="old('email')" required autofocus
                    placeholder="nama@email.com"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#2d6a4f] outline-none text-gray-800">
                
                @if($errors->has('email'))
                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <div class="flex flex-col gap-4">
                <button type="submit" class="w-full bg-[#2d6a4f] hover:bg-[#1b4332] text-white font-bold py-3 rounded-lg transition">
                    KIRIM LINK RESET
                </button>
                
                <a href="{{ route('login') }}" class="text-center text-sm font-semibold text-gray-400 hover:text-[#2d6a4f] transition-colors">
                    Kembali ke Login
                </a>
            </div>
        </form>
    </div>

</body>
</html>