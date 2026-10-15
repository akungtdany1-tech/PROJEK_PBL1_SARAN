<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('produk', 'harga_beli')) {
            Schema::table('produk', function (Blueprint $table) {
                $table->integer('harga_beli')->default(0)->after('harga_satuan');
            });
        }

        // Backfill harga pokok dari harga beli pembelian terakhir per produk.
        // Iterasi dari pembelian terbaru, sehingga tulis terakhir = harga beli terakhir.
        $rows = DB::table('detail_transaksi as dt')
            ->join('transaksi as t', 't.id_transaksi', '=', 'dt.id_transaksi')
            ->where('t.jenis', 'pembelian')
            ->where('dt.harga_beli', '>', 0)
            ->orderByDesc('t.id_transaksi')
            ->get(['dt.id_produk', 'dt.harga_beli']);

        foreach ($rows as $r) {
            DB::table('produk')
                ->where('id_produk', $r->id_produk)
                ->update(['harga_beli' => $r->harga_beli]);
        }
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn('harga_beli');
        });
    }
};