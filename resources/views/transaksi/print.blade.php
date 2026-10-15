<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #{{ $transaksi->id_transaksi }}</title>
    <style>
        @page { size: 58mm auto; margin: 0; }
        body { font-family: 'Courier New', Courier, monospace; width: 54mm; font-size: 11px; color: #000; margin: 2mm; line-height: 1.2; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        .item-name { font-weight: bold; display: block; }
        .meta-info { font-size: 10px; }
    </style>
</head>
<body onload="window.print(); window.onafterprint = function() { window.close(); }">

    <div class="text-center">
        <h3 style="margin: 0; font-size: 14px;">SARANA AGRO MAKMUR</h3>
        <p class="meta-info" style="margin: 2px 0;">Sistem POS Toko</p>
    </div>

    <div class="line"></div>

    <div class="meta-info">
        <div>No. Nota : TR-{{ $transaksi->id_transaksi }}</div>
        <div>Tanggal  : {{ ($transaksi->created_at ?? $transaksi->tanggal) instanceof \Carbon\CarbonInterface ? $transaksi->created_at->format('d/m/Y H:i') : \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y H:i') }}</div>
        <div>Kasir    : {{ $transaksi->kasir->name ?? 'Kasir' }}</div>
        <div>Pelanggan: {{ $transaksi->pelanggan->nama_pelanggan ?? 'Umum' }}</div>
    </div>

    <div class="line"></div>

    <table>
        <tbody>
            @foreach($transaksi->detail as $detail)
            <tr>
                <td colspan="2">
                    <span class="item-name">{{ $detail->produk->nama_produk ?? 'Produk Terhapus' }}</span>
                    <span class="meta-info">{{ $detail->jumlah }} x Rp {{ number_format($detail->harga, 0, ',', '.') }} ({{ ucfirst($detail->tipe ?? 'eceran') }})</span>
                </td>
                <td class="text-right" style="vertical-align: bottom;">
                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                </td>
            </tr>
            @if($detail->nominal_diskon > 0)
            <tr>
                <td colspan="2" class="meta-info" style="color: #555; padding-left: 5px;">↳ Pot. Harga</td>
                <td class="text-right" style="color: #555; font-size: 10px;">-Rp {{ number_format($detail->nominal_diskon * $detail->jumlah, 0, ',', '.') }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($transaksi->total_diskon > 0)
        <tr>
            <td>Total Diskon:</td>
            <td class="text-right">-Rp {{ number_format($transaksi->total_diskon, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr style="font-weight: bold;">
            <td>TOTAL:</td>
            <td class="text-right">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar:</td>
            <td class="text-right">Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembalian:</td>
            <td class="text-right">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="text-center meta-info" style="margin-top: 10px;">
        @if($transaksi->catatan)
            <p style="margin: 0 0 5px 0;">Catatan: "{{ $transaksi->catatan }}"</p>
        @endif
        *** TERIMA KASIH ***
    </div>

</body>
</html>