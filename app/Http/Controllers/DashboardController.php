<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();

        // Jika user memilih bulan dari dropdown manual
        if ($request->filled('bulan')) {
            $bulan = (int) $request->bulan;
            $tahun = (int) ($request->tahun ?? $now->year);
            $filter = 'custom';

            $selectedDate = Carbon::createFromDate($tahun, $bulan, 1);
            $start = $selectedDate->copy()->startOfMonth();
            $end   = $selectedDate->copy()->endOfMonth();
        } else {
            // Filter cepat (bulan_ini, 3_bulan, 1_tahun)
            $filter = $request->filter ?? 'bulan_ini';
            $bulan  = $now->month;
            $tahun  = $now->year;

            if ($filter == '3_bulan') {
                $start = $now->copy()->subMonths(2)->startOfMonth();
                $end   = $now->copy()->endOfMonth();
            } elseif ($filter == '1_tahun') {
                $start = $now->copy()->subYear()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
            } else { // bulan_ini
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
            }
        }

        // 4 kartu statistik sesuai rentang tanggal periode
        $totalTransaksiHariIni = Transaksi::where('jenis', 'penjualan')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->count();

        $totalPenjualanHariIni = Transaksi::where('jenis', 'penjualan')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('total');

        $produkTerjualHariIni = DetailTransaksi::whereHas('transaksi', function($q) use ($start, $end) {
            $q->where('jenis', 'penjualan')
              ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
        })->sum('jumlah');

        // Hitung stok menipis (realtime)
        $stokMenipis = Produk::where(function ($q) {
            $q->where('stok_toko', '<=', 10)
              ->orWhere('stok_gudang', '<=', 5);
        })->count();

        $jumlahStokMenipis = $stokMenipis;

        // Transaksi terakhir pada periode terpilih
        $transaksiTerakhir = Transaksi::with(['detail.produk', 'pelanggan'])
            ->where('jenis', 'penjualan')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->latest('id_transaksi')
            ->take(5)
            ->get();

        // Produk terlaris pada periode terpilih
        $produkTerlaris = DetailTransaksi::whereHas('transaksi', function($q) use ($start, $end) {
            $q->where('jenis', 'penjualan')
              ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
        })
        ->join('produk', 'detail_transaksi.id_produk', '=', 'produk.id_produk')
        ->select('produk.nama_produk', DB::raw('SUM(detail_transaksi.jumlah) as total_terjual'))
        ->groupBy('produk.id_produk', 'produk.nama_produk')
        ->orderByDesc('total_terjual')
        ->take(5)
        ->get();

        // Grafik harian / bulanan sesuai rentang tanggal
        $grafikDays = min($start->diffInDays($end) + 1, 365);
        $grafik = collect(range(0, $grafikDays - 1))->map(function($i) use ($start) {
            $date = $start->copy()->addDays($i);
            return [
                'tanggal' => $date->format('d M'),
                'total'   => Transaksi::where('jenis', 'penjualan')
                                ->whereDate('tanggal', $date->toDateString())
                                ->sum('total'),
            ];
        });

        // Opsi 12 bulan terakhir untuk dropdown
        $opsibulan = collect(range(0, 11))->map(function($i) {
            $date = Carbon::now()->subMonths($i);
            return [
                'bulan' => $date->month,
                'tahun' => $date->year,
                'label' => $date->translatedFormat('F Y'),
            ];
        })->values();

        return view('dashboard', compact(
            'filter', 'start', 'end',
            'totalTransaksiHariIni',
            'totalPenjualanHariIni',
            'produkTerjualHariIni',
            'stokMenipis',
            'jumlahStokMenipis',
            'transaksiTerakhir',
            'produkTerlaris',
            'grafik',
            'opsibulan',
            'bulan',
            'tahun'
        ));
    }
}
