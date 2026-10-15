<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <a href="{{ route('diskon.index') }}" class="hover:text-[#2d6a4f]">Diskon</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Tambah Diskon</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">Tambah Diskon</h2>
            <p class="text-xs text-gray-400">Buat promo diskon untuk produk</p>
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('diskon.store') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Diskon</label>
                    <input type="text" name="nama_diskon" value="{{ old('nama_diskon') }}"
                           placeholder="cth: Promo Lebaran 20%"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    @error('nama_diskon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Besar Diskon (%)</label>
                        <input type="number" name="besar_diskon" value="{{ old('besar_diskon') }}" min="1" max="100"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min. Beli Eceran (pcs)</label>
                        <input type="number" name="minimal_beli" id="minBeli" value="{{ old('minimal_beli', 0) }}" min="0"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min. Beli Grosir (pcs)</label>
                        <input type="number" name="minimal_beli_grosir" id="minBeliGrosir" value="{{ old('minimal_beli_grosir', 0) }}" min="0"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    </div>
                </div>
                
                <div class="sm:col-span-2">
                     <div id="previewDiskon" class="text-sm text-[#2d6a4f] bg-green-50 border border-green-100 rounded-xl px-4 py-3 min-h-[40px]"></div>
                     </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Berlaku</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="lokasi_berlaku" value="semua" {{ old('lokasi_berlaku', 'semua') == 'semua' ? 'checked' : '' }} class="text-[#2d6a4f] focus:ring-[#2d6a4f]">
                            <span class="text-sm text-gray-700">Semua</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="lokasi_berlaku" value="gudang" {{ old('lokasi_berlaku') == 'gudang' ? 'checked' : '' }} class="text-[#2d6a4f] focus:ring-[#2d6a4f]">
                            <span class="text-sm text-gray-700">Gudang</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="lokasi_berlaku" value="toko" {{ old('lokasi_berlaku') == 'toko' ? 'checked' : '' }} class="text-[#2d6a4f] focus:ring-[#2d6a4f]">
                            <span class="text-sm text-gray-700">Toko</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="mulai_tgl" value="{{ old('mulai_tgl') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="selesai_tgl" value="{{ old('selesai_tgl') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Pelanggan</label>
                    <select name="id_pelanggan" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                        <option value="">Semua Pelanggan</option>
                        @foreach($pelanggan as $plg)
                            <option value="{{ $plg->id_pelanggan }}" {{ old('id_pelanggan') == $plg->id_pelanggan ? 'selected' : '' }}>
                                {{ $plg->nama_pelanggan }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Kosongkan jika ingin diskon berlaku untuk semua pelanggan.</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Produk</label>
                    <div class="flex gap-2 mb-2">
                        <input type="text" id="searchProduk" placeholder="Cari nama produk..."
                               class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                        <select id="filterKategori"
                                class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                            <option value="">Semua Kategori</option>
                            @foreach($produk->groupBy('id_kategori') as $idKat => $produkPerKat)
                                <option value="{{ $idKat }}">{{ $produkPerKat->first()->kategori->nama_kategori ?? '-' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="listProduk" class="grid grid-cols-2 sm:grid-cols-3 gap-2 border border-gray-200 rounded-xl p-4 max-h-56 overflow-y-auto">
                        @foreach($produk as $p)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer produk-item"
                                   data-nama="{{ strtolower($p->nama_produk) }}"
                                   data-kategori="{{ $p->id_kategori }}">
                                <input type="checkbox" name="id_produk[]" value="{{ $p->id_produk }}"
                                       {{ in_array($p->id_produk, old('id_produk', [])) ? 'checked' : '' }}
                                       class="rounded text-[#2d6a4f]">
                                <span>
                                    {{ $p->nama_produk }}
                                    <span class="text-xs text-gray-400">({{ $p->kategori->nama_kategori ?? '-' }})</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('id_produk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="is_aktif" value="1"
                               {{ old('is_aktif', '1') ? 'checked' : '' }}
                               class="rounded text-[#2d6a4f]">
                        <span class="font-medium">Aktifkan diskon ini</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit"
                        class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition">
                    Simpan
                </button>
                <a href="{{ route('diskon.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
// Search & filter produk
const searchInput = document.getElementById('searchProduk');
const filterKategori = document.getElementById('filterKategori');
const items = document.querySelectorAll('.produk-item');

function filterProduk() {
    const keyword = searchInput.value.toLowerCase();
    const kategori = filterKategori.value;
    items.forEach(item => {
        const matchNama = item.dataset.nama.includes(keyword);
        const matchKat  = kategori === '' || item.dataset.kategori === kategori;
        item.style.display = (matchNama && matchKat) ? '' : 'none';
    });
}
searchInput.addEventListener('input', filterProduk);
filterKategori.addEventListener('change', filterProduk);

// Lokasi berlaku → hide/show field min beli
const radioLokasi  = document.querySelectorAll('input[name="lokasi_berlaku"]');
const wrapEceran   = document.getElementById('minBeli').closest('div');
const wrapGrosir   = document.getElementById('minBeliGrosir').closest('div');

function updateLokasi() {
    const val = document.querySelector('input[name="lokasi_berlaku"]:checked').value;
    if (val === 'semua') {
        wrapEceran.style.display  = '';
        wrapGrosir.style.display  = '';
    } else if (val === 'gudang') {
        wrapEceran.style.display  = 'none';
        wrapGrosir.style.display  = '';
    } else { // toko
        wrapEceran.style.display  = '';
        wrapGrosir.style.display  = 'none';
    }
    updatePreview();
}
radioLokasi.forEach(r => r.addEventListener('change', updateLokasi));
updateLokasi(); // jalanin saat load

// Preview diskon
function updatePreview() {
    const besar     = parseFloat(document.querySelector('input[name="besar_diskon"]').value) || 0;
    const lokasi    = document.querySelector('input[name="lokasi_berlaku"]:checked').value;
    const minEceran = parseInt(document.getElementById('minBeli').value) || 1;
    const minGrosir = parseInt(document.getElementById('minBeliGrosir').value) || 1;
    const preview   = document.getElementById('previewDiskon');

    if (besar <= 0) {
        preview.innerHTML = '';
        return;
    }

    let teks = '';
    if (lokasi === 'semua' || lokasi === 'toko') {
        teks += `Eceran: beli min <b>${minEceran} pcs</b> → hemat <b>${besar}%</b><br>`;
    }
    if (lokasi === 'semua' || lokasi === 'gudang') {
        teks += `Grosir: beli min <b>${minGrosir} pcs</b> → hemat <b>${besar}%</b>`;
    }
    preview.innerHTML = teks;
}

document.querySelector('input[name="besar_diskon"]').addEventListener('input', updatePreview);
document.getElementById('minBeli').addEventListener('input', updatePreview);
document.getElementById('minBeliGrosir').addEventListener('input', updatePreview);
</script>
</x-app-layout>