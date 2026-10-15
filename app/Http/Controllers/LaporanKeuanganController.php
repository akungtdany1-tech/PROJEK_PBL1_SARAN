<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\DetailJurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganController extends Controller
{
    // Total mutasi per akun dalam rentang tanggal
    // return: array [kode_akun => ['debet' => int, 'kredit' => int]]
    private function mutasiPerAkun(?string $dari = null, ?string $sampai = null): array
    {
        $q = DetailJurnal::query()
            ->join('jurnal as j', 'detail_jurnal.id_jurnal', '=', 'j.id_jurnal')
            ->groupBy('detail_jurnal.id_akun')
            ->select(
                'detail_jurnal.id_akun',
                DB::raw('SUM(detail_jurnal.debet) as total_debet'),
                DB::raw('SUM(detail_jurnal.kredit) as total_kredit')
            );

        if ($dari) $q->where('j.tanggal', '>=', $dari);
        if ($sampai) $q->where('j.tanggal', '<=', $sampai);

        $hasil = [];
        foreach ($q->get() as $row) {
            $hasil[$row->id_akun] = [
                'debet' => (int) $row->total_debet,
                'kredit' => (int) $row->total_kredit,
            ];
        }
        return $hasil;
    }

    private function saldoAkun(array $mutasi, int $idAkun, string $posSaldo): int
    {
        $m = $mutasi[$idAkun] ?? ['debet' => 0, 'kredit' => 0];
        $selisih = $m['debet'] - $m['kredit'];
        return $posSaldo === 'debet' ? $selisih : -$selisih;
    }

    // ═══════════════ LABA RUGI ═══════════════

    public function labaRugi(Request $request)
    {
        $dari = $request->get('dari') ?: now()->startOfMonth()->toDateString();
        $sampai = $request->get('sampai') ?: now()->toDateString();

        $mutasi = $this->mutasiPerAkun($dari, $sampai);
        $akun = Akun::all()->keyBy('id_akun');

        // Pendapatan (4101) — saldo kredit
        $pendapatan = $akun->filter(fn($a) => $a->kode_akun === '4101')
            ->map(fn($a) => $this->saldoAkun($mutasi, $a->id_akun, 'kredit'));

        // HPP = harga pokok barang yang terjual (debet akun 5101) pada periode
        $hpp = 0;
        if ($p = $akun->firstWhere('kode_akun', '5101')) {
            $hpp = $this->saldoAkun($mutasi, $p->id_akun, 'debet');
        }

        // Beban operasional — saldo debet
        $bebanList = $akun->where('kategori_laba_rugi', 'beban_operasional')->values();
        $bebanItems = $bebanList->map(fn($a) => [
            'akun' => $a,
            'nilai' => $this->saldoAkun($mutasi, $a->id_akun, 'debet'),
        ]);
        $totalBeban = $bebanItems->sum('nilai');
        $totalPendapatan = $pendapatan->sum();
        $labaBersih = $totalPendapatan - $hpp - $totalBeban;

        return view('akuntansi.laba-rugi', compact(
            'dari', 'sampai',
            'totalPendapatan', 'hpp', 'bebanItems', 'totalBeban', 'labaBersih'
        ));
    }

    // ═══════════════ NERACA ═══════════════

    public function neraca(Request $request)
    {
        $sampai = $request->get('sampai') ?: now()->toDateString();

        // Semua mutasi sampai tanggal neraca
        $mutasi = $this->mutasiPerAkun(null, $sampai);
        $akun = Akun::all()->keyBy('id_akun');

        // Aktiva (debet) & Kewajiban (kredit) dari kategori neraca
        $aktiva = collect();
        $kewajiban = collect();
        $modalItems = collect();

        foreach ($akun as $a) {
            $selisih = ($mutasi[$a->id_akun] ?? ['debet' => 0, 'kredit' => 0]);
            $net = $selisih['debet'] - $selisih['kredit'];

            // Kontribusi ke ekuitas selalu kredit - debet:
            // pendapatan (kredit-normal) menambah, prive & beban (debet-normal) mengurangi.
            $kontribusiEkuitas = -$net;

            if ($a->pos_laporan !== 'neraca') continue;

            if ($a->kategori_neraca === 'aktiva_lancar' || $a->kategori_neraca === 'aktiva_tetap') {
                $aktiva->push(['akun' => $a, 'saldo' => $net]);
            } elseif (str_starts_with($a->kategori_neraca ?? '', 'kewajiban')) {
                $kewajiban->push(['akun' => $a, 'saldo' => $kontribusiEkuitas]); // saldo kredit
            } elseif ($a->kategori_neraca === 'modal') {
                $modalItems->push(['akun' => $a, 'saldo' => $kontribusiEkuitas]);
            }
        }

        // Laba ditahan = total laba akumulasi sampai tanggal
        $labaDitahan = 0;
        foreach ($akun as $a) {
            if ($a->pos_laporan !== 'laba_rugi') continue;
            $selisih = ($mutasi[$a->id_akun] ?? ['debet' => 0, 'kredit' => 0]);
            $labaDitahan += $selisih['kredit'] - $selisih['debet'];
        }

        $aktiva = $aktiva->filter(fn($x) => $x['saldo'] != 0)->values();
        $kewajiban = $kewajiban->filter(fn($x) => $x['saldo'] != 0)->values();
        $modalItems = $modalItems->filter(fn($x) => $x['saldo'] != 0)->values();

        $totalAktiva = $aktiva->sum('saldo');
        $totalKewajiban = $kewajiban->sum('saldo');
        $totalModal = $modalItems->sum('saldo') + $labaDitahan;
        $totalPasiva = $totalKewajiban + $totalModal;

        return view('akuntansi.neraca', compact(
            'sampai', 'aktiva', 'kewajiban', 'modalItems', 'labaDitahan',
            'totalAktiva', 'totalKewajiban', 'totalModal', 'totalPasiva'
        ));
    }

    // ═══════════════ ARUS KAS ═══════════════

    public function arusKas(Request $request)
    {
        $dari = $request->get('dari') ?: now()->startOfMonth()->toDateString();
        $sampai = $request->get('sampai') ?: now()->toDateString();
        $sebelumPeriode = date('Y-m-d', strtotime($dari . ' -1 day'));

        $kas = Akun::where('kode_akun', '1101')->firstOrFail();
        $mutasiLalu = $this->mutasiPerAkun(null, $sebelumPeriode);
        $mutasiPeriode = $this->mutasiPerAkun($dari, $sampai);

        $saldoAwal = $this->saldoAkun($mutasiLalu, $kas->id_akun, 'debet');

        $m = $mutasiPeriode[$kas->id_akun] ?? ['debet' => 0, 'kredit' => 0];
        $penerimaan = $m['debet'];
        $pengeluaran = $m['kredit'];
        $saldoAkhir = $saldoAwal + $penerimaan - $pengeluaran;

        // Rincian mutasi kas dalam periode
        $rincian = DetailJurnal::with('jurnal')
            ->join('jurnal', 'detail_jurnal.id_jurnal', '=', 'jurnal.id_jurnal')
            ->select('detail_jurnal.*')
            ->where('detail_jurnal.id_akun', $kas->id_akun)
            ->whereBetween('jurnal.tanggal', [$dari, $sampai])
            ->orderBy('jurnal.tanggal', 'asc')
            ->orderBy('detail_jurnal.id_detail_jurnal', 'asc')
            ->get();

        return view('akuntansi.arus-kas', compact(
            'dari', 'sampai', 'saldoAwal', 'penerimaan', 'pengeluaran', 'saldoAkhir', 'rincian'
        ));
    }
}