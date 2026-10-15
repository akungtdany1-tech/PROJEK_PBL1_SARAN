<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Akuntansi</span>
            <span>›</span>
            <span class="text-gray-600 font-medium">Jurnal Umum</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800">Jurnal Umum</h2>
        <p class="text-xs text-gray-400 mb-6">Pencatatan transaksi keuangan (debet = kredit)</p>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4">{{ session('error') }}</div>
        @endif

        {{-- Form Jurnal Manual --}}
        <form method="POST" action="{{ route('akuntansi.jurnal.store') }}"
              x-data="{
                rows: [{ id_akun: '', debet: '', kredit: '' }, { id_akun: '', debet: '', kredit: '' }],
                totalDebet() { return this.rows.reduce((s, r) => s + (parseInt(r.debet) || 0), 0); },
                totalKredit() { return this.rows.reduce((s, r) => s + (parseInt(r.kredit) || 0), 0); },
                seimbang() { return this.totalDebet() === this.totalKredit() && this.rows.every(r => r.id_akun); }
              }"
              class="border border-dashed border-gray-300 rounded-xl p-5">
            @csrf
            <p class="text-xs font-bold uppercase text-gray-400 mb-4">Entry Jurnal Manual</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}" required maxlength="500"
                           placeholder="cth: Setoran modal awal"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                </div>
            </div>

            <div class="flex items-center gap-3 mb-2">
                <p class="text-xs font-bold text-gray-500 w-10">Baris</p>
                <p class="text-xs font-bold text-gray-500 flex-1">Akun</p>
                <p class="text-xs font-bold text-gray-500 w-32 text-right">Debet</p>
                <p class="text-xs font-bold text-gray-500 w-32 text-right">Kredit</p>
                <p class="w-8"></p>
            </div>

            <template x-for="(row, i) in rows" :key="i">
                <div class="flex items-center gap-3 mb-2">
                    <p class="text-xs text-gray-400 w-10" x-text="i + 1"></p>
                    <select x-model="row.id_akun" :name="'id_akun[' + i + ']'" required class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f] bg-white">
                        <option value="">— Pilih Akun —</option>
                        @foreach($akun as $a)
                            <option value="{{ $a->id_akun }}">{{ $a->kode_akun }} — {{ $a->nama_akun }}</option>
                        @endforeach
                    </select>
                    <input type="number" x-model="row.debet" :name="'debet[' + i + ']'" min="0" placeholder="0"
                           class="w-32 border border-gray-200 rounded-lg px-3 py-2 text-sm text-right focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                    <input type="number" x-model="row.kredit" :name="'kredit[' + i + ']'" min="0" placeholder="0"
                           class="w-32 border border-gray-200 rounded-lg px-3 py-2 text-sm text-right focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                    <button type="button" @click="rows.splice(i, 1)" x-show="rows.length > 2"
                            class="w-8 h-8 flex items-center justify-center text-red-400 hover:text-red-600 rounded-lg hover:bg-red-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>

            <div class="flex flex-wrap items-center justify-between gap-3 mt-4">
                <div>
                    <button type="button" @click="rows.push({ id_akun: '', debet: '', kredit: '' })"
                            class="flex items-center gap-2 text-[#2d6a4f] hover:text-[#1b4332] text-sm font-semibold">
                        <span class="w-6 h-6 rounded-full border-2 border-current flex items-center justify-center">+</span>
                        Tambah Baris
                    </button>
                </div>

                <div class="flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500">Debet:</span>
                        <b class="tabular-nums font-bold" x-text="'Rp ' + totalDebet().toLocaleString('id-ID')">Rp 0</b>
                        <span class="mx-2 text-gray-300">|</span>
                        <span class="text-gray-500">Kredit:</span>
                        <b class="tabular-nums font-bold" x-text="'Rp ' + totalKredit().toLocaleString('id-ID')">Rp 0</b>
                    </div>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-lg text-sm font-bold transition"
                            :class="seimbang() ? 'bg-[#2d6a4f] hover:bg-[#1b4332] text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                            :disabled="!seimbang()">
                        Simpan Jurnal
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Daftar Jurnal --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Daftar Jurnal</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                        <th class="px-6 py-3 rounded-l-lg">Tanggal</th>
                        <th class="px-4 py-3">No. Jurnal</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3">Akun</th>
                        <th class="px-4 py-3 text-right">Debet</th>
                        <th class="px-4 py-3 text-right">Kredit</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jurnal as $j)
                        @php $first = true; @endphp
                        @foreach($j->detail as $d)
                            <tr class="hover:bg-gray-50 {{ $first ? '' : '' }}">
                                @if($first)
                                    <td class="px-6 py-3 text-gray-600 align-top" rowspan="{{ $j->detail->count() }}">
                                        {{ \Carbon\Carbon::parse($j->tanggal)->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500 align-top" rowspan="{{ $j->detail->count() }}">
                                        {{ $j->no_jurnal }}<br>
                                        <span class="text-gray-400">oleh {{ $j->user->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-800 align-top" rowspan="{{ $j->detail->count() }}">
                                        {{ $j->keterangan }}
                                    </td>
                                @endif
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                    <span class="font-mono text-xs text-gray-400">{{ $d->akun->kode_akun }}</span>
                                    {{ $d->akun->nama_akun }}
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ $d->debet ? 'Rp ' . number_format($d->debet, 0, ',', '.') : '' }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ $d->kredit ? 'Rp ' . number_format($d->kredit, 0, ',', '.') : '' }}</td>
                                @if($first)
                                    <td class="px-4 py-3 text-right align-top" rowspan="{{ $j->detail->count() }}">
                                        <form method="POST" action="{{ route('akuntansi.jurnal.destroy', $j->id_jurnal) }}" onsubmit="return confirm('Hapus jurnal {{ $j->no_jurnal }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-500 hover:text-red-700 text-xs font-semibold">Hapus</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                            @php $first = false; @endphp
                        @endforeach
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada jurnal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>