<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <a href="{{ route('transaksi.index') }}" class="hover:text-[#2d6a4f]">Transaksi</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">#{{ $transaksi->id_transaksi }}</span>
        </div>
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Detail Transaksi #{{ $transaksi->id_transaksi }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ ($transaksi->created_at ?? \Carbon\Carbon::parse($transaksi->tanggal))->format('d/m/Y H:i') }} WIB
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold capitalize">
                        {{ $transaksi->metode_pembayaran }}
                    </span>
                    <a href="{{ route('transaksi.index') }}"
                       class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">
                        ← Kembali
                    </a>
                    <form action="{{ route('transaksi.destroy', $transaksi->id_transaksi) }}" method="POST"
                          onsubmit="return confirm('Yakin hapus transaksi ini? Stok akan dikembalikan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-xl text-sm font-semibold transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="flex gap-4">
            <div class="flex-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Produk yang Dibeli</h3>
                <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 text-xs">
                            <th class="pb-3 text-left font-semibold">Produk</th>
                            <th class="pb-3 text-left font-semibold">Tipe</th>
                            <th class="pb-3 text-left font-semibold">Harga</th>
                            <th class="pb-3 text-left font-semibold">Jumlah</th>
                            <th class="pb-3 text-left font-semibold">Diskon</th>
                            <th class="pb-3 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaksi->detail as $d)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="py-3 font-medium text-gray-800">{{ $d->produk->nama_produk ?? 'Produk Terhapus' }}</td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold
                                    {{ $d->tipe === 'grosir' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }}">
                                    {{ ucfirst($d->tipe) }}
                                </span>
                            </td>
                            <td class="py-3 text-gray-600">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                            <td class="py-3 text-gray-600">{{ $d->jumlah }}</td>
                            <td class="py-3 text-orange-500">
                                @if($d->nominal_diskon > 0)
                                    {{-- FIX: nominal_diskon di DB sudah per-unit, dikali jumlah untuk tampil total diskon item --}}
                                    - Rp {{ number_format($d->nominal_diskon * $d->jumlah, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 text-right font-semibold text-gray-800">
                                Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>

            <div class="w-72 flex flex-col gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-3">Info Transaksi</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kasir</span>
                            <span class="text-gray-800 font-medium">{{ $transaksi->kasir->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Pelanggan</span>
                            <span class="text-gray-800 font-medium">{{ $transaksi->pelanggan->nama_pelanggan ?? 'Umum' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Metode</span>
                            <span class="text-gray-800 font-medium capitalize">{{ $transaksi->metode_pembayaran }}</span>
                        </div>
                        @if($transaksi->catatan)
                        <div class="pt-2 border-t border-gray-100">
                            <span class="text-gray-500 block mb-1">Catatan</span>
                            <span class="text-gray-700 text-xs italic">"{{ $transaksi->catatan }}"</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-3">Ringkasan Pembayaran</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-orange-500">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($transaksi->total_diskon, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-800 text-base border-t border-gray-100 pt-2">
                            <span>Total</span>
                            <span>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600 pt-1">
                            <span>Dibayar</span>
                            <span>Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-[#2d6a4f] font-bold bg-green-50 p-2 rounded-lg mt-2">
                            <span>Kembalian</span>
                            <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>