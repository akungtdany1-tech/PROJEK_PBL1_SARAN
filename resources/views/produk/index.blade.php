<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Produk</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

        <div class="flex items-center justify-between mb-1">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Data Produk</h2>
                <p class="text-xs text-gray-400">Kelola data produk toko</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('produk.create') }}" class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                    + Tambah
                </a>
                <a href="{{ route('produk.sampah') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 shadow-md transition">
                    Sampah
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->has('transfer'))
            <div class="mt-3 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
                {{ $errors->first('transfer') }}
            </div>
        @endif

        <form method="GET" action="{{ route('produk.index') }}" class="flex items-center justify-between mt-4 mb-5 gap-3">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-600">Kategori</label>
                    <select name="kategori" onchange="this.form.submit()"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori as $k)
                            <option value="{{ $k->id_kategori }}" {{ request('kategori') == $k->id_kategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-600">Urutkan</label>
                    <select name="sort" onchange="this.form.submit()"
                            class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                        <option value="">Default</option>
                        <option value="stok_toko_asc" {{ request('sort') == 'stok_toko_asc' ? 'selected' : '' }}>Stok Toko Terkecil</option>
                        <option value="stok_gudang_asc" {{ request('sort') == 'stok_gudang_asc' ? 'selected' : '' }}>Stok Gudang Terkecil</option>
                        <option value="stok_toko_desc" {{ request('sort') == 'stok_toko_desc' ? 'selected' : '' }}>Stok Toko Terbesar</option>
                        <option value="stok_gudang_desc" {{ request('sort') == 'stok_gudang_desc' ? 'selected' : '' }}>Stok Gudang Terbesar</option>
                        <option value="menipis" {{ request('sort') == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                    </select>
                </div>

                <button type="button" onclick="bukaModalPilihProdukTransfer()"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Transfer Stok
                </button>
            </div>

            <div class="flex items-center gap-2 border border-gray-200 rounded-xl px-3 py-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari produk"
                       class="text-sm outline-none w-48">
                <button type="submit">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                    </svg>
                </button>
            </div>
        </form>

        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-gray-500 text-xs">
                    <th class="pb-3 text-left font-semibold">Kode</th>
                    <th class="pb-3 text-left font-semibold">Nama Produk</th>
                    <th class="pb-3 text-left font-semibold">Kategori</th>
                    <th class="pb-3 text-left font-semibold">Harga Eceran</th>
                    <th class="pb-3 text-left font-semibold">Harga Grosir</th>
                    <th class="pb-3 text-left font-semibold">Stok Gudang</th>
                    <th class="pb-3 text-left font-semibold">Stok Toko</th>
                    <th class="pb-3 text-left font-semibold">Isi/Dus</th>
                    <th class="pb-3 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produk as $p)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="py-3 text-gray-500">{{ $p->kode_produk }}</td>
                    <td class="py-3 font-medium text-gray-800">{{ $p->nama_produk }}</td>
                    <td class="py-3 text-gray-600">{{ $p->kategori->nama_kategori ?? '-' }}</td>
                    <td class="py-3 text-gray-600">Rp {{ number_format($p->harga_satuan, 0, ',', '.') }}</td>
                    <td class="py-3 text-gray-600">Rp {{ number_format($p->harga_grosir ?? 0, 0, ',', '.') }}</td>
                    
                    {{-- Stok Gudang dengan penanda kritis --}}
                    <td class="py-3">
                        <span class="px-2 py-1 rounded-lg text-xs font-semibold
                            {{ $p->stok_gudang <= 5 ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700' }}">
                            {{ $p->stok_gudang }} dus
                            @if($p->stok_gudang <= 5) <span class="ml-1 font-bold">(!)</span> @endif
                        </span>
                    </td>

                    {{-- Stok Toko dengan penanda kritis --}}
                    <td class="py-3">
                        <span class="px-2 py-1 rounded-lg text-xs font-semibold
                            {{ $p->stok_toko <= 10 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-700' }}">
                            {{ $p->stok_toko }} pcs
                            @if($p->stok_toko <= 10) <span class="ml-1 font-bold">(!)</span> @endif
                        </span>
                    </td>

                    <td class="py-3 text-gray-600">{{ $p->isi_per_dus }} pcs</td>
                    <td class="py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('produk.edit', $p->id_produk) }}"
                               class="w-8 h-8 bg-yellow-100 hover:bg-yellow-200 rounded-lg flex items-center justify-center transition">
                                <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('produk.destroy', $p->id_produk) }}" method="POST"
                                  onsubmit="return confirm('Yakin hapus produk ini?')">
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
                    <td colspan="9" class="py-10 text-center text-gray-400">Belum ada data produk</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- MODAL 1: PILIH PRODUK --}}
    {{-- (Bagian Modal tetap sama seperti kode sebelumnya) --}}
    <div id="modalPilihProdukTransfer" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 p-6 flex flex-col max-h-[85vh]">
             <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Pilih Produk untuk Transfer</h3>
                    <p class="text-xs text-gray-400">Gudang → Toko</p>
                </div>
                <button onclick="tutupModalPilihProdukTransfer()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex gap-2 mb-4">
                <input type="text" id="searchTransfer" placeholder="Cari nama produk..." oninput="filterTransfer()" class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select id="filterKategoriTransfer" onchange="filterTransfer()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($produk->groupBy('id_kategori') as $idKat => $produkPerKat)
                        <option value="{{ $idKat }}">{{ $produkPerKat->first()->kategori->nama_kategori ?? '-' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="overflow-y-auto flex-1">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-white">
                        <tr class="border-b border-gray-100 text-gray-500 text-xs">
                            <th class="pb-2 text-left font-semibold">Nama Produk</th>
                            <th class="pb-2 text-left font-semibold">Kategori</th>
                            <th class="pb-2 text-left font-semibold">Stok Gudang</th>
                            <th class="pb-2 text-left font-semibold">Stok Toko</th>
                            <th class="pb-2 text-left font-semibold">Isi/Dus</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produk as $p)
                        <tr class="border-b border-gray-50 hover:bg-blue-50 transition transfer-produk-row" data-nama="{{ strtolower($p->nama_produk) }}" data-kategori="{{ $p->id_kategori }}">
                            <td class="py-3 font-medium text-gray-800">{{ $p->nama_produk }}</td>
                            <td class="py-3 text-gray-500">{{ $p->kategori->nama_kategori ?? '-' }}</td>
                            <td class="py-3"><span class="px-2 py-0.5 rounded-lg text-xs font-semibold {{ $p->stok_gudang <= 5 ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700' }}">{{ $p->stok_gudang }} dus</span></td>
                            <td class="py-3"><span class="px-2 py-0.5 rounded-lg text-xs font-semibold {{ $p->stok_toko <= 10 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-700' }}">{{ $p->stok_toko }} pcs</span></td>
                            <td class="py-3 text-gray-600">{{ $p->isi_per_dus }} pcs</td>
                            <td class="py-3">
                                @if($p->stok_gudang > 0)
                                <button type="button" onclick="pilihProdukTransfer({{ $p->id_produk }}, '{{ addslashes($p->nama_produk) }}', {{ $p->stok_gudang }}, {{ $p->stok_toko }}, {{ $p->isi_per_dus }}, '{{ route('produk.transferStok', $p->id_produk) }}')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg transition">Pilih</button>
                                @else
                                <span class="text-xs text-red-400">Stok kosong</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL 2: FORM TRANSFER --}}
    <div id="modalTransfer" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Transfer Stok</h2>
                    <p class="text-xs text-gray-400">Gudang → Toko</p>
                </div>
                <button onclick="tutupModalTransfer()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="formTransfer" method="POST" action="">
                @csrf
                <div class="mb-4 bg-blue-50 rounded-xl p-4 text-sm">
                    <p class="text-xs text-gray-400 mb-1">Produk</p>
                    <p id="namaProdukTransfer" class="font-bold text-gray-800 mb-3">-</p>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-white rounded-lg p-2"><p class="text-xs text-gray-400">Stok Gudang</p><p id="infoStokGudang" class="font-bold text-blue-600">-</p></div>
                        <div class="bg-white rounded-lg p-2"><p class="text-xs text-gray-400">Stok Toko</p><p id="infoStokToko" class="font-bold text-green-600">-</p></div>
                        <div class="bg-white rounded-lg p-2"><p class="text-xs text-gray-400">Isi/Dus</p><p id="infoIsiPerDus" class="font-bold text-gray-700">-</p></div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Transfer (dus)</label>
                    <input type="number" name="jumlah_dus" id="inputJumlahDus" min="1" value="1" oninput="hitungHasil()" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p id="warningStok" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>
                <div class="mb-5 bg-green-50 rounded-xl p-3 flex justify-between items-center text-sm">
                    <span class="text-gray-600">Stok Toko Setelah Transfer</span>
                    <span id="infoHasil" class="font-bold text-[#2d6a4f] text-base">-</span>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="kembaliPilihProduk()" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">← Ganti</button>
                    <button type="submit" id="btnTransfer" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-xl transition">Transfer</button>
                    <button type="button" onclick="tutupModalTransfer()" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let transferStokGudang = 0;
        let transferStokToko   = 0;
        let transferIsiPerDus  = 0;

        function bukaModalPilihProdukTransfer() {
            document.getElementById('modalPilihProdukTransfer').classList.remove('hidden');
            document.getElementById('searchTransfer').focus();
        }

        function tutupModalPilihProdukTransfer() {
            document.getElementById('modalPilihProdukTransfer').classList.add('hidden');
            document.getElementById('searchTransfer').value = '';
            document.getElementById('filterKategoriTransfer').value = '';
            filterTransfer();
        }

        function tutupModalTransfer() {
            document.getElementById('modalTransfer').classList.add('hidden');
            document.getElementById('inputJumlahDus').value = 1;
        }

        function kembaliPilihProduk() {
            document.getElementById('modalTransfer').classList.add('hidden');
            bukaModalPilihProdukTransfer();
        }

        function filterTransfer() {
            const keyword  = document.getElementById('searchTransfer').value.toLowerCase();
            const kategori = document.getElementById('filterKategoriTransfer').value;
            document.querySelectorAll('.transfer-produk-row').forEach(row => {
                const matchNama = row.dataset.nama.includes(keyword);
                const matchKat  = kategori === '' || row.dataset.kategori === kategori;
                row.style.display = (matchNama && matchKat) ? '' : 'none';
            });
        }

        function pilihProdukTransfer(id, nama, stokGudang, stokToko, isiPerDus, url) {
            transferStokGudang = stokGudang;
            transferStokToko   = stokToko;
            transferIsiPerDus  = isiPerDus;

            document.getElementById('namaProdukTransfer').textContent = nama;
            document.getElementById('infoStokGudang').textContent = stokGudang + ' dus';
            document.getElementById('infoStokToko').textContent   = stokToko + ' pcs';
            document.getElementById('infoIsiPerDus').textContent  = isiPerDus + ' pcs/dus';
            document.getElementById('formTransfer').action = url;
            document.getElementById('inputJumlahDus').value = 1;

            tutupModalPilihProdukTransfer();
            hitungHasil();
            document.getElementById('modalTransfer').classList.remove('hidden');
        }

        function hitungHasil() {
            const jumlahDus  = parseInt(document.getElementById('inputJumlahDus').value) || 0;
            const tambahan   = jumlahDus * transferIsiPerDus;
            const hasil      = transferStokToko + tambahan;

            document.getElementById('infoHasil').textContent = hasil + ' pcs';

            const warning    = document.getElementById('warningStok');
            const btnTransfer = document.getElementById('btnTransfer');

            if (jumlahDus > transferStokGudang) {
                warning.textContent = `Stok gudang tidak cukup! Tersedia: ${transferStokGudang} dus`;
                warning.classList.remove('hidden');
                btnTransfer.disabled = true;
                btnTransfer.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                warning.classList.add('hidden');
                btnTransfer.disabled = false;
                btnTransfer.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    </script>
</x-app-layout>