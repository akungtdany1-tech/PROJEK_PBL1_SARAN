<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    // 🔹 TAMPILKAN DATA
    public function index()
    {
        $pelanggan = Pelanggan::all();
        return view('pelanggan.index', compact('pelanggan'));
    }

    // 🔹 FORM TAMBAH
    public function create()
    {
        return view('pelanggan.create');
    }

    // 🔹 SIMPAN DATA
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required',
            'tipe' => 'required'
        ]);

        Pelanggan::create($request->all());

        return redirect('/pelanggan')->with('success', 'Data pelanggan berhasil ditambahkan');
    }

    // 🔹 FORM EDIT
    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('pelanggan.edit', compact('pelanggan'));
    }

    // 🔹 UPDATE DATA
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required',
            'tipe' => 'required'
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($request->all());

        return redirect('/pelanggan')->with('success', 'Data pelanggan berhasil diupdate');
    }

    // 🔹 HAPUS DATA (soft delete)
    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        return redirect('/pelanggan')->with('success', 'Data pelanggan berhasil dihapus (masuk ke Sampah)');
    }

    // 🔹 SAMPAH (hanya yang sudah dihapus)
    public function sampah()
    {
        $sampah = Pelanggan::onlyTrashed()->latest('deleted_at')->get();
        return view('pelanggan.sampah', compact('sampah'));
    }

    // 🔹 PULIHKAN (restore)
    public function pulihkan($id)
    {
        $p = Pelanggan::withTrashed()->findOrFail($id);
        $p->restore();

        return redirect()->route('pelanggan.sampah')->with('success', 'Pelanggan berhasil dipulihkan');
    }

    // 🔹 HAPUS PERMANEN
    public function hapusPermanen($id)
    {
        $p = Pelanggan::withTrashed()->findOrFail($id);

        // Cegah hapus permanen kalau masih dipakai di transaksi aktif
        if (Transaksi::where('id_pelanggan', $id)->exists()) {
            return back()->with('error', "Pelanggan \"{$p->nama_pelanggan}\" tidak bisa dihapus permanen karena masih dipakai di transaksi.");
        }

        $p->forceDelete();

        return redirect()->route('pelanggan.sampah')->with('success', 'Pelanggan berhasil dihapus permanen');
    }
}