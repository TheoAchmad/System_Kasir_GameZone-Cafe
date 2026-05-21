<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaksi->id }} — GameZone</title>
    <style>
        /* ═══════════════════════════════════════
           RESET & BASE
        ═══════════════════════════════════════ */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            background: #f5f5f5;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* ═══════════════════════════════════════
           STRUK WRAPPER
        ═══════════════════════════════════════ */
        .struk {
            background: #fff;
            width: 300px;
            padding: 20px 18px;
            box-shadow: 0 2px 12px rgba(0,0,0,.15);
            border-radius: 4px;
            position: relative;
        }

        /* Efek zigzag atas dan bawah struk */
        .struk::before, .struk::after {
            content: '';
            display: block;
            height: 10px;
            background:
                radial-gradient(circle at 5px -2px, transparent 5px, #fff 5px) 0 0 / 10px 10px,
                radial-gradient(circle at 5px 12px, #f5f5f5 5px, transparent 5px) 0 0 / 10px 10px;
            margin: 0 -18px;
        }
        .struk::before { margin-top: -20px; margin-bottom: 12px; }
        .struk::after  { margin-top: 12px; margin-bottom: -20px;
            background:
                radial-gradient(circle at 5px 12px, transparent 5px, #fff 5px) 0 0 / 10px 10px,
                radial-gradient(circle at 5px -2px, #f5f5f5 5px, transparent 5px) 0 0 / 10px 10px;
        }

        /* ═══════════════════════════════════════
           HEADER
        ═══════════════════════════════════════ */
        .header { text-align: center; margin-bottom: 14px; }
        .brand  {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 3px;
            font-family: Arial, sans-serif;
            margin-bottom: 2px;
        }
        .brand span { color: #6d28d9; }
        .cabang { font-size: 11px; color: #555; letter-spacing: 1px; }
        .tagline { font-size: 10px; color: #999; margin-top: 3px; }

        /* ═══════════════════════════════════════
           DIVIDER
        ═══════════════════════════════════════ */
        .divider     { border: none; border-top: 1px dashed #ccc; margin: 10px 0; }
        .divider-dot { text-align: center; color: #ccc; font-size: 10px; margin: 8px 0; letter-spacing: 3px; }

        /* ═══════════════════════════════════════
           INFO BARIS
        ═══════════════════════════════════════ */
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }
        .info-row .label { color: #666; }
        .info-row .val   { font-weight: 600; text-align: right; max-width: 60%; }

        /* ═══════════════════════════════════════
           BADGE TIPE TRANSAKSI
        ═══════════════════════════════════════ */
        .tipe-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .tipe-rental  { background: #ede9fe; color: #6d28d9; }
        .tipe-cafe    { background: #d1fae5; color: #065f46; }

        /* ═══════════════════════════════════════
           ITEM PESANAN
        ═══════════════════════════════════════ */
        .items-section { margin: 8px 0; }
        .items-header  { font-size: 10px; color: #999; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 6px; }

        .item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 5px;
            font-size: 11px;
        }
        .item-left  { flex: 1; }
        .item-name  { font-weight: 600; color: #111; }
        .item-qty   { color: #888; font-size: 10px; margin-top: 1px; }
        .item-price { font-weight: 700; color: #111; white-space: nowrap; margin-left: 8px; }

        /* ═══════════════════════════════════════
           SEWA ROW
        ═══════════════════════════════════════ */
        .sewa-row {
            background: #f3f0ff;
            border-radius: 4px;
            padding: 6px 8px;
            margin-bottom: 8px;
            font-size: 11px;
        }
        .sewa-row .sewa-title { font-weight: 700; color: #6d28d9; margin-bottom: 3px; }
        .sewa-row .sewa-detail { color: #555; }

        /* ═══════════════════════════════════════
           TOTALS
        ═══════════════════════════════════════ */
        .totals { margin-top: 8px; }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
            color: #555;
        }
        .total-row.subtotal { color: #888; font-size: 10px; }
        .total-row.grand {
            font-size: 15px;
            font-weight: 900;
            color: #111;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 2px solid #111;
        }
        .total-row.bayar   { color: #333; font-size: 12px; margin-top: 4px; }
        .total-row.kembalian {
            color: #065f46;
            font-weight: 700;
            font-size: 13px;
            background: #d1fae5;
            padding: 4px 6px;
            border-radius: 3px;
            margin-top: 4px;
        }

        /* ═══════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════ */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 4px;
            line-height: 1.8;
        }
        .footer .thankyou {
            font-size: 13px;
            font-weight: 700;
            color: #6d28d9;
            font-family: Arial, sans-serif;
            letter-spacing: 1px;
        }

        /* ═══════════════════════════════════════
           TOMBOL PRINT (hanya tampil di layar)
        ═══════════════════════════════════════ */
        .btn-wrap {
            width: 300px;
            margin: 16px auto 0;
            display: flex;
            gap: 10px;
        }
        .btn-print {
            flex: 1;
            padding: 12px;
            background: #6d28d9;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: .5px;
        }
        .btn-print:hover { background: #5b21b6; }
        .btn-close {
            flex: 1;
            padding: 12px;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-close:hover { background: #e5e7eb; }

        /* ═══════════════════════════════════════
           PRINT MEDIA — sembunyikan tombol, hapus background
        ═══════════════════════════════════════ */
        @media print {
            body {
                background: #fff;
                padding: 0;
                display: block;
            }
            .struk {
                box-shadow: none;
                width: 100%;
                max-width: 80mm; /* lebar thermal printer standard */
                margin: 0 auto;
                padding: 10px 8px;
            }
            .struk::before, .struk::after { display: none; }
            .btn-wrap { display: none; }
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<div class="struk">
    {{-- HEADER --}}
    <div class="header">
        <div class="brand"><span>Game</span>Zone</div>
        <div class="cabang">
            {{ $transaksi->kasir?->cabang?->nama_cabang ?? 'GameZone Cafe' }}
        </div>
        @if($transaksi->kasir?->cabang?->alamat)
        <div class="tagline">{{ $transaksi->kasir->cabang->alamat }}</div>
        @endif
    </div>

    <hr class="divider">

    {{-- INFO TRANSAKSI --}}
    <div class="info-row">
        <span class="label">No. Struk</span>
        <span class="val">#{{ str_pad($transaksi->id, 5, '0', STR_PAD_LEFT) }}</span>
    </div>
    <div class="info-row">
        <span class="label">Tanggal</span>
        <span class="val">{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y H:i') }}</span>
    </div>
    <div class="info-row">
        <span class="label">Kasir</span>
        <span class="val">{{ $transaksi->kasir?->name ?? '-' }}</span>
    </div>
    <div class="info-row">
        <span class="label">Tipe</span>
        <span class="val">
            <span class="tipe-badge {{ $transaksi->tipe_transaksi === 'rental' ? 'tipe-rental' : 'tipe-cafe' }}">
                {{ $transaksi->tipe_transaksi === 'rental' ? 'Rental PS' : 'Cafe Only' }}
            </span>
        </span>
    </div>

    @if($transaksi->rental)
    <div class="info-row">
        <span class="label">Pelanggan</span>
        <span class="val">{{ $transaksi->rental->nama_pelanggan }}</span>
    </div>
    <div class="info-row">
        <span class="label">Unit PS</span>
        <span class="val">
            {{ $transaksi->rental->psUnit->nomor_ps ?? '-' }}
            ({{ $transaksi->rental->psUnit->tipe_ps ?? '' }})
        </span>
    </div>
    @endif

    <hr class="divider">

    {{-- DETAIL SEWA (jika rental) --}}
    @if($transaksi->rental && $transaksi->subtotal_sewa > 0)
    <div class="sewa-row">
        <div class="sewa-title">🎮 Sewa PlayStation</div>
        <div class="sewa-detail">
            @php
                $r = $transaksi->rental;
                $mulai = \Carbon\Carbon::parse($r->jam_mulai)->format('H:i');
                $selesai = \Carbon\Carbon::parse($r->jam_selesai)->format('H:i');
                $ps = $r->psUnit;
                $hpj = number_format($ps->harga_per_jam, 0, ',', '.');
            @endphp
            {{ $mulai }} — {{ $selesai }}
            &nbsp;·&nbsp; Rp {{ $hpj }}/jam
        </div>
    </div>
    @endif

    {{-- ITEM F&B --}}
    @if($transaksi->orders->count() > 0)
    <div class="items-section">
        <div class="items-header">🍔 Pesanan F&B</div>
        @foreach($transaksi->orders as $order)
        <div class="item">
            <div class="item-left">
                <div class="item-name">{{ $order->menu?->nama_menu ?? 'Menu' }}</div>
                <div class="item-qty">
                    {{ $order->qty }} x Rp {{ number_format($order->harga, 0, ',', '.') }}
                </div>
            </div>
            <div class="item-price">
                Rp {{ number_format($order->subtotal, 0, ',', '.') }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <hr class="divider">

    {{-- TOTALS --}}
    <div class="totals">
        @if($transaksi->tipe_transaksi === 'rental')
        <div class="total-row subtotal">
            <span>Subtotal Sewa</span>
            <span>Rp {{ number_format($transaksi->subtotal_sewa, 0, ',', '.') }}</span>
        </div>
        @if($transaksi->subtotal_menu > 0)
        <div class="total-row subtotal">
            <span>Subtotal F&B</span>
            <span>Rp {{ number_format($transaksi->subtotal_menu, 0, ',', '.') }}</span>
        </div>
        @endif
        @endif

        @if($transaksi->diskon > 0)
        <div class="total-row subtotal">
            <span>Diskon</span>
            <span>- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="total-row grand">
            <span>TOTAL</span>
            <span>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
        </div>

        <div class="total-row bayar">
            <span>Dibayar ({{ strtoupper($transaksi->metode_bayar) }})</span>
            <span>Rp {{ number_format($transaksi->uang_bayar, 0, ',', '.') }}</span>
        </div>

        @if($transaksi->kembalian > 0)
        <div class="total-row kembalian">
            <span>Kembalian</span>
            <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    <div class="divider-dot">• • • • • • • • • •</div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="thankyou">Terima Kasih!</div>
        <div>Sampai jumpa lagi di GameZone</div>
        <div style="margin-top:4px;font-size:9px">
            Struk ini adalah bukti pembayaran yang sah
        </div>
    </div>
</div>

{{-- TOMBOL PRINT --}}
<div class="btn-wrap">
    <button class="btn-print" onclick="window.print()">
        🖨️ Cetak Struk
    </button>
    <button class="btn-close" onclick="window.close()">
        ✕ Tutup
    </button>
</div>

<script>
    // Auto print jika ada query ?autoprint=1
    const params = new URLSearchParams(window.location.search);
    if (params.get('autoprint') === '1') {
        window.onload = () => {
            setTimeout(() => window.print(), 500);
        };
    }
</script>

</body>
</html>