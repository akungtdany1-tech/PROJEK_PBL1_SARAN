<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suplier extends Model
{
    use SoftDeletes, LogsActivity;
    protected $table = 'suplier'; // ← hapus s
    protected $primaryKey = 'id_suplier';

    protected $fillable = [
        'nama_suplier',
        'no_hp',
        'alamat',
    ];
}