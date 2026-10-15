<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with('kategori');

        if ($request->kategori) {
            $query->where('id_kategori', $request->kategori);
        }

        if ($request->search) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        switch ($request->sort) {
            case 'stok_toko_asc':
                $query->orderBy('stok_toko', 'asc');
                break;
            case 'stok_toko_desc':
                $query->orderBy('stok_toko', 'desc');
                break;
            case 'stok_gudang_asc':
                $query->orderBy('stok_gudang', 'asc');
                break;
            case 'stok_gudang_desc':
                $query->orderBy('stok_gudang', 'desc');
                break;
            case 'menipis':
                $query->where(function($q) {
                    $q->where('stok_toko', '<=', 10)
                      ->orWhere('stok_gudang', '<=', 5);
                })->orderBy('stok_toko', 'asc');
                break;
        }

        $produk = $query->get();
        
        // REVISI: Hanya ambil kategori yang aktif untuk filter
        $kategori = Kategori::where('status', 'active')->get();

        return view('produk.index', compact('produk', 'kategori'));
    }

    public function create()
    {
        // REVISI: Hanya ambil kategori yang aktif agar tidak bisa pilih kategori yang sudah "dihapus"
        $kategori = Kategori::where('status', 'active')->get();
        return view('produk.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk'   => 'required|string|max:150|unique:produk,nama_produk',
            'id_kategori'   => 'required|exists:kategori,id_kategori',
            'harga_satuan'  => 'required|integer|min:0',
            'harga_beli'    => 'nullable|integer|min:0',
            'harga_grosir'  => 'nullable|integer|min:0|lt:harga_satuan',
            'minimal_grosir'=> 'nullable|integer|min:0',
            'stok_gudang'   => 'required|integer|min:0',
            'stok_toko'     => 'required|integer|min:0',
            'isi_per_dus'   => 'required|integer|min:1',
            'satuan'        => 'nullable|in:pcs,box,kg,liter',
        ], [
            'harga_grosir.lt' => 'Harga grosir harus lebih kecil dari harga satuan',
        ]);

        // Generate kode otomatis
        $last = Produk::latest('id_produk')->first();
        $no = $last ? (int) substr($last->kode_produk, 3) : 0;
        $kode = 'PRD' . str_pad($no + 1, 3, '0', STR_PAD_LEFT);

        Produk::create([
            'kode_produk'   => $kode,
            'nama_produk'   => $request->nama_produk,
            'id_kategori'   => $request->id_kategori,
            'harga_satuan'  => $request->harga_satuan,
            'harga_beli'    => $request->harga_beli ?: 0,
            'harga_grosir'  => $request->harga_grosir,
            'minimal_grosir'=> $request->minimal_grosir,
            'stok_gudang'   => $request->stok_gudang,
            'stok_toko'     => $request->stok_toko,
            'isi_per_dus'   => $request->isi_per_dus,
            'satuan'        => $request->satuan,
        ]);

        return redirect('/produk')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        // REVISI: Pastikan kategori yang aktif muncul, 
        // jika ingin kategori lama tetap muncul meski inactive, 
        // logikanya bisa ditambah (tapi untuk sekarang gunakan filter aktif saja)
        $kategori = Kategori::where('status', 'active')->get();
        return view('produk.edit', compact('produk', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama_produk'   => 'required|string|max:150|unique:produk,nama_produk,' . $id . ',id_produk',
            'id_kategori'   => 'required|exists:kategori,id_kategori',
            'harga_satuan'  => 'required|integer|min:0',
            'harga_beli'    => 'nullable|integer|min:0',
            'harga_grosir'  => 'nullable|integer|min:0|lt:harga_satuan',
            'minimal_grosir'=> 'nullable|integer|min:0',
            'stok_gudang'   => 'required|integer|min:0',
            'stok_toko'     => 'required|integer|min:0',
            'isi_per_dus'   => 'required|integer|min:1',
            'satuan'        => 'nullable|in:pcs,box,kg,liter',
        ], [
            'harga_grosir.lt' => 'Harga grosir harus lebih kecil dari harga satuan',
        ]);

        $produk->update([
            'nama_produk'   => $request->nama_produk,
            'id_kategori'   => $request->id_kategori,
            'harga_satuan'  => $request->harga_satuan,
            'harga_beli'    => $request->harga_beli ?: 0,
            'harga_grosir'  => $request->harga_grosir,
            'minimal_grosir'=> $request->minimal_grosir,
            'stok_gudang'   => $request->stok_gudang,
            'stok_toko'     => $request->stok_toko,
            'isi_per_dus'   => $request->isi_per_dus,
            'satuan'        => $request->satuan,
        ]);

        return redirect('/produk')->with('success', 'Produk berhasil diupdate');
    }

    public function sampah()
    {
        $sampah = Produk::onlyTrashed()->with('kategori')->latest('deleted_at')->get();
        return view('produk.sampah', compact('sampah'));
    }

    public function pulihkan($id)
    {
        $p = \App\Models\Produk::withTrashed()->findOrFail($id);
        $p->restore();
        return redirect()->route('produk.sampah')->with('success','Produk dipulihkan');
    }

    public function hapusPermanen($id)
    {
        $p = \App\Models\Produk::withTrashed()->findOrFail($id);
        $p->forceDelete();
        return redirect()->route('produk.sampah')->with('success','Produk dihapus permanen');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        // Cegah penghapusan yang menghapus riwayat/laporan keuangan (FK cascade)
        if (DetailTransaksi::where('id_produk', $id)->exists()) {
            return back()->with('error', "Produk \"{$produk->nama_produk}\" tidak bisa dihapus karena sudah pernah dipakai di transaksi.");
        }

        if ($produk->diskon()->exists()) {
            return back()->with('error', "Produk \"{$produk->nama_produk}\" tidak bisa dihapus karena terpasang pada diskon. Hapus diskonnya dulu.");
        }

        $produk->delete();
        return redirect()->route('produk.index')->with('success','Produk dihapus (lihat Sampah)');
    }

    public function transferStok(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'jumlah_dus' => 'required|integer|min:1',
        ]);

        if ($request->jumlah_dus > $produk->stok_gudang) {
            return back()->withErrors([
                'transfer' => "Stok tidak cukup! Tersedia: {$produk->stok_gudang} dus"
            ]);
        }

        $tambah = $request->jumlah_dus * $produk->isi_per_dus;

        $produk->stok_gudang -= $request->jumlah_dus;
        $produk->stok_toko   += $tambah;
        $produk->save();

        return back()->with('success', 'Transfer berhasil');
    }
}