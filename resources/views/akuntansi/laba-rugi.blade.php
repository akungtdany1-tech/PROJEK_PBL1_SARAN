<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Akuntansi</span>
            <span>›</span>
            <span class="text-gray-600 font-medium">Laporan Laba Rugi</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Laporan Laba Rugi</h2>
                <p class="text-xs text-gray-400">Pendapatan, HPP, dan beban operasional</p>
            </div>
            <button onclick="window.print()"
                    class="flex items-center gap-2 bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak
            </button>
        </div>

        <form method="GET" action="{{ route('akuntansi.laba-rugi') }}" class="flex items-end gap-4 mb-6">
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

        {{-- Kartu Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="border border-gray-200 rounded-lg p-5">
                <p class="text-xs font-medium text-gray-600 mb-1">Total Pendapatan</p>
                <p class="text-xl font-bold text-[#2d6a4f]">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-5">
                <p class="text-xs font-medium text-gray-600 mb-1">Harga Pokok Penjualan (HPP)</p>
                <p class="text-xl font-bold text-gray-800">Rp {{ number_format($hpp, 0, ',', '.') }}</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-5">
                <p class="text-xs font-medium text-gray-600 mb-1">Beban Operasional</p>
                <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalBeban, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Tabel Laba Rugi --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 font-bold text-gray-800">Pendapatan Penjualan</td>
                        <td class="px-4 py-3 text-right font-semibold tabular-nums text-[#2d6a4f]">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-gray-700">Harga Pokok Penjualan (HPP)</td>
                        <td class="px-4 py-3 text-right tabular-nums text-red-500">(Rp {{ number_format($hpp, 0, ',', '.') }})</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 font-bold text-gray-800">Laba Kotor</td>
                        <td class="px-4 py-3 text-right font-semibold tabular-nums text-gray-800">Rp {{ number_format($totalPendapatan - $hpp, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-gray-100">
                        <td class="px-4 py-3 font-bold text-gray-700" colspan="2">Beban Operasional</td>
                    </tr>
                    @foreach($bebanItems as $item)
                        <tr>
                            <td class="px-8 py-2.5 text-gray-600">
                                <span class="font-mono text-xs text-gray-400">{{ $item['akun']->kode_akun }}</span>
                                {{ $item['akun']->nama_akun }}
                            </td>
                            <td class="px-4 py-2.5 text-right tabular-nums text-red-500">(Rp {{ number_format($item['nilai'], 0, ',', '.') }})</td>
                        </tr>
                    @endforeach
                    <tr class="border-t-2 border-gray-200">
                        <td class="px-4 py-4 font-bold text-lg text-gray-800">Laba Bersih</td>
                        <td class="px-4 py-4 text-right font-black text-lg tabular-nums {{ $labaBersih >= 0 ? 'text-[#2d6a4f]' : 'text-red-600' }}">
                            {{ $labaBersih < 0 ? '-' : '' }}Rp {{ number_format(abs($labaBersih), 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="text-[11px] text-gray-400 mt-4">
            * HPP dihitung dari harga pokok barang yang terjual (debet akun HPP) pada periode ini.
        </p>
    </div>
</x-app-layout>