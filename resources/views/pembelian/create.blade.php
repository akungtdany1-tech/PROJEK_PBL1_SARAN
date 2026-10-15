<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <a href="{{ route('pembelian.index') }}" class="hover:text-[#2d6a4f]">Pembelian</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Tambah Pembelian</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-4xl">

        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">Tambah Pembelian</h2>
            <p class="text-xs text-gray-400">Catat pembelian produk dari suplier</p>
        </div>

        {{-- DEBUG ERRORS --}}
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- SESSION DEBUG --}}
        @if(session('debug_data'))
            <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-600 px-4 py-3 rounded-xl text-sm">
                <strong>Debug Data:</strong>
                <pre class="text-xs mt-2">{{ json_encode(session('debug_data'), JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- FORM DENGAN ID --}}
        <form method="POST" action="{{ route('pembelian.store') }}" id="formPembelian">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Suplier</label>
                    <select name="id_suplier" id="id_suplier"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                        <option value="">-- Pilih Suplier --</option>
                        @foreach($suplier as $s)
                            <option value="{{ $s->id_suplier }}" {{ old('id_suplier') == $s->id_suplier ? 'selected' : '' }}>
                                {{ $s->nama_suplier }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="2" placeholder="Opsional..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            {{-- Tabel Detail Produk --}}
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-700 text-sm">Detail Produk</h3>
                    <button type="button" onclick="bukaModalProduk()"
                            class="text-sm bg-green-50 hover:bg-green-100 text-[#2d6a4f] font-semibold px-3 py-1.5 rounded-lg border border-green-200 transition">
                        + Tambah Produk
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-500 text-xs">
                                <th class="pb-2 text-left font-semibold">Produk</th>
                                <th class="pb-2 text-left font-semibold">Jumlah</th>
                                <th class="pb-2 text-left font-semibold">Harga Beli</th>
                                <th class="pb-2 text-left font-semibold">Subtotal</th>
                                <th class="pb-2"></th>
                            </tr>
                        </thead>
                        <tbody id="barisProduk">
                            <tr id="emptyRow">
                                <td colspan="5" class="py-8 text-center text-gray-400 text-sm">
                                    Belum ada produk — klik "+ Tambah Produk"
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-4">
                    <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-3 flex items-center gap-4">
                        <span class="text-sm font-semibold text-gray-700">Total Pembelian:</span>
                        <span class="text-lg font-bold text-[#2d6a4f]" id="totalPembelian">Rp 0</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="button" onclick="simpanPembelian()"
                        class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition">
                    Simpan
                </button>
                <a href="{{ route('pembelian.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- MODAL PILIH PRODUK --}}
    <div id="modalProduk" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 p-6 flex flex-col max-h-[85vh]">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Pilih Produk</h3>
                <button onclick="tutupModalProduk()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex gap-2 mb-4">
                <input type="text" id="modalSearch" placeholder="Cari nama produk..."
                       oninput="filterModalProduk()"
                       class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                <select id="modalFilterKategori" onchange="filterModalProduk()"
                        class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
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
                            <th class="pb-2 text-left font-semibold">Harga Satuan</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody id="modalTabelProduk">
                        @foreach($produk as $p)
                        <tr class="border-b border-gray-50 hover:bg-green-50 transition modal-produk-row"
                            data-nama="{{ strtolower($p->nama_produk) }}"
                            data-kategori="{{ $p->id_kategori }}">
                            <td class="py-3 font-medium text-gray-800">{{ $p->nama_produk }}</td>
                            <td class="py-3 text-gray-500">{{ $p->kategori->nama_kategori ?? '-' }}</td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold
                                    {{ $p->stok_gudang <= 5 ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $p->stok_gudang }} pcs
                                </span>
                            </td>
                            <td class="py-3 text-gray-600">Rp {{ number_format($p->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-3">
                                <button type="button"
                                        onclick="pilihProduk({{ $p->id_produk }}, '{{ addslashes($p->nama_produk) }}')"
                                        class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-xs px-3 py-1.5 rounded-lg transition">
                                    Pilih
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let barisCount = 0;

        function bukaModalProduk() {
            document.getElementById('modalProduk').classList.remove('hidden');
            document.getElementById('modalSearch').focus();
        }

        function tutupModalProduk() {
            document.getElementById('modalProduk').classList.add('hidden');
            document.getElementById('modalSearch').value = '';
            document.getElementById('modalFilterKategori').value = '';
            filterModalProduk();
        }

        function filterModalProduk() {
            const keyword  = document.getElementById('modalSearch').value.toLowerCase();
            const kategori = document.getElementById('modalFilterKategori').value;
            document.querySelectorAll('.modal-produk-row').forEach(row => {
                const matchNama = row.dataset.nama.includes(keyword);
                const matchKat  = kategori === '' || row.dataset.kategori === kategori;
                row.style.display = (matchNama && matchKat) ? '' : 'none';
            });
        }

        function pilihProduk(id, nama) {
            const existing = document.querySelector(`tr[data-produk-id="${id}"]`);
            if (existing) {
                tutupModalProduk();
                existing.querySelector('.jumlah-input').focus();
                return;
            }

            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) emptyRow.remove();

            const tbody = document.getElementById('barisProduk');
            const tr = document.createElement('tr');
            tr.setAttribute('data-produk-id', id);
            tr.innerHTML = `
                <td class="py-2 pr-2 font-medium text-gray-800">${nama}
                    <input type="hidden" name="id_produk[]" value="${id}">
                </td>
                <td class="py-2 pr-2">
                    <input type="number" name="jumlah[]" min="1" placeholder="0"
                           class="w-24 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f] jumlah-input">
                </td>
                <td class="py-2 pr-2">
                    <input type="number" name="harga_beli[]" min="0" placeholder="0"
                           class="w-32 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f] harga-input">
                </td>
                <td class="py-2 pr-2">
                    <input type="text" readonly placeholder="0"
                           class="w-32 border border-gray-100 bg-gray-50 rounded-lg px-3 py-2 text-sm subtotal-input">
                </td>
                <td class="py-2">
                    <button type="button" onclick="hapusBaris(this)"
                            class="w-8 h-8 bg-red-100 hover:bg-red-200 rounded-lg flex items-center justify-center transition">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            tutupModalProduk();
            hitungTotal();
        }

        function hapusBaris(btn) {
            btn.closest('tr').remove();
            hitungTotal();
            if (document.querySelectorAll('#barisProduk tr').length === 0) {
                document.getElementById('barisProduk').innerHTML = `
                    <tr id="emptyRow">
                        <td colspan="5" class="py-8 text-center text-gray-400 text-sm">
                            Belum ada produk — klik "+ Tambah Produk"
                        </td>
                    </tr>`;
            }
        }

        function hitungTotal() {
            let total = 0;
            document.querySelectorAll('#barisProduk tr[data-produk-id]').forEach(baris => {
                const jumlah   = parseFloat(baris.querySelector('.jumlah-input').value) || 0;
                const harga    = parseFloat(baris.querySelector('.harga-input').value) || 0;
                const subtotal = jumlah * harga;
                baris.querySelector('.subtotal-input').value = subtotal > 0 ? 'Rp ' + subtotal.toLocaleString('id-ID') : '';
                total += subtotal;
            });
            document.getElementById('totalPembelian').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('jumlah-input') || e.target.classList.contains('harga-input')) {
                hitungTotal();
            }
        });

        // ========== FUNGSI SIMPAN DENGAN FETCH ==========
        function simpanPembelian() {
            // Validasi basic
            const tanggal = document.getElementById('tanggal').value;
            if (!tanggal) {
                alert('Tanggal harus diisi!');
                return;
            }

            const produkRows = document.querySelectorAll('#barisProduk tr[data-produk-id]');
            if (produkRows.length === 0) {
                alert('Minimal 1 produk harus diisi!');
                return;
            }

            // Cek apakah semua produk punya jumlah dan harga
            let valid = true;
            produkRows.forEach(row => {
                const jumlah = row.querySelector('.jumlah-input').value;
                const harga = row.querySelector('.harga-input').value;
                if (!jumlah || jumlah <= 0) {
                    alert('Jumlah harus diisi dan lebih dari 0!');
                    valid = false;
                }
                if (!harga || harga <= 0) {
                    alert('Harga beli harus diisi dan lebih dari 0!');
                    valid = false;
                }
            });
            if (!valid) return;

            const form = document.getElementById('formPembelian');
            const formData = new FormData(form);

            // Debug: log semua data
            console.log('=== DATA YANG DIKIRIM ===');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Kirim dengan fetch
            fetch('{{ route("pembelian.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                if (data.success) {
                    alert('Pembelian berhasil disimpan!');
                    window.location.href = data.redirect;
                } else if (data.errors) {
                    // Tampilkan error validasi
                    let errorMsg = 'Validasi gagal:\n';
                    for (let key in data.errors) {
                        errorMsg += '- ' + data.errors[key].join('\n  ') + '\n';
                    }
                    alert(errorMsg);
                } else if (data.error) {
                    alert('Error: ' + data.error);
                } else {
                    alert('Terjadi kesalahan tidak diketahui');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Terjadi kesalahan koneksi: ' + error.message);
            });
        }
    </script>
</x-app-layout>