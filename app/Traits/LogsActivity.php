<?php

namespace App\Traits;

use App\Services\RiwayatService;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            if ($model->shouldLogActivity()) {
                RiwayatService::catat($model->activityPrefix() . '_buat', $model->activityLabel(), $model->activityDetails(), $model);
            }
        });

        static::updated(function ($model) {
            if ($model->shouldLogActivity()) {
                RiwayatService::catat(
                    $model->activityPrefix() . '_ubah',
                    $model->activityLabel(),
                    ['perubahan' => implode(', ', array_keys($model->getChanges()))] + $model->activityDetails(),
                    $model
                );
            }
        });

        static::deleted(function ($model) {
            if ($model->shouldLogActivity()) {
                $aksi = method_exists($model, 'isForceDeleting') && $model->isForceDeleting()
                    ? $model->activityPrefix() . '_hapus_permanen'
                    : $model->activityPrefix() . '_hapus';

                RiwayatService::catat($aksi, $model->activityLabel(), $model->activityDetails(), $model);
            }
        });

        static::restored(function ($model) {
            if ($model->shouldLogActivity()) {
                RiwayatService::catat($model->activityPrefix() . '_pulihkan', $model->activityLabel(), $model->activityDetails(), $model);
            }
        });
    }

    public function shouldLogActivity(): bool
    {
        return true;
    }

    public function activityPrefix(): string
    {
        return match (class_basename($this)) {
            'Kategori' => 'kategori',
            'Produk' => 'produk',
            'Diskon' => 'diskon',
            'Pelanggan' => 'pelanggan',
            'Suplier' => 'suplier',
            'Akun' => 'akun',
            'Jurnal' => 'jurnal',
            'BebanOperasional' => 'beban',
            'User' => 'kasir',
            default => strtolower(class_basename($this)),
        };
    }

    public function activityLabel(): string
    {
        $nama = $this->getAttribute('nama_kategori')
            ?? $this->getAttribute('nama_produk')
            ?? $this->getAttribute('nama_diskon')
            ?? $this->getAttribute('nama_pelanggan')
            ?? $this->getAttribute('nama_suplier')
            ?? $this->getAttribute('nama_akun')
            ?? $this->getAttribute('nama_beban')
            ?? $this->getAttribute('no_jurnal')
            ?? $this->getAttribute('name');

        return trim($this->activityPrefix() . ($nama ? ': ' . $nama : ' #' . $this->getKey()));
    }

    public function activityDetails(): array
    {
        $detail = [];
        foreach (['kode_produk', 'harga_satuan', 'harga_grosir', 'stok_gudang', 'stok_toko', 'besar_diskon', 'mulai_tgl', 'selesai_tgl', 'no_hp', 'kode_akun', 'tanggal', 'jumlah'] as $field) {
            if ($this->getAttribute($field) !== null) {
                $detail[$field] = $this->getAttribute($field);
            }
        }

        return $detail;
    }
}
