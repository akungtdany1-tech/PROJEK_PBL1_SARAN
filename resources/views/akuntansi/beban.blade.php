<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Akuntansi</span>
            <span>›</span>
            <span class="text-gray-600 font-medium">Beban Operasional</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800">Beban Operasional</h2>
        <p class="text-xs text-gray-400 mb-6">Catat pengeluaran operasional toko (gaji, sewa, listrik, dll)</p>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('akuntansi.beban.store') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Beban</label>
                <select name="id_akun" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f] bg-white">
                    <option value="">— Pilih Jenis Beban —</option>
                    @foreach($akunBeban as $a)
                        <option value="{{ $a->id_akun }}">{{ $a->kode_akun }} — {{ $a->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nama Beban / Keterangan</label>
                <input type="text" name="nama_beban" value="{{ old('nama_beban') }}" required maxlength="150"
                       placeholder="cth: Gaji karyawan bulan September"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Jumlah (Rp)</label>
                <input type="number" name="jumlah" value="{{ old('jumlah') }}" required min="1"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
            </div>
            <div class="md:col-span-2 xl:col-span-1 flex items-end">
                <button type="submit" class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition w-full">
                    Simpan Beban
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Riwayat Beban</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                        <th class="px-6 py-3 rounded-l-lg">Tanggal</th>
                        <th class="px-4 py-3">Jenis Beban</th>
                        <th class="px-4 py-3">Nama Beban</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-right">Jumlah</th>
                        <th class="px-4 py-3">Oleh</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($beban as $b)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->tanggal)->format('d M Y') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-mono text-xs text-gray-400">{{ $b->akun->kode_akun }}</span>
                                {{ $b->akun->nama_akun }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $b->nama_beban }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $b->keterangan }}</td>
                            <td class="px-4 py-3 text-right font-bold tabular-nums text-red-600">Rp {{ number_format($b->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $b->user->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('akuntansi.beban.destroy', $b->id_beban) }}" onsubmit="return confirm('Hapus beban {{ $b->nama_beban }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada beban operasional.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>