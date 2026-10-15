<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <a href="{{ route('diskon.index') }}" class="hover:text-[#2d6a4f]">Diskon</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Edit Diskon</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-4xl">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">Edit Diskon</h2>
            <p class="text-xs text-gray-400">Ubah promo diskon produk</p>
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

        <form method="POST" action="{{ route('diskon.update', $diskon->id_diskon) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Diskon</label>
                    <input type="text" name="nama_diskon" value="{{ old('nama_diskon', $diskon->nama_diskon) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                </div>

                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Besar Diskon (%)</label>
                        <input type="number" name="besar_diskon" value="{{ old('besar_diskon', $diskon->besar_diskon) }}"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min. Beli Eceran (pcs)</label>
                        <input type="number" name="minimal_beli" id="minBeli" value="{{ old('minimal_beli', $diskon->minimal_beli) }}" min="0"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min. Beli Grosir (pcs)</label>
                        <input type="number" name="minimal_beli_grosir" id="minBeliGrosir" value="{{ old('minimal_beli_grosir', $diskon->minimal_beli_grosir) }}" min="0"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi Berlaku</label>
                    <div class="flex gap-4">
                        @foreach(['semua', 'gudang', 'toko'] as $loc)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="lokasi_berlaku" value="{{ $loc }}" 
                                       {{ old('lokasi_berlaku', $diskon->lokasi_berlaku) == $loc ? 'checked' : '' }} 
                                       class="text-[#2d6a4f] focus:ring-[#2d6a4f]">
                                <span class="text-sm text-gray-700 capitalize">{{ $loc }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="mulai_tgl" value="{{ old('mulai_tgl', $diskon->mulai_tgl) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="selesai_tgl" value="{{ old('selesai_tgl', $diskon->selesai_tgl) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Pelanggan</label>
                    <select name="id_pelanggan" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                        <option value="">Semua Pelanggan</option>
                        @foreach($pelanggan as $plg)
                            <option value="{{ $plg->id_pelanggan }}" 
                                {{ old('id_pelanggan', $diskon->id_pelanggan) == $plg->id_pelanggan ? 'selected' : '' }}>
                                {{ $plg->nama_pelanggan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Produk</label>
                    <div id="listProduk" class="grid grid-cols-2 sm:grid-cols-3 gap-2 border border-gray-200 rounded-xl p-4 max-h-56 overflow-y-auto">
                        @foreach($produk as $p)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="id_produk[]" value="{{ $p->id_produk }}"
                                       {{ in_array($p->id_produk, old('id_produk', $produkTerpilih)) ? 'checked' : '' }}
                                       class="rounded text-[#2d6a4f]">
                                <span>{{ $p->nama_produk }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="is_aktif" value="1"
                               {{ old('is_aktif', $diskon->is_aktif) ? 'checked' : '' }}
                               class="rounded text-[#2d6a4f]">
                        <span class="font-medium">Aktifkan diskon ini</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit"
                        class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition">
                    Update Simpan
                </button>
                <a href="{{ route('diskon.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>