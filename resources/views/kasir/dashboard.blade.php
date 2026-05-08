@extends('layouts.kasir')

@section('content')
<div id="app" style="display:flex;height:100%;overflow:hidden">

    {{-- ══ GRID PS ══ --}}
    <div style="flex:1;display:flex;flex-direction:column;overflow:hidden">
        <div style="padding:14px 24px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);flex-shrink:0">
            <div style="display:flex;align-items:center;gap:14px">
                <span style="font-size:13px;font-weight:600">Meja Kasir</span>
                <span style="font-size:12px;color:var(--t2)" id="stat-text">—</span>
            </div>
            <button onclick="openCafeOnly()"
                style="display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;background:var(--gdim);border:1px solid var(--green);color:var(--green);font-family:'Inter',sans-serif;font-size:12px;font-weight:600;cursor:pointer">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Cafe Only
            </button>
        </div>

        <div style="flex:1;overflow-y:auto;padding:20px 24px">
            <div id="ps-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px"></div>
        </div>
    </div>

    {{-- ══ PANEL KANAN ══ --}}
    <div style="width:290px;background:var(--bg2);border-left:1px solid var(--border);display:flex;flex-direction:column;flex-shrink:0">
        <div id="panel-empty" style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--t3);text-align:center;padding:24px">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin-bottom:12px;opacity:.2">
                <rect x="2" y="6" width="20" height="12" rx="2"/>
                <circle cx="8" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                <line x1="14" y1="9" x2="14" y2="15"/><line x1="11" y1="12" x2="17" y2="12"/>
            </svg>
            <div style="font-size:13px">Pilih card PS<br>untuk melihat detail</div>
        </div>

        <div id="panel-detail" style="display:none;flex-direction:column;height:100%">
            <div id="ph-header" style="padding:15px 18px;border-bottom:1px solid var(--border)">
                <div style="display:flex;justify-content:space-between;align-items:flex-start">
                    <div>
                        <div id="ph-title" style="font-size:16px;font-weight:700"></div>
                        <div id="ph-player" style="font-size:12px;color:var(--t2);margin-top:2px"></div>
                    </div>
                    <div id="ph-badge" style="font-size:10px;font-weight:600;padding:3px 9px;border-radius:5px;letter-spacing:.5px"></div>
                </div>
            </div>
            <div style="display:flex;justify-content:space-between;padding:11px 18px;background:var(--bg);border-bottom:1px solid var(--border);font-size:12px">
                <div><div style="color:var(--t3);font-size:10px;margin-bottom:3px">MULAI</div><div id="ph-start" style="font-family:'Space Mono',monospace;font-weight:700"></div></div>
                <div style="color:var(--t3);align-self:center">→</div>
                <div style="text-align:right"><div style="color:var(--t3);font-size:10px;margin-bottom:3px">SELESAI</div><div id="ph-end" style="font-family:'Space Mono',monospace;font-weight:700"></div></div>
            </div>
            <div style="display:flex;gap:8px;padding:10px 18px;border-bottom:1px solid var(--border)">
                <button id="btn-addtime" onclick="openTambahWaktu()"
                    style="flex:1;padding:8px;border-radius:8px;border:1px solid var(--blue);background:var(--bdim);color:var(--blue);font-family:'Inter',sans-serif;font-size:12px;font-weight:500;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    + Add Time
                </button>
                <button onclick="openFnB()"
                    style="flex:1;padding:8px;border-radius:8px;border:1px solid var(--purple);background:var(--pdim);color:var(--purple2);font-family:'Inter',sans-serif;font-size:12px;font-weight:500;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/></svg>
                    + F&B
                </button>
            </div>
            <div style="flex:1;overflow-y:auto;padding:12px 18px">
                <div style="font-size:10px;color:var(--t3);letter-spacing:.8px;margin-bottom:10px">F&B ORDERS</div>
                <div id="orders-list"></div>
                <div id="orders-empty" style="text-align:center;color:var(--t3);font-size:12px;padding:14px 0">Belum ada pesanan</div>
            </div>
            <div style="padding:14px 18px;border-top:1px solid var(--border)">
                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--t2);margin-bottom:3px"><span>Sewa</span><span id="ft-sewa">Rp 0</span></div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--t2);margin-bottom:10px"><span>F&B</span><span id="ft-fnb">Rp 0</span></div>
                <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:700;margin-bottom:14px"><span>Total</span><span id="ft-total" style="color:var(--green)">Rp 0</span></div>
                <button onclick="openBayar()" class="btn btn-g btn-full btn-lg">Checkout & Pay</button>
            </div>
        </div>
    </div>
</div>

{{-- ══ NOTIFICATION OVERLAY (waktu habis) ══ --}}
<div id="notif-overlay" style="display:none;position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:8000;min-width:340px">
    <div style="background:#1a0d0d;border:1px solid var(--red);border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 4px 32px rgba(239,69,69,.3)">
        <div style="width:38px;height:38px;border-radius:10px;background:var(--rdim);display:flex;align-items:center;justify-content:center;flex-shrink:0;animation:pulse-red 1s ease infinite">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div style="flex:1">
            <div style="font-size:13px;font-weight:700;color:#fca5a5" id="notif-title">WAKTU HABIS</div>
            <div style="font-size:12px;color:#9f1239;margin-top:2px" id="notif-sub">PS ? — Segera selesaikan sesi!</div>
        </div>
        <button onclick="dismissNotif()" style="background:none;border:none;color:#4a1515;cursor:pointer;font-size:18px;line-height:1">✕</button>
    </div>
</div>

{{-- MODALS --}}

{{-- Modal Mulai Rental --}}
<div id="m-rental" class="modal-wrap" style="display:none">
<div class="modal" onclick="event.stopPropagation()">
    <div class="modal-hd">
        <h3>New Session — <span id="mr-psname"></span></h3>
        <button class="modal-close" onclick="closeModal('m-rental')">&#215;</button>
    </div>
    <div class="fg">
        <label>Nama Pelanggan</label>
        <input type="text" id="mr-nama" placeholder="Masukkan nama pelanggan...">
    </div>
    <div class="fg">
        <label>Mode Billing</label>
        <div class="mode-grid">
            <div class="mode-card on" id="mc-down" onclick="setMode('down')">
                <div class="mc-icon">⏬</div>
                <div class="mc-title">Countdown</div>
                <div class="mc-sub">Durasi ditentukan di awal</div>
            </div>
            <div class="mode-card" id="mc-up" onclick="setMode('up')">
                <div class="mc-icon">⏫</div>
                <div class="mc-title">Open Billing</div>
                <div class="mc-sub">Bayar sesuai waktu main</div>
            </div>
        </div>
    </div>
    <div class="fg" id="fg-durasi">
        <label>Durasi (Jam)</label>
        <div class="qty-row">
            <button class="qbtn" onclick="adjDurasi(-0.5)">−</button>
            <input type="number" id="mr-durasi" value="1" min="0.5" step="0.5" class="qinput">
            <button class="qbtn" onclick="adjDurasi(0.5)">+</button>
        </div>
        <div style="font-size:11px;color:var(--t3);margin-top:6px" id="mr-estimasi">Estimasi: Rp 0</div>
    </div>
    <div style="display:flex;gap:10px;margin-top:4px">
        <button class="btn btn-gh" style="flex:1" onclick="closeModal('m-rental')">Batal</button>
        <button class="btn btn-p" style="flex:1" onclick="submitRental()">Mulai Sesi</button>
    </div>
</div>
</div>

{{-- Modal Tambah Waktu --}}
<div id="m-addtime" class="modal-wrap" style="display:none">
<div class="modal" style="width:360px" onclick="event.stopPropagation()">
    <div class="modal-hd">
        <h3>Tambah Waktu — <span id="at-psname"></span></h3>
        <button class="modal-close" onclick="closeModal('m-addtime')">&#215;</button>
    </div>
    <div style="text-align:center;margin-bottom:20px">
        <div style="font-size:10px;color:var(--t3);margin-bottom:4px">SELESAI SAAT INI</div>
        <div style="font-size:28px;font-weight:700;color:var(--yellow);font-family:'Space Mono',monospace" id="at-current">--:--</div>
    </div>
    <div class="fg">
        <label>Tambahan Durasi (Jam)</label>
        <div class="qty-row">
            <button class="qbtn" onclick="adjAddTime(-0.5)">−</button>
            <input type="number" id="at-jam" value="1" min="0.5" step="0.5" class="qinput">
            <button class="qbtn" onclick="adjAddTime(0.5)">+</button>
        </div>
        <div style="font-size:11px;color:var(--purple2);margin-top:6px" id="at-biaya">+ Biaya: Rp 0</div>
    </div>
    <div style="display:flex;gap:10px">
        <button class="btn btn-gh" style="flex:1" onclick="closeModal('m-addtime')">Batal</button>
        <button class="btn btn-p" style="flex:1" onclick="submitAddTime()">Tambah Waktu</button>
    </div>
</div>
</div>

{{-- Modal F&B --}}
<div id="m-fnb" class="modal-wrap" style="display:none">
<div class="modal lg" onclick="event.stopPropagation()">
    <div class="modal-hd">
        <h3>Order F&B — <span id="fnb-psname"></span></h3>
        <button class="modal-close" onclick="closeModal('m-fnb')">&#215;</button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 220px;gap:16px;height:400px">
        <div style="overflow-y:auto;padding-right:6px" id="fnb-menu-list"></div>
        <div style="border-left:1px solid var(--border);padding-left:16px;display:flex;flex-direction:column">
            <div style="font-size:10px;color:var(--t3);letter-spacing:.8px;margin-bottom:10px">KERANJANG</div>
            <div style="flex:1;overflow-y:auto" id="fnb-cart-list"></div>
            <div style="border-top:1px solid var(--border);padding-top:10px;margin-top:8px">
                <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;margin-bottom:12px">
                    <span>Total</span><span style="color:var(--green)" id="fnb-total">Rp 0</span>
                </div>
                <button class="btn btn-g btn-full" onclick="submitFnB()">Tambah ke Tagihan</button>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Modal Bayar --}}
<div id="m-bayar" class="modal-wrap" style="display:none">
<div class="modal" onclick="event.stopPropagation()">
    <div class="modal-hd">
        <h3>Checkout — <span id="by-psname"></span></h3>
        <button class="modal-close" onclick="closeModal('m-bayar')">&#215;</button>
    </div>
    <div class="pay-sum">
        <div class="ps-row"><span>Subtotal Sewa</span><span id="by-sewa">Rp 0</span></div>
        <div class="ps-row"><span>Subtotal F&B</span><span id="by-fnb">Rp 0</span></div>
        <div class="ps-row tot"><span>Total Bayar</span><span id="by-total" style="color:var(--green)">Rp 0</span></div>
    </div>
    <div class="fg">
        <label>Metode Pembayaran</label>
        <div class="pay-grid">
            <div class="pay-opt on" id="po-tunai" onclick="setMetode('tunai')">💵 Tunai</div>
            <div class="pay-opt" id="po-transfer" onclick="setMetode('transfer')">🏦 Transfer</div>
            <div class="pay-opt" id="po-qris" onclick="setMetode('qris')">📱 QRIS</div>
        </div>
    </div>
    <div class="fg">
        <label>Uang yang Dibayar</label>
        <input type="number" id="by-uang" placeholder="Masukkan nominal..."
            oninput="calcKembali()"
            style="font-family:'Space Mono',monospace;font-size:18px;font-weight:700;text-align:right">
    </div>
    <div class="kembali-box" id="kembali-box" style="display:none">
        <div class="klbl">Kembalian</div>
        <div class="kamt" id="kembali-amt">Rp 0</div>
    </div>
    <div style="display:flex;gap:10px;margin-top:14px">
        <button class="btn btn-gh" style="flex:1" onclick="closeModal('m-bayar')">Batal</button>
        <button class="btn btn-g btn-lg" style="flex:2" onclick="submitBayar()">Selesaikan Transaksi</button>
    </div>
</div>
</div>

{{-- Modal Cafe Only --}}
<div id="m-cafe" class="modal-wrap" style="display:none">
<div class="modal lg" onclick="event.stopPropagation()">
    <div class="modal-hd">
        <h3>Transaksi Cafe Only</h3>
        <button class="modal-close" onclick="closeModal('m-cafe')">&#215;</button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 260px;gap:16px;max-height:65vh">
        <div style="overflow-y:auto;padding-right:6px" id="cafe-menu-list"></div>
        <div style="border-left:1px solid var(--border);padding-left:16px;display:flex;flex-direction:column;overflow-y:auto">
            <div style="font-size:10px;color:var(--t3);letter-spacing:.8px;margin-bottom:10px">PESANAN</div>
            <div style="flex:1;min-height:80px" id="cafe-cart-list"></div>
            <div style="border-top:1px solid var(--border);padding-top:10px;margin-top:8px">
                <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:700;margin-bottom:12px">
                    <span>Total</span><span style="color:var(--green)" id="cafe-total">Rp 0</span>
                </div>
                <div class="fg">
                    <label>Metode</label>
                    <div class="pay-grid">
                        <div class="pay-opt on" id="cpo-tunai" onclick="setCafeMetode('tunai')" style="font-size:11px;padding:8px 4px">Tunai</div>
                        <div class="pay-opt" id="cpo-transfer" onclick="setCafeMetode('transfer')" style="font-size:11px;padding:8px 4px">Transfer</div>
                        <div class="pay-opt" id="cpo-qris" onclick="setCafeMetode('qris')" style="font-size:11px;padding:8px 4px">QRIS</div>
                    </div>
                </div>
                <div class="fg">
                    <label>Uang Bayar</label>
                    <input type="number" id="cafe-uang" placeholder="Nominal..." oninput="calcCafeKembali()"
                        style="font-family:'Space Mono',monospace;font-weight:700;text-align:right">
                </div>
                <div class="kembali-box" id="cafe-kembali-box" style="display:none;margin-bottom:10px">
                    <div class="klbl">Kembalian</div>
                    <div class="kamt" id="cafe-kembali-amt">Rp 0</div>
                </div>
                <button class="btn btn-g btn-full btn-lg" onclick="submitCafe()">Bayar</button>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
// ══════════════════════════════════
// DATA & STATE
// ══════════════════════════════════
let psUnits    = @json($psUnits);
let menus      = @json($menus);
let selectedPs = null;
let notifQueue = []; // antrian notifikasi waktu habis
let notifShowing = false;
let alarmPlayed  = {};

// Form state
let mrMode='down', mrPsId=null, atRentalId=null, fnbRentalId=null;
let fnbCart={}, cafeCart={};
let byMetode='tunai', cafeMetode='tunai';
let byTotal=0, cafeTotal=0;

// ══════════════════════════════════
// INIT
// ══════════════════════════════════
document.addEventListener('DOMContentLoaded', ()=>{
    renderGrid();
    startTimerLoop();
    startPolling();
});

// ══════════════════════════════════
// CARD RENDER — mirip desain foto
// ══════════════════════════════════
function renderGrid(){
    const grid=document.getElementById('ps-grid');
    grid.innerHTML='';
    let kosong=0,dipakai=0;
    psUnits.forEach(ps=>{
        if(ps.status==='kosong') kosong++;
        if(ps.status==='dipakai') dipakai++;
        grid.appendChild(makeCard(ps));
    });
    document.getElementById('stat-text').textContent=`${kosong} tersedia · ${dipakai} dipakai`;
}

function makeCard(ps){
    const div=document.createElement('div');
    const expired=ps.rental?.is_expired;
    const dipakai=ps.status==='dipakai';
    const kosong =ps.status==='kosong';
    const active =selectedPs?.id===ps.id;

    // Warna sesuai desain foto
    let bg, border, glowColor='';
    if(kosong){
        bg='linear-gradient(160deg,#1a1b2e 0%,#141520 100%)';
        border=active?'rgba(139,143,168,.6)':'rgba(255,255,255,.06)';
    } else if(dipakai){
        if(expired){
            bg='linear-gradient(160deg,#2a0f0f 0%,#1a0808 100%)';
            border='#ef4545';
            glowColor='rgba(239,69,69,.25)';
        } else {
            bg='linear-gradient(160deg,#1f1a0a 0%,#161005 100%)';
            border=active?'#f5a623':'rgba(245,166,35,.5)';
            glowColor='rgba(245,166,35,.15)';
        }
    } else {
        bg='#0f1018'; border='rgba(255,255,255,.04)';
    }

    div.style.cssText=`
        background:${bg};border:1.5px solid ${border};border-radius:14px;
        padding:18px 16px;cursor:pointer;transition:all .2s;
        position:relative;min-height:200px;
        ${glowColor?`box-shadow:0 0 20px ${glowColor};`:''}
        ${expired?'animation:pulseborder 1.2s ease infinite;':''}
    `;
    div.onclick=()=>clickCard(ps);

    // Status dot
    const dotColor=kosong?'#3dd68c':dipakai?(expired?'#ef4545':'#f5a623'):'#4a4d6a';

    // PS type badge color
    const badgeBg=ps.tipe_ps==='PS5'?'rgba(77,157,224,.2)':'rgba(124,111,224,.2)';
    const badgeColor=ps.tipe_ps==='PS5'?'#4d9de0':'#9d8ff5';

    // Controller icon color per status
    const ctrlColor=kosong?'rgba(139,143,168,.25)':dipakai?(expired?'rgba(239,69,69,.5)':'rgba(245,166,35,.7)'):'rgba(74,77,106,.3)';

    let body='';
    if(kosong){
        body=`
        <div style="text-align:center;padding:20px 0 14px">
            ${controllerSVG(ctrlColor)}
            <div style="margin-top:16px">
                <div style="display:flex;align-items:center;justify-content:center;gap:4px;margin-bottom:5px">
                    <span style="width:6px;height:1px;background:rgba(255,255,255,.15);display:inline-block"></span>
                    <span style="font-size:10px;color:rgba(255,255,255,.15);font-family:'Space Mono',monospace;letter-spacing:2px">- : -</span>
                    <span style="width:6px;height:1px;background:rgba(255,255,255,.15);display:inline-block"></span>
                </div>
                <div style="font-size:11px;color:#3dd68c;letter-spacing:2px;font-weight:600;font-family:'Space Mono',monospace">AVAILABLE</div>
            </div>
        </div>`;
    } else if(dipakai && ps.rental){
        const timerColor=expired?'#ef4545':'#f5a623';
        const statusLabel=expired?'TIME UP':'IN USE';
        const statusColor=expired?'#ef4545':'#f5a623';
        body=`
        <div style="text-align:center;padding:10px 0 8px">
            ${controllerSVG(ctrlColor)}
            <div style="margin-top:12px">
                <div style="width:100%;overflow:hidden;padding:0 4px">
    <div id="timer-${ps.id}" style="
        font-size:26px;
        font-weight:700;
        letter-spacing:2px;
        font-family:'Space Mono',monospace;
        color:${timerColor};
        line-height:1;
        text-align:center;
        white-space:nowrap;
    ">${ps.rental.timer_display||'--:--:--'}</div>
                <div style="font-size:10px;color:${statusColor};letter-spacing:2px;margin-top:6px;font-weight:600">${statusLabel}</div>
            </div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px">
            <div id="sub-${ps.id}" style="font-size:11px;color:rgba(255,255,255,.4)">${rp(ps.rental.subtotal_sementara||0)}</div>
        </div>`;
    } else {
        body=`<div style="text-align:center;padding:20px 0;opacity:.2">${controllerSVG('#6b7280')}<div style="font-size:10px;color:var(--t3);margin-top:10px;letter-spacing:1px">MAINTENANCE</div></div>`;
    }

    div.innerHTML=`
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px">
            <div>
                <div style="font-size:16px;font-weight:700;letter-spacing:-.3px">${ps.nomor_ps}</div>
                <div style="display:inline-block;margin-top:5px;padding:2px 8px;border-radius:4px;font-size:9px;font-weight:700;letter-spacing:.5px;background:${badgeBg};color:${badgeColor}">${ps.tipe_ps}</div>
            </div>
            <div style="display:flex;align-items:center;gap:5px;margin-top:2px">
                ${kosong&&ps.rental?.nama_pelanggan===undefined?'':dipakai&&ps.rental?`<span style="font-size:11px;color:rgba(255,255,255,.4)">${ps.rental.nama_pelanggan}</span>`:''}
                <div style="width:7px;height:7px;border-radius:50%;background:${dotColor};${glowColor?`box-shadow:0 0 5px ${dotColor};`:''}"></div>
            </div>
        </div>
        ${body}
    `;
    return div;
}

function controllerSVG(color){
    return `<svg width="52" height="36" viewBox="0 0 52 36" fill="${color}" style="display:block;margin:0 auto">
        <path d="M4 10 Q4 4 10 4 L18 4 L20 8 L32 8 L34 4 L42 4 Q48 4 48 10 L48 22 Q48 32 40 32 L34 32 Q30 32 28 28 L24 28 Q22 32 18 32 L12 32 Q4 32 4 22 Z"/>
        <rect x="8" y="14" width="2" height="8" rx="1" fill="rgba(0,0,0,.3)"/>
        <rect x="5" y="17" width="8" height="2" rx="1" fill="rgba(0,0,0,.3)"/>
        <circle cx="38" cy="15" r="2" fill="rgba(0,0,0,.3)"/>
        <circle cx="38" cy="21" r="2" fill="rgba(0,0,0,.3)"/>
        <circle cx="35" cy="18" r="2" fill="rgba(0,0,0,.3)"/>
        <circle cx="41" cy="18" r="2" fill="rgba(0,0,0,.3)"/>
    </svg>`;
}

// ══════════════════════════════════
// TIMER LOOP — FIX: berhenti saat expired (mode down)
// ══════════════════════════════════
function startTimerLoop(){
    setInterval(()=>{
        const now=Date.now();
        psUnits.forEach(ps=>{
            if(ps.status!=='dipakai'||!ps.rental) return;
            const r=ps.rental;
            const hpj=parseFloat(ps.harga_per_jam)||0;

            if(r.mode_billing==='down'){
                const diff=r.jam_selesai_ts-now;

                if(diff<=0){
                    // ✅ WAKTU HABIS — TIMER BERHENTI, HARGA TIDAK BERTAMBAH LAGI
                    r.timer_display='00:00:00';
                    r.is_expired=true;

                    // Harga DIKUNCI saat jam_selesai
                    if(!r.harga_terkunci){
                        const menitActual=(r.jam_selesai_ts-r.jam_mulai_ts)/60000;
                        r.subtotal_sewa=(menitActual/60)*hpj;
                        r.harga_terkunci=true; // flag supaya tidak dihitung ulang
                    }

                    // Trigger alarm & notifikasi (hanya sekali)
                    if(!alarmPlayed[ps.id]){
                        alarmPlayed[ps.id]=true;
                        playAlarm();
                        pushNotif(ps.nomor_ps, ps.rental.nama_pelanggan);
                    }
                } else {
                    // Masih berjalan
                    r.timer_display=fmtMs(diff);
                    r.is_expired=false;
                    const menitBerjalan=(now-r.jam_mulai_ts)/60000;
                    r.subtotal_sewa=(menitBerjalan/60)*hpj;
                }
            } else {
                // Mode UP — tetap berjalan terus
                const elapsed=now-r.jam_mulai_ts;
                r.timer_display=fmtMs(elapsed);
                r.is_expired=false;
                r.subtotal_sewa=(elapsed/3600000)*hpj;
            }

            r.subtotal_sementara=r.subtotal_sewa+(r.subtotal_menu||0);

            // Update DOM timer
            const tel=document.getElementById('timer-'+ps.id);
            const sel=document.getElementById('sub-'+ps.id);
            if(tel){
                tel.textContent=r.timer_display;
                tel.style.color=r.is_expired?'#ef4545':'#f5a623';
            }
            if(sel) sel.textContent=rp(r.subtotal_sementara);

            // Update panel kanan
            if(selectedPs?.id===ps.id) updatePanel(ps);
        });
    },1000);
}

// ══════════════════════════════════
// NOTIFIKASI WAKTU HABIS
// ══════════════════════════════════
function pushNotif(psName, playerName){
    notifQueue.push({psName,playerName});
    if(!notifShowing) showNextNotif();
}
function showNextNotif(){
    if(!notifQueue.length){ notifShowing=false; return; }
    notifShowing=true;
    const n=notifQueue.shift();
    document.getElementById('notif-title').textContent=`⚠ WAKTU HABIS — ${n.psName}`;
    document.getElementById('notif-sub').textContent=`${n.playerName} — Segera selesaikan sesi!`;
    document.getElementById('notif-overlay').style.display='block';
    // Auto dismiss 8 detik
    setTimeout(()=>{ dismissNotif(); },8000);
}
function dismissNotif(){
    document.getElementById('notif-overlay').style.display='none';
    setTimeout(showNextNotif,400);
}

// ══════════════════════════════════
// POLLING (30 detik)
// ══════════════════════════════════
function startPolling(){
    setInterval(async()=>{
        try{
            const data=await api('/kasir/api/ps-units');
            data.forEach(np=>{
                const op=psUnits.find(p=>p.id===np.id);
                if(op?.rental&&np.rental){
                    np.rental.timer_display=op.rental.timer_display||'--:--:--';
                    np.rental.is_expired=op.rental.is_expired||false;
                    np.rental.subtotal_sewa=op.rental.subtotal_sewa||0;
                    np.rental.subtotal_sementara=op.rental.subtotal_sementara||0;
                    np.rental.harga_terkunci=op.rental.harga_terkunci||false;
                }
            });
            psUnits=data;
            renderGrid();
            if(selectedPs){
                const f=psUnits.find(p=>p.id===selectedPs.id);
                f?showPanel(selectedPs=f):clearPanel();
            }
        }catch(e){}
    },30000);
}

// ══════════════════════════════════
// CARD CLICK & PANEL
// ══════════════════════════════════
function clickCard(ps){
    selectedPs=ps;
    renderGrid();
    if(ps.status==='kosong') openRental(ps);
    else if(ps.status==='dipakai'&&ps.rental) showPanel(ps);
}
function clearPanel(){ selectedPs=null; document.getElementById('panel-empty').style.display='flex'; document.getElementById('panel-detail').style.display='none'; }
function showPanel(ps){ document.getElementById('panel-empty').style.display='none'; document.getElementById('panel-detail').style.display='flex'; updatePanel(ps); }
function updatePanel(ps){
    if(!ps.rental) return;
    const r=ps.rental;
    document.getElementById('ph-title').textContent=ps.nomor_ps;
    document.getElementById('ph-player').textContent=r.nama_pelanggan;
    document.getElementById('ph-start').textContent=r.jam_mulai_fmt;
    document.getElementById('ph-end').textContent=r.jam_selesai_fmt||'Open';
    const badge=document.getElementById('ph-badge');
    badge.textContent=r.is_expired?'TIME UP':'IN USE';
    badge.style.cssText=r.is_expired?'background:var(--rdim);color:var(--red);font-size:10px;font-weight:600;padding:3px 9px;border-radius:5px;letter-spacing:.5px':'background:var(--ydim);color:var(--yellow);font-size:10px;font-weight:600;padding:3px 9px;border-radius:5px;letter-spacing:.5px';
    document.getElementById('btn-addtime').style.display=r.mode_billing==='down'?'flex':'none';
    const ol=document.getElementById('orders-list');
    const oe=document.getElementById('orders-empty');
    if(r.orders&&r.orders.length>0){
        oe.style.display='none';
        ol.innerHTML=r.orders.map(o=>`<div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid var(--border)">
            <div><div style="font-size:13px">${o.menu?.nama_menu||''}</div><div style="font-size:11px;color:var(--t3)">x${o.qty} @ ${rp(o.harga)}</div></div>
            <div style="font-size:12px;color:var(--purple2);font-weight:500">${rp(o.subtotal)}</div>
        </div>`).join('');
    } else { oe.style.display='block'; ol.innerHTML=''; }
    const sewa=r.subtotal_sewa||0, fnb=r.subtotal_menu||0;
    document.getElementById('ft-sewa').textContent=rp(sewa);
    document.getElementById('ft-fnb').textContent=rp(fnb);
    document.getElementById('ft-total').textContent=rp(sewa+fnb);
}

// ══════════════════════════════════
// MODALS
// ══════════════════════════════════
function openModal(id){ document.getElementById(id).style.display='flex'; }
function closeModal(id){ document.getElementById(id).style.display='none'; }
document.addEventListener('keydown',e=>{ if(e.key==='Escape') document.querySelectorAll('.modal-wrap').forEach(m=>m.style.display='none'); });

// Rental
function openRental(ps){ mrPsId=ps.id; document.getElementById('mr-psname').textContent=ps.nomor_ps; document.getElementById('mr-nama').value=''; document.getElementById('mr-durasi').value=1; setMode('down'); updateEst(ps); openModal('m-rental'); setTimeout(()=>document.getElementById('mr-nama').focus(),80); }
function setMode(m){ mrMode=m; document.getElementById('mc-down').className='mode-card'+(m==='down'?' on':''); document.getElementById('mc-up').className='mode-card'+(m==='up'?' on':''); document.getElementById('fg-durasi').style.display=m==='down'?'block':'none'; }
function adjDurasi(d){ const i=document.getElementById('mr-durasi'); i.value=Math.max(0.5,(parseFloat(i.value)||1)+d); updateEst(psUnits.find(p=>p.id===mrPsId)); }
function updateEst(ps){ const d=parseFloat(document.getElementById('mr-durasi')?.value||1); document.getElementById('mr-estimasi').textContent='Estimasi: '+rp(d*(ps?.harga_per_jam||0)); }
async function submitRental(){
    const nama=document.getElementById('mr-nama').value.trim();
    if(!nama){ toast('Nama pelanggan wajib diisi','err'); return; }
    const dur=parseFloat(document.getElementById('mr-durasi').value)||1;
    try{ await api('/kasir/rental/mulai','POST',{ps_id:mrPsId,nama_pelanggan:nama,mode_billing:mrMode,durasi_awal:mrMode==='down'?dur:null}); closeModal('m-rental'); toast('Sesi berhasil dimulai!'); await refresh(); }catch(e){ toast(e.message,'err'); }
}

// Tambah Waktu
function openTambahWaktu(){ if(!selectedPs?.rental) return; atRentalId=selectedPs.rental.id; document.getElementById('at-psname').textContent=selectedPs.nomor_ps; document.getElementById('at-current').textContent=selectedPs.rental.jam_selesai_fmt||'--:--'; document.getElementById('at-jam').value=1; updateAtBiaya(); openModal('m-addtime'); }
function adjAddTime(d){ const i=document.getElementById('at-jam'); i.value=Math.max(0.5,(parseFloat(i.value)||1)+d); updateAtBiaya(); }
function updateAtBiaya(){ const j=parseFloat(document.getElementById('at-jam').value)||0; document.getElementById('at-biaya').textContent='+ Biaya: '+rp(j*(selectedPs?.harga_per_jam||0)); }
async function submitAddTime(){ const j=parseFloat(document.getElementById('at-jam').value)||0; if(j<=0){ toast('Masukkan durasi','err'); return; } try{ await api('/kasir/rental/'+atRentalId+'/tambah-waktu','POST',{tambahan_jam:j}); closeModal('m-addtime'); toast('Waktu ditambah!'); delete alarmPlayed[selectedPs.id]; if(selectedPs?.rental) selectedPs.rental.harga_terkunci=false; await refresh(); }catch(e){ toast(e.message,'err'); } }

// F&B
function openFnB(){ if(!selectedPs?.rental) return; fnbRentalId=selectedPs.rental.id; fnbCart={}; document.getElementById('fnb-psname').textContent=selectedPs.nomor_ps+' — '+selectedPs.rental.nama_pelanggan; renderFnBMenu(); renderFnBCart(); openModal('m-fnb'); }
function renderFnBMenu(){ let h=''; Object.entries(menus).forEach(([k,items])=>{ h+=`<div class="cat-label">${k.toUpperCase()}</div><div class="menu-grid">`; items.forEach(m=>{ const q=fnbCart[m.id]||0; h+=`<div class="mcard${q>0?' sel':''}" onclick="addFnB(${m.id})"><div class="mcard-name">${m.nama_menu}</div><div class="mcard-price">${rp(m.harga)}</div>${q>0?`<div class="mcard-badge">${q}</div>`:''}</div>`; }); h+='</div>'; }); document.getElementById('fnb-menu-list').innerHTML=h; }
function addFnB(id){ fnbCart[id]=(fnbCart[id]||0)+1; renderFnBMenu(); renderFnBCart(); }
function adjFnB(id,d){ fnbCart[id]=Math.max(0,(fnbCart[id]||0)+d); renderFnBMenu(); renderFnBCart(); }
function renderFnBCart(){ const all=Object.values(menus).flat(); let h='',t=0; Object.entries(fnbCart).forEach(([id,q])=>{ if(q<=0) return; const m=all.find(x=>x.id==id); if(!m) return; const s=m.harga*q; t+=s; h+=`<div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid var(--border)"><div style="flex:1;min-width:0"><div style="font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${m.nama_menu}</div><div style="font-size:11px;color:var(--purple2)">${rp(s)}</div></div><div style="display:flex;align-items:center;gap:5px;margin-left:6px"><button class="qbtn" style="width:22px;height:22px;font-size:12px" onclick="adjFnB(${id},-1)">−</button><span style="font-size:12px;min-width:16px;text-align:center">${q}</span><button class="qbtn" style="width:22px;height:22px;font-size:12px" onclick="adjFnB(${id},1)">+</button></div></div>`; }); document.getElementById('fnb-cart-list').innerHTML=h||'<div style="color:var(--t3);font-size:12px;text-align:center;padding:12px 0">Pilih menu...</div>'; document.getElementById('fnb-total').textContent=rp(t); }
async function submitFnB(){ const items=Object.entries(fnbCart).filter(([,q])=>q>0).map(([id,qty])=>({menu_id:parseInt(id),qty})); if(!items.length){ toast('Pilih menu dulu','err'); return; } try{ await api('/kasir/order/rental/'+fnbRentalId,'POST',{items}); closeModal('m-fnb'); toast('Pesanan ditambahkan!'); await refresh(); }catch(e){ toast(e.message,'err'); } }

// Bayar
function openBayar(){
    if(!selectedPs?.rental) return;
    const r=selectedPs.rental;
    const sewa=r.subtotal_sewa||0, fnb=r.subtotal_menu||0;
    byTotal=sewa+fnb;
    byMetode='tunai';
    document.getElementById('by-psname').textContent=selectedPs.nomor_ps;
    document.getElementById('by-sewa').textContent=rp(sewa);
    document.getElementById('by-fnb').textContent=rp(fnb);
    document.getElementById('by-total').textContent=rp(byTotal);
    document.getElementById('by-uang').value='';
    document.getElementById('kembali-box').style.display='none';
    ['po-tunai','po-transfer','po-qris'].forEach((id,i)=>document.getElementById(id).className='pay-opt'+(i===0?' on':''));
    openModal('m-bayar');
}
function setMetode(m){ byMetode=m; const map={tunai:'po-tunai',transfer:'po-transfer',qris:'po-qris'}; Object.values(map).forEach(id=>document.getElementById(id).className='pay-opt'); document.getElementById(map[m]).className='pay-opt on'; }
function calcKembali(){ const u=parseFloat(document.getElementById('by-uang').value)||0; const kb=document.getElementById('kembali-box'); if(u>=byTotal){ kb.style.display='block'; document.getElementById('kembali-amt').textContent=rp(u-byTotal); }else{ kb.style.display='none'; } }
async function submitBayar(){ const u=parseFloat(document.getElementById('by-uang').value)||0; if(u<byTotal){ toast('Uang bayar kurang!','err'); return; } if(!confirm('Selesaikan sesi '+selectedPs.nomor_ps+'?\nTidak bisa dibatalkan.')){ return; } try{ const d=await api('/kasir/rental/'+selectedPs.rental.id+'/selesaikan','POST',{metode_bayar:byMetode,uang_bayar:u,harga_final:byTotal}); closeModal('m-bayar'); toast('Transaksi selesai! Kembalian: '+rp(d.transaksi.kembalian)); clearPanel(); await refresh(); }catch(e){ toast(e.message,'err'); } }

// Cafe Only
function openCafeOnly(){ cafeCart={}; cafeMetode='tunai'; document.getElementById('cafe-uang').value=''; document.getElementById('cafe-kembali-box').style.display='none'; renderCafeMenu(); renderCafeCart(); openModal('m-cafe'); }
function renderCafeMenu(){ let h=''; Object.entries(menus).forEach(([k,items])=>{ h+=`<div class="cat-label">${k.toUpperCase()}</div><div class="menu-grid">`; items.forEach(m=>{ const q=cafeCart[m.id]||0; h+=`<div class="mcard${q>0?' sel':''}" onclick="addCafe(${m.id})"><div class="mcard-name">${m.nama_menu}</div><div class="mcard-price">${rp(m.harga)}</div>${q>0?`<div class="mcard-badge">${q}</div>`:''}</div>`; }); h+='</div>'; }); document.getElementById('cafe-menu-list').innerHTML=h; }
function addCafe(id){ cafeCart[id]=(cafeCart[id]||0)+1; renderCafeMenu(); renderCafeCart(); }
function adjCafe(id,d){ cafeCart[id]=Math.max(0,(cafeCart[id]||0)+d); renderCafeMenu(); renderCafeCart(); }
function renderCafeCart(){ const all=Object.values(menus).flat(); let h=''; cafeTotal=0; Object.entries(cafeCart).forEach(([id,q])=>{ if(q<=0) return; const m=all.find(x=>x.id==id); if(!m) return; const s=m.harga*q; cafeTotal+=s; h+=`<div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid var(--border)"><div style="flex:1;min-width:0"><div style="font-size:12px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${m.nama_menu}</div><div style="font-size:11px;color:var(--purple2)">${rp(s)}</div></div><div style="display:flex;align-items:center;gap:4px;margin-left:4px"><button class="qbtn" style="width:20px;height:20px;font-size:11px" onclick="adjCafe(${id},-1)">−</button><span style="font-size:12px;min-width:14px;text-align:center">${q}</span><button class="qbtn" style="width:20px;height:20px;font-size:11px" onclick="adjCafe(${id},1)">+</button></div></div>`; }); document.getElementById('cafe-cart-list').innerHTML=h||'<div style="color:var(--t3);font-size:12px;text-align:center;padding:10px 0">Pilih menu...</div>'; document.getElementById('cafe-total').textContent=rp(cafeTotal); }
function setCafeMetode(m){ cafeMetode=m; const map={tunai:'cpo-tunai',transfer:'cpo-transfer',qris:'cpo-qris'}; Object.values(map).forEach(id=>document.getElementById(id).className='pay-opt'); document.getElementById(map[m]).className='pay-opt on'; }
function calcCafeKembali(){ const u=parseFloat(document.getElementById('cafe-uang').value)||0; const kb=document.getElementById('cafe-kembali-box'); if(u>=cafeTotal){ kb.style.display='block'; document.getElementById('cafe-kembali-amt').textContent=rp(u-cafeTotal); }else{ kb.style.display='none'; } }
async function submitCafe(){ const items=Object.entries(cafeCart).filter(([,q])=>q>0).map(([id,qty])=>({menu_id:parseInt(id),qty})); if(!items.length){ toast('Pilih menu dulu','err'); return; } const u=parseFloat(document.getElementById('cafe-uang').value)||0; if(u<cafeTotal){ toast('Uang bayar kurang!','err'); return; } try{ await api('/kasir/order/cafe-only','POST',{items,metode_bayar:cafeMetode,uang_bayar:u}); closeModal('m-cafe'); toast('Transaksi Cafe Only berhasil!'); }catch(e){ toast(e.message,'err'); } }

// ══════════════════════════════════
// HELPERS
// ══════════════════════════════════
function fmtMs(ms){ const s=Math.floor(ms/1000); return `${String(Math.floor(s/3600)).padStart(2,'0')}:${String(Math.floor((s%3600)/60)).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`; }
function playAlarm(){ document.getElementById('alarm')?.play().catch(()=>{}); }
async function refresh(){ try{ const d=await api('/kasir/api/ps-units'); psUnits=d; renderGrid(); if(selectedPs){ const f=psUnits.find(p=>p.id===selectedPs.id); f?(selectedPs=f,showPanel(f)):clearPanel(); } }catch(e){} }

// CSS
const st=document.createElement('style');
st.textContent=`
@keyframes pulseborder{0%,100%{box-shadow:0 0 12px rgba(239,69,69,.2)}50%{box-shadow:0 0 24px rgba(239,69,69,.5)}}
@keyframes pulse-red{0%,100%{opacity:1}50%{opacity:.5}}
`;
document.head.appendChild(st);
</script>
@endpush