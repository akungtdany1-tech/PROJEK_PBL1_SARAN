<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <a href="{{ route('pembelian.index') }}" class="hover:text-[#2d6a4f]">Pembelian</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">#{{ $pembelian->id_transaksi }}</span>
        </div>
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Detail Pembelian #{{ $pembelian->id_transaksi }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ \Carbon\Carbon::parse($pembelian->tanggal)->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('pembelian.index') }}"
                       class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">
                        ← Kembali
                    </a>
                    <form action="{{ route('pembelian.destroy', $pembelian->id_transaksi) }}" method="POST"
                          onsubmit="return confirm('Yakin hapus pembelian ini? Stok akan berkurang.')">
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
                <h3 class="font-bold text-gray-800 mb-4">Detail Produk</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-gray-500 text-xs">
                                <th class="pb-3 text-left font-semibold">Produk</th>
                                <th class="pb-3 text-left font-semibold">Jumlah</th>
                                <th class="pb-3 text-left font-semibold">Harga Beli</th>
                                <th class="pb-3 text-right font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembelian->detail as $d)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                <td class="py-3 font-medium text-gray-800">{{ $d->produk->nama_produk ?? 'Produk Terhapus' }}</td>
                                <td class="py-3 text-gray-600">{{ $d->jumlah }}</td>
                                <td class="py-3 text-gray-600">Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                                <td class="py-3 text-right font-semibold text-gray-800">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-gray-200 bg-gray-50">
                                <td colspan="3" class="py-3 text-right font-bold text-gray-700">Total:</td>
                                <td class="py-3 text-right font-bold text-[#2d6a4f] text-base">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="w-80 flex flex-col gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-3">Info Pembelian</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-gray-500">Suplier</span>
                            <span class="text-gray-800 font-medium">{{ $pembelian->suplier->nama_suplier ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-gray-500">Petugas</span>
                            <span class="text-gray-800 font-medium">
                                {{ $pembelian->user->name ?? '-' }}
                                <span class="text-xs text-gray-400">({{ $pembelian->user->role ?? '-' }})</span>
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-gray-500">Tanggal</span>
                            <span class="text-gray-800 font-medium">{{ \Carbon\Carbon::parse($pembelian->tanggal)->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($pembelian->keterangan)
                        <div class="pt-2">
                            <span class="text-gray-500 block mb-1">Keterangan</span>
                            <span class="text-gray-700 text-xs italic bg-gray-50 p-2 rounded-lg block">"{{ $pembelian->keterangan }}"</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-3">Ringkasan</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between font-bold text-gray-800 text-lg">
                            <span>Total Pembelian</span>
                            <span class="text-[#2d6a4f]">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>