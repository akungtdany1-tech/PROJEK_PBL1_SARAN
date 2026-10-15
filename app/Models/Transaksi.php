<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'jenis',
        'tanggal',
        'id_user',
        'id_pelanggan',
        'id_suplier',
        'id_akun',
        'subtotal',
        'total_diskon',
        'total',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'keterangan',
        'catatan',
    ];

    public function detail()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    // id_user bisa kasir atau pemilik/pemilik2
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    // alias kasir (backward compat)
    public function kasir()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function suplier()
    {
        return $this->belongsTo(Suplier::class, 'id_suplier');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun');
    }

    public function scopePenjualan($query)
    {
        return $query->where('jenis', 'penjualan');
    }

    public function scopePembelian($query)
    {
        return $query->where('jenis', 'pembelian');
    }
}