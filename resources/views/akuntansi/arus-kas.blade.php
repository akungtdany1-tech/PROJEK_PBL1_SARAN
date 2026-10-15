<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Akuntansi</span>
            <span>›</span>
            <span class="text-gray-600 font-medium">Arus Kas</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Laporan Arus Kas</h2>
                <p class="text-xs text-gray-400">Kas masuk dan kas keluar periode</p>
            </div>
            <button onclick="window.print()"
                    class="flex items-center gap-2 bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak
            </button>
        </div>

        <form method="GET" action="{{ route('akuntansi.arus-kas') }}" class="flex items-end gap-4 mb-6">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Periode</label>
                <div class="flex items-center gap-2">
                    <input type="date" name="dari" value="{{ $dari }}"
                           class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                    <span class="text-gray-400 text-sm">—</span>
                    <input type="date" name="sampai" value="{{ $sampai }}"
                           class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                </div>
            </div>
            <button type="submit" class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-5 py-2 rounded-lg transition">Filter</button>
        </form>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="border border-gray-200 rounded-lg p-5">
                <p class="text-xs font-medium text-gray-600 mb-1">Saldo Awal</p>
                <p class="text-xl font-bold text-gray-800 tabular-nums">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-5">
                <p class="text-xs font-medium text-gray-600 mb-1">Kas Masuk</p>
                <p class="text-xl font-bold text-[#2d6a4f] tabular-nums">+Rp {{ number_format($penerimaan, 0, ',', '.') }}</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-5">
                <p class="text-xs font-medium text-gray-600 mb-1">Kas Keluar</p>
                <p class="text-xl font-bold text-red-500 tabular-nums">-Rp {{ number_format($pengeluaran, 0, ',', '.') }}</p>
            </div>
            <div class="border-2 border-[#2d6a4f] rounded-lg p-5">
                <p class="text-xs font-medium text-gray-600 mb-1">Saldo Akhir</p>
                <p class="text-xl font-black text-[#2d6a4f] tabular-nums">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Rincian Mutasi --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                        <th class="px-4 py-3 rounded-l-lg">Tanggal</th>
                        <th class="px-4 py-3">No. Jurnal</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-right">Masuk (Debet)</th>
                        <th class="px-4 py-3 text-right rounded-r-lg">Keluar (Kredit)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($rincian as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($r->jurnal->tanggal)->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $r->jurnal->no_jurnal }}</td>
                            <td class="px-4 py-3 text-gray-800">{{ $r->jurnal->keterangan }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-[#2d6a4f] {{ $r->debet ? 'font-semibold' : '' }}">
                                {{ $r->debet ? '+Rp ' . number_format($r->debet, 0, ',', '.') : '' }}
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums text-red-500 {{ $r->kredit ? 'font-semibold' : '' }}">
                                {{ $r->kredit ? '-Rp ' . number_format($r->kredit, 0, ',', '.') : '' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Tidak ada mutasi kas pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>