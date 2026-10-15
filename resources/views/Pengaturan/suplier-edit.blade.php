<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-[#2d6a4f]">Dashboard</a>
            <span>›</span>
            <a href="{{ route('pengaturan.suplier') }}" class="hover:text-[#2d6a4f]">Akun Suplier</a>
            <span>›</span>
            <span class="text-gray-600 font-medium">Edit Suplier</span>
        </div>
    </x-slot>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-md">
        <h3 class="font-bold text-gray-800 mb-1">Edit Suplier</h3>
        <p class="text-xs text-gray-400 mb-5">Perbarui informasi suplier</p>

        <form method="POST" action="{{ route('pengaturan.suplier.update', $suplier->id_suplier) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Suplier</label>
                    <input type="text" name="nama_suplier" value="{{ old('nama_suplier', $suplier->nama_suplier) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    @error('nama_suplier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $suplier->no_hp) }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">
                    @error('no_hp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="3"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2d6a4f]">{{ old('alamat', $suplier->alamat) }}</textarea>
                    @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="bg-[#2d6a4f] hover:bg-[#1b4332] text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition">
                        Update
                    </button>
                    <a href="{{ route('pengaturan.suplier') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>