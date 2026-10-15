<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Akuntansi</span>
            <span>›</span>
            <span class="text-gray-600 font-medium">Chart of Accounts</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg border border-gray-200 p-6">

        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Chart of Accounts (COA)</h2>
                <p class="text-xs text-gray-400">Daftar akun pencatatan transaksi keuangan</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4">{{ session('error') }}</div>
        @endif

        {{-- Tabel COA --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                        <th class="px-4 py-3 rounded-l-lg">Kode</th>
                        <th class="px-4 py-3">Nama Akun</th>
                        <th class="px-4 py-3">Saldo Normal</th>
                        <th class="px-4 py-3">Laporan</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 rounded-r-lg text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($akun as $a)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-gray-700">{{ $a->kode_akun }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $a->nama_akun }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $a->pos_saldo === 'debet' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ ucfirst($a->pos_saldo) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 capitalize">{{ str_replace('_', ' ', $a->pos_laporan) }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ str_replace('_', ' ', $a->kategori_neraca ?? $a->kategori_laba_rugi ?? '-') }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('akuntansi.coa.destroy', $a->id_akun) }}" onsubmit="return confirm('Hapus akun {{ $a->nama_akun }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-xs font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada akun. Jalankan seeder atau tambah akun di bawah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Form Tambah Akun --}}
        <form method="POST" action="{{ route('akuntansi.coa.store') }}" class="mt-8 border-t border-gray-100 pt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Kode Akun</label>
                <input type="text" name="kode_akun" maxlength="20" required placeholder="cth: 4101"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Nama Akun</label>
                <input type="text" name="nama_akun" maxlength="150" required placeholder="cth: Pendapatan Penjualan"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Saldo Normal</label>
                <select name="pos_saldo" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                    <option value="debet">Debet</option>
                    <option value="kredit">Kredit</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Posisi Laporan</label>
                <select name="pos_laporan" id="pos_laporan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                    <option value="neraca">Neraca</option>
                    <option value="laba_rugi">Laba Rugi</option>
                </select>
            </div>
            <div id="kategori_neraca_wrap">
                <label class="block text-xs font-medium text-gray-500 mb-1">Kategori Neraca</label>
                <select name="kategori_neraca" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                    <option value="aktiva_lancar">Aktiva Lancar</option>
                    <option value="aktiva_tetap">Aktiva Tetap</option>
                    <option value="kewajiban_lancar">Kewajiban Lancar</option>
                    <option value="kewajiban_jangka_panjang">Kewajiban Jangka Panjang</option>
                    <option value="modal">Modal</option>
                </select>
            </div>
            <div id="kategori_laba_rugi_wrap" class="hidden">
                <label class="block text-xs font-medium text-gray-500 mb-1">Kategori Laba Rugi</label>
                <select name="kategori_laba_rugi" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#2d6a4f]">
                    <option value="pendapatan">Pendapatan</option>
                    <option value="beban_pokok">Beban Pokok</option>
                    <option value="beban_operasional">Beban Operasional</option>
                    <option value="pendapatan_lain">Pendapatan Lain</option>
                    <option value="beban_lain">Beban Lain</option>
                </select>
            </div>
            <div class="md:col-span-2 xl:col-span-3 flex items-end">
                <button type="submit"
                        class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition">
                    + Tambah Akun
                </button>
            </div>
        </form>
    </div>

    <script>
        const posLaporan = document.getElementById('pos_laporan');
        const wrapNeraca = document.getElementById('kategori_neraca_wrap');
        const wrapLabaRugi = document.getElementById('kategori_laba_rugi_wrap');
        posLaporan.addEventListener('change', () => {
            wrapNeraca.classList.toggle('hidden', posLaporan.value !== 'neraca');
            wrapLabaRugi.classList.toggle('hidden', posLaporan.value !== 'laba_rugi');
        });
    </script>
</x-app-layout>