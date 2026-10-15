<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diskon extends Model
{
    use SoftDeletes, LogsActivity;
    protected $table = 'diskon';
    protected $primaryKey = 'id_diskon';

    protected $fillable = [
        'nama_diskon',
        'besar_diskon',
        'minimal_beli',
        'minimal_beli_grosir',
        'mulai_tgl',
        'selesai_tgl',
        'is_aktif',
        'lokasi_berlaku',
        'id_pelanggan', // Tambahkan field id_pelanggan untuk relasi
    ];

    protected $casts = [
        'mulai_tgl'   => 'date',
        'selesai_tgl' => 'date',
        'is_aktif'    => 'boolean',
    ];

    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'diskon_produk', 'id_diskon', 'id_produk');
    }

    // Cek apakah diskon sedang aktif & dalam periode berlaku
    public function isAktifHariIni(): bool
    {
        $today = now()->toDateString();
        return $this->is_aktif
            && $this->mulai_tgl <= $today
            && $this->selesai_tgl >= $today;
    }

    // app/Models/Diskon.php

// Tambahkan relasi ke model Pelanggan
public function pelanggan()
{
    return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
}
}