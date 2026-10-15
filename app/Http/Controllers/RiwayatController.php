<?php

namespace App\Http\Controllers;

use App\Models\RiwayatAksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $q = RiwayatAksi::with('user');

        // Filter rentang tanggal
        $dari = $request->get('dari');
        $sampai = $request->get('sampai');
        if ($dari) $q->whereDate('created_at', '>=', $dari);
        if ($sampai) $q->whereDate('created_at', '<=', $sampai);

        // Filter jenis kejadian → cocokkan prefix aksi
        $kejadian = $request->get('kejadian') ?: '';
        if ($kejadian === 'data_master') {
            $q->where(function ($sub) {
                $sub->where('aksi', 'not like', 'penjualan%')
                    ->where('aksi', 'not like', 'pembelian%')
                    ->where('aksi', 'not like', 'login%')
                    ->where('aksi', 'not like', 'logout%');
            });
        } elseif ($kejadian && in_array($kejadian, ['penjualan', 'pembelian', 'login', 'kategori', 'produk', 'diskon'])) {
            $q->where('aksi', 'like', $kejadian . '%');
        }

        // Kata kunci pada label
        $cari = $request->get('cari');
        if ($cari) $q->where('label', 'like', '%' . $cari . '%');

        $riwayat = $q->latest('id_riwayat')->get();

        return view('riwayat.index', compact('riwayat', 'dari', 'sampai', 'kejadian', 'cari'));
    }

    // ponytail: destroy disabled — riwayat audit immutabel, jangan expose DELETE lagi
    // public function destroy($id) { ... }
}