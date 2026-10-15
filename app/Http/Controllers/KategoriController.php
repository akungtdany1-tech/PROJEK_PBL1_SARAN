<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
{
    // Cuma ambil yang statusnya active
    $kategoris = Kategori::where('status', 'active')->get(); 

    return view('kategori.index', compact('kategoris'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|regex:/^[a-zA-Z\s]+$/|max:255',
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.regex'    => 'Nama kategori hanya boleh huruf dan spasi.',
            'nama_kategori.max'      => 'Nama kategori maksimal 255 karakter.',
        ]);

        $cekData = Kategori::where('nama_kategori', $request->nama_kategori)->first();

        if ($cekData) {
            if ($cekData->status === 'inactive') {
                $cekData->update(['status' => 'active']);
                return redirect()->route('kategori.index')->with('success', 'Kategori sudah ada dan telah diaktifkan kembali!');
            }
            return back()->withErrors(['nama_kategori' => 'Nama kategori sudah ada dan masih aktif.']);
        }

        $kategori = Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'status'        => 'active'
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => "required|regex:/^[a-zA-Z\s]+$/|max:255|unique:kategori,nama_kategori,{$id},id_kategori",
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.regex'    => 'Nama kategori hanya boleh huruf dan spasi.',
            'nama_kategori.max'      => 'Nama kategori maksimal 255 karakter.',
            'nama_kategori.unique'   => 'Nama kategori sudah ada, gunakan nama lain.',
        ]);

        $kategori = Kategori::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success','Kategori dihapus (lihat Sampah)');
    }

    public function sampah()
    {
        $sampah = Kategori::onlyTrashed()->latest('deleted_at')->get();
        return view('kategori.sampah', compact('sampah'));
    }

    public function pulihkan($id)
    {
        $k = Kategori::withTrashed()->findOrFail($id);
        $k->restore();
        return redirect()->route('kategori.sampah')->with('success','Kategori dipulihkan');
    }

    public function hapusPermanen($id)
    {
        $k = Kategori::withTrashed()->findOrFail($id);
        $k->forceDelete();
        return redirect()->route('kategori.sampah')->with('success','Kategori dihapus permanen');
    }
}