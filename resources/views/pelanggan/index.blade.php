<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Pelanggan</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

        <div class="flex items-center justify-between mb-1">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Data Pelanggan</h2>
                <p class="text-xs text-gray-400">Kelola data pelanggan</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pelanggan.create') }}" class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
                    + Tambah
                </a>
                <a href="{{ route('pelanggan.sampah') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 shadow-md transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Sampah
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto mt-5">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-gray-500 text-xs">
                        <th class="pb-3 text-left font-semibold">No</th>
                        <th class="pb-3 text-left font-semibold">Nama</th>
                        <th class="pb-3 text-left font-semibold">No HP</th>
                        <th class="pb-3 text-left font-semibold">Alamat</th>
                        <th class="pb-3 text-left font-semibold">Tipe</th>
                        <th class="pb-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggan as $p)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                        <td class="py-3 text-gray-500">{{ $loop->iteration }}</td>
                        <td class="py-3 font-medium text-gray-800">{{ $p->nama_pelanggan }}</td>
                        <td class="py-3 text-gray-500">{{ $p->no_hp }}</td>
                        <td class="py-3 text-gray-500">{{ $p->alamat }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $p->tipe }}
                            </span>
                        </td>
                        <td class="py-3">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('pelanggan.edit', $p->id_pelanggan) }}" class="w-8 h-8 bg-yellow-100 hover:bg-yellow-200 rounded-lg flex items-center justify-center transition">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('pelanggan.destroy', $p->id_pelanggan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 bg-red-100 hover:bg-red-200 rounded-lg flex items-center justify-center transition">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-gray-400">Belum ada data pelanggan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
