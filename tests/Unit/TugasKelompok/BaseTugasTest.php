<?php

namespace Tests\Unit\TugasKelompok;

use App\Models\User;
use App\Models\Akun;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\Suplier;
use App\Models\Transaksi;
use App\Models\BebanOperasional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class BaseTugasTest extends TestCase
{
    use RefreshDatabase;

    protected User $pemilik;
    protected User $kasir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemilik = User::factory()->create([
            'role' => 'pemilik',
            'email' => 'pemilik@test.com',
        ]);

        $this->kasir = User::factory()->create([
            'role' => 'kasir',
            'email' => 'kasir@test.com',
        ]);

        $this->seedAkunDasar();
    }

    protected function seedAkunDasar(): void
    {
        $akuns = [
            ['kode_akun' => '1101', 'nama_akun' => 'Kas', 'pos_saldo' => 'debet', 'pos_laporan' => 'neraca', 'kategori_neraca' => 'aktiva_lancar'],
            ['kode_akun' => '1102', 'nama_akun' => 'Bank', 'pos_saldo' => 'debet', 'pos_laporan' => 'neraca', 'kategori_neraca' => 'aktiva_lancar'],
            ['kode_akun' => '1103', 'nama_akun' => 'Piutang Usaha', 'pos_saldo' => 'debet', 'pos_laporan' => 'neraca', 'kategori_neraca' => 'aktiva_lancar'],
            ['kode_akun' => '1104', 'nama_akun' => 'Persediaan Barang', 'pos_saldo' => 'debet', 'pos_laporan' => 'neraca', 'kategori_neraca' => 'aktiva_lancar'],
            ['kode_akun' => '2101', 'nama_akun' => 'Hutang Usaha', 'pos_saldo' => 'kredit', 'pos_laporan' => 'neraca', 'kategori_neraca' => 'kewajiban_lancar'],
            ['kode_akun' => '3101', 'nama_akun' => 'Modal Pemilik', 'pos_saldo' => 'kredit', 'pos_laporan' => 'neraca', 'kategori_neraca' => 'modal'],
            ['kode_akun' => '4101', 'nama_akun' => 'Pendapatan Penjualan', 'pos_saldo' => 'kredit', 'pos_laporan' => 'laba_rugi', 'kategori_laba_rugi' => 'pendapatan'],
            ['kode_akun' => '5101', 'nama_akun' => 'Harga Pokok Penjualan', 'pos_saldo' => 'debet', 'pos_laporan' => 'laba_rugi', 'kategori_laba_rugi' => 'beban_pokok'],
            ['kode_akun' => '5102', 'nama_akun' => 'Beban Operasional', 'pos_saldo' => 'debet', 'pos_laporan' => 'laba_rugi', 'kategori_laba_rugi' => 'beban_operasional'],
        ];

        foreach ($akuns as $a) {
            Akun::firstOrCreate(['kode_akun' => $a['kode_akun']], $a);
        }
    }

    protected function createKategori(): Kategori
    {
        return Kategori::create([
            'nama_kategori' => 'Kategori Test ' . uniqid(),
        ]);
    }

    protected function createProduk(?Kategori $kategori = null): Produk
    {
        $kategori = $kategori ?? $this->createKategori();

        return Produk::create([
            'kode_produk' => 'TST' . strtoupper(substr(uniqid(), -8)),
            'nama_produk' => 'Produk Test ' . uniqid(),
            'id_kategori' => $kategori->id_kategori,
            'stok_gudang' => 100,
            'stok_toko' => 50,
            'harga_satuan' => 10000,
            'harga_beli' => 7000,
            'isi_per_dus' => 1,
            'satuan' => 'pcs',
        ]);
    }

    protected function createPelanggan(): Pelanggan
    {
        return Pelanggan::create([
            'nama_pelanggan' => 'Pelanggan Test ' . uniqid(),
            'no_hp' => '08123456789',
            'alamat' => 'Alamat Test',
            'tipe' => 'member',
        ]);
    }

    protected function createSuplier(): Suplier
    {
        return Suplier::create([
            'nama_suplier' => 'Suplier Test ' . uniqid(),
            'no_hp' => '08987654321',
            'alamat' => 'Alamat Suplier',
        ]);
    }
}
