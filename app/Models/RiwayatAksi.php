<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatAksi extends Model
{
    protected $table = 'riwayat_aksi';
    protected $primaryKey = 'id_riwayat';

    protected $fillable = [
        'id_user',
        'role',
        'aksi',
        'id_transaksi',
        'label',
        'total',
        'tanggal_transaksi',
        'detail',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'detail' => 'array',
        'total' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}