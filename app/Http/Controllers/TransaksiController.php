<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\Kategori;
use App\Models\Diskon;
use App\Models\Suplier;
use App\Models\LaporanPembelian;
use App\Services\JurnalService;
use App\Services\RiwayatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // PENJUALAN 

    public function index()
    {
        $transaksi = Transaksi::with(['pelanggan', 'user'])
            ->penjualan()
            ->where('id_user', Auth::id())
            ->latest('id_transaksi')
            ->get();
        return view('transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        $today = now()->toDateString();
        $produk = Produk::with(['kategori', 'diskon' => function($query) use ($today) {
            $query->where('is_aktif', true)
                ->where('mulai_tgl', '<=', $today)
                ->where('selesai_tgl', '>=', $today);
        }])->get();
        $kategori = Kategori::all();
        $pelanggan = Pelanggan::all();

        $diskonPelanggan = Diskon::where('is_aktif', true)
            ->where('mulai_tgl', '<=', $today)
            ->where('selesai_tgl', '>=', $today)
            ->whereNotNull('id_pelanggan')
            ->with('produk')
            ->get()
            ->groupBy('id_pelanggan');

        return view('transaksi.create', compact('produk', 'kategori', 'pelanggan', 'diskonPelanggan'));
    }

    /** ponytail: polling ringan stok realtime — hapus jika pakai websocket */
    public function stokTerkini()
    {
        $map = [];
        foreach (Produk::select('id_produk','stok_toko','stok_gudang','harga_satuan','harga_grosir','nama_produk')->get() as $p) {
            $map[$p->id_produk] = ['stok_toko'=>$p->stok_toko,'stok_gudang'=>$p->stok_gudang,'harga_satuan'=>$p->harga_satuan,'harga_grosir'=>$p->harga_grosir,'nama'=>$p->nama_produk,'deleted'=>false];
        }
        foreach (Produk::onlyTrashed()->select('id_produk','nama_produk')->get() as $p) {
            $map[$p->id_produk] = ['deleted'=>true,'nama'=>$p->nama_produk];
        }
        return response()->json($map);
    }

    public function store(Request $request)
    {
        if (is_string($request->keranjang)) {
            $request->merge(['keranjang' => json_decode($request->keranjang, true)]);
        }

        $request->validate([
            'keranjang'             => 'required|array|min:1',
            'keranjang.*.id_produk' => 'required|integer',
            'keranjang.*.jumlah'    => 'required|integer|min:1',
            'keranjang.*.tipe'      => 'required|in:eceran,grosir',
            'metode_pembayaran'     => 'required|in:tunai,transfer,kartu',
            'bayar'                 => 'required|numeric|min:0',
        ]);

        $subtotalKeseluruhan = 0;
        $totalDiskon = 0;
        $items = [];
        $errorsStok = [];

        foreach ($request->keranjang as $item) {
            $produk = Produk::withTrashed()->find($item['id_produk']);
            if (!$produk || $produk->trashed()) {
                $nama = $produk->nama_produk ?? "ID {$item['id_produk']}";
                return back()->withErrors(['keranjang' => "Produk \"{$nama}\" sudah dihapus pemilik. Refresh halaman transaksi."])->withInput();
            }
            $jumlah = (int) $item['jumlah'];
            $tipe   = $item['tipe'];

            $harga = ($tipe === 'grosir')
                ? ($produk->harga_grosir ?? $produk->harga_satuan)
                : $produk->harga_satuan;

            if ($tipe === 'grosir') {
                if ($jumlah > $produk->stok_gudang) $errorsStok[] = "Stok gudang {$produk->nama_produk} sisa {$produk->stok_gudang}, minta {$jumlah}.";
            } else {
                if ($jumlah > $produk->stok_toko) $errorsStok[] = "Stok toko {$produk->nama_produk} sisa {$produk->stok_toko}, minta {$jumlah}.";
            }

            $nominalDiskonPerUnit = 0;
            $today = now()->toDateString();
            $diskonAktif = $produk->diskon()
                ->where('is_aktif', true)
                ->where('mulai_tgl', '<=', $today)
                ->where('selesai_tgl', '>=', $today)
                ->where(function($q) use ($tipe) {
                    $q->where('lokasi_berlaku', 'semua')
                      ->orWhere('lokasi_berlaku', $tipe === 'grosir' ? 'gudang' : 'toko');
                })
                ->where(function($q) use ($request) {
                    $q->whereNull('id_pelanggan')
                      ->orWhere('id_pelanggan', $request->id_pelanggan ?: null);
                })
                ->orderByDesc('besar_diskon')
                ->orderBy('id_diskon')
                ->first();

            if ($diskonAktif) {
                $minimalBeli = $tipe === 'grosir'
                    ? ($diskonAktif->minimal_beli_grosir ?? 0)
                    : ($diskonAktif->minimal_beli ?? 0);
                if ($jumlah >= $minimalBeli) {
                    $nominalDiskonPerUnit = round($harga * ($diskonAktif->besar_diskon / 100));
                }
            }

            $nominalDiskonTotal   = $nominalDiskonPerUnit * $jumlah;
            $subtotalItem         = ($harga * $jumlah) - $nominalDiskonTotal;
            $subtotalKeseluruhan += ($harga * $jumlah);
            $totalDiskon         += $nominalDiskonTotal;

            $items[] = [
                'id_produk'      => $produk->id_produk,
                'jumlah'         => $jumlah,
                'tipe'           => $tipe,
                'harga'          => $harga,
                'nominal_diskon' => $nominalDiskonPerUnit,
                'subtotal'       => $subtotalItem,
            ];
        }

        if ($errorsStok) return back()->withErrors(['stok' => implode(' ', $errorsStok)])->withInput();

        $total     = $subtotalKeseluruhan - $totalDiskon;
        $bayar     = (int) $request->bayar;
        $kembalian = $bayar - $total;

        if ($bayar < $total)
            return back()->withErrors(['bayar' => 'Uang bayar tidak cukup.'])->withInput();

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::create([
                'jenis'             => 'penjualan',
                'tanggal'           => now()->toDateString(),
                'id_user'           => Auth::id(),
                'id_pelanggan'      => $request->id_pelanggan ?: null,
                'subtotal'          => $subtotalKeseluruhan,
                'total_diskon'      => $totalDiskon,
                'total'             => $total,
                'bayar'             => $bayar,
                'kembalian'         => $kembalian,
                'metode_pembayaran' => $request->metode_pembayaran,
                'catatan'           => $request->catatan,
            ]);

            foreach ($items as $item) {
                $p = Produk::where('id_produk', $item['id_produk'])->lockForUpdate()->first();
                if (!$p) throw new \Exception("Produk ID {$item['id_produk']} sudah dihapus saat checkout.");
                $avail = $item['tipe']==='grosir' ? $p->stok_gudang : $p->stok_toko;
                if ($item['jumlah'] > $avail) throw new \Exception("Stok {$p->nama_produk} sisa {$avail}, minta {$item['jumlah']}.");
                DetailTransaksi::create([
                    'id_transaksi'   => $transaksi->id_transaksi,
                    'id_produk'      => $item['id_produk'],
                    'tipe'           => $item['tipe'],
                    'tipe_stok'      => $item['tipe'] === 'grosir' ? 'gudang' : 'toko',
                    'jumlah'         => $item['jumlah'],
                    'harga'          => $item['harga'],
                    'harga_beli'     => $p->harga_beli ?? 0,
                    'nominal_diskon' => $item['nominal_diskon'],
                    'subtotal'       => $item['subtotal'],
                ]);
                if ($item['tipe'] === 'grosir') $p->stok_gudang -= $item['jumlah'];
                else $p->stok_toko -= $item['jumlah'];
                $p->save();
            }

            // ponytail: jurnal disabled — restore: uncomment baris di bawah
            // JurnalService::buatJurnalPenjualan($transaksi);
            RiwayatService::catatTransaksi('penjualan_buat', $transaksi);

            DB::commit();
            return redirect()->route('transaksi.show', $transaksi->id_transaksi)
                ->with('success', 'Transaksi Berhasil!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['detail.produk', 'pelanggan', 'kasir'])
            ->penjualan()
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        if (request()->wantsJson() || request()->ajax() || request('type') === 'json') {
            return response()->json($transaksi);
        }

        return view('transaksi.show', compact('transaksi'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transaksi = Transaksi::with('detail')
                ->penjualan()
                ->where('id_user', Auth::id())
                ->findOrFail($id);
            foreach ($transaksi->detail as $detail) {
                $produk = Produk::find($detail->id_produk);
                if ($produk) {
                    if ($detail->tipe === 'grosir') {
                        $produk->stok_gudang += $detail->jumlah;
                    } else {
                        $produk->stok_toko += $detail->jumlah;
                    }
                    $produk->save();
                }
            }
            // ponytail: jurnal disabled — restore: uncomment baris di bawah
            // JurnalService::voidJurnal('penjualan', $transaksi->id_transaksi);
            RiwayatService::catatTransaksi('penjualan_hapus', $transaksi);
            $transaksi->delete();
            DB::commit();
            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus (masuk ke Sampah).');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus transaksi.');
        }
    }

    // 🔹 SAMPAH (soft delete)
    public function sampah()
    {
        $transaksi = Transaksi::onlyTrashed()
            ->penjualan()
            ->where('id_user', Auth::id())
            ->latest('id_transaksi')
            ->get();
        return view('transaksi.sampah', compact('transaksi'));
    }

    // 🔹 PULIHKAN (restore)
    public function pulihkan($id)
    {
        $t = Transaksi::withTrashed()->findOrFail($id);
        $t->restore();

        return redirect()->route('transaksi.sampah')->with('success', 'Transaksi berhasil dipulihkan');
    }

    // 🔹 HAPUS PERMANEN
    public function hapusPermanen($id)
    {
        $t = Transaksi::withTrashed()->findOrFail($id);

        // Cegah hapus permanen kalau masih memiliki detail transaksi yang aktif
        if ($t->detail()->exists()) {
            return back()->with('error', 'Transaksi tidak bisa dihapus permanen karena masih memiliki detail.');
        }

        $t->forceDelete();

        return redirect()->route('transaksi.sampah')->with('success', 'Transaksi berhasil dihapus permanen');
    }

    // PEMBELIAN 

    public function indexPembelian()
    {
        $pembelian = Transaksi::with(['detail.produk', 'suplier', 'user'])
            ->where('jenis', 'pembelian')
            ->where('id_user', Auth::id())
            ->latest('id_transaksi')
            ->get();
        return view('pembelian.index', compact('pembelian'));
    }

    public function createPembelian()
    {
        $produk  = Produk::with('kategori')->get();
        $suplier = Suplier::all();
        return view('pembelian.create', compact('produk', 'suplier'));
    }

    public function storePembelian(Request $request)
{
    $validator = \Validator::make($request->all(), [
        'tanggal'      => 'required|date',
        'id_suplier'   => 'nullable|exists:suplier,id_suplier',
        'keterangan'   => 'nullable|string|max:500',
        'id_produk'    => 'required|array|min:1',
        'id_produk.*'  => 'required|exists:produk,id_produk',
        'jumlah.*'     => 'required|integer|min:1',
        'harga_beli.*' => 'required|integer|min:0',
    ]);

    if ($validator->fails()) {
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        return back()->withErrors($validator)->withInput();
    }

    $total = 0;
    foreach ($request->id_produk as $i => $id_produk) {
        $total += $request->jumlah[$i] * $request->harga_beli[$i];
    }

    DB::beginTransaction();
    try {
        $transaksi = Transaksi::create([
            'jenis'       => 'pembelian',
            'tanggal'     => $request->tanggal,
            'id_user'     => Auth::id(),
            'id_suplier'  => $request->id_suplier,
            'total'       => $total,
            'keterangan'  => $request->keterangan,
        ]);

        foreach ($request->id_produk as $i => $id_produk) {
            $jumlah     = $request->jumlah[$i];
            $harga_beli = $request->harga_beli[$i];
            $subtotal   = $jumlah * $harga_beli;

            DetailTransaksi::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'id_produk'    => $id_produk,
                'tipe_stok'    => 'gudang',
                'jumlah'       => $jumlah,
                'harga_beli'   => $harga_beli,
                'harga'        => 0,
                'subtotal'     => $subtotal,
            ]);

            $produk = Produk::findOrFail($id_produk);
            $produk->stok_gudang += $jumlah;
            $produk->harga_beli = $harga_beli;
            $produk->save();
        }

        LaporanPembelian::create([
            'id_pembelian' => $transaksi->id_transaksi,
            'tanggal'      => $transaksi->tanggal,
            'total'        => $transaksi->total,
            'id_user'      => Auth::id(),
        ]);

        // ponytail: jurnal disabled — restore: uncomment baris di bawah
        // JurnalService::buatJurnalPembelian($transaksi);
        RiwayatService::catatTransaksi('pembelian_buat', $transaksi);

        DB::commit();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('pembelian.index'),
                'message' => 'Pembelian berhasil disimpan'
            ]);
        }

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil disimpan.');

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Error storePembelian: ' . $e->getMessage());
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    public function showPembelian($id)
{
    $pembelian = Transaksi::with(['detail.produk', 'suplier', 'user'])
        ->where('jenis', 'pembelian')
        ->where('id_user', Auth::id())
        ->findOrFail($id);
    return view('pembelian.show', compact('pembelian'));
}

// CETAK STRUK 
    public function printStruk($id)
    {
        $transaksi = Transaksi::with(['detail.produk', 'pelanggan', 'kasir'])
            ->penjualan()
            ->where('id_user', Auth::id())
            ->findOrFail($id);

        return view('transaksi.print', compact('transaksi'));
    }

    public function destroyPembelian($id)
    {
        DB::beginTransaction();
        try {
            $pembelian = Transaksi::with('detail')->where('id_user', Auth::id())->findOrFail($id);
            foreach ($pembelian->detail as $detail) {
                $produk = Produk::find($detail->id_produk);
                if ($produk) {
                    $produk->stok_gudang += $detail->jumlah;
                    $produk->save();
                }
            }
            // ponytail: jurnal disabled — restore: uncomment baris di bawah
            // JurnalService::voidJurnal('pembelian', $pembelian->id_transaksi);
            RiwayatService::catatTransaksi('pembelian_hapus', $pembelian);
            $pembelian->delete();
            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus pembelian.');
        }
    }
}
