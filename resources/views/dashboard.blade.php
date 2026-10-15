<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Dashboard</h2>
                <p class="text-sm text-gray-500">Selamat datang kembali, <span class="text-[#2d6a4f] font-semibold">{{ auth()->user()->name }}</span>!</p>
            </div>

            {{-- Filter Periode Global Dashboard --}}
            <div class="flex flex-wrap items-center gap-2 bg-white px-4 py-2 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs font-semibold text-gray-500 mr-1">Periode:</span>
                <a href="?filter=bulan_ini" class="px-3 py-1 text-xs rounded-lg border {{ $filter == 'bulan_ini' ? 'bg-[#2d6a4f] text-white border-[#2d6a4f]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#2d6a4f]' }} transition">Bulan ini</a>
                <a href="?filter=3_bulan" class="px-3 py-1 text-xs rounded-lg border {{ $filter == '3_bulan' ? 'bg-[#2d6a4f] text-white border-[#2d6a4f]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#2d6a4f]' }} transition">3 bulan</a>
                <a href="?filter=1_tahun" class="px-3 py-1 text-xs rounded-lg border {{ $filter == '1_tahun' ? 'bg-[#2d6a4f] text-white border-[#2d6a4f]' : 'bg-white text-gray-600 border-gray-200 hover:border-[#2d6a4f]' }} transition">1 tahun</a>

                <form method="GET" action="{{ route('dashboard') }}" class="inline-flex items-center ml-2">
                    <select name="bulan" id="bulanSelect"
                            class="text-xs border border-gray-200 rounded-lg px-2.5 py-1 focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                        @foreach($opsibulan as $ob)
                            <option value="{{ $ob['bulan'] }}"
                                    {{ $bulan == $ob['bulan'] && $tahun == $ob['tahun'] ? 'selected' : '' }}
                                    data-tahun="{{ $ob['tahun'] }}">
                                {{ $ob['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="tahun" id="tahunInput" value="{{ $tahun }}">
                </form>
            </div>
        </div>
    </x-slot>

    {{-- 4 Kartu Statistik --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-lg p-5 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-gray-500 font-medium">Total Transaksi Periode</p>
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#2d6a4f]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $totalTransaksiHariIni }}</p>
            <p class="text-xs text-gray-400 mt-1">Transaksi</p>
        </div>

        <div class="bg-white rounded-lg p-5 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-gray-500 font-medium">Total Penjualan Periode</p>
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <span class="text-[#2d6a4f] font-bold text-xs">Rp</span>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Pendapatan</p>
        </div>

        <div class="bg-white rounded-lg p-5 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-gray-500 font-medium">Produk Terjual Periode</p>
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#2d6a4f]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $produkTerjualHariIni }}</p>
            <p class="text-xs text-gray-400 mt-1">Item</p>
        </div>

        <div class="bg-white rounded-lg p-5 border border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-gray-500 font-medium">Stok Menipis</p>
                <div class="w-8 h-8 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $stokMenipis }}</p>
            <p class="text-xs text-gray-400 mt-1">Produk</p>
            @if(auth()->user()->role === 'pemilik' || auth()->user()->role === 'pemilik2')
            <a href="{{ route('produk.index', ['sort' => 'menipis']) }}"
            class="mt-3 inline-block px-4 py-2 bg-[#2d6a4f] text-white text-sm font-semibold rounded-lg hover:bg-[#1b4332] transition">
            Cek Stok Detail
            </a>
            @endif
        </div>

    </div>

    {{-- Grafik + Produk Terlaris --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Grafik --}}
        <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-700">Grafik Penjualan</h3>
            </div>
            <canvas id="grafikPenjualan" height="120"></canvas>
        </div>

        {{-- Produk Terlaris --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="font-bold text-gray-700 mb-4">Produk Terlaris Periode</h3>
            <div class="space-y-3">
                @forelse($produkTerlaris as $index => $p)
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold bg-gray-100 text-gray-600">
                        {{ $index + 1 }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $p->nama_produk }}</p>
                        <p class="text-xs text-gray-400">Terjual {{ $p->total_terjual }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Transaksi Terakhir --}}
    <div class="bg-white rounded-lg border border-gray-200 p-5 mb-6">
        <h3 class="font-bold text-gray-700 mb-4">Transaksi Terakhir</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-gray-400 text-xs border-b">
                    <th class="pb-2 text-left">Pelanggan</th>
                    <th class="pb-2 text-left">Produk</th>
                    <th class="pb-2 text-left">Tanggal</th>
                    <th class="pb-2 text-left">Total</th>
                    <th class="pb-2 text-left">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksiTerakhir as $t)
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="py-3 text-gray-700">{{ $t->pelanggan->nama_pelanggan ?? '-' }}</td>
                    <td class="py-3 text-gray-700">{{ $t->detail->first()->produk->nama_produk ?? '-' }}</td>
                    <td class="py-3 text-gray-500">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                    <td class="py-3 text-gray-700 font-medium">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    <td class="py-3 text-gray-400">{{ \Carbon\Carbon::parse($t->created_at)->format('H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-gray-400">Belum ada transaksi hari ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($grafik->pluck('tanggal'));
        const data   = @json($grafik->pluck('total'));

        new Chart(document.getElementById('grafikPenjualan'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: data,
                    borderColor: '#2d6a4f',
                    backgroundColor: 'rgba(45,106,79,0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#2d6a4f',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: val => 'Rp ' + new Intl.NumberFormat('id-ID').format(val)
                        }
                    }
                }
            }
        });

        document.getElementById('bulanSelect').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            document.getElementById('tahunInput').value = selected.dataset.tahun;
            this.form.submit();
        });
    </script>

</x-app-layout>