<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GameZone POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg:        #0d0e1a;
            --bg2:       #13141f;
            --bg3:       #1a1b2e;
            --bg4:       #1f2035;
            --border:    rgba(255,255,255,0.07);
            --border2:   rgba(255,255,255,0.12);
            --t1:        #e8e9f3;
            --t2:        #8b8fa8;
            --t3:        #4a4d6a;
            --purple:    #7c6fe0;
            --purple2:   #9d8ff5;
            --pdim:      rgba(124,111,224,0.15);
            --green:     #3dd68c;
            --gdim:      rgba(61,214,140,0.12);
            --yellow:    #f5a623;
            --ydim:      rgba(245,166,35,0.12);
            --red:       #ef4545;
            --rdim:      rgba(239,69,69,0.12);
            --blue:      #4d9de0;
            --bdim:      rgba(77,157,224,0.12);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; overflow: hidden; background: var(--bg); color: var(--t1); font-family: 'Inter', sans-serif; font-size: 14px; }
        ::-webkit-scrollbar { width: 3px; height: 3px; }
        ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 2px; }

        /* ── Global UI ── */
        .mono { font-family: 'Space Mono', monospace; }

        input, select, textarea {
            background: var(--bg3); border: 1px solid var(--border2);
            color: var(--t1); padding: 9px 13px; border-radius: 8px;
            font-family: 'Inter', sans-serif; font-size: 13px; outline: none; width: 100%;
            transition: border-color .2s;
        }
        input:focus, select:focus { border-color: var(--purple); }
        select option { background: var(--bg3); }
        label { font-size: 11px; color: var(--t2); letter-spacing: .6px; display: block; margin-bottom: 5px; text-transform: uppercase; }
        .fg { margin-bottom: 14px; }
        .fg2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 18px; border-radius: 8px; border: none; font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; transition: all .15s; }
        .btn-p  { background: var(--purple); color: #fff; }
        .btn-p:hover  { background: var(--purple2); }
        .btn-g  { background: var(--green);  color: #051910; }
        .btn-g:hover  { background: #55eaa8; }
        .btn-r  { background: var(--red);    color: #fff; }
        .btn-gh { background: transparent; border: 1px solid var(--border2); color: var(--t2); }
        .btn-gh:hover { background: var(--bg4); color: var(--t1); }
        .btn-full { width: 100%; }
        .btn-lg { padding: 13px 18px; font-size: 15px; font-weight: 600; }
        .btn:disabled { opacity: .45; cursor: not-allowed; }

        /* Modal */
        .modal-wrap {
            position: fixed; inset: 0; background: rgba(0,0,0,.65);
            z-index: 9000; display: flex; align-items: center; justify-content: center;
            backdrop-filter: blur(2px);
        }
        .modal {
            background: var(--bg2); border: 1px solid var(--border2);
            border-radius: 16px; padding: 26px; width: 460px;
            max-height: 92vh; overflow-y: auto; position: relative;
        }
        .modal.lg { width: 700px; }
        .modal-hd { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; }
        .modal-hd h3 { font-size: 16px; font-weight: 600; color: var(--t1); }
        .modal-close { background: none; border: none; color: var(--t2); font-size: 22px; cursor: pointer; line-height: 1; padding: 2px; }
        .modal-close:hover { color: var(--t1); }

        /* Mode selector */
        .mode-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 14px; }
        .mode-card { border: 1px solid var(--border2); border-radius: 10px; padding: 14px 12px; cursor: pointer; text-align: center; transition: all .15s; }
        .mode-card.on { border-color: var(--purple); background: var(--pdim); }
        .mode-card .mc-icon { font-size: 22px; margin-bottom: 6px; }
        .mode-card .mc-title { font-size: 13px; font-weight: 600; color: var(--t1); }
        .mode-card .mc-sub { font-size: 11px; color: var(--t3); margin-top: 3px; }
        .mode-card.on .mc-title { color: var(--purple2); }

        /* Qty control */
        .qty-row { display: flex; align-items: center; gap: 8px; }
        .qbtn { width: 32px; height: 32px; border-radius: 7px; background: var(--bg4); border: 1px solid var(--border2); color: var(--t1); font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .15s; }
        .qbtn:hover { background: var(--pdim); border-color: var(--purple); }
        .qinput { text-align: center; font-family: 'Space Mono', monospace; font-size: 20px; font-weight: 700; }

        /* Metode pembayaran */
        .pay-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; margin-bottom: 14px; }
        .pay-opt { border: 1px solid var(--border2); border-radius: 8px; padding: 10px 6px; text-align: center; cursor: pointer; font-size: 12px; font-weight: 500; color: var(--t2); transition: all .15s; }
        .pay-opt.on { border-color: var(--green); background: var(--gdim); color: var(--green); }

        /* Toast */
        #toasts { position: fixed; top: 14px; right: 14px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; pointer-events: none; }
        .toast { background: var(--bg3); border: 1px solid var(--border2); border-left: 3px solid var(--green); padding: 10px 16px; border-radius: 8px; font-size: 13px; color: var(--t1); animation: tin .25s ease; pointer-events: all; }
        .toast.err { border-left-color: var(--red); }
        @keyframes tin { from { transform: translateX(60px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        /* Menu grid in modal */
        .menu-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; }
        .mcard { background: var(--bg3); border: 1px solid var(--border); border-radius: 10px; padding: 11px; cursor: pointer; transition: all .15s; position: relative; }
        .mcard:hover { border-color: var(--border2); background: var(--bg4); }
        .mcard.sel { border-color: var(--purple); background: var(--pdim); }
        .mcard-name { font-size: 12px; font-weight: 500; color: var(--t1); margin-bottom: 3px; }
        .mcard-price { font-size: 11px; color: var(--purple2); }
        .mcard-badge { position: absolute; top: 6px; right: 6px; background: var(--purple); color: #fff; font-size: 10px; font-weight: 700; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .cat-label { font-size: 10px; color: var(--t3); letter-spacing: 1px; text-transform: uppercase; margin: 12px 0 7px; }

        /* Pay summary */
        .pay-sum { background: var(--bg3); border-radius: 10px; padding: 14px; margin-bottom: 14px; }
        .ps-row { display: flex; justify-content: space-between; font-size: 13px; color: var(--t2); padding: 3px 0; }
        .ps-row.tot { border-top: 1px solid var(--border2); margin-top: 8px; padding-top: 10px; font-size: 17px; font-weight: 700; color: var(--t1); }
        .kembali-box { background: var(--gdim); border: 1px solid var(--green); border-radius: 8px; padding: 12px; text-align: center; margin-top: 10px; }
        .kembali-box .klbl { font-size: 10px; color: var(--green); letter-spacing: .6px; text-transform: uppercase; }
        .kembali-box .kamt { font-size: 24px; font-weight: 700; color: var(--green); font-family: 'Space Mono', monospace; }
    </style>
</head>
<body>
<div style="display:flex;height:100vh;overflow:hidden">

    {{-- SIDEBAR --}}
    <aside style="width:240px;background:var(--bg2);border-right:1px solid var(--border);display:flex;flex-direction:column;flex-shrink:0">
        {{-- Logo --}}
        <div style="padding:22px 20px 16px">
            <div style="font-size:20px;font-weight:700;letter-spacing:-.5px">
                <span style="color:var(--purple)">Game</span><span style="color:var(--t1)">Zone</span>
                <span style="font-size:11px;font-weight:400;color:var(--t3);margin-left:4px">POS</span>
            </div>
            <div style="font-size:11px;color:var(--t3);margin-top:2px">Terminal 01</div>
        </div>
        <div style="height:1px;background:var(--border);margin:0 16px"></div>

        {{-- Nav --}}
        <nav style="padding:12px 10px;flex:1">
            <a href="{{ route('kasir.dashboard') }}"
               style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;margin-bottom:2px;font-size:13px;font-weight:500;
               {{ request()->routeIs('kasir.dashboard') ? 'background:var(--pdim);color:var(--purple2)' : 'color:var(--t2)' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>
                Dashboard
            </a>
            <a href="{{ route('kasir.riwayat') }}"
               style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;margin-bottom:2px;font-size:13px;font-weight:500;
               {{ request()->routeIs('kasir.riwayat') ? 'background:var(--pdim);color:var(--purple2)' : 'color:var(--t2)' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/><polyline points="14 2 14 8 20 8"/></svg>
                Riwayat Transaksi
            </a>
        </nav>

        {{-- New Session btn --}}
        <!-- <div style="padding:12px 10px;border-top:1px solid var(--border)">
            <button onclick="document.dispatchEvent(new CustomEvent('open-new-session'))"
                style="width:100%;padding:11px;border-radius:10px;background:linear-gradient(135deg,var(--purple),#5b4fcf);color:#fff;border:none;font-family:'Inter',sans-serif;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Session
            </button>
        </div> -->

        {{-- User --}}
        <div style="padding:14px 16px;border-top:1px solid var(--border)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                <div style="width:34px;height:34px;border-radius:50%;background:var(--pdim);border:1px solid var(--purple);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--purple2)">
                    {{ strtoupper(substr(auth()->user()->name,0,2)) }}
                </div>
                <div>
                    <div style="font-size:13px;font-weight:500">{{ auth()->user()->name }}</div>
                    <div style="font-size:11px;color:var(--t3)">Kasir</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-gh btn-full" style="font-size:12px;padding:7px">Logout</button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <main style="flex:1;overflow:hidden;display:flex;flex-direction:column">
        {{-- Topbar --}}
        <div style="height:56px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 24px;flex-shrink:0;background:var(--bg2)">
            <div style="font-size:18px;font-weight:700;color:var(--t1)">GameZone Cafe</div>
            <div style="display:flex;align-items:center;gap:14px">
                <div style="font-size:12px;color:var(--t2)" id="clock-display">--:--:--</div>
                <div style="width:34px;height:34px;border-radius:50%;background:var(--pdim);border:1px solid var(--purple);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--purple2)">
                    {{ strtoupper(substr(auth()->user()->name,0,2)) }}
                </div>
            </div>
        </div>
        @yield('content')
    </main>
</div>

<div id="toasts"></div>
<audio id="alarm" loop>
    <source src="data:audio/wav;base64,UklGRl9vT1dBVkVmbXQIBAAAAAABAAEARKwAAESsAAABAAgAZGF0YQ==" type="audio/wav">
</audio>

@stack('modals')
@stack('scripts')

<script>
// Clock
setInterval(()=>{
    const el=document.getElementById('clock-display');
    if(el) el.textContent=new Date().toLocaleTimeString('id-ID');
},1000);

// Toast
function toast(msg,type='ok'){
    const c=document.getElementById('toasts');
    const t=document.createElement('div');
    t.className='toast'+(type==='err'?' err':'');
    t.textContent=msg;
    c.appendChild(t);
    setTimeout(()=>t.remove(),3500);
}

// Format rupiah
function rp(n){return'Rp '+Math.round(n||0).toLocaleString('id-ID');}

// CSRF fetch helper
const CSRF=document.querySelector('meta[name="csrf-token"]').content;
async function api(url,method='GET',body=null){
    const o={method,headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'}};
    if(body) o.body=JSON.stringify(body);
    const r=await fetch(url,o);
    const d=await r.json();
    if(!r.ok) throw new Error(d.error||d.message||'Error');
    return d;
}
</script>
</body>
</html>