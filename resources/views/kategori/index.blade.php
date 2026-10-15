<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Kategori</span>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form Tambah Kategori --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-1">Tambah Kategori</h3>
            <p class="text-xs text-gray-400 mb-5">Tambah kategori produk baru</p>

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('kategori.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                        <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}"
                               placeholder="Contoh: Kelistrikan, Pertanian, dll"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    </div>
                    <button type="submit"
                            class="w-full bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold py-2.5 rounded-xl transition">
                        Tambah Kategori
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabel Daftar Kategori --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-bold text-gray-800">Daftar Kategori</h3>
                    <p class="text-xs text-gray-400">Kelola semua kategori produk toko</p>
                </div>
                <a href="{{ route('kategori.sampah') }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 shadow-md transition">
                    Sampah
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-500 text-xs">
                            <th class="pb-3 text-left font-semibold">ID</th>
                            <th class="pb-3 text-left font-semibold">Nama Kategori</th>
                            <th class="pb-3 text-left font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($kategoris as $k)
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="py-3 text-gray-500">{{ $loop->iteration }}</td>
                            <td class="py-3 font-medium text-gray-800">{{ $k->nama_kategori }}</td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('kategori.edit', $k->id_kategori) }}"
                                       class="w-8 h-8 bg-yellow-100 hover:bg-yellow-200 rounded-lg flex items-center justify-center transition"
                                       title="Edit">
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('kategori.destroy', $k->id_kategori) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')" class="inline">@csrf @method('DELETE')<button type="submit"
                                                class="w-8 h-8 bg-red-100 hover:bg-red-200 rounded-lg flex items-center justify-center transition"
                                                title="Hapus">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-gray-400">Belum ada kategori yang ditambahkan</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
{{-- Modal Konfirmasi Hapus Custom --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4" onclick="closeDeleteModal()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200" onclick="event.stopPropagation()">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Hapus Kategori</h3>
        </div>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus kategori <strong id="modalNama" class="text-[#2d6a4f]"></strong>? Data akan dipindahkan ke Sampah dan bisa dipulihkan kapan saja.</p>
        <div class="flex gap-3 justify-end">
            <button onclick="closeDeleteModal()" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition">Batal</button>
            <button onclick="confirmDelete()" class="px-4 py-2 bg-[#2d6a4f] text-white rounded-lg text-sm font-medium hover:bg-[#1b4332] transition">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
let deleteId = null;
function openDeleteModal(id, nama) {
    deleteId = id;
    document.getElementById('modalNama').textContent = nama || 'ini';
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeDeleteModal() {
    deleteId = null;
    document.getElementById('deleteModal').classList.add('hidden');
}
function confirmDelete() {
    if (!deleteId) return;
    fetch(`/kategori/${deleteId}`, {
        method: 'POST', headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded'
        }, body: '_token=' + document.querySelector('meta[name="csrf-token"]').content + '&_method=DELETE'
    })
    .then(r => r.json()).then(json => {
        if (json.success || json.redirect) {
            const row = document.getElementById('row-'+deleteId);
            if (row) row.remove();
        }
        closeDeleteModal();
    }).catch(e => { console.error(e); closeDeleteModal(); });
}
</script>
