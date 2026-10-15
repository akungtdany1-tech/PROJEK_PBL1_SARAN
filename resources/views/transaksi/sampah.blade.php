@extends('layouts.app')

@section('header')
<div class="flex items-center gap-2 text-sm text-gray-400">
    <a href="{{ route('transaksi.index') }}" class="hover:text-[#2d6a4f]">Transaksi</a>
    <span>›</span>
    <span class="text-gray-600 font-medium">Sampah</span>
</div>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Sampah Transaksi Penjualan</h2>
            <p class="text-xs text-gray-400">Pulihkan transaksi yang terhapus atau hapus secara permanen.</p>
        </div>
        <a href="{{ route('transaksi.index') }}" class="text-sm font-semibold text-[#2d6a4f] hover:underline">← Kembali ke Transaksi</a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-gray-500 text-xs">
                    <th class="pb-3 text-left font-semibold uppercase tracking-wider">ID</th>
                    <th class="pb-3 text-left font-semibold uppercase tracking-wider">Tanggal Transaksi</th>
                    <th class="pb-3 text-left font-semibold uppercase tracking-wider">Pelanggan</th>
                    <th class="pb-3 text-left font-semibold uppercase tracking-wider">Metode</th>
                    <th class="pb-3 text-left font-semibold uppercase tracking-wider">Total Akhir</th>
                    <th class="pb-3 text-left font-semibold uppercase tracking-wider">Dihapus Pada</th>
                    <th class="pb-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($transaksi as $t)
                <tr class="hover:bg-gray-50 transition group">
                    <td class="py-4 text-gray-400 font-mono">#{{ $t->id_transaksi }}</td>
                    <td class="py-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}
                    </td>
                    <td class="py-4 font-medium text-gray-700">{{ $t->pelanggan->nama_pelanggan ?? 'Umum' }}</td>
                    <td class="py-4">
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-600 border border-blue-100 rounded-md text-[10px] font-bold uppercase">
                            {{ $t->metode_pembayaran }}
                        </span>
                    </td>
                    <td class="py-4 font-bold text-gray-800">
                        Rp {{ number_format($t->total, 0, ',', '.') }}
                    </td>
                    <td class="py-4 text-gray-500 text-xs">
                        {{ $t->deleted_at ? $t->deleted_at->format('d M Y, H:i') : '-' }}
                    </td>
                    <td class="py-4">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Tombol Pulihkan --}}
                            <form action="{{ route('transaksi.pulihkan', $t->id_transaksi) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1.5 bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-xs font-semibold rounded-lg transition"
                                        title="Pulihkan transaksi">
                                    Pulihkan
                                </button>
                            </form>

                            {{-- Tombol Hapus Permanen --}}
                            <form action="{{ route('transaksi.hapusPermanen', $t->id_transaksi) }}" method="POST"
                                  onsubmit="return confirm('Hapus transaksi ini secara permanen? Tindakan ini tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition"
                                        title="Hapus permanen">
                                    Hapus Permanen
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-gray-400">
                        Tidak ada transaksi di dalam sampah.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
