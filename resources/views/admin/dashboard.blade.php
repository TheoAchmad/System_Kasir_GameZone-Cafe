<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GameZone — Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{
            --bg:#0d0e1a;--bg2:#13141f;--bg3:#1a1b2e;--bg4:#1f2035;
            --border:rgba(255,255,255,0.07);--border2:rgba(255,255,255,0.12);
            --t1:#e8e9f3;--t2:#8b8fa8;--t3:#4a4d6a;
            --purple:#7c6fe0;--p2:#9d8ff5;--pdim:rgba(124,111,224,0.15);
            --green:#3dd68c;--gdim:rgba(61,214,140,0.12);
            --yellow:#f5a623;--ydim:rgba(245,166,35,0.12);
            --red:#ef4545;--rdim:rgba(239,69,69,0.12);
            --blue:#4d9de0;--bdim:rgba(77,157,224,0.12);
        }
        *{box-sizing:border-box;margin:0;padding:0}
        html,body{height:100%;overflow:hidden;background:var(--bg);color:var(--t1);font-family:'Inter',sans-serif;font-size:14px}
        ::-webkit-scrollbar{width:3px;height:3px}
        ::-webkit-scrollbar-thumb{background:var(--border2);border-radius:2px}

        input,select{background:var(--bg3);border:1px solid var(--border2);color:var(--t1);padding:9px 13px;border-radius:8px;font-family:'Inter',sans-serif;font-size:13px;outline:none;width:100%;transition:border-color .2s}
        input:focus,select:focus{border-color:var(--purple)}
        select option{background:var(--bg3)}
        label{font-size:11px;color:var(--t2);letter-spacing:.6px;display:block;margin-bottom:5px;text-transform:uppercase}
        .fg{margin-bottom:14px}
        .fg2{display:grid;grid-template-columns:1fr 1fr;gap:12px}

        .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:9px 18px;border-radius:8px;border:none;font-family:'Inter',sans-serif;font-size:13px;font-weight:500;cursor:pointer;transition:all .15s}
        .btn-p{background:var(--purple);color:#fff}.btn-p:hover{background:var(--p2)}
        .btn-g{background:var(--green);color:#051910}.btn-g:hover{background:#55eaa8}
        .btn-r{background:var(--red);color:#fff}.btn-r:hover{background:#ff6060}
        .btn-gh{background:transparent;border:1px solid var(--border2);color:var(--t2)}.btn-gh:hover{background:var(--bg4);color:var(--t1)}
        .btn-full{width:100%}.btn-sm{padding:6px 12px;font-size:12px}

        .modal-wrap{position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:9000;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(2px)}
        .modal{background:var(--bg2);border:1px solid var(--border2);border-radius:16px;padding:26px;width:460px;max-height:92vh;overflow-y:auto}
        .modal-hd{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px}
        .modal-hd h3{font-size:16px;font-weight:600}
        .modal-close{background:none;border:none;color:var(--t2);font-size:22px;cursor:pointer;line-height:1}
        .modal-close:hover{color:var(--t1)}

        .badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:5px;font-size:10px;font-weight:600;letter-spacing:.5px}
        .badge-g{background:var(--gdim);color:var(--green)}
        .badge-y{background:var(--ydim);color:var(--yellow)}
        .badge-r{background:var(--rdim);color:var(--red)}
        .badge-p{background:var(--pdim);color:var(--p2)}
        .badge-b{background:var(--bdim);color:var(--blue)}

        .stat-card{background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:18px 20px}
        .stat-val{font-size:26px;font-weight:700;font-family:'Space Mono',monospace;margin:6px 0 4px}
        .stat-lbl{font-size:11px;color:var(--t2);letter-spacing:.5px;text-transform:uppercase}

        table{width:100%;border-collapse:collapse}
        th{font-size:10px;color:var(--t3);letter-spacing:.8px;text-transform:uppercase;padding:8px 14px;text-align:left;border-bottom:1px solid var(--border)}
        td{padding:10px 14px;border-bottom:1px solid var(--border);font-size:13px;vertical-align:middle}
        tr:last-child td{border-bottom:none}
        tr:hover td{background:rgba(255,255,255,.02)}

        #toasts{position:fixed;top:14px;right:14px;z-index:9999;display:flex;flex-direction:column;gap:8px;pointer-events:none}
        .toast{background:var(--bg3);border:1px solid var(--border2);border-left:3px solid var(--green);padding:10px 16px;border-radius:8px;font-size:13px;color:var(--t1);animation:tin .25s ease;pointer-events:all}
        .toast.err{border-left-color:var(--red)}
        @keyframes tin{from{transform:translateX(60px);opacity:0}to{transform:translateX(0);opacity:1}}

        .nav-pill{padding:9px 14px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;color:var(--t2);border:none;background:transparent;font-family:'Inter',sans-serif;transition:all .15s;display:flex;align-items:center;gap:8px;width:100%;text-align:left}
        .nav-pill:hover{color:var(--t1);background:var(--bg4)}
        .nav-pill.active{background:var(--pdim);color:var(--p2)}

        .section{display:none}.section.active{display:block}
        .cabang-btn{padding:7px 16px;border-radius:7px;border:1px solid var(--border2);background:var(--bg3);color:var(--t2);font-family:'Inter',sans-serif;font-size:12px;font-weight:500;cursor:pointer;transition:all .15s}
        .cabang-btn.active{border-color:var(--purple);background:var(--pdim);color:var(--p2)}

        /* Timer cell — monospace biar tidak lompat-lompat */
        .timer-cell{font-family:'Space Mono',monospace;font-size:13px;font-variant-numeric:tabular-nums;min-width:80px;display:inline-block}
    </style>
</head>
<body>
<div style="display:flex;height:100vh;overflow:hidden">

    {{-- SIDEBAR --}}
    <aside style="width:220px;background:var(--bg2);border-right:1px solid var(--border);display:flex;flex-direction:column;flex-shrink:0">
        <div style="padding:20px 18px 14px">
            <div style="font-size:19px;font-weight:700"><span style="color:var(--purple)">Game</span>Zone</div>
            <div style="font-size:10px;color:var(--t3);margin-top:2px;letter-spacing:.5px">ADMIN PANEL</div>
        </div>
        <div style="height:1px;background:var(--border);margin:0 14px"></div>
        <nav style="padding:10px 8px;flex:1;display:flex;flex-direction:column;gap:2px">
            <button class="nav-pill active" onclick="showSection('overview')" id="nav-overview">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Overview
            </button>
            <button class="nav-pill" onclick="showSection('ps')" id="nav-ps">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="8" cy="12" r="1.5" fill="currentColor" stroke="none"/><line x1="14" y1="9" x2="14" y2="15"/><line x1="11" y1="12" x2="17" y2="12"/></svg>
                Kelola PS
            </button>
            <button class="nav-pill" onclick="showSection('menu')" id="nav-menu">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/></svg>
                Kelola Menu
            </button>
            <button class="nav-pill" onclick="showSection('kasir')" id="nav-kasir">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Kasir & Cabang
            </button>
            <button class="nav-pill" onclick="showSection('laporan')" id="nav-laporan">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/><polyline points="14 2 14 8 20 8"/></svg>
                Laporan
            </button>
        </nav>
        <div style="padding:14px 12px;border-top:1px solid var(--border)">
            <div style="font-size:12px;color:var(--t2);margin-bottom:8px;font-weight:500">{{ auth()->user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-gh btn-full" style="font-size:12px;padding:7px">Logout</button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <div style="flex:1;display:flex;flex-direction:column;overflow:hidden">
        <div style="height:54px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 24px;flex-shrink:0;background:var(--bg2)">
            <div style="font-size:15px;font-weight:600" id="page-title">Overview</div>
            <div style="display:flex;align-items:center;gap:10px">
                <span style="font-size:11px;color:var(--t3)">Cabang:</span>
                @foreach($cabangList as $c)
                <button class="cabang-btn {{ $loop->first ? 'active' : '' }}"
                    onclick="setCabang({{ $c->id }}, this)">{{ $c->nama_cabang }}</button>
                @endforeach
                <div style="width:1px;height:20px;background:var(--border);margin:0 4px"></div>
                <div style="font-size:12px;color:var(--t2);font-family:'Space Mono',monospace" id="admin-clock">--:--:--</div>
            </div>
        </div>

        <div style="flex:1;overflow-y:auto;padding:22px 24px" id="main-content">

            {{-- OVERVIEW --}}
            <div class="section active" id="sec-overview">
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
                    <div class="stat-card">
                        <div class="stat-lbl">Total PS</div>
                        <div class="stat-val" style="color:var(--blue)" id="st-totalps">—</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-lbl">PS Aktif</div>
                        <div class="stat-val" style="color:var(--yellow)" id="st-aktif">—</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-lbl">Pendapatan Hari Ini</div>
                        <div class="stat-val" style="color:var(--green);font-size:17px" id="st-pendapatan">Rp —</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-lbl">Transaksi Hari Ini</div>
                        <div class="stat-val" style="color:var(--purple)" id="st-trx">—</div>
                    </div>
                </div>
                <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;overflow:hidden">
                    <div style="padding:13px 18px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
                        <span style="font-size:13px;font-weight:600">Status PS — Realtime</span>
                        <span style="font-size:11px;color:var(--t3)" id="last-sync">—</span>
                    </div>
                    <div style="overflow-x:auto">
                        <table>
                            <thead>
                                <tr>
                                    <th>Unit</th><th>Tipe</th><th>Status</th>
                                    <th>Pelanggan</th><th>Mode</th>
                                    <th>Timer</th><th>Subtotal Sewa</th>
                                </tr>
                            </thead>
                            <tbody id="ps-status-table"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- KELOLA PS --}}
            <div class="section" id="sec-ps">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                    <span style="font-size:14px;font-weight:600">Unit PlayStation</span>
                    <button class="btn btn-p btn-sm" onclick="openModalAddPs()">+ Tambah PS</button>
                </div>
                <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;overflow:hidden">
                    <table><thead><tr><th>Nomor PS</th><th>Tipe</th><th>Harga/Jam</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody id="ps-table"></tbody></table>
                </div>
            </div>

            {{-- KELOLA MENU --}}
            <div class="section" id="sec-menu">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                    <span style="font-size:14px;font-weight:600">Menu F&B</span>
                    <button class="btn btn-p btn-sm" onclick="openModalAddMenu()">+ Tambah Menu</button>
                </div>
                <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;overflow:hidden">
                    <table><thead><tr><th>Nama Menu</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody id="menu-table"></tbody></table>
                </div>
            </div>

            {{-- KASIR & CABANG --}}
            <div class="section" id="sec-kasir">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                    <span style="font-size:14px;font-weight:600">Kasir & Cabang</span>
                    <button class="btn btn-p btn-sm" onclick="openModalAddKasir()">+ Tambah Kasir</button>
                </div>
                <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;overflow:hidden">
                    <table><thead><tr><th>Nama</th><th>Email</th><th>Cabang</th><th>Role</th><th>Aksi</th></tr></thead>
                    <tbody id="kasir-table"></tbody></table>
                </div>
            </div>

            {{-- LAPORAN --}}
            <div class="section" id="sec-laporan">
                <div style="display:flex;gap:12px;align-items:flex-end;margin-bottom:18px">
                    <div class="fg" style="flex:1;margin:0"><label>Dari</label><input type="date" id="lap-dari" value="{{ now()->format('Y-m-d') }}"></div>
                    <div class="fg" style="flex:1;margin:0"><label>Sampai</label><input type="date" id="lap-sampai" value="{{ now()->format('Y-m-d') }}"></div>
                    <button class="btn btn-p" onclick="loadLaporan()">Tampilkan</button>
                </div>
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px">
                    <div class="stat-card"><div class="stat-lbl">Total</div><div class="stat-val" style="color:var(--green);font-size:18px" id="lap-total">Rp 0</div></div>
                    <div class="stat-card"><div class="stat-lbl">Rental PS</div><div class="stat-val" style="color:var(--blue);font-size:18px" id="lap-rental">Rp 0</div></div>
                    <div class="stat-card"><div class="stat-lbl">Cafe F&B</div><div class="stat-val" style="color:var(--purple);font-size:18px" id="lap-cafe">Rp 0</div></div>
                    <div class="stat-card"><div class="stat-lbl">Transaksi</div><div class="stat-val" id="lap-count">0</div></div>
                </div>
                <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;overflow:hidden">
                    <table><thead><tr><th>Waktu</th><th>Tipe</th><th>Detail</th><th>Metode</th><th>Total</th></tr></thead>
                    <tbody id="lap-table"></tbody></table>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL PS --}}
<div id="m-ps" class="modal-wrap" style="display:none">
<div class="modal" onclick="event.stopPropagation()">
    <div class="modal-hd"><h3 id="mps-title">Tambah PS</h3><button class="modal-close" onclick="closeModal('m-ps')">&#215;</button></div>
    <input type="hidden" id="mps-id">
    <div class="fg"><label>Nomor PS</label><input type="text" id="mps-nomor" placeholder="PS 01"></div>
    <div class="fg2">
        <div class="fg" style="margin:0"><label>Tipe</label><select id="mps-tipe"><option value="PS5">PS5</option><option value="PS4">PS4</option></select></div>
        <div class="fg" style="margin:0"><label>Status</label><select id="mps-status"><option value="kosong">Kosong</option><option value="maintenance">Maintenance</option></select></div>
    </div>
    <div class="fg"><label>Harga per Jam (Rp)</label><input type="number" id="mps-harga" placeholder="10000"></div>
    <div style="display:flex;gap:10px;margin-top:4px">
        <button class="btn btn-gh" style="flex:1" onclick="closeModal('m-ps')">Batal</button>
        <button class="btn btn-p" style="flex:1" onclick="submitPs()">Simpan</button>
    </div>
</div>
</div>

{{-- MODAL MENU --}}
<div id="m-menu" class="modal-wrap" style="display:none">
<div class="modal" onclick="event.stopPropagation()">
    <div class="modal-hd"><h3 id="mmenu-title">Tambah Menu</h3><button class="modal-close" onclick="closeModal('m-menu')">&#215;</button></div>
    <input type="hidden" id="mmenu-id">
    <div class="fg"><label>Nama Menu</label><input type="text" id="mmenu-nama" placeholder="Nama menu..."></div>
    <div class="fg2">
        <div class="fg" style="margin:0"><label>Kategori</label><select id="mmenu-kat"><option value="makanan">Makanan</option><option value="minuman">Minuman</option><option value="snack">Snack</option></select></div>
        <div class="fg" style="margin:0"><label>Harga (Rp)</label><input type="number" id="mmenu-harga" placeholder="15000"></div>
    </div>
    <div class="fg2">
        <div class="fg" style="margin:0"><label>Stok</label><input type="number" id="mmenu-stok" placeholder="50"></div>
        <div class="fg" style="margin:0"><label>Status</label><select id="mmenu-aktif"><option value="1">Aktif</option><option value="0">Non-Aktif</option></select></div>
    </div>
    <div style="display:flex;gap:10px;margin-top:4px">
        <button class="btn btn-gh" style="flex:1" onclick="closeModal('m-menu')">Batal</button>
        <button class="btn btn-p" style="flex:1" onclick="submitMenu()">Simpan</button>
    </div>
</div>
</div>

{{-- MODAL KASIR --}}
<div id="m-kasir" class="modal-wrap" style="display:none">
<div class="modal" onclick="event.stopPropagation()">
    <div class="modal-hd"><h3>Tambah Kasir</h3><button class="modal-close" onclick="closeModal('m-kasir')">&#215;</button></div>
    <div class="fg"><label>Nama</label><input type="text" id="mk-nama" placeholder="Nama kasir..."></div>
    <div class="fg"><label>Email</label><input type="email" id="mk-email" placeholder="kasir@gamezone.com"></div>
    <div class="fg"><label>Password</label><input type="password" id="mk-pass" placeholder="Minimal 8 karakter"></div>
    <div class="fg"><label>Cabang</label>
        <select id="mk-cabang">
            @foreach($cabangList as $c)
            <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
            @endforeach
        </select>
    </div>
    <div style="display:flex;gap:10px;margin-top:4px">
        <button class="btn btn-gh" style="flex:1" onclick="closeModal('m-kasir')">Batal</button>
        <button class="btn btn-p" style="flex:1" onclick="submitKasir()">Tambah</button>
    </div>
</div>
</div>

<div id="toasts"></div>

{{-- ══ AUDIO ALARM ══ --}}
{{-- Letakkan file alarm.mp3 di public/sounds/alarm.mp3 --}}
<audio id="alarm-audio" preload="auto">
    <source src="{{ asset('sounds/alarm.mp3') }}" type="audio/mpeg">
</audio>

<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const cabangData = @json($cabangList);
let activeCabang = cabangData[0]?.id || null;
let activeSec    = 'overview';

// ── State untuk timer realtime di admin ──
// Menyimpan data PS yang sedang aktif dengan timestamp
let adminPsData  = [];   // array of { id, nomor_ps, tipe_ps, status, harga_per_jam, pelanggan, mode_billing, jam_mulai_ts, jam_selesai_ts }
let adminAlarmPlayed = {};

// ══════════════════════════════════════════
// UTILS
// ══════════════════════════════════════════
async function api(url, method = 'GET', body = null) {
    const o = { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } };
    if (body) o.body = JSON.stringify(body);
    const r = await fetch(url, o);
    const d = await r.json();
    if (!r.ok) throw new Error(d.error || d.message || 'Error');
    return d;
}

function toast(msg, type = 'ok') {
    const c = document.getElementById('toasts');
    const t = document.createElement('div');
    t.className = 'toast' + (type === 'err' ? ' err' : '');
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

function rp(n) { return 'Rp ' + Math.round(n || 0).toLocaleString('id-ID'); }
function openModal(id) { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

function fmtMs(ms) {
    if (ms <= 0) return '00:00:00';
    const s = Math.floor(ms / 1000);
    return String(Math.floor(s / 3600)).padStart(2,'0') + ':' +
           String(Math.floor((s % 3600) / 60)).padStart(2,'0') + ':' +
           String(s % 60).padStart(2,'0');
}

function playAlarm() {
    const a = document.getElementById('alarm-audio');
    if (a) { a.currentTime = 0; a.play().catch(() => {}); }
}

// ══════════════════════════════════════════
// CLOCK
// ══════════════════════════════════════════
setInterval(() => {
    const el = document.getElementById('admin-clock');
    if (el) el.textContent = new Date().toLocaleTimeString('id-ID');
}, 1000);

// ══════════════════════════════════════════
// CABANG & SECTION
// ══════════════════════════════════════════
function setCabang(id, btn) {
    activeCabang = id;
    document.querySelectorAll('.cabang-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    refreshSection();
}

const sections = ['overview','ps','menu','kasir','laporan'];
function showSection(sec) {
    activeSec = sec;
    sections.forEach(s => {
        document.getElementById('sec-' + s).className = 'section' + (s === sec ? ' active' : '');
        document.getElementById('nav-' + s).className = 'nav-pill' + (s === sec ? ' active' : '');
    });
    const titles = { overview:'Overview', ps:'Kelola PS', menu:'Kelola Menu', kasir:'Kasir & Cabang', laporan:'Laporan' };
    document.getElementById('page-title').textContent = titles[sec];
    refreshSection();
}

function refreshSection() {
    if (activeSec === 'overview') loadOverview();
    else if (activeSec === 'ps') loadPs();
    else if (activeSec === 'menu') loadMenu();
    else if (activeSec === 'kasir') loadKasir();
    else if (activeSec === 'laporan') loadLaporan();
}

// ══════════════════════════════════════════
// OVERVIEW — Load data + jalankan timer realtime
// ══════════════════════════════════════════
async function loadOverview() {
    try {
        const d = await api(`/admin/api/overview?cabang=${activeCabang}`);

        document.getElementById('st-totalps').textContent    = d.total_ps;
        document.getElementById('st-aktif').textContent      = d.ps_aktif;
        document.getElementById('st-pendapatan').textContent = rp(d.pendapatan_hari);
        document.getElementById('st-trx').textContent        = d.total_trx;
        document.getElementById('last-sync').textContent     = 'Sync: ' + new Date().toLocaleTimeString('id-ID');

        // Simpan data PS dengan timestamp untuk timer realtime
        adminPsData = d.ps_realtime || [];

        // Render tabel awal
        renderPsStatusTable();

    } catch(e) { toast(e.message, 'err'); }
}

// Render tabel PS — HANYA buat baris baru, timer diupdate via JS setiap detik
function renderPsStatusTable() {
    const tb = document.getElementById('ps-status-table');
    tb.innerHTML = adminPsData.map(ps => {
        const badgeTipe  = ps.tipe_ps === 'PS5' ? 'badge-b' : 'badge-p';
        const badgeStatus = ps.status === 'kosong' ? 'badge-g' : ps.status === 'dipakai' ? 'badge-y' : 'badge-r';
        const isAktif = ps.status === 'dipakai';

        return `<tr id="psrow-${ps.id}">
            <td><span style="font-weight:600">${ps.nomor_ps}</span></td>
            <td><span class="badge ${badgeTipe}">${ps.tipe_ps}</span></td>
            <td id="psstatus-${ps.id}"><span class="badge ${badgeStatus}">${ps.status.toUpperCase()}</span></td>
            <td style="color:var(--t2)" id="psplayer-${ps.id}">${ps.pelanggan || '—'}</td>
            <td style="font-size:12px;color:var(--t3)" id="psmode-${ps.id}">${isAktif ? (ps.mode_billing === 'down' ? '⏬ Countdown' : '⏫ Open') : '—'}</td>
            <td><span class="timer-cell" id="pstimer-${ps.id}" style="color:${isAktif ? 'var(--yellow)' : 'var(--t3)'}">${isAktif ? '--:--:--' : '—'}</span></td>
            <td id="pssub-${ps.id}" style="color:var(--green)">${isAktif ? '—' : '—'}</td>
        </tr>`;
    }).join('');
}

// ══════════════════════════════════════════
// TIMER REALTIME DI ADMIN — berjalan setiap 1 detik
// Sama persis logika dengan kasir dashboard
// ══════════════════════════════════════════
function startAdminTimerLoop() {
    setInterval(() => {
        if (activeSec !== 'overview') return;
        const now = Date.now();

        adminPsData.forEach(ps => {
            if (ps.status !== 'dipakai' || !ps.jam_mulai_ts) return;

            const timerEl = document.getElementById('pstimer-' + ps.id);
            const subEl   = document.getElementById('pssub-'   + ps.id);
            const statEl  = document.getElementById('psstatus-'+ ps.id);
            if (!timerEl) return;

            const hpj = parseFloat(ps.harga_per_jam) || 0;
            let timerText, subtotalSewa, isExpired = false;

            if (ps.mode_billing === 'down') {
                const diff = ps.jam_selesai_ts - now;

                if (diff <= 0) {
                    // ✅ BERHENTI — tidak tambah harga lagi
                    isExpired  = true;
                    timerText  = '00:00:00';
                    const menitFixed = (ps.jam_selesai_ts - ps.jam_mulai_ts) / 60000;
                    subtotalSewa = (menitFixed / 60) * hpj;

                    // Alarm sekali
                    if (!adminAlarmPlayed[ps.id]) {
                        adminAlarmPlayed[ps.id] = true;
                        playAlarm();
                    }

                    // Update badge status → TIME UP
                    if (statEl && !statEl.dataset.expired) {
                        statEl.innerHTML = '<span class="badge badge-r">TIME UP</span>';
                        statEl.dataset.expired = '1';
                    }
                } else {
                    timerText = fmtMs(diff);
                    const menitBerjalan = (now - ps.jam_mulai_ts) / 60000;
                    subtotalSewa = (menitBerjalan / 60) * hpj;
                }
            } else {
                // Open billing — terus berjalan
                const elapsed = now - ps.jam_mulai_ts;
                timerText    = fmtMs(elapsed);
                subtotalSewa = (elapsed / 3600000) * hpj;
            }

            timerEl.textContent = timerText;
            timerEl.style.color = isExpired ? 'var(--red)' : 'var(--yellow)';
            if (subEl) subEl.textContent = rp(subtotalSewa);
        });
    }, 1000);
}

// Polling 30 detik — refresh data dari server (timestamp baru)
function startAdminPolling() {
    setInterval(async () => {
        if (activeSec !== 'overview') return;
        try {
            const d = await api(`/admin/api/overview?cabang=${activeCabang}`);
            // Update stat cards
            document.getElementById('st-totalps').textContent    = d.total_ps;
            document.getElementById('st-aktif').textContent      = d.ps_aktif;
            document.getElementById('st-pendapatan').textContent = rp(d.pendapatan_hari);
            document.getElementById('st-trx').textContent        = d.total_trx;
            document.getElementById('last-sync').textContent     = 'Sync: ' + new Date().toLocaleTimeString('id-ID');

            // Update adminPsData dengan data baru tapi jaga state alarm
            const newData = d.ps_realtime || [];
            newData.forEach(np => {
                if (adminAlarmPlayed[np.id]) np._alarmDone = true;
            });
            adminPsData = newData;

            // Re-render hanya jika ada perubahan status (PS baru dipakai/selesai)
            renderPsStatusTable();
        } catch(e) {}
    }, 30000);
}

// ══════════════════════════════════════════
// KELOLA PS
// ══════════════════════════════════════════
async function loadPs() {
    try {
        const d = await api(`/admin/api/ps?cabang=${activeCabang}`);
        document.getElementById('ps-table').innerHTML = d.map(ps => `<tr>
            <td><span style="font-weight:600">${ps.nomor_ps}</span></td>
            <td><span class="badge ${ps.tipe_ps === 'PS5' ? 'badge-b' : 'badge-p'}">${ps.tipe_ps}</span></td>
            <td style="color:var(--green)">${rp(ps.harga_per_jam)}/jam</td>
            <td><span class="badge ${ps.status === 'kosong' ? 'badge-g' : ps.status === 'dipakai' ? 'badge-y' : 'badge-r'}">${ps.status}</span></td>
            <td style="display:flex;gap:6px;flex-wrap:wrap">
                <button class="btn btn-gh btn-sm" onclick='editPs(${JSON.stringify(ps)})'>Edit</button>
                <button class="btn btn-r btn-sm" onclick="deletePs(${ps.id})">Hapus</button>
            </td>
        </tr>`).join('');
    } catch(e) { toast(e.message, 'err'); }
}

function openModalAddPs() {
    document.getElementById('mps-id').value     = '';
    document.getElementById('mps-title').textContent = 'Tambah PS';
    document.getElementById('mps-nomor').value  = '';
    document.getElementById('mps-tipe').value   = 'PS5';
    document.getElementById('mps-harga').value  = 10000;
    document.getElementById('mps-status').value = 'kosong';
    openModal('m-ps');
}

function editPs(ps) {
    document.getElementById('mps-id').value     = ps.id;
    document.getElementById('mps-title').textContent = 'Edit PS — ' + ps.nomor_ps;
    document.getElementById('mps-nomor').value  = ps.nomor_ps;
    document.getElementById('mps-tipe').value   = ps.tipe_ps;
    document.getElementById('mps-harga').value  = ps.harga_per_jam;
    document.getElementById('mps-status').value = ps.status;
    openModal('m-ps');
}

async function submitPs() {
    const id     = document.getElementById('mps-id').value;
    const body   = {
        nomor_ps:      document.getElementById('mps-nomor').value,
        tipe_ps:       document.getElementById('mps-tipe').value,
        harga_per_jam: parseFloat(document.getElementById('mps-harga').value),
        status:        document.getElementById('mps-status').value,
        cabang_id:     activeCabang,
    };

    try {
        if (id) {
            // ✅ FIX: gunakan PATCH method — route pakai method spoofing
            await api(`/admin/ps/${id}`, 'POST', { ...body, _method: 'PATCH' });
        } else {
            await api('/admin/ps', 'POST', body);
        }
        closeModal('m-ps');
        toast('PS berhasil disimpan!');
        loadPs();
    } catch(e) { toast(e.message, 'err'); }
}

async function deletePs(id) {
    if (!confirm('Hapus PS ini?')) return;
    try {
        await api(`/admin/ps/${id}`, 'DELETE');
        toast('PS dihapus!');
        loadPs();
    } catch(e) { toast(e.message, 'err'); }
}

// ══════════════════════════════════════════
// KELOLA MENU
// ══════════════════════════════════════════
async function loadMenu() {
    try {
        const d = await api(`/admin/api/menu?cabang=${activeCabang}`);
        document.getElementById('menu-table').innerHTML = d.map(m => `<tr>
            <td style="font-weight:500">${m.nama_menu}</td>
            <td><span class="badge ${m.kategori === 'makanan' ? 'badge-y' : m.kategori === 'minuman' ? 'badge-b' : 'badge-p'}">${m.kategori}</span></td>
            <td style="color:var(--green)">${rp(m.harga)}</td>
            <td><span style="color:${m.stok < 10 ? 'var(--red)' : 'var(--t2)'}">${m.stok}</span></td>
            <td><span class="badge ${m.is_aktif ? 'badge-g' : 'badge-r'}">${m.is_aktif ? 'AKTIF' : 'OFF'}</span></td>
            <td style="display:flex;gap:6px;flex-wrap:wrap">
                <button class="btn btn-gh btn-sm" onclick='editMenu(${JSON.stringify(m)})'>Edit</button>
                <button class="btn btn-r btn-sm" onclick="deleteMenu(${m.id})">Hapus</button>
            </td>
        </tr>`).join('');
    } catch(e) { toast(e.message, 'err'); }
}

function openModalAddMenu() {
    document.getElementById('mmenu-id').value    = '';
    document.getElementById('mmenu-title').textContent = 'Tambah Menu';
    document.getElementById('mmenu-nama').value  = '';
    document.getElementById('mmenu-kat').value   = 'makanan';
    document.getElementById('mmenu-harga').value = '';
    document.getElementById('mmenu-stok').value  = '';
    document.getElementById('mmenu-aktif').value = '1';
    openModal('m-menu');
}

function editMenu(m) {
    document.getElementById('mmenu-id').value    = m.id;
    document.getElementById('mmenu-title').textContent = 'Edit Menu — ' + m.nama_menu;
    document.getElementById('mmenu-nama').value  = m.nama_menu;
    document.getElementById('mmenu-kat').value   = m.kategori;
    document.getElementById('mmenu-harga').value = m.harga;
    document.getElementById('mmenu-stok').value  = m.stok;
    document.getElementById('mmenu-aktif').value = m.is_aktif ? '1' : '0';
    openModal('m-menu');
}

async function submitMenu() {
    const id   = document.getElementById('mmenu-id').value;
    const body = {
        nama_menu:  document.getElementById('mmenu-nama').value,
        kategori:   document.getElementById('mmenu-kat').value,
        harga:      parseFloat(document.getElementById('mmenu-harga').value),
        stok:       parseInt(document.getElementById('mmenu-stok').value),
        is_aktif:   document.getElementById('mmenu-aktif').value === '1',
        cabang_id:  activeCabang,
    };

    try {
        if (id) {
            // ✅ FIX: gunakan PATCH method spoofing
            await api(`/admin/menu/${id}`, 'POST', { ...body, _method: 'PATCH' });
        } else {
            await api('/admin/menu', 'POST', body);
        }
        closeModal('m-menu');
        toast('Menu berhasil disimpan!');
        loadMenu();
    } catch(e) { toast(e.message, 'err'); }
}

async function deleteMenu(id) {
    if (!confirm('Hapus menu ini?')) return;
    try {
        await api(`/admin/menu/${id}`, 'DELETE');
        toast('Menu dihapus!');
        loadMenu();
    } catch(e) { toast(e.message, 'err'); }
}

// ══════════════════════════════════════════
// KASIR
// ══════════════════════════════════════════
async function loadKasir() {
    try {
        const d = await api('/admin/api/kasir');
        document.getElementById('kasir-table').innerHTML = d.map(u => `<tr>
            <td style="font-weight:500">${u.name}</td>
            <td style="color:var(--t2);font-size:12px">${u.email}</td>
            <td><span class="badge badge-p">${u.cabang?.nama_cabang || '—'}</span></td>
            <td><span class="badge ${u.role === 'admin' ? 'badge-y' : 'badge-g'}">${u.role.toUpperCase()}</span></td>
            <td><button class="btn btn-r btn-sm" onclick="deleteKasir(${u.id})">Hapus</button></td>
        </tr>`).join('');
    } catch(e) { toast(e.message, 'err'); }
}

function openModalAddKasir() {
    document.getElementById('mk-nama').value  = '';
    document.getElementById('mk-email').value = '';
    document.getElementById('mk-pass').value  = '';
    openModal('m-kasir');
}

async function submitKasir() {
    const body = {
        name:      document.getElementById('mk-nama').value,
        email:     document.getElementById('mk-email').value,
        password:  document.getElementById('mk-pass').value,
        cabang_id: document.getElementById('mk-cabang').value,
        role:      'kasir',
    };
    try {
        await api('/admin/kasir', 'POST', body);
        closeModal('m-kasir');
        toast('Kasir berhasil ditambahkan!');
        loadKasir();
    } catch(e) { toast(e.message, 'err'); }
}

async function deleteKasir(id) {
    if (!confirm('Hapus kasir ini?')) return;
    try {
        await api(`/admin/kasir/${id}`, 'DELETE');
        toast('Kasir dihapus!');
        loadKasir();
    } catch(e) { toast(e.message, 'err'); }
}

// ══════════════════════════════════════════
// LAPORAN
// ══════════════════════════════════════════
async function loadLaporan() {
    const dari   = document.getElementById('lap-dari').value;
    const sampai = document.getElementById('lap-sampai').value;
    try {
        const d = await api(`/admin/api/laporan?cabang=${activeCabang}&dari=${dari}&sampai=${sampai}`);
        document.getElementById('lap-total').textContent  = rp(d.total);
        document.getElementById('lap-rental').textContent = rp(d.rental);
        document.getElementById('lap-cafe').textContent   = rp(d.cafe);
        document.getElementById('lap-count').textContent  = d.count;
        document.getElementById('lap-table').innerHTML    = d.transaksi.map(t => `<tr>
            <td style="font-size:12px;color:var(--t2)">${t.tanggal}</td>
            <td><span class="badge ${t.tipe === 'rental' ? 'badge-p' : 'badge-g'}">${t.tipe === 'rental' ? 'Rental' : 'Cafe'}</span></td>
            <td style="font-size:12px">${t.detail}</td>
            <td style="font-size:12px;color:var(--t2)">${t.metode}</td>
            <td style="color:var(--green);font-weight:600">${rp(t.total)}</td>
        </tr>`).join('');
    } catch(e) { toast(e.message, 'err'); }
}

// ══════════════════════════════════════════
// INIT
// ══════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    loadOverview();
    startAdminTimerLoop();  // ← timer realtime setiap 1 detik
    startAdminPolling();    // ← sync data dari server setiap 30 detik
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.modal-wrap').forEach(m => m.style.display = 'none');
});
</script>
</body>
</html>