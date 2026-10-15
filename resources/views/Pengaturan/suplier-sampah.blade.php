<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span>Pengaturan</span>
            <span>›</span>
            <span class="text-gray-600 font-medium">Suplier</span>
            <span>›</span>
            <span class="text-gray-600 font-medium">Sampah</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Sampah Suplier</h2>
                <p class="text-xs text-gray-400">Data suplier yang dihapus</p>
            </div>
            <a href="{{ route('pengaturan.suplier') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-500 text-white text-xs font-bold rounded-lg hover:bg-gray-600 shadow-md transition">
                ← Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-gray-500 text-xs">
                    <th class="pb-3 text-left font-semibold">No</th>
                    <th class="pb-3 text-left font-semibold">Nama</th>
                    <th class="pb-3 text-left font-semibold">No. HP</th>
                    <th class="pb-3 text-left font-semibold">alamat</th>
                    <th class="pb-3 text-left font-semibold">Dihapus Pada</th>
                    <th class="pb-3 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sampah as $s)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="py-3 text-gray-500">{{ $loop->iteration }}</td>
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
                    <td class="py-3 text-gray-500 text-xs">{{ $s->deleted_at?->format('d/m/Y H:i') }}</td>
                    <td class="py-3">
                        <div class="flex items-center gap-2">
                            <form action="{{ route('pengaturan.suplier.pulihkan', $s->id_suplier) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-[#2d6a4f] text-white text-xs font-semibold rounded-lg hover:bg-[#1b4332] transition" onclick="return confirm('Yakin ingin memulihkan suplier ini?')">
                                    Pulihkan
                                </button>
                            </form>
                            <form action="{{ route('pengaturan.suplier.hapusPermanen', $s->id_suplier) }}" method="POST" class="inline" onsubmit="return confirm('Hapus permanen? Tidak bisa di-undo.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition">
                                    Hapus Permanen
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-400">Tidak ada suplier di sampah</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-app-layout>