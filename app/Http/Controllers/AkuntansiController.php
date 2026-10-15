<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\DetailJurnal;
use App\Models\BebanOperasional;
use App\Services\JurnalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AkuntansiController extends Controller
{
    // ═══════════════ CHART OF ACCOUNTS ═══════════════

    public function coa()
    {
        $akun = Akun::orderBy('kode_akun')->get();
        return view('akuntansi.coa', compact('akun'));
    }

    public function coaStore(Request $request)
    {
        $request->validate([
            'kode_akun' => 'required|string|max:20|unique:akun,kode_akun',
            'nama_akun' => 'required|string|max:150',
            'pos_saldo' => 'required|in:debet,kredit',
            'pos_laporan' => 'required|in:neraca,laba_rugi',
            'kategori_neraca' => 'nullable|in:aktiva_lancar,aktiva_tetap,kewajiban_lancar,kewajiban_jangka_panjang,modal',
            'kategori_laba_rugi' => 'nullable|in:pendapatan,beban_pokok,beban_operasional,pendapatan_lain,beban_lain',
        ]);

        Akun::create($request->only([
            'kode_akun', 'nama_akun', 'pos_saldo', 'pos_laporan',
            'kategori_neraca', 'kategori_laba_rugi',
        ]));

        return back()->with('success', 'Akun berhasil ditambahkan.');
    }

    public function coaDestroy($id)
    {
        $akun = Akun::findOrFail($id);

        if (DetailJurnal::where('id_akun', $id)->exists()) {
            return back()->with('error', 'Akun sudah dipakai di jurnal, tidak bisa dihapus.');
        }

        $akun->delete();
        return back()->with('success', 'Akun berhasil dihapus.');
    }

    // ═══════════════ BUKU BESAR ═══════════════

    public function bukuBesar(Request $request)
    {
        $akunList = Akun::orderBy('kode_akun')->get();
        $akunId = $request->get('akun');
        $dari = $request->get('dari') ?: date('Y-01-01');
        $sampai = $request->get('sampai') ?: now()->toDateString();

        if ($akunId && $akunId !== 'semua') {
            $akun = Akun::findOrFail($akunId);
            $mutasi = DetailJurnal::with('jurnal')
                ->join('jurnal', 'detail_jurnal.id_jurnal', '=', 'jurnal.id_jurnal')
                ->select('detail_jurnal.*')
                ->where('detail_jurnal.id_akun', $akunId)
                ->whereBetween('jurnal.tanggal', [$dari, $sampai])
                ->orderBy('jurnal.tanggal', 'asc')
                ->orderBy('detail_jurnal.id_detail_jurnal', 'asc')
                ->get();

            $saldo = 0;
            foreach ($mutasi as $row) {
                $selisih = $row->debet - $row->kredit;
                $saldo += ($akun->pos_saldo === 'kredit') ? -$selisih : $selisih;
                $row->saldo_berjalan = $saldo;
            }

            return view('akuntansi.buku-besar', compact('akunList', 'akun', 'mutasi', 'dari', 'sampai', 'akunId'));
        }

        // Semua akun: ringkasan per akun
        $ringkasan = DB::table('detail_jurnal as dj')
            ->join('jurnal as j', 'dj.id_jurnal', '=', 'j.id_jurnal')
            ->join('akun as a', 'dj.id_akun', '=', 'a.id_akun')
            ->whereBetween('j.tanggal', [$dari, $sampai])
            ->groupBy('dj.id_akun', 'a.kode_akun', 'a.nama_akun', 'a.pos_saldo')
            ->select(
                'dj.id_akun',
                'a.kode_akun',
                'a.nama_akun',
                'a.pos_saldo',
                DB::raw('SUM(dj.debet) as total_debet'),
                DB::raw('SUM(dj.kredit) as total_kredit')
            )
            ->orderBy('a.kode_akun')
            ->get()
            ->map(function ($r) {
                $r = (array) $r;
                $debet = (int) $r['total_debet'];
                $kredit = (int) $r['total_kredit'];
                $r['saldo'] = $r['pos_saldo'] === 'debet' ? ($debet - $kredit) : ($kredit - $debet);
                return (object) $r;
            });

        return view('akuntansi.buku-besar', compact('akunList', 'ringkasan', 'dari', 'sampai', 'akunId'));
    }

    // ═══════════════ JURNAL UMUM ═══════════════

    public function jurnal()
    {
        $jurnal = Jurnal::with(['detail.akun', 'user'])
            ->latest('tanggal')
            ->latest('id_jurnal')
            ->get();
        $akun = Akun::orderBy('kode_akun')->get();
        return view('akuntansi.jurnal', compact('jurnal', 'akun'));
    }

    public function jurnalStore(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:500',
            'id_akun' => 'required|array|min:2',
            'id_akun.*' => 'required|exists:akun,id_akun',
            'debet' => 'required|array',
            'debet.*' => 'nullable|integer|min:0',
            'kredit' => 'required|array',
            'kredit.*' => 'nullable|integer|min:0',
        ]);

        $lines = [];
        foreach ($request->id_akun as $i => $id) {
            $d = (int) ($request->debet[$i] ?? 0);
            $k = (int) ($request->kredit[$i] ?? 0);
            if ($d === 0 && $k === 0) continue;
            if ($d > 0 && $k > 0) {
                return back()->with('error', 'Satu baris tidak boleh berisi debet dan kredit sekaligus.')->withInput();
            }
            $lines[] = [
                'id_akun' => $id,
                'debet' => $d,
                'kredit' => $k,
                'keterangan' => $request->keterangan,
            ];
        }

        try {
            JurnalService::buatJurnalManual($request->tanggal, $request->keterangan, $lines, Auth::id());
            return back()->with('success', 'Jurnal manual berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function jurnalDestroy($id)
    {
        Jurnal::findOrFail($id)->delete();
        return back()->with('success', 'Jurnal berhasil dihapus.');
    }

    // ═══════════════ BEBAN OPERASIONAL ═══════════════

    public function beban()
    {
        $beban = BebanOperasional::with(['akun', 'user'])
            ->latest('tanggal')
            ->latest('id_beban')
            ->get();
        $akunBeban = Akun::where('kategori_laba_rugi', 'beban_operasional')->orderBy('kode_akun')->get();
        return view('akuntansi.beban', compact('beban', 'akunBeban'));
    }

    public function bebanStore(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_akun' => 'required|exists:akun,id_akun',
            'nama_beban' => 'required|string|max:150',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request) {
            $beban = BebanOperasional::create([
                'tanggal' => $request->tanggal,
                'id_akun' => $request->id_akun,
                'nama_beban' => $request->nama_beban,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'id_user' => Auth::id(),
            ]);

            JurnalService::buatJurnalBeban($beban);
        });

        return back()->with('success', 'Beban operasional berhasil dicatat.');
    }

    public function bebanDestroy($id)
    {
        DB::transaction(function () use ($id) {
            JurnalService::voidJurnal('beban', $id);
            BebanOperasional::findOrFail($id)->delete();
        });

        return back()->with('success', 'Beban berhasil dihapus.');
    }
}