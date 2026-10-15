<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span>Pengaturan</span>
            <span>›</span>
            <span class="text-gray-600 font-medium">Akun Suplier</span>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form Tambah Suplier --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-1">Tambah Suplier</h3>
            <p class="text-xs text-gray-400 mb-5">Catat informasi suplier baru</p>

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pengaturan.suplier.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama </label>
                        <input type="text" name="nama_suplier" value="{{ old('nama_suplier') }}"
                               placeholder="Nama suplier"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                               placeholder="08xxxxxxxxxx"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="alamat" rows="3"
                                  placeholder="Alamat lengkap suplier"
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">{{ old('alamat') }}</textarea>
                    </div>
                    <button type="submit"
                            class="w-full bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold py-2.5 rounded-xl transition">
                        Tambah Suplier
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabel Daftar Suplier --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-gray-800 mb-1">Daftar Suplier</h3>
                    <p class="text-xs text-gray-400 mb-0">Kelola semua informasi suplier</p>
                </div>
                <a href="{{ route('pengaturan.suplier.sampah') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 shadow-md transition">
                    Sampah
                </a>
            </div>

            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-500 text-xs">
                        <th class="pb-3 text-left font-semibold">No</th>
                        <th class="pb-3 text-left font-semibold">Nama</th>
                        <th class="pb-3 text-left font-semibold">No. HP</th>
                        <th class="pb-3 text-left font-semibold">Alamat</th>
                        <th class="pb-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supliers as $i => $s)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                        <td class="py-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="py-3 font-medium text-gray-800">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-[#2d6a4f] flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($s->nama_suplier, 0, 1)) }}
                                </div>
                                {{ $s->nama_suplier }}
                            </div>
                        </td>
                        <td class="py-3 text-gray-500">{{ $s->no_hp }}</td>
                        <td class="py-3 text-gray-500 max-w-xs truncate">{{ $s->alamat }}</td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pengaturan.suplier.edit', $s->id_suplier) }}"
                                   class="w-8 h-8 bg-yellow-100 hover:bg-yellow-200 rounded-lg flex items-center justify-center transition">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('pengaturan.suplier.destroy', $s->id_suplier) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus suplier ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-8 h-8 bg-red-100 hover:bg-red-200 rounded-lg flex items-center justify-center transition">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400">Belum ada data suplier</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
</x-app-layout>