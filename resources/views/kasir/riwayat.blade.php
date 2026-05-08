@extends('layouts.kasir')

@section('content')
<div style="flex:1;overflow-y:auto;padding:24px">
    <div style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center">
        <div>
            <h2 style="font-size:18px;font-weight:700">Riwayat Transaksi</h2>
            <div style="font-size:12px;color:var(--t2);margin-top:2px">{{ now()->format('d F Y') }}</div>
        </div>
    </div>

    @if($transaksi->isEmpty())
    <div style="text-align:center;padding:60px;color:var(--t3)">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin:0 auto 12px;display:block;opacity:.3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/></svg>
        <div style="font-size:13px">Belum ada transaksi hari ini</div>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:10px">
        @foreach($transaksi as $t)
        <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:16px;display:flex;align-items:center;gap:16px">
            <div style="width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;
                {{ $t->tipe_transaksi==='rental' ? 'background:var(--pdim)' : 'background:var(--gdim)' }}">
                {{ $t->tipe_transaksi==='rental' ? '🎮' : '☕' }}
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:600">
                    {{ $t->tipe_transaksi==='rental' ? ($t->rental?->psUnit?->nomor_ps ?? 'PS ?').' — '.($t->rental?->nama_pelanggan ?? '-') : 'Cafe Only' }}
                </div>
                <div style="font-size:11px;color:var(--t2);margin-top:2px">
                    {{ $t->tipe_transaksi==='rental' ? 'Rental PS' : 'F&B' }}
                    · {{ ucfirst($t->metode_bayar) }}
                    · {{ \Carbon\Carbon::parse($t->tanggal)->format('H:i') }}
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0">
                <div style="font-size:15px;font-weight:700;color:var(--green)">{{ 'Rp '.number_format($t->total_bayar,0,',','.') }}</div>
                <div style="font-size:10px;padding:2px 8px;border-radius:4px;margin-top:4px;display:inline-block;
                    {{ $t->status_bayar==='lunas' ? 'background:var(--gdim);color:var(--green)' : 'background:var(--rdim);color:var(--red)' }}">
                    {{ strtoupper($t->status_bayar) }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection