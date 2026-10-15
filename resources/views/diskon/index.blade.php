<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Diskon</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Data Diskon</h2>
                <p class="text-xs text-gray-400">Kelola promo dan diskon produk</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('diskon.create') }}" class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                    + Tambah
                </a>
                <a href="{{ route('diskon.sampah') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 shadow-md transition">
                    Sampah
                </a>
            </div>
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
                    <th class="pb-3 text-left font-semibold">Nama Diskon</th>
                    <th class="pb-3 text-left font-semibold">Besar</th>
                    <th class="pb-3 text-left font-semibold">Lokasi</th>
                    <th class="pb-3 text-left font-semibold">Pelanggan</th>
                    <th class="pb-3 text-left font-semibold">Min. Beli Eceran</th>
                    <th class="pb-3 text-left font-semibold">Min. Beli Grosir</th>
                    <th class="pb-3 text-left font-semibold">Periode</th>
                    <th class="pb-3 text-left font-semibold">Produk</th>
                    <th class="pb-3 text-left font-semibold">Status</th>
                    <th class="pb-3 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($diskon as $d)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="py-3 font-medium text-gray-800">{{ $d->nama_diskon }}</td>
                    <td class="py-3 text-gray-600">{{ $d->besar_diskon }}%</td>
                    <td class="py-3">
                        @if($d->lokasi_berlaku === 'semua')
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold">Semua</span>
                        @elseif($d->lokasi_berlaku === 'gudang')
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold">Gudang</span>
                        @else
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">Toko</span>
                        @endif
                    </td>
                    <td class="py-3">
                        @if($d->id_pelanggan)
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold">
                                {{ $d->pelanggan->nama_pelanggan ?? '-' }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">Semua</span>
                        @endif
                    </td>
                    <td class="py-3 text-gray-600">{{ $d->minimal_beli }} pcs</td>
                    <td class="py-3 text-gray-600">{{ $d->minimal_beli_grosir }} pcs</td>
                    <td class="py-3 text-gray-500 text-xs">
                        {{ $d->mulai_tgl->format('d/m/Y') }} — {{ $d->selesai_tgl->format('d/m/Y') }}
                    </td>
                    <td class="py-3 text-gray-600">
                        <div class="flex flex-wrap gap-1">
                            @foreach($d->produk as $p)
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-lg text-xs">{{ $p->nama_produk }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="py-3">
                        @if($d->isAktifHariIni())
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">Aktif</span>
                        @elseif(!$d->is_aktif)
                            <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs font-semibold">Nonaktif</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded-lg text-xs font-semibold">Belum/Kadaluarsa</span>
                        @endif
                    </td>
                    <td class="py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('diskon.edit', $d->id_diskon) }}"
                               class="w-8 h-8 bg-yellow-100 hover:bg-yellow-200 rounded-lg flex items-center justify-center transition">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('diskon.destroy', $d->id_diskon) }}" method="POST"
                                  onsubmit="return confirm('Yakin hapus diskon ini?')">
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
                    <td colspan="10" class="py-10 text-center text-gray-400">Belum ada data diskon</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-app-layout>