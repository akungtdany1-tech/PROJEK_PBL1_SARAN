<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400"><a href="{{ route('produk.index') }}" class="hover:text-[#2d6a4f]">Produk</a><span>›</span><span class="text-gray-600 font-medium">Sampah</span></div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5"><div><h2 class="text-xl font-bold text-gray-800">Sampah Produk</h2><p class="text-xs text-gray-400">Cek detail produk sebelum dipulihkan atau dihapus permanen.</p></div><a href="{{ route('produk.index') }}" class="text-sm font-semibold text-[#2d6a4f]">← Kembali</a></div>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b border-gray-100 text-left text-xs text-gray-500"><th class="pb-3">Kode / Produk</th><th class="pb-3">Kategori</th><th class="pb-3">Harga</th><th class="pb-3">Stok Gudang / Toko</th><th class="pb-3">Dihapus Pada</th><th class="pb-3 text-right">Aksi</th></tr></thead><tbody>
            @forelse($sampah as $s)
                <tr class="border-b border-gray-50 hover:bg-gray-50"><td class="py-3"><p class="font-medium text-gray-800">{{ $s->nama_produk }}</p><p class="text-xs text-gray-400">{{ $s->kode_produk }}</p></td><td class="py-3 text-gray-600">{{ $s->kategori->nama_kategori ?? 'Kategori tidak tersedia' }}</td><td class="py-3 text-gray-600"><div>Eceran: Rp {{ number_format($s->harga_satuan, 0, ',', '.') }}</div><div class="text-xs">Grosir: Rp {{ number_format($s->harga_grosir ?? 0, 0, ',', '.') }}</div></td><td class="py-3 text-gray-600">{{ $s->stok_gudang }} dus / {{ $s->stok_toko }} pcs</td><td class="py-3 text-gray-500">{{ $s->deleted_at?->format('d M Y, H:i') }}</td><td class="py-3"><div class="flex justify-end gap-2"><form action="{{ route('produk.pulihkan', $s->id_produk) }}" method="POST">@csrf<button class="px-3 py-1.5 bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-xs font-semibold rounded-lg">Pulihkan</button></form><form action="{{ route('produk.hapusPermanen', $s->id_produk) }}" method="POST" onsubmit="return confirm('Hapus produk ini secara permanen? Tindakan ini tidak dapat dibatalkan.')">@csrf @method('DELETE')<button class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg">Hapus Permanen</button></form></div></td></tr>
            @empty
                <tr><td colspan="6" class="py-10 text-center text-gray-400">Sampah produk masih kosong.</td></tr>
            @endforelse
        </tbody></table></div>
    </div>
</x-app-layout>
