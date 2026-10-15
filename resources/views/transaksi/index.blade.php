<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Transaksi</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
            {{-- Kiri: Tombol Tambah --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('transaksi.create') }}" class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-4 py-2 rounded-xl transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Transaksi Baru
                </a>
            </div>

            {{-- Kanan: Tombol Sampah --}}
            <a href="{{ route('transaksi.sampah') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 shadow-md transition" title="Lihat transaksi di sampah">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22"/></svg>
                Sampah
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-500 text-xs">
                        <th class="pb-3 text-left font-semibold uppercase tracking-wider">ID</th>
                        <th class="pb-3 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="pb-3 text-left font-semibold uppercase tracking-wider">Pelanggan</th>
                        <th class="pb-3 text-left font-semibold uppercase tracking-wider">Kasir</th>
                        <th class="pb-3 text-left font-semibold uppercase tracking-wider">Metode</th>
                        <th class="pb-3 text-left font-semibold uppercase tracking-wider">Total Akhir</th>
                        <th class="pb-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transaksi as $t)
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="py-4 text-gray-400 font-mono">#{{ $t->id_transaksi }}</td>
                        <td class="py-4 text-gray-600">
                            {{ \Carbon\Carbon::parse($t->created_at ?? $t->tanggal)->format('d/m/y') }}
                            <span class="text-[10px] block text-gray-400">{{ \Carbon\Carbon::parse($t->created_at ?? $t->tanggal)->format('H:i') }} WIB</span>
                        </td>
                        <td class="py-4 font-medium text-gray-700">{{ $t->pelanggan->nama_pelanggan ?? 'Umum' }}</td>
                        <td class="py-4 text-gray-600 text-xs">{{ $t->user->name ?? $t->kasir->name ?? '-' }}</td>
                        <td class="py-4">
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 border border-blue-100 rounded-md text-[10px] font-bold uppercase">
                                {{ $t->metode_pembayaran }}
                            </span>
                        </td>
                        <td class="py-4 font-bold text-gray-800">
                            Rp {{ number_format($t->total, 0, ',', '.') }}
                            @if($t->total_diskon > 0)
                                <span class="block text-[10px] text-orange-500 font-normal">Hemat Rp {{ number_format($t->total_diskon, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" data-id="{{ $t->id_transaksi }}"
                                        class="btn-lihat-struk w-9 h-9 bg-emerald-50 hover:bg-emerald-600 text-emerald-600 hover:text-white rounded-xl flex items-center justify-center transition-all duration-200"
                                        title="Lihat Struk">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </button>

                                <a href="{{ route('transaksi.show', $t->id_transaksi) }}?type=html"
                                   class="w-9 h-9 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-xl flex items-center justify-center transition-all duration-200"
                                   title="Detail Transaksi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                <form action="{{ route('transaksi.destroy', $t->id_transaksi) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus transaksi ini? Stok akan otomatis dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-9 h-9 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all duration-200"
                                            title="Hapus Transaksi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-50 p-4 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-gray-400">Belum ada data transaksi tersimpan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalStruk" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50 p-4 transition-all duration-300">
        <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl mx-auto my-auto">
            
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-lg font-bold text-gray-800">Detail Struk Transaksi</h3>
                <button type="button" onclick="tutupModalStruk()" class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
            </div>

            <div id="kontenStruk" class="space-y-4 max-h-[60vh] overflow-y-auto pr-1 text-sm text-gray-600">
                <div class="text-center py-4">Memuat data...</div>
            </div>

            <div class="flex justify-end gap-2 border-t pt-3 mt-4">
                <button type="button" onclick="tutupModalStruk()" class="px-4 py-2 border rounded-xl text-gray-600 hover:bg-gray-50 text-sm font-medium">
                    Tutup
                </button>
                <button type="button" onclick="cetakStruk()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium flex items-center gap-1">
                    Cetak Struk
                </button>
            </div>
        </div>
    </div>

    <script>
    const urlShowBase  = "{{ route('transaksi.index') }}";
    const urlPrintBase = "{{ route('transaksi.print', '__ID__') }}";
    let idStrukAktif = null;

    // Escape HTML sebelum disisipkan via innerHTML (cegah stored XSS)
    function esc(value) {
        return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function formatWaktu(data) {
        const tgl = new Date(data.created_at || data.tanggal || null);
        if (isNaN(tgl.getTime())) return '-';
        return tgl.toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'})
            + ' ' + tgl.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) + ' WIB';
    }

    document.querySelectorAll('.btn-lihat-struk').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const idStruk = this.getAttribute('data-id');
            if (!idStruk) return;

            idStrukAktif = idStruk;
            const modal = document.getElementById('modalStruk');
            const konten = document.getElementById('kontenStruk');

            if (!modal || !konten) {
                alert('Error: Wadah modal tidak ditemukan!');
                return;
            }

            modal.classList.remove('hidden');
            konten.innerHTML = '<div class="text-center py-8"><span class="text-gray-500">Memuat data transaksi...</span></div>';

            fetch(`${urlShowBase.replace(/\/+$/, '')}/${idStruk}?type=json`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memuat data dari server');
                }
                return response.json();
            })
            .then(data => {
                const tglFormatted = formatWaktu(data);

                // Deteksi nama kasir dari relasi user maupun kasir
                const namaKasir = esc(data.user?.name || data.kasir?.name || '-');
                const namaPelanggan = esc(data.pelanggan?.nama_pelanggan || 'Umum');
                const metode = esc(data.metode_pembayaran || '-');

                let detailItems = '';
                if(data.detail && data.detail.length > 0) {
                    data.detail.forEach(item => {
                        const namaProduk = esc(item.produk ? item.produk.nama_produk : 'Produk Terhapus');
                        const hargaItem = item.harga ? Number(item.harga) : 0;
                        const subtotalItem = item.subtotal ? Number(item.subtotal) : (hargaItem * Number(item.jumlah || 0));

                        detailItems += `
                            <div class="flex justify-between text-xs py-1 border-b border-dashed">
                                <div>
                                    <span class="font-medium text-gray-800">${namaProduk}</span>
                                    <span class="block text-gray-400">${item.jumlah || 0} x Rp ${hargaItem.toLocaleString('id-ID')}</span>
                                </div>
                                <span class="font-bold text-gray-700">Rp ${subtotalItem.toLocaleString('id-ID')}</span>
                            </div>
                        `;
                    });
                } else {
                    detailItems = '<div class="text-center text-xs text-gray-400 py-2">Tidak ada item</div>';
                }

                konten.innerHTML = `
                    <div class="text-center border-b pb-3 mb-3 border-dashed">
                        <h4 class="font-bold text-base text-gray-800">Sarana Agro Makmur</h4>
                        <p class="text-[11px] text-gray-400">Nota: TR-${esc(data.id_transaksi || '')} | ${tglFormatted}</p>
                    </div>
                    <div class="text-xs space-y-1 bg-gray-50 p-3 rounded-xl mb-3">
                        <div class="flex justify-between"><span>Kasir:</span><span class="font-medium">${namaKasir}</span></div>
                        <div class="flex justify-between"><span>Pelanggan:</span><span class="font-medium">${namaPelanggan}</span></div>
                        <div class="flex justify-between"><span>Metode:</span><span class="font-bold uppercase text-emerald-600">${metode}</span></div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-gray-500 mb-1">Daftar Item:</p>
                        ${detailItems}
                    </div>
                    <div class="pt-3 space-y-1 text-xs border-t border-dashed mt-3">
                        <div class="flex justify-between"><span>Subtotal:</span><span>Rp ${Number(data.subtotal || 0).toLocaleString('id-ID')}</span></div>
                        <div class="flex justify-between text-orange-500"><span>Diskon:</span><span>-Rp ${Number(data.total_diskon || 0).toLocaleString('id-ID')}</span></div>
                        <div class="flex justify-between font-bold text-sm text-gray-800 pt-1 border-t"><span>TOTAL:</span><span>Rp ${Number(data.total || 0).toLocaleString('id-ID')}</span></div>
                        <div class="flex justify-between pt-1"><span>Bayar:</span><span>Rp ${Number(data.bayar || 0).toLocaleString('id-ID')}</span></div>
                        <div class="flex justify-between font-medium text-emerald-600"><span>Kembalian:</span><span>Rp ${Number(data.kembalian || 0).toLocaleString('id-ID')}</span></div>
                    </div>
                    <div class="text-center mt-3 pt-2 border-t border-dashed">
                        <button type="button" onclick="cetakStruk()"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium">
                            Cetak Struk
                        </button>
                    </div>
                `;
            })
            .catch(error => {
                konten.innerHTML = `<div class="text-center py-4 text-red-500 font-medium">Gagal mengambil data transaksi.</div>`;
                console.error(error);
            });
        });
    });

    function cetakStruk() {
        if (!idStrukAktif) {
            alert('Data struk belum dimuat.');
            return;
        }
        // Cetak lewat halaman struk termal 58mm, bukan window.print() di halaman daftar
        window.open(urlPrintBase.replace('__ID__', idStrukAktif), '_blank');
    }

    function tutupModalStruk() {
        document.getElementById('modalStruk').classList.add('hidden');
    }

    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modalStruk');
        if (event.target === modal) {
            tutupModalStruk();
        }
    });
    </script>
</x-app-layout>