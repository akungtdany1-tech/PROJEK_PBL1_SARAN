<?php

namespace App\Services;

use App\Models\Jurnal;
use App\Models\DetailJurnal;
use App\Models\Akun;
use App\Models\Transaksi;
use App\Models\BebanOperasional;
use Illuminate\Support\Facades\DB;

class JurnalService
{
    // ═══ PENJUALAN ═══
    // D 1101 Kas = total, C 4101 Pendapatan Penjualan = total
    // D 5101 HPP, C 1104 Persediaan = harga pokok barang yang terjual
    public static function buatJurnalPenjualan(Transaksi $t): Jurnal
    {
        $noJurnal = self::noJurnal($t->tanggal);
        $total = (int) $t->total;

        $hpp = $t->detail->sum(fn($d) => (int) $d->harga_beli * (int) $d->jumlah);

        $jurnal = Jurnal::create([
            'no_jurnal'  => $noJurnal,
            'tanggal'    => $t->tanggal,
            'ref_type'   => 'penjualan',
            'ref_id'     => $t->id_transaksi,
            'keterangan' => 'Penjualan #' . $t->id_transaksi,
            'total'      => $total,
            'id_user'    => $t->id_user,
        ]);

        $lines = [
            [
                'id_jurnal'    => $jurnal->id_jurnal,
                'id_akun'      => self::akunId('1101'),
                'debet'        => $total,
                'kredit'       => 0,
                'keterangan'   => 'Kas masuk dari penjualan',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_jurnal'    => $jurnal->id_jurnal,
                'id_akun'      => self::akunId('4101'),
                'debet'        => 0,
                'kredit'       => $total,
                'keterangan'   => 'Pendapatan penjualan',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ];

        if ($hpp > 0) {
            $lines[] = [
                'id_jurnal'    => $jurnal->id_jurnal,
                'id_akun'      => self::akunId('5101'),
                'debet'        => $hpp,
                'kredit'       => 0,
                'keterangan'   => 'Harga pokok penjualan',
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
            $lines[] = [
                'id_jurnal'    => $jurnal->id_jurnal,
                'id_akun'      => self::akunId('1104'),
                'debet'        => 0,
                'kredit'       => $hpp,
                'keterangan'   => 'Persediaan berkurang (HPP)',
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        DetailJurnal::insert($lines);

        return $jurnal;
    }

    // ═══ PEMBELIAN ═══
    // D 1104 Persediaan = total, C 2101 Utang Usaha = total
    public static function buatJurnalPembelian(Transaksi $t): Jurnal
    {
        $noJurnal = self::noJurnal($t->tanggal);
        $total = (int) $t->total;

        $jurnal = Jurnal::create([
            'no_jurnal'  => $noJurnal,
            'tanggal'    => $t->tanggal,
            'ref_type'   => 'pembelian',
            'ref_id'     => $t->id_transaksi,
            'keterangan' => 'Pembelian #' . $t->id_transaksi,
            'total'      => $total,
            'id_user'    => $t->id_user,
        ]);

        DetailJurnal::insert([
            [
                'id_jurnal'    => $jurnal->id_jurnal,
                'id_akun'      => self::akunId('1104'),
                'debet'        => $total,
                'kredit'       => 0,
                'keterangan'   => 'Persediaan bertambah',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_jurnal'    => $jurnal->id_jurnal,
                'id_akun'      => self::akunId('2101'),
                'debet'        => 0,
                'kredit'       => $total,
                'keterangan'   => 'Utang usaha',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

        return $jurnal;
    }

    // ═══ BEBAN OPERASIONAL ═══
    // D [akun beban] = jumlah, C 1101 Kas = jumlah
    public static function buatJurnalBeban(BebanOperasional $b): Jurnal
    {
        $noJurnal = self::noJurnal($b->tanggal);

        $jurnal = Jurnal::create([
            'no_jurnal'  => $noJurnal,
            'tanggal'    => $b->tanggal,
            'ref_type'   => 'beban',
            'ref_id'     => $b->id_beban,
            'keterangan' => 'Beban: ' . $b->nama_beban,
            'total'      => (int) $b->jumlah,
            'id_user'    => $b->id_user,
        ]);

        $akunBeban = Akun::findOrFail($b->id_akun);

        DetailJurnal::insert([
            [
                'id_jurnal'    => $jurnal->id_jurnal,
                'id_akun'      => $b->id_akun,
                'debet'        => (int) $b->jumlah,
                'kredit'       => 0,
                'keterangan'   => $b->nama_beban,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'id_jurnal'    => $jurnal->id_jurnal,
                'id_akun'      => self::akunId('1101'),
                'debet'        => 0,
                'kredit'       => (int) $b->jumlah,
                'keterangan'   => 'Kas keluar untuk ' . $b->nama_beban,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

        return $jurnal;
    }

    // ═══ JURNAL MANUAL ═══
    public static function buatJurnalManual(string $tanggal, ?string $keterangan, array $lines, int $idUser): Jurnal
    {
        $totalDebet = array_sum(array_column($lines, 'debet'));
        $totalKredit = array_sum(array_column($lines, 'kredit'));

        if ($totalDebet !== $totalKredit) {
            throw new \Exception('Total debet harus sama dengan total kredit.');
        }

        $noJurnal = self::noJurnal($tanggal);

        $jurnal = Jurnal::create([
            'no_jurnal'  => $noJurnal,
            'tanggal'    => $tanggal,
            'ref_type'   => null,
            'ref_id'     => null,
            'keterangan' => $keterangan,
            'total'      => $totalDebet,
            'id_user'    => $idUser,
        ]);

        $inserts = [];
        foreach ($lines as $line) {
            $inserts[] = [
                'id_jurnal'    => $jurnal->id_jurnal,
                'id_akun'      => $line['id_akun'],
                'debet'        => (int) $line['debet'],
                'kredit'       => (int) $line['kredit'],
                'keterangan'   => $line['keterangan'] ?? null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }
        DetailJurnal::insert($inserts);

        return $jurnal;
    }

    // ═══ VOID JURNAL ═══
    public static function voidJurnal(string $refType, int $refId): void
    {
        Jurnal::where('ref_type', $refType)->where('ref_id', $refId)->delete();
    }

    // ═══ HELPERS ═══
    public static function akunId(string $kode): int
    {
        $akun = Akun::where('kode_akun', $kode)->first();
        if (!$akun) {
            throw new \Exception("Akun {$kode} tidak ditemukan.");
        }
        return $akun->id_akun;
    }

    private static function noJurnal(string $tanggal): string
    {
        $prefix = 'JR-' . date('Ymd', strtotime($tanggal)) . '-';
        $lastJurnal = Jurnal::where('no_jurnal', 'like', $prefix . '%')
            ->orderByDesc('no_jurnal')
            ->first();

        if ($lastJurnal) {
            $lastNum = (int) substr($lastJurnal->no_jurnal, -3);
            $nextNum = str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '001';
        }

        return $prefix . $nextNum;
    }
}
