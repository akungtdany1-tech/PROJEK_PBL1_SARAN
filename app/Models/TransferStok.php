<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferStok extends Model
{
    protected $table = 'transfer_stok';
    protected $primaryKey = 'id_transfer';

    protected $fillable = [
        'id_produk',
        'jumlah_dus',
        'jumlah_pcs',
        'tanggal',
        'keterangan',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}