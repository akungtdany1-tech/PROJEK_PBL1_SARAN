<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - Sarana Agro Makmur</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f0f2f1] flex items-center justify-center min-h-screen p-6">
    <div class="text-center bg-white p-10 rounded-3xl shadow-xl max-w-md">
        <div class="flex justify-center mb-6">
            <img src="{{ asset('images/logotoko.png') }}" class="w-20 h-20 opacity-50">
        </div>
        <h1 class="text-6xl font-black text-[#2d6a4f] mb-4">Waduh!</h1>
        <p class="text-gray-600 mb-8 font-medium">
            Halaman yang lo cari nggak ada, atau mungkin link reset password lo udah kadaluwarsa.
        </p>
        <a href="{{ route('login') }}" class="inline-block bg-[#2d6a4f] text-white px-8 py-3 rounded-2xl font-bold hover:bg-[#1b4332] transition-all">
            Balik ke Login
        </a>
    </div>
</body>
</html>
