<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jurnal extends Model
{
    use SoftDeletes, LogsActivity;
    protected $table = 'jurnal';
    protected $primaryKey = 'id_jurnal';
    protected $fillable = [
        'no_jurnal', 'tanggal', 'ref_type', 'ref_id',
        'keterangan', 'total', 'id_user',
    ];

    public function detail()
    {
        return $this->hasMany(DetailJurnal::class, 'id_jurnal');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function ref()
    {
        return $this->morphTo();
    }
}
