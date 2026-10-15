<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailJurnal extends Model
{
    protected $table = 'detail_jurnal';
    protected $primaryKey = 'id_detail_jurnal';
    protected $fillable = [
        'id_jurnal', 'id_akun', 'debet', 'kredit', 'keterangan',
    ];

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun');
    }
}
