<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPembelian extends Model
{
    protected $table = 'laporan_pembelian';
    protected $primaryKey = 'id_laporan_pembelian';

    protected $fillable = [
        'id_pembelian',
        'id_user',
        'tanggal',
        'total',
    ];

    // Relasi ke transaksi (karena id_pembelian = id_transaksi jenis pembelian)
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_pembelian', 'id_transaksi');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}