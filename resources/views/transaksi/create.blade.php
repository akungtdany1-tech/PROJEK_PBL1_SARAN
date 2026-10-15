<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Transaksi Baru</span>
        </div>
    </x-slot>

    {{-- FIX: Tampilan error validasi & error server --}}
    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ponytail: realtime notice — polling 10s, hilangkan jika pakai websocket --}}
    <div id="syncNotice" class="hidden mb-3 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-2 rounded-xl text-xs flex items-center justify-between">
        <span id="syncNoticeText">Stok diperbarui</span>
        <button onclick="this.parentElement.classList.add('hidden')" class="text-amber-600 font-bold">✕</button>
    </div>

    <div class="flex gap-4 h-full">

        {{-- KIRI: Daftar Produk --}}
        <div class="flex-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col">

            {{-- Filter & Search --}}
            <div class="flex gap-2 mb-4">
                <select id="filterKategori"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $k)
                        <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
                <div class="flex-1 flex items-center gap-2 border border-gray-200 rounded-xl px-3 py-2">
                    <input type="text" id="searchProduk" placeholder="Cari produk..."
                           class="flex-1 text-sm outline-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Tabel Produk --}}
            <div class="overflow-y-auto overflow-x-auto flex-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 text-xs">
                            <th class="pb-2 text-left font-semibold">Nama Produk</th>
                            <th class="pb-2 text-left font-semibold">Harga Eceran</th>
                            <th class="pb-2 text-left font-semibold">Harga Grosir</th>
                            <th class="pb-2 text-left font-semibold">Stok Toko</th>
                            <th class="pb-2 text-left font-semibold">Stok Gudang</th>
                            <th class="pb-2 text-left font-semibold">Diskon</th>
                            <th class="pb-2 text-left font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody id="tableProduk">
                        @foreach($produk as $p)
                        @php
                            $today = now()->toDateString();
                            // Generic discounts (no customer) for product table display
                            $diskonUmum = $p->diskon()
                                ->where('is_aktif', true)
                                ->whereNull('id_pelanggan')
                                ->where('mulai_tgl', '<=', $today)
                                ->where('selesai_tgl', '>=', $today)
                                ->orderByDesc('besar_diskon')
                                ->orderBy('id_diskon')
                                ->first();

                            // All active discounts (including customer-specific) for JS modal calculations
                            $semuaDiskon = $p->diskon()
                                ->where('is_aktif', true)
                                ->where('mulai_tgl', '<=', $today)
                                ->where('selesai_tgl', '>=', $today)
                                ->orderByDesc('besar_diskon')
                                ->orderBy('id_diskon')
                                ->get();

                            $semuaDiskonJson = $semuaDiskon->map(fn($d) => [
                                'besar'        => $d->besar_diskon,
                                'min_eceran'   => $d->minimal_beli,
                                'min_grosir'   => $d->minimal_beli_grosir,
                                'lokasi'       => $d->lokasi_berlaku,
                                'id_pelanggan' => $d->id_pelanggan,
                            ])->toJson();
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-green-50 transition cursor-pointer produk-row"
                            data-nama="{{ strtolower($p->nama_produk) }}"
                            data-kategori="{{ $p->id_kategori }}"
                            data-id="{{ $p->id_produk }}"
                            data-nama-display="{{ $p->nama_produk }}"
                            data-harga-satuan="{{ $p->harga_satuan }}"
                            data-harga-grosir="{{ $p->harga_grosir ?? $p->harga_satuan }}"
                            data-minimal-grosir="{{ $p->minimal_grosir ?? 0 }}"
                            data-stok-toko="{{ $p->stok_toko }}"
                            data-stok-gudang="{{ $p->stok_gudang }}"
                            data-diskon="{{ $diskonUmum ? $diskonUmum->besar_diskon : 0 }}"
                            data-minimal-diskon="{{ $diskonUmum ? $diskonUmum->minimal_beli : 0 }}"
                            data-minimal-diskon-grosir="{{ $diskonUmum ? $diskonUmum->minimal_beli_grosir : 0 }}"
                            data-lokasi-diskon="{{ $diskonUmum ? $diskonUmum->lokasi_berlaku : 'semua' }}"
                            data-pelanggan-diskon="{{ $diskonUmum ? ($diskonUmum->id_pelanggan ?? '') : '' }}"
                            data-semua-diskon="{{ $semuaDiskonJson }}">
                            <td class="py-3 font-medium text-gray-800">{{ $p->nama_produk }}</td>
                            <td class="py-3 text-gray-600">Rp {{ number_format($p->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-3 text-gray-600">Rp {{ number_format($p->harga_grosir ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold
                                    {{ $p->stok_toko <= 10 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-700' }}">
                                    {{ $p->stok_toko }} pcs
                                </span>
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold
                                    {{ $p->stok_gudang <= 10 ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $p->stok_gudang }} pcs
                                </span>
                            </td>
                            <td class="py-3">
                                @if($diskonUmum)
                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-600 rounded-lg text-xs font-semibold">
                                        {{ $diskonUmum->besar_diskon }}%
                                        @if($diskonUmum->minimal_beli > 0)
                                            | Eceran min {{ $diskonUmum->minimal_beli }}
                                        @endif
                                        @if($diskonUmum->minimal_beli_grosir > 0)
                                            | Grosir min {{ $diskonUmum->minimal_beli_grosir }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <button type="button"
                                        onclick="tambahKeKeranjang(this)"
                                        class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-xs px-3 py-1.5 rounded-lg transition">
                                    + Tambah
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- KANAN: Keranjang --}}
        <div class="w-80 bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800">Keranjang</h3>
                <div id="trashZone"
                     onclick="clearKeranjang()"
                     ondragover="event.preventDefault(); this.classList.add('scale-125', 'text-red-600')"
                     ondragleave="this.classList.remove('scale-125', 'text-red-600')"
                     ondrop="dropKeHapus(event)"
                     class="text-red-400 hover:text-red-600 transition-all duration-200 cursor-pointer p-1 rounded-lg hover:bg-red-50"
                     title="Klik untuk kosongkan semua / Drag produk ke sini untuk hapus">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform duration-200" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/>
                    </svg>
                </div>
            </div>

            {{-- List keranjang --}}
            <div id="keranjangList" class="flex-1 overflow-y-auto space-y-3 mb-4">
                <p id="keranjangKosong" class="text-center text-gray-400 text-sm py-8">Belum ada produk</p>
            </div>

            {{-- Summary --}}
            <div class="border-t border-gray-100 pt-3 space-y-1">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span id="summarySubtotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-sm text-orange-500">
                    <span>Diskon</span>
                    <span id="summaryDiskon">- Rp 0</span>
                </div>
                <div class="flex justify-between text-base font-bold text-gray-800 pt-1 border-t border-gray-100">
                    <span>Total</span>
                    <span id="summaryTotal">Rp 0</span>
                </div>
            </div>

            <button onclick="checkout()"
                    class="mt-4 w-full bg-[#2d6a4f] hover:bg-[#1b4332] text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Checkout
            </button>
        </div>
    </div>

    {{-- MODAL CHECKOUT --}}
    <div id="modalCheckout" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-5">Checkout Transaksi</h2>

            <form id="formCheckout" method="POST" action="{{ route('transaksi.store') }}">
                @csrf
                {{-- FIX: keranjang dikirim sebagai JSON string --}}
                <input type="hidden" id="keranjangInput" name="keranjang">

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pelanggan</label>
                        <select name="id_pelanggan" id="selectPelangganModal"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                            <option value="">-- Umum --</option>
                            @foreach($pelanggan as $p)
                                @php
                                    $diskonKhusus = $diskonPelanggan->get($p->id_pelanggan)?->first();
                                @endphp
                                <option value="{{ $p->id_pelanggan }}" data-diskon="{{ $diskonKhusus ? $diskonKhusus->besar_diskon : 0 }}">
                                    {{ $p->nama_pelanggan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kasir</label>
                        <input type="text" value="{{ auth()->user()->name }}" disabled
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-500">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                    <input type="hidden" name="metode_pembayaran" value="tunai">
                    <span class="text-sm text-gray-700 font-medium">Tunai</span>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bayar (Rp)</label>
                        <input type="number" name="bayar" id="inputBayar" min="0"
                               oninput="hitungKembalian()"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kembalian</label>
                        <input type="text" id="displayKembalian" disabled
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-[#2d6a4f] font-bold">
                    </div>
                </div>

                {{-- Ringkasan --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-4 space-y-1 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span id="modalSubtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-orange-500">
                        <span>Diskon</span>
                        <span id="modalDiskon">- Rp 0</span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-800 text-base border-t border-gray-200 pt-2 mt-2">
                        <span>Total</span>
                        <span id="modalTotal">Rp 0</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                    <textarea name="catatan" rows="2" placeholder="Tambahkan catatan..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]"></textarea>
                </div>

                <div class="flex gap-3">
                    {{-- FIX: pakai onclick untuk set keranjang sebelum submit --}}
                    <button type="button" id="btnSimpan" onclick="submitTransaksi()"
                            class="flex-1 bg-[#2d6a4f] hover:bg-[#1b4332] text-white font-semibold py-3 rounded-xl transition">
                        Simpan Transaksi
                    </button>
                    <button type="button" onclick="tutupModal()"
                            class="px-6 py-3 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const stokTerkiniUrl = "{{ route('transaksi.stokTerkini') }}";
    const logoUrl = "{{ asset('images/logotoko.png') }}";
    let keranjang = [];
    let pelangganTerpilih = null;

    // ponytail: polling ringan — ganti websocket jika perlu realtime push
    let _pollTimer = null;
    async function syncStok() {
        try {
            const res = await fetch(stokTerkiniUrl, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const map = await res.json();
            let changed = 0, removed = 0;
            document.querySelectorAll('.produk-row').forEach(row => {
                const id = row.dataset.id;
                const data = map[id];
                if (!data || data.deleted) {
                    row.style.opacity = '0.35';
                    row.querySelector('button')?.setAttribute('disabled','disabled');
                    row.title = 'Produk sudah dihapus pemilik';
                    // hapus dari keranjang jika ada
                    const idx = keranjang.findIndex(k => k.id_produk === id);
                    if (idx !== -1) { keranjang.splice(idx, 1); removed++; }
                    changed++;
                } else {
                    row.dataset.stokToko = data.stok_toko;
                    row.dataset.stokGudang = data.stok_gudang;
                    row.dataset.hargaSatuan = data.harga_satuan;
                    row.dataset.hargaGrosir = data.harga_grosir;
                    // update badge text
                    const tBadge = row.children[3]?.querySelector('span');
                    const gBadge = row.children[4]?.querySelector('span');
                    if (tBadge) { tBadge.textContent = data.stok_toko + ' pcs'; tBadge.className = 'px-2 py-0.5 rounded-lg text-xs font-semibold ' + (data.stok_toko <= 10 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-700'); }
                    if (gBadge) { gBadge.textContent = data.stok_gudang + ' pcs'; gBadge.className = 'px-2 py-0.5 rounded-lg text-xs font-semibold ' + (data.stok_gudang <= 10 ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700'); }
                    // sync keranjang stok snapshot
                    keranjang.forEach(k => { if (k.id_produk === id) { k.stokToko = data.stok_toko; k.stokGudang = data.stok_gudang; k.hargaSatuan = data.harga_satuan; k.hargaGrosir = data.harga_grosir; }});
                }
            });
            // produk baru yang belum ada di DOM? reload sekali biar kasir tau
            const existingIds = new Set([...document.querySelectorAll('.produk-row')].map(r => r.dataset.id));
            const baru = Object.keys(map).filter(id => !map[id].deleted && !existingIds.has(id));
            if (baru.length > 0) { showSyncNotice(baru.length + ' produk baru tersedia — refresh untuk melihat'); }
            if (removed > 0) { showSyncNotice(removed + ' produk di keranjang sudah dihapus pemilik, dikeluarkan otomatis'); renderKeranjang(); }
            else if (changed > 0) { renderKeranjang(); }
        } catch(e) {}
    }
    function showSyncNotice(msg) {
        const el = document.getElementById('syncNotice');
        const tx = document.getElementById('syncNoticeText');
        if (!el || !tx) return;
        tx.textContent = msg;
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 6000);
    }
    function startPolling() { if (_pollTimer) clearInterval(_pollTimer); syncStok(); _pollTimer = setInterval(syncStok, 10000); document.addEventListener('visibilitychange', () => { if (!document.hidden) syncStok(); }); }
    document.addEventListener('DOMContentLoaded', startPolling);

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('searchProduk').addEventListener('input', filterProduk);
        document.getElementById('filterKategori').addEventListener('change', filterProduk);

        const selectPelanggan = document.getElementById('selectPelangganModal');
        if (selectPelanggan) {
            selectPelanggan.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const id = selectedOption.value;
                const diskon = parseFloat(selectedOption.dataset.diskon) || 0;

                if (id) {
                    pelangganTerpilih = { id, diskon };
                } else {
                    pelangganTerpilih = null;
                }

                renderKeranjang();
                calculateTotal();
            });
        }
    });

    function filterProduk() {
        const keyword = document.getElementById('searchProduk').value.toLowerCase();
        const kategori = document.getElementById('filterKategori').value;
        document.querySelectorAll('.produk-row').forEach(row => {
            const matchNama = row.dataset.nama.includes(keyword);
            const matchKat  = kategori === '' || row.dataset.kategori === kategori;
            row.style.display = (matchNama && matchKat) ? '' : 'none';
        });
    }

    function tambahKeKeranjang(btn) {
        const row = btn.closest('tr');
        const id          = row.dataset.id;
        const nama        = row.dataset.namaDisplay;
        const hargaSatuan = parseInt(row.dataset.hargaSatuan);
        const hargaGrosir = parseInt(row.dataset.hargaGrosir);
        const stokToko    = parseInt(row.dataset.stokToko);
        const stokGudang  = parseInt(row.dataset.stokGudang);

        const existing = keranjang.find(k => k.id_produk === id);
        if (existing) {
            existing.jumlah += 1;
        } else {
            keranjang.push({
                id_produk: id,
                nama,
                hargaSatuan,
                hargaGrosir,
                stokToko,
                stokGudang,
                jumlah: 1,
                tipe: 'eceran',
                semuaDiskon: JSON.parse(row.dataset.semuaDiskon || '[]'),
            });
        }
        renderKeranjang();
    }

    function renderKeranjang() {
        const list = document.getElementById('keranjangList');
        const kosong = document.getElementById('keranjangKosong');

        const items = list.querySelectorAll('div.bg-gray-100, div.bg-red-50');
        items.forEach(item => item.remove());

        if (keranjang.length === 0) {
            kosong.style.display = '';
            updateSummary(0, 0);
            return;
        }

        kosong.style.display = 'none';

        keranjang.forEach((item, index) => {
            const harga = item.tipe === 'grosir' ? item.hargaGrosir : item.hargaSatuan;
            const subtotalProduk = harga * item.jumlah;
            const maxStok = item.tipe === 'grosir' ? item.stokGudang : item.stokToko;
            const stokKurang = item.jumlah > maxStok;

            const div = document.createElement('div');
            div.className = `${stokKurang ? 'bg-red-50 border-red-200' : 'bg-gray-100 border-gray-200'} border rounded-xl p-3 text-sm mb-2`;

            div.innerHTML = `
                <div class="flex justify-between items-start mb-2">
                    <span class="font-medium text-gray-800 text-xs">${item.nama}</span>
                    <button onclick="hapusItem(${index})" class="text-red-400 hover:text-red-600">✕</button>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <select onchange="ubahTipe(${index}, this.value)" class="text-xs border rounded-lg px-2 py-1">
                        <option value="eceran" ${item.tipe === 'eceran' ? 'selected' : ''}>Eceran</option>
                        <option value="grosir" ${item.tipe === 'grosir' ? 'selected' : ''}>Grosir</option>
                    </select>
                    <div class="flex items-center gap-1 ml-auto">
                        <button onclick="ubahJumlah(${index}, -1)" class="w-6 h-6 bg-gray-200 rounded">-</button>
                        <input type="number" value="${item.jumlah}" class="w-10 text-center text-xs border rounded" readonly>
                        <button onclick="ubahJumlah(${index}, 1)" class="w-6 h-6 bg-gray-200 rounded">+</button>
                    </div>
                </div>
                <div class="flex justify-between text-xs font-bold">
                    <span>Subtotal</span>
                    <span>Rp ${formatRp(subtotalProduk)}</span>
                </div>
                ${stokKurang ? `<div class="text-[10px] text-red-500 mt-1">⚠ Stok ${item.tipe} sisa ${maxStok}</div>` : ''}
            `;
            list.appendChild(div);
        });

        calculateTotal();
    }

    function calculateTotal() {
        let subtotal = 0;
        let diskonNominal = 0;

        keranjang.forEach(item => {
            const harga = item.tipe === 'grosir' ? item.hargaGrosir : item.hargaSatuan;
            subtotal += harga * item.jumlah;

            const diskonCocok = (item.semuaDiskon || []).find(d => {
                const lokasiOke = d.lokasi === 'semua'
                    || (d.lokasi === 'gudang' && item.tipe === 'grosir')
                    || (d.lokasi === 'toko'   && item.tipe === 'eceran');
                if (!lokasiOke) return false;

                const untukSemua     = !d.id_pelanggan;
                const pelangganCocok = pelangganTerpilih && String(pelangganTerpilih.id) === String(d.id_pelanggan);
                if (!untukSemua && !pelangganCocok) return false;

                const minBeli = item.tipe === 'grosir' ? d.min_grosir : d.min_eceran;
                if (item.jumlah < minBeli) return false;

                return true;
            });

            if (diskonCocok) {
                // FIX: diskon per unit dikali jumlah
                diskonNominal += Math.round(harga * (diskonCocok.besar / 100)) * item.jumlah;
            }
        });

        updateSummary(subtotal, diskonNominal);
    }

    function updateSummary(sub, diskon) {
        const total = sub - diskon;
        document.getElementById('summarySubtotal').textContent = 'Rp ' + formatRp(sub);
        document.getElementById('summaryDiskon').textContent = '- Rp ' + formatRp(diskon);
        document.getElementById('summaryTotal').textContent = 'Rp ' + formatRp(total);

        document.getElementById('modalSubtotal').textContent = 'Rp ' + formatRp(sub);
        document.getElementById('modalDiskon').textContent = '- Rp ' + formatRp(diskon);
        document.getElementById('modalTotal').textContent = 'Rp ' + formatRp(total);
        document.getElementById('inputBayar').value = total;
        hitungKembalian();
    }

    function ubahTipe(index, tipe) {
        keranjang[index].tipe = tipe;
        renderKeranjang();
    }

    function ubahJumlah(index, delta) {
        keranjang[index].jumlah += delta;
        if (keranjang[index].jumlah <= 0) keranjang.splice(index, 1);
        renderKeranjang();
    }

    function hapusItem(index) {
        keranjang.splice(index, 1);
        renderKeranjang();
    }

    function clearKeranjang() {
        if (keranjang.length === 0) return;
        if (!confirm('Kosongkan semua keranjang?')) return;
        keranjang = [];
        renderKeranjang();
    }

    async function checkout() {
        if (keranjang.length === 0) return alert('Keranjang kosong!');
        // re-sync sekali sebelum buka modal — cegah stale checkout
        await syncStok();
        let stokAman = true;
        let hapusNama = null;
        keranjang.forEach(item => {
            const max = item.tipe === 'grosir' ? item.stokGudang : item.stokToko;
            if (item.jumlah > max) stokAman = false;
        });
        // jika ada item yang barusan kehapus karena produk dihapus pemilik, keranjang sudah ter-update
        if (keranjang.length === 0) return alert('Keranjang kosong — produk sudah dihapus pemilik. Silakan pilih ulang.');
        if (!stokAman) { renderKeranjang(); return alert('Ada produk melebihi stok terbaru — cek tanda merah di keranjang.'); }

        document.getElementById('keranjangInput').value = JSON.stringify(keranjang);
        document.getElementById('modalCheckout').classList.remove('hidden');
    }

    // FIX: pastikan keranjang ter-set ulang sebelum submit (antisipasi perubahan di modal)
    function submitTransaksi() {
        const bayar = parseInt(document.getElementById('inputBayar').value) || 0;
        const total = parseInt(document.getElementById('modalTotal').textContent.replace(/[^0-9]/g, '')) || 0;

        if (bayar < total) {
            alert('Uang bayar kurang dari total!');
            return;
        }

        // Update keranjang input dengan data terbaru (termasuk pelanggan yang dipilih di modal)
        document.getElementById('keranjangInput').value = JSON.stringify(keranjang);
        document.getElementById('formCheckout').submit();
    }

    function tutupModal() {
        document.getElementById('modalCheckout').classList.add('hidden');
    }

    function hitungKembalian() {
        const total = parseInt(document.getElementById('modalTotal').textContent.replace(/[^0-9]/g, '')) || 0;
        const bayar = parseInt(document.getElementById('inputBayar').value) || 0;
        const kembalian = bayar - total;
        const display = document.getElementById('displayKembalian');

        if (bayar < total) {
            display.value = "Kurang Rp " + formatRp(total - bayar);
            display.classList.add('text-red-500');
            display.classList.remove('text-[#2d6a4f]');
        } else {
            display.value = "Rp " + formatRp(kembalian);
            display.classList.remove('text-red-500');
            display.classList.add('text-[#2d6a4f]');
        }
    }

    function formatRp(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    </script>
</x-app-layout>