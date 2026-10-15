<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BebanOperasional extends Model
{
    use SoftDeletes, LogsActivity;
    protected $table = 'beban_operasional';
    protected $primaryKey = 'id_beban';
    protected $fillable = [
        'tanggal', 'id_akun', 'nama_beban', 'jumlah', 'keterangan', 'id_user',
    ];

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
