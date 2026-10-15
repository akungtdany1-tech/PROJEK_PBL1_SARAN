<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Akuntansi</span>
            <span>›</span>
            <span class="text-gray-600 font-medium">Neraca</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Neraca</h2>
                <p class="text-xs text-gray-400">Posisi keuangan per <b>{{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</b></p>
            </div>
            <button onclick="window.print()"
                    class="flex items-center gap-2 bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak
            </button>
        </div>

        <form method="GET" action="{{ route('akuntansi.neraca') }}" class="flex items-end gap-4 mb-6">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Neraca</label>
                <input type="date" name="sampai" value="{{ $sampai }}"
                       class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
            </div>
            <button type="submit" class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-5 py-2 rounded-lg transition">Filter</button>
        </form>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- ASET --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-5 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 text-lg">ASET</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="px-5 py-2.5 bg-gray-50/50 text-xs font-bold text-gray-500 uppercase">Aktiva Lancar</div>
                    @foreach($aktiva->where('akun.kategori_neraca', 'aktiva_lancar') as $a)
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-sm text-gray-700">
                                <span class="font-mono text-xs text-gray-400">{{ $a['akun']->kode_akun }}</span>
                                {{ $a['akun']->nama_akun }}
                            </span>
                            <span class="text-sm font-semibold tabular-nums">Rp {{ number_format($a['saldo'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="px-5 py-2.5 bg-gray-50/50 text-xs font-bold text-gray-500 uppercase">Aktiva Tetap</div>
                    @foreach($aktiva->where('akun.kategori_neraca', 'aktiva_tetap') as $a)
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-sm text-gray-700">
                                <span class="font-mono text-xs text-gray-400">{{ $a['akun']->kode_akun }}</span>
                                {{ $a['akun']->nama_akun }}
                            </span>
                            <span class="text-sm font-semibold tabular-nums">Rp {{ number_format($a['saldo'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="flex items-center justify-between px-5 py-4 bg-gray-50 border-t border-gray-200">
                        <span class="font-bold text-gray-800">TOTAL ASET</span>
                        <span class="font-black text-lg tabular-nums text-[#2d6a4f]">Rp {{ number_format($totalAktiva, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- KEWAJIBAN + MODAL --}}
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-5 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800 text-lg">KEWAJIBAN & MODAL</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="px-5 py-2.5 bg-gray-50/50 text-xs font-bold text-gray-500 uppercase">Kewajiban</div>
                    @foreach($kewajiban as $k)
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-sm text-gray-700">
                                <span class="font-mono text-xs text-gray-400">{{ $k['akun']->kode_akun }}</span>
                                {{ $k['akun']->nama_akun }}
                            </span>
                            <span class="text-sm font-semibold tabular-nums">Rp {{ number_format($k['saldo'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach

                    <div class="px-5 py-2.5 bg-gray-50/50 text-xs font-bold text-gray-500 uppercase">Modal</div>
                    @foreach($modalItems as $m)
                        <div class="flex items-center justify-between px-5 py-3">
                            <span class="text-sm text-gray-700">
                                <span class="font-mono text-xs text-gray-400">{{ $m['akun']->kode_akun }}</span>
                                {{ $m['akun']->nama_akun }}
                            </span>
                            <span class="text-sm font-semibold tabular-nums {{ $m['saldo'] < 0 ? 'text-red-500' : '' }}">
                                {{ $m['saldo'] < 0 ? '(' : '' }}Rp {{ number_format(abs($m['saldo']), 0, ',', '.') }}{{ $m['saldo'] < 0 ? ')' : '' }}
                            </span>
                        </div>
                    @endforeach
                    <div class="flex items-center justify-between px-5 py-3">
                        <span class="text-sm text-gray-700">Laba Ditahan</span>
                        <span class="text-sm font-semibold tabular-nums {{ $labaDitahan < 0 ? 'text-red-500' : 'text-[#2d6a4f]' }}">
                            {{ $labaDitahan < 0 ? '(' : '' }}Rp {{ number_format(abs($labaDitahan), 0, ',', '.') }}{{ $labaDitahan < 0 ? ')' : '' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between px-5 py-4 bg-gray-50 border-t border-gray-200">
                        <span class="font-bold text-gray-800">TOTAL KEWAJIBAN & MODAL</span>
                        <span class="font-black text-lg tabular-nums text-[#2d6a4f]">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-4 text-center">
            <div class="text-xs text-gray-500">Total Aset</div>
            <div class="text-xs text-gray-500">Total Kewajiban</div>
            <div class="text-xs text-gray-500">Total Modal</div>
            <div class="font-bold tabular-nums">Rp {{ number_format($totalAktiva, 0, ',', '.') }}</div>
            <div class="font-bold tabular-nums">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</div>
            <div class="font-bold tabular-nums">Rp {{ number_format($totalModal, 0, ',', '.') }}</div>
        </div>

        @if($totalAktiva !== $totalPasiva)
            <div class="mt-4 bg-amber-50 border border-amber-200 text-amber-700 text-sm px-4 py-3 rounded-lg">
                Perhatian: Total aset (Rp {{ number_format($totalAktiva, 0, ',', '.') }}) tidak sama dengan total kewajiban & modal (Rp {{ number_format($totalPasiva, 0, ',', '.') }}). Periksa jurnal pembuka modal.
            </div>
        @endif

        <p class="text-[11px] text-gray-400 mt-4">
            * Untuk saldo awal realistis, buat jurnal manual setoran modal: Debet <span class="font-mono">1101 Kas</span> — Kredit <span class="font-mono">3101 Modal Pemilik</span>.
        </p>
    </div>
</x-app-layout>