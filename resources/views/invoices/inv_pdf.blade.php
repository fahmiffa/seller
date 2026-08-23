<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->inv_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; font-weight: bold; }
        .header h1 { margin: 0; padding: 0; font-size: 20px; text-decoration: underline; }

        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; }

        .items { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items th, .items td { border: 1px solid #000; padding: 6px 8px; }
        .items th { background-color: #f2f2f2; text-align: center; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .totals { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .totals td { padding: 4px 8px; }

        .signatures { width: 100%; text-align: center; margin-top: 40px; border-collapse: collapse; }
        .signatures td { width: 50%; padding-bottom: 80px; vertical-align: bottom; }
        .sign-line { border-top: 1px solid #000; width: 60%; margin: 0 auto 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
    </div>

    <table class="info-table">
        <tr>
            <td width="120"><strong>No. Invoice</strong></td>
            <td>: {{ $invoice->inv_number }}</td>
        </tr>
        <tr>
            <td><strong>No. Referensi PO</strong></td>
            <td>: {{ $order->po_number ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Invoice</strong></td>
            <td>: {{ $invoice->inv_date ? $invoice->inv_date->format('d F Y') : now()->format('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal PO</strong></td>
            <td>: {{ $order->tanggal_po ? $order->tanggal_po->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td><strong>Status Bayar</strong></td>
            <td>: {{ strtoupper($invoice->payment_status) }}</td>
        </tr>
    </table>

    <div style="width: 100%; margin-bottom: 20px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <strong>Dari (Supplier):</strong><br>
                    {{ $order->supplier ? $order->supplier->nama_supplier : '-' }}<br>
                    @if($order->supplier && $order->supplier->alamat)
                        {{ $order->supplier->alamat }}<br>
                    @endif
                    @if($order->supplier && $order->supplier->telepon)
                        Telp: {{ $order->supplier->telepon }}
                    @endif
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <strong>Kepada (Unit):</strong><br>
                    {{ $order->unit ? $order->unit->name : '-' }}<br>
                    (Diverifikasi oleh: {{ $order->user ? $order->user->name : '-' }})
                </td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="25%">Barang</th>
                <th width="25%">Spesifikasi</th>
                <th width="10%">Qty</th>
                <th width="15%">Harga</th>
                <th width="20%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotalTotal = 0; @endphp
            @foreach($order->items as $index => $item)
                @php
                    $subtotalItem = $item->jumlah * $item->harga_supplier;
                    $subtotalTotal += $subtotalItem;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->komoditas ? $item->komoditas->name : '-' }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->jumlah, 0, ',', '.') }} {{ $item->satuan ? $item->satuan->nama_satuan : '' }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_supplier, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($subtotalItem, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-right"><strong>Subtotal</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($subtotalTotal, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    @php
        $ppn   = $subtotalTotal * 0.11;
        $total = $subtotalTotal + $ppn;
    @endphp
    <table class="totals">
        <tr>
            <td width="70%"></td>
            <td width="15%" class="text-right"><strong>PPN (11%):</strong></td>
            <td width="15%" class="text-right">Rp {{ number_format($ppn, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="text-right"><strong>Total:</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($total, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

    <table class="signatures">
        <tr>
            <td><strong>Pihak Penerima (Unit)</strong></td>
            <td><strong>Pihak Distributor</strong></td>
        </tr>
        <tr>
            <td>
                <div class="sign-line"></div>
                Nama: {{ $order->unit ? $order->unit->name : '________________' }}<br>
                Jabatan: ________________
            </td>
            <td>
                <div class="sign-line"></div>
                Nama: {{ $order->user ? $order->user->name : '________________' }}<br>
                Jabatan: ________________
            </td>
        </tr>
    </table>
</body>
</html>
