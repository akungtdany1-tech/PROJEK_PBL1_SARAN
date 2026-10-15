<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiskonProduk extends Model
{
    protected $table = 'diskon_produk';

    protected $fillable = [
        'id_diskon',
        'id_produk',
    ];

    public function diskon()
    {
        return $this->belongsTo(Diskon::class, 'id_diskon');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}