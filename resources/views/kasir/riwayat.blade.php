@extends('layouts.kasir')

@section('content')
<div style="flex:1;overflow-y:auto;padding:24px">

    <div style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center">
        <div>
            <h2 style="font-size:18px;font-weight:700">Riwayat Transaksi</h2>
            <div style="font-size:12px;color:var(--t2);margin-top:2px">
                {{ now()->format('d F Y') }}
            </div>
        </div>
        <div style="font-size:12px;color:var(--t2)">
            Total: <span style="color:var(--green);font-weight:700">
                Rp {{ number_format($transaksi->sum('total_bayar'), 0, ',', '.') }}
            </span>
        </div>
    </div>

    @if($transaksi->isEmpty())
    <div style="text-align:center;padding:60px;color:var(--t3)">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
            style="margin:0 auto 12px;display:block;opacity:.3">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
        </svg>
        <div style="font-size:13px">Belum ada transaksi hari ini</div>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:8px">
        @foreach($transaksi as $t)
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:14px">

            {{-- Icon --}}
            <div style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;
                {{ $t->tipe_transaksi === 'rental' ? 'background:var(--pdim)' : 'background:var(--gdim)' }}">
                {{ $t->tipe_transaksi === 'rental' ? '🎮' : '☕' }}
            </div>

            {{-- Info --}}
            <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:600">
                    @if($t->tipe_transaksi === 'rental')
                        {{ $t->rental?->psUnit?->nomor_ps ?? 'PS ?' }}
                        — {{ $t->rental?->nama_pelanggan ?? '-' }}
                    @else
                        Cafe Only
                    @endif
                </div>
                <div style="font-size:11px;color:var(--t2);margin-top:2px;display:flex;gap:8px;flex-wrap:wrap">
                    <span>{{ $t->tipe_transaksi === 'rental' ? 'Rental PS' : 'F&B' }}</span>
                    <span style="color:var(--border2)">·</span>
                    <span>{{ ucfirst($t->metode_bayar) }}</span>
                    <span style="color:var(--border2)">·</span>
                    <span>{{ \Carbon\Carbon::parse($t->tanggal)->format('H:i') }}</span>
                    @if($t->orders->count() > 0)
                    <span style="color:var(--border2)">·</span>
                    <span>{{ $t->orders->count() }} item F&B</span>
                    @endif
                </div>
            </div>

            {{-- Total --}}
            <div style="text-align:right;flex-shrink:0">
                <div style="font-size:15px;font-weight:700;color:var(--green)">
                    Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
                </div>
                <div style="font-size:10px;padding:2px 8px;border-radius:4px;margin-top:4px;display:inline-block;
                    {{ $t->status_bayar === 'lunas' ? 'background:var(--gdim);color:var(--green)' : 'background:var(--rdim);color:var(--red)' }}">
                    {{ strtoupper($t->status_bayar) }}
                </div>
            </div>

            {{-- Tombol Cetak --}}
            <div style="flex-shrink:0;margin-left:8px">
                <a href="{{ route('kasir.struk.show', $t->id) }}?autoprint=0"
                   target="_blank"
                   style="display:inline-flex;align-items:center;gap:5px;padding:7px 12px;border-radius:7px;background:var(--bg3);border:1px solid var(--border2);color:var(--t2);text-decoration:none;font-size:12px;font-weight:500;transition:all .15s"
                   onmouseover="this.style.borderColor='var(--purple)';this.style.color='var(--purple2)'"
                   onmouseout="this.style.borderColor='var(--border2)';this.style.color='var(--t2)'">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    Struk
                </a>
            </div>

        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection