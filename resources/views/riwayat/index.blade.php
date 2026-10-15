<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Riwayat Aktivitas</span>
        </div>
    </x-slot>

    @php
        $badgeAksi = [
            'penjualan_buat'   => ['Penjualan Dibuat', 'bg-green-100 text-green-700'],
            'penjualan_hapus'  => ['Penjualan Dihapus', 'bg-red-100 text-red-700'],
            'pembelian_buat'   => ['Pembelian Dibuat', 'bg-blue-100 text-blue-700'],
            'pembelian_hapus'  => ['Pembelian Dihapus', 'bg-red-100 text-red-700'],
            'login'            => ['Login', 'bg-gray-100 text-gray-600'],
            'logout'           => ['Logout', 'bg-gray-100 text-gray-600'],
            'kategori_buat'    => ['Kategori Dibuat', 'bg-amber-100 text-amber-700'],
            'kategori_ubah'    => ['Kategori Diubah', 'bg-yellow-100 text-yellow-700'],
            'kategori_hapus'   => ['Kategori Dihapus', 'bg-red-100 text-red-700'],
            'kategori_pulihkan'=> ['Kategori Dipulihkan', 'bg-teal-100 text-teal-700'],
            'kategori_hapus_permanen'=> ['Kategori Dihapus Permanen', 'bg-red-200 text-red-800'],
            'produk_buat'      => ['Produk Dibuat', 'bg-green-100 text-green-700'],
            'produk_hapus'     => ['Produk Dihapus', 'bg-red-100 text-red-700'],
            'diskon_buat'      => ['Diskon Dibuat', 'bg-purple-100 text-purple-700'],
            'diskon_hapus'     => ['Diskon Dihapus', 'bg-red-100 text-red-700'],
        ];
        $badgeRole = [
            'pemilik'  => 'bg-green-100 text-green-700',
            'pemilik2' => 'bg-blue-100 text-blue-700',
            'kasir'    => 'bg-amber-100 text-amber-700',
        ];
    @endphp

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Riwayat Aktivitas</h2>
            <p class="text-xs text-gray-400">Jejak siapa melakukan apa — transaksi & login</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4">{{ session('error') }}</div>
    @endif

    <!-- Filter -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <div class="flex gap-2 mb-3 overflow-x-auto">
            <a href="{{ route('riwayat.index') }}" class="px-3 py-1 text-xs font-semibold rounded-lg {{ !$kejadian ? 'bg-[#2d6a4f] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Semua</a>
            <a href="{{ route('riwayat.index', ['kejadian'=>'penjualan']) }}" class="px-3 py-1 text-xs font-semibold rounded-lg {{ $kejadian == 'penjualan' ? 'bg-[#2d6a4f] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Penjualan</a>
            <a href="{{ route('riwayat.index', ['kejadian'=>'pembelian']) }}" class="px-3 py-1 text-xs font-semibold rounded-lg {{ $kejadian == 'pembelian' ? 'bg-[#2d6a4f] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Pembelian</a>
            <a href="{{ route('riwayat.index', ['kejadian'=>'login']) }}" class="px-3 py-1 text-xs font-semibold rounded-lg {{ $kejadian == 'login' ? 'bg-[#2d6a4f] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Login</a>
            <a href="{{ route('riwayat.index', ['kejadian'=>'data_master']) }}" class="px-3 py-1 text-xs font-semibold rounded-lg {{ $kejadian == 'data_master' ? 'bg-[#2d6a4f] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Data Master</a>
        </div>
        <form method="GET" action="{{ route('riwayat.index') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
                <input type="date" name="dari" value="{{ $dari }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                <input type="date" name="sampai" value="{{ $sampai }}"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Kejadian</label>
                <select name="kejadian" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f] bg-white">
                    <option value="">Semua</option>
                    <option value="penjualan" {{ $kejadian === 'penjualan' ? 'selected' : '' }}>Penjualan</option>
                    <option value="pembelian" {{ $kejadian === 'pembelian' ? 'selected' : '' }}>Pembelian</option>
                    <option value="login" {{ $kejadian === 'login' ? 'selected' : '' }}>Login / Logout</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                <input type="text" name="cari" value="{{ $cari }}" placeholder="cth: Penjualan #12"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Filter
                </button>
                @if($dari || $sampai || $kejadian || $cari)
                    <a href="{{ route('riwayat.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700 px-3 py-2.5">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Riwayat -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Semua Aktivitas <span class="text-xs font-normal text-gray-400">({{ $riwayat->count() }})</span></h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                        <th class="px-6 py-3 rounded-l-lg">Aksi</th>
                        <th class="px-4 py-3">Objek</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Waktu Aksi</th>
                        <th class="px-4 py-3">Oleh</th>
                        <th class="px-4 py-3">IP</th>
                        <th class="px-4 py-3 rounded-r-lg"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayat as $r)
                        @php [$namaAksi, $warnaAksi] = $badgeAksi[$r->aksi] ?? [$r->aksi, 'bg-gray-100 text-gray-600']; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 whitespace-nowrap">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $warnaAksi }}">{{ $namaAksi }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ $r->label }}</div>
                                <details class="text-xs text-gray-400 mt-0.5">
                                    <summary class="cursor-pointer hover:text-[#2d6a4f]">detail</summary>
                                    <div class="mt-1 text-gray-500 space-y-0.5">
                                        @if($r->detail)
                                            @if(isset($r->detail['jumlah_item'])) <div>Jumlah item: {{ $r->detail['jumlah_item'] }}</div> @endif
                                            @if(isset($r->detail['metode'])) <div>Metode bayar: {{ $r->detail['metode'] }}</div> @endif
                                            @if(isset($r->detail['id_pelanggan'])) <div>Pelanggan ID: {{ $r->detail['id_pelanggan'] }}</div> @endif
                                            @if(isset($r->detail['id_suplier'])) <div>Suplier ID: {{ $r->detail['id_suplier'] }}</div> @endif
                                            @if(!empty($r->detail['catatan'])) <div>Catatan: {{ $r->detail['catatan'] }}</div> @endif
                                        @else
                                            <div>—</div>
                                        @endif
                                    </div>
                                </details>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums {{ $r->total > 0 ? 'font-bold text-gray-800' : 'text-gray-300' }}">
                                {{ $r->total > 0 ? 'Rp ' . number_format($r->total, 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap text-gray-600">
                                {{ \Carbon\Carbon::parse($r->created_at)->format('d M Y, H:i') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-medium text-gray-700">{{ $r->user->name ?? '—' }}</span>
                                @if($r->role)
                                    <span class="ml-1 inline-block text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeRole[$r->role] ?? 'bg-gray-100 text-gray-600' }}">{{ $r->role }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $r->ip_address ?? '—' }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                Belum ada aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>