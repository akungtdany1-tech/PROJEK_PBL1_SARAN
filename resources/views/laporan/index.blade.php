<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Laporan</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg border border-gray-200 p-6">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Laporan {{ $tab === 'pembelian' ? 'Pembelian' : 'Penjualan' }}
                </h2>
                <p class="text-xs text-gray-400">
                    Kelola dan pantau laporan {{ $tab === 'pembelian' ? 'pembelian' : 'penjualan' }} toko
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(auth()->user()->role == 'pemilik' || auth()->user()->role == 'kasir')
                <a href="{{ route('laporan.export', ['dari' => $dari, 'sampai' => $sampai, 'tab' => $tab]) }}"
                   class="flex items-center gap-2 bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    </svg>
                    Export Excel
                </a>
                @endif

                <button onclick="window.print()"
                        class="flex items-center gap-2 bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Laporan
                </button>
            </div>
        </div>

        {{-- Filter Periode --}}
        <form method="GET" action="{{ route('laporan.index') }}" class="mb-6" id="formLaporan">
            {{-- Simpan tab aktif saat filter --}}
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Periode Tanggal</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="dari" id="inputDari" value="{{ $dari }}"
                               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                        <span class="text-gray-400 text-sm">—</span>
                        <input type="date" name="sampai" id="inputSampai" value="{{ $sampai }}"
                               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                    </div>
                </div>
                <button type="submit"
                        class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Filter
                </button>
            </div>

            {{-- Tombol Shortcut Periode --}}
            <div class="flex flex-wrap gap-2 mt-3">
                <button type="button" onclick="setPeriode('bulan-ini')"
                        class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition">
                    Bulan Ini
                </button>
                <button type="button" onclick="setPeriode('3-bulan')"
                        class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition">
                    3 Bulan Terakhir
                </button>
                <button type="button" onclick="setPeriode('1-tahun')"
                        class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition">
                    1 Tahun Terakhir
                </button>
            </div>
        </form>

        <script>
        function setPeriode(tipe) {
            const dari   = document.getElementById('inputDari');
            const sampai = document.getElementById('inputSampai');
            const today  = new Date();
            let start    = new Date();

            if (tipe === 'bulan-ini') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
            } else if (tipe === '3-bulan') {
                start = new Date(today.getFullYear(), today.getMonth() - 3, today.getDate());
            } else if (tipe === '1-tahun') {
                start = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate());
            }

            dari.value   = start.toISOString().split('T')[0];
            sampai.value = today.toISOString().split('T')[0];
            document.getElementById('formLaporan').submit();
        }
        </script>

        {{-- Statistik --}}
        <div class="grid grid-cols-2 {{ auth()->user()->role == 'pemilik' ? 'lg:grid-cols-4' : 'lg:grid-cols-2' }} gap-4 mb-6">

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#2d6a4f]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-gray-600">Total Penjualan</p>
                </div>
                <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">Total semua transaksi</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#2d6a4f]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-gray-600">Total Transaksi</p>
                </div>
                <p class="text-xl font-bold text-gray-800">{{ $totalTransaksi }}</p>
                <p class="text-xs text-gray-400 mt-1">Jumlah transaksi</p>
            </div>

            @if(auth()->user()->role == 'pemilik')
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#2d6a4f]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-gray-600">Total Pembelian</p>
                </div>
                <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">Total pengeluaran</p>
            </div>

            <div class="border border-gray-200 rounded-lg p-5 {{ $labaBersih >= 0 ? 'bg-white' : 'bg-red-50 border-red-100' }}">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 {{ $labaBersih >= 0 ? 'text-[#2d6a4f]' : 'text-red-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-medium text-gray-600">Laba Bersih</p>
                </div>
                <p class="text-xl font-bold {{ $labaBersih >= 0 ? 'text-[#2d6a4f]' : 'text-red-500' }}">
                    {{ $labaBersih < 0 ? '-' : '' }}Rp {{ number_format(abs($labaBersih), 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $labaBersih >= 0 ? 'Untung' : 'Rugi' }} periode ini</p>
            </div>
            @endif

        </div>

        {{-- TAB PENJUALAN / PEMBELIAN --}}
        <div class="flex gap-1 mb-5 bg-gray-100 p-1 rounded-lg w-fit">
            <a href="{{ route('laporan.index', ['dari' => $dari, 'sampai' => $sampai, 'tab' => 'penjualan']) }}"
               class="px-5 py-2 rounded-lg text-sm font-semibold transition
                      {{ $tab === 'penjualan' ? 'bg-white text-[#2d6a4f] shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Penjualan
            </a>
            @if(auth()->user()->role == 'pemilik')
            <a href="{{ route('laporan.index', ['dari' => $dari, 'sampai' => $sampai, 'tab' => 'pembelian']) }}"
               class="px-5 py-2 rounded-lg text-sm font-semibold transition
                      {{ $tab === 'pembelian' ? 'bg-white text-[#2d6a4f] shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                Pembelian
            </a>
            @endif
        </div>

        {{-- Tabel Data Penjualan --}}
        @if($tab === 'penjualan')
        <h3 class="font-bold text-gray-700 mb-3">Data Penjualan</h3>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs">
                    <th class="px-4 py-3 text-left font-semibold rounded-l-lg">No</th>
                    <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
                    <th class="px-4 py-3 text-left font-semibold">Pelanggan</th>
                    <th class="px-4 py-3 text-left font-semibold">Produk</th>
                    <th class="px-4 py-3 text-left font-semibold">Total</th>
                    <th class="px-4 py-3 text-left font-semibold rounded-r-lg">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $i => $t)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-500">{{ $transaksi->firstItem() + $i }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $t->pelanggan->nama_pelanggan ?? 'Umum' }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $t->detail->first()->produk->nama_produk ?? '-' }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('transaksi.show', $t->id_transaksi) }}"
                           class="flex items-center gap-1 text-xs text-[#2d6a4f] font-semibold bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition w-fit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-400">Belum ada data penjualan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Tabel Data Pembelian --}}
       
        {{-- Tabel Data Pembelian --}}
@else
<h3 class="font-bold text-gray-700 mb-3">Data Pembelian</h3>
<div class="overflow-x-auto">
<table class="w-full text-sm">
    <thead>
        <tr class="bg-gray-50 text-gray-500 text-xs">
            <th class="px-4 py-3 text-left font-semibold rounded-l-lg">No</th>
            <th class="px-4 py-3 text-left font-semibold">Tanggal</th>
            <th class="px-4 py-3 text-left font-semibold">Supplier</th>
            <th class="px-4 py-3 text-left font-semibold">Produk</th>
            <th class="px-4 py-3 text-left font-semibold">Total</th>
            <th class="px-4 py-3 text-left font-semibold">Petugas</th>  {{-- TAMBAH INI --}}
            <th class="px-4 py-3 text-left font-semibold rounded-r-lg">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transaksi as $i => $t)
        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
            <td class="px-4 py-3 text-gray-500">{{ $transaksi->firstItem() + $i }}</td>
            <td class="px-4 py-3 text-gray-700">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
            <td class="px-4 py-3 text-gray-700">{{ $t->suplier->nama_suplier ?? '-' }}</td>
            <td class="px-4 py-3 text-gray-700">{{ $t->detail->first()->produk->nama_produk ?? '-' }}</td>
            <td class="px-4 py-3 font-medium text-gray-800">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
            <td class="px-4 py-3 text-gray-700">{{ $t->user->name ?? '-' }}</td>  {{-- TAMBAH INI --}}
            <td class="px-4 py-3">
                <a href="{{ route('pembelian.show', $t->id_transaksi) }}"
                   class="flex items-center gap-1 text-xs text-[#2d6a4f] font-semibold bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition w-fit">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Detail
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="py-10 text-center text-gray-400">Belum ada data pembelian</td>  {{-- colspan jadi 7 --}}
        </tr>
        @endforelse
    </tbody>
</table>
</div>
@endif

        {{-- Pagination --}}
        <div class="flex items-center justify-between mt-4">
            <p class="text-xs text-gray-400">
                Menampilkan {{ $transaksi->firstItem() ?? 0 }} - {{ $transaksi->lastItem() ?? 0 }} dari {{ $transaksi->total() }} transaksi
            </p>
            <div class="flex items-center gap-1">
                @if($transaksi->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $transaksi->previousPageUrl() }}&dari={{ $dari }}&sampai={{ $sampai }}&tab={{ $tab }}"
                       class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @endif

                @if($transaksi->hasMorePages())
                    <a href="{{ $transaksi->nextPageUrl() }}&dari={{ $dari }}&sampai={{ $sampai }}&tab={{ $tab }}"
                       class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                @endif
            </div>
        </div>

    </div>

    <style>
        @media print {
            aside, header, .no-print, nav, button, form, a[href] { display: none !important; }
            main { padding: 0 !important; margin: 0 !important; }
            .bg-white { box-shadow: none !important; border: none !important; }
            .rounded-lg { border-radius: 0 !important; }
        }
    </style>

</x-app-layout>