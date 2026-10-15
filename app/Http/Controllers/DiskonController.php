<?php

namespace App\Http\Controllers;

use App\Models\Diskon;
use App\Models\Produk;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class DiskonController extends Controller
{
    public function index()
    {
        $diskon = Diskon::with('produk')->latest('id_diskon')->get();
        return view('diskon.index', compact('diskon'));
    }

    public function create()
    {
        $produk = Produk::with('kategori')->get();
        $pelanggan = Pelanggan::all();
        return view('diskon.create', compact('produk', 'pelanggan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_diskon'         => 'required|string|max:150',
            'besar_diskon'        => 'required|integer|min:1|max:100',
            'minimal_beli'        => 'nullable|integer|min:0', // Buat nullable
            'minimal_beli_grosir' => 'nullable|integer|min:0', // Buat nullable
            'mulai_tgl'           => 'required|date',
            'selesai_tgl'         => 'required|date|after_or_equal:mulai_tgl',
            'lokasi_berlaku'      => 'required|in:semua,gudang,toko',
            'id_produk'           => 'required|array|min:1',
            'id_produk.*'         => 'exists:produk,id_produk',
            'id_pelanggan'        => 'nullable|exists:pelanggan,id_pelanggan',
        ], [
            'nama_diskon.required'  => 'Nama diskon wajib diisi.',
            'besar_diskon.required' => 'Besar diskon wajib diisi.',
            'mulai_tgl.required'    => 'Tanggal mulai wajib diisi.',
            'selesai_tgl.required'  => 'Tanggal selesai wajib diisi.',
            'id_produk.required'    => 'Minimal satu produk wajib dipilih.',
        ]);

        $diskon = Diskon::create([
            'nama_diskon'         => $request->nama_diskon,
            'besar_diskon'        => $request->besar_diskon,
            'minimal_beli'        => $request->minimal_beli ?? 0, // Jika kosong isi 0
            'minimal_beli_grosir' => $request->minimal_beli_grosir ?? 0, // Jika kosong isi 0
            'mulai_tgl'           => $request->mulai_tgl,
            'selesai_tgl'         => $request->selesai_tgl,
            'lokasi_berlaku'      => $request->lokasi_berlaku,
            'id_pelanggan'        => $request->id_pelanggan,
            'is_aktif'            => $request->has('is_aktif'),
        ]);

        $diskon->produk()->sync($request->id_produk);

        return redirect()->route('diskon.index')->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $diskon = Diskon::with('produk')->findOrFail($id);
        $produk = Produk::with('kategori')->get();
        $pelanggan = Pelanggan::all();
        $produkTerpilih = $diskon->produk->pluck('id_produk')->toArray();
        
        return view('diskon.edit', compact('diskon', 'produk', 'produkTerpilih', 'pelanggan'));
    }

    public function update(Request $request, $id)
    {
        $diskon = Diskon::findOrFail($id);

        $request->validate([
            'nama_diskon'         => 'required|string|max:150',
            'besar_diskon'        => 'required|integer|min:1|max:100',
            'minimal_beli'        => 'nullable|integer|min:0',
            'minimal_beli_grosir' => 'nullable|integer|min:0',
            'mulai_tgl'           => 'required|date',
            'selesai_tgl'         => 'required|date|after_or_equal:mulai_tgl',
            'lokasi_berlaku'      => 'required|in:semua,gudang,toko',
            'id_produk'           => 'required|array|min:1',
            'id_produk.*'         => 'exists:produk,id_produk',
            'id_pelanggan'        => 'nullable|exists:pelanggan,id_pelanggan',
        ]);

        $diskon->update([
            'nama_diskon'         => $request->nama_diskon,
            'besar_diskon'        => $request->besar_diskon,
            'minimal_beli'        => $request->minimal_beli ?? 0,
            'minimal_beli_grosir' => $request->minimal_beli_grosir ?? 0,
            'mulai_tgl'           => $request->mulai_tgl,
            'selesai_tgl'         => $request->selesai_tgl,
            'lokasi_berlaku'      => $request->lokasi_berlaku,
            'id_pelanggan'        => $request->id_pelanggan,
            'is_aktif'            => $request->has('is_aktif'),
        ]);

        $diskon->produk()->sync($request->id_produk);

        return redirect()->route('diskon.index')->with('success', 'Diskon berhasil diupdate.');
    }

    public function sampah()
    {
        $sampah = Diskon::onlyTrashed()->with('produk')->latest('deleted_at')->get();
        return view('diskon.sampah', compact('sampah'));
    }

    public function pulihkan($id)
    {
        $d = \App\Models\Diskon::withTrashed()->findOrFail($id);
        $d->restore();
        return redirect()->route('diskon.sampah')->with('success','Diskon dipulihkan');
    }

    public function hapusPermanen($id)
    {
        $d = \App\Models\Diskon::withTrashed()->findOrFail($id);
        $d->forceDelete();
        return redirect()->route('diskon.sampah')->with('success','Diskon dihapus permanen');
    }

    public function destroy($id)
    {
        Diskon::destroy($id);
        return back()->with('success', 'Diskon berhasil dihapus.');
    }
}