<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('kategori.index') }}" class="hover:text-[#2d6a4f]">Kategori</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Sampah</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Sampah Kategori</h2>
                <p class="text-xs text-gray-400">Kategori yang dihapus dapat dipulihkan atau dihapus permanen.</p>
            </div>
            <a href="{{ route('kategori.index') }}" class="text-sm font-semibold text-[#2d6a4f] hover:text-[#1b4332]">← Kembali</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100 text-left text-xs text-gray-500"><th class="pb-3">Nama Kategori</th><th class="pb-3">Status Sebelum Dihapus</th><th class="pb-3">Dihapus Pada</th><th class="pb-3 text-right">Aksi</th></tr></thead>
                <tbody>
                    @forelse($sampah as $s)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="py-3 font-medium text-gray-800">{{ $s->nama_kategori }}</td>
                            <td class="py-3"><span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $s->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($s->status) }}</span></td>
                            <td class="py-3 text-gray-500">{{ $s->deleted_at?->format('d M Y, H:i') }}</td>
                            <td class="py-3"><div class="flex justify-end gap-2"><form action="{{ route('kategori.pulihkan', $s->id_kategori) }}" method="POST">@csrf<button class="px-3 py-1.5 bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-xs font-semibold rounded-lg">Pulihkan</button></form><form action="{{ route('kategori.hapusPermanen', $s->id_kategori) }}" method="POST" onsubmit="return confirm('Hapus kategori ini secara permanen? Tindakan ini tidak dapat dibatalkan.')">@csrf @method('DELETE')<button class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg">Hapus Permanen</button></form></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-10 text-center text-gray-400">Sampah kategori masih kosong.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
