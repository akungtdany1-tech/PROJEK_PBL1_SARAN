<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Pembelian</span>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Data Pembelian</h2>
                <p class="text-xs text-gray-400">Kelola pembelian produk dari suplier</p>
            </div>
            <a href="{{ route('pembelian.create') }}">
                <button class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                    + Tambah
                </button>
            </a>
        </div>

        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-gray-500 text-xs">
                    <th class="pb-3 text-left font-semibold">ID</th>
                    <th class="pb-3 text-left font-semibold">Tanggal</th>
                    <th class="pb-3 text-left font-semibold">Suplier</th>
                    <th class="pb-3 text-left font-semibold">Total</th>
                    <th class="pb-3 text-left font-semibold">Petugas</th>  {{-- TAMBAH INI --}}
                    <th class="pb-3 text-left font-semibold">Keterangan</th>
                    <th class="pb-3 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembelian as $p)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="py-3 text-gray-500 font-mono">#{{ $p->id_transaksi }}</td>
                    <td class="py-3 text-gray-700">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                    <td class="py-3 text-gray-700">{{ $p->suplier->nama_suplier ?? '-' }}</td>
                    <td class="py-3 font-medium text-gray-800">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                    <td class="py-3 text-gray-700">{{ $p->user->name ?? '-' }} {{-- TAMBAH INI --}}</td>
                    <td class="py-3 text-gray-500">{{ $p->keterangan ?? '-' }}</td>
                    <td class="py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pembelian.show', $p->id_transaksi) }}"
                               class="w-8 h-8 bg-blue-100 hover:bg-blue-200 rounded-lg flex items-center justify-center transition">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <form action="{{ route('pembelian.destroy', $p->id_transaksi) }}" method="POST"
                                  onsubmit="return confirm('Yakin hapus pembelian ini? Stok produk akan berkurang.')">
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
                    <td colspan="7" class="py-10 text-center text-gray-400">Belum ada data pembelian</td> {{-- colspan jadi 7 --}}
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-app-layout>