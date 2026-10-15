<?php

namespace App\Services;

use App\Models\RiwayatAksi;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RiwayatService
{
    public static function catatTransaksi(string $aksi, Transaksi $t): void
    {
        RiwayatAksi::create([
            'id_user'           => Auth::id(),
            'role'              => Auth::user()?->role,
            'aksi'              => $aksi,
            'id_transaksi'      => $t->id_transaksi,
            'label'             => ucfirst($t->jenis) . ' #' . $t->id_transaksi,
            'total'             => (int) $t->total,
            'tanggal_transaksi' => $t->tanggal,
            'detail'            => [
                'jumlah_item' => $t->detail()->count(),
                'metode' => $t->metode_pembayaran,
                'id_pelanggan' => $t->id_pelanggan,
                'id_suplier' => $t->id_suplier,
                'catatan' => $t->catatan ?? $t->keterangan,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
        ]);
    }

    public static function catatAuth(string $aksi): void
    {
        RiwayatAksi::create([
            'id_user' => Auth::id(),
            'role' => Auth::user()?->role,
            'aksi' => $aksi,
            'label' => $aksi === 'login' ? 'Login' : 'Logout',
            'ip_address' => request()->ip(),
            'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
        ]);
    }

    /**
     * Mencatat aktivitas data master dan halaman Sampah.
     * Detail disimpan sebagai JSON supaya tiap modul dapat menyimpan atribut pentingnya.
     */
    public static function catat(string $aksi, string $label, array $detail = [], ?Model $model = null): void
    {
        RiwayatAksi::create([
            'id_user' => Auth::id(),
            'role' => Auth::user()?->role,
            'aksi' => $aksi,
            'label' => $label,
            'detail' => array_filter([
                'model' => $model ? class_basename($model) : null,
                'id' => $model?->getKey(),
                ...$detail,
            ], static fn ($value) => $value !== null && $value !== ''),
            'ip_address' => request()->ip(),
            'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
        ]);
    }
}
