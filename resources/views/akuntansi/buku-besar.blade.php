<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Akuntansi</span>
            <span>›</span>
            <span class="text-gray-600 font-medium">Buku Besar</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg border border-gray-200 p-6">

        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Buku Besar</h2>
                <p class="text-xs text-gray-400">Ringkasan saldo akun dari jurnal umum</p>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('akuntansi.buku-besar') }}" class="flex items-end gap-4 mb-6 flex-wrap">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Akun</label>
                <select name="akun" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f] min-w-[220px]">
                    <option value="semua" {{ ($akunId ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Akun</option>
                    @foreach($akunList as $a)
                        <option value="{{ $a->id_akun }}" {{ ($akunId ?? '') == $a->id_akun ? 'selected' : '' }}>
                            {{ $a->kode_akun }} — {{ $a->nama_akun }}
                        </option>
                    @endforeach
                </select>
            </div>
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

        @if(isset($akun))
            {{-- Detil per akun --}}
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800">
                    {{ $akun->kode_akun }} — {{ $akun->nama_akun }}
                    <span class="ml-2 text-[11px] px-2 py-0.5 rounded {{ $akun->pos_saldo === 'debet' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600' }} font-bold">
                        Saldo Normal {{ ucfirst($akun->pos_saldo) }}
                    </span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                            <th class="px-4 py-3 rounded-l-lg">Tanggal</th>
                            <th class="px-4 py-3">No. Jurnal</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3 text-right">Debet</th>
                            <th class="px-4 py-3 text-right">Kredit</th>
                            <th class="px-4 py-3 text-right rounded-r-lg">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($mutasi as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($row->jurnal->tanggal)->format('d M Y') }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $row->jurnal->no_jurnal }}</td>
                                <td class="px-4 py-3 text-gray-800">{{ $row->jurnal->keterangan }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ $row->debet ? 'Rp ' . number_format($row->debet, 0, ',', '.') : '' }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ $row->kredit ? 'Rp ' . number_format($row->kredit, 0, ',', '.') : '' }}</td>
                                <td class="px-4 py-3 text-right font-bold tabular-nums {{ $row->saldo_berjalan >= 0 ? 'text-[#2d6a4f]' : 'text-red-500' }}">
                                    {{ $row->saldo_berjalan < 0 ? '-' : '' }}Rp {{ number_format(abs($row->saldo_berjalan), 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada mutasi pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            {{-- Ringkasan semua akun --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                            <th class="px-4 py-3 rounded-l-lg">Kode</th>
                            <th class="px-4 py-3">Nama Akun</th>
                            <th class="px-4 py-3 text-right">Total Debet</th>
                            <th class="px-4 py-3 text-right">Total Kredit</th>
                            <th class="px-4 py-3 text-right rounded-r-lg">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ringkasan as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-gray-700">{{ $r->kode_akun }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $r->nama_akun }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">Rp {{ number_format($r->total_debet, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">Rp {{ number_format($r->total_kredit, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold tabular-nums {{ $r->saldo >= 0 ? 'text-[#2d6a4f]' : 'text-red-500' }}">
                                    {{ $r->saldo < 0 ? '-' : '' }}Rp {{ number_format(abs($r->saldo), 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada jurnal pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>