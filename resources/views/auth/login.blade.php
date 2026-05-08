<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameZone — Operator Authorization</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{
            min-height:100vh;
            background:#0d0e1a;
            display:flex;align-items:center;justify-content:center;
            font-family:'Inter',sans-serif;
            position:relative;overflow:hidden;
        }

        /* Subtle grid background */
        body::before{
            content:'';position:fixed;inset:0;
            background-image:
                linear-gradient(rgba(124,111,224,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124,111,224,0.04) 1px, transparent 1px);
            background-size:40px 40px;pointer-events:none;
        }

        /* Glow blobs */
        .blob{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;opacity:.18;}
        .blob-1{width:400px;height:400px;background:#7c6fe0;top:-100px;left:-100px;}
        .blob-2{width:300px;height:300px;background:#4d4b9e;bottom:-80px;right:-60px;}

        .card{
            position:relative;
            width:420px;
            background:#13141f;
            border:1px solid rgba(124,111,224,0.3);
            border-radius:16px;
            overflow:hidden;
            padding:36px 32px 28px;
        }

        /* Top purple accent bar */
        .card::before{
            content:'';position:absolute;top:0;left:0;right:0;height:3px;
            background:linear-gradient(90deg,transparent,#7c6fe0,#9d8ff5,#7c6fe0,transparent);
        }

        /* Icon box */
        .icon-box{
            width:52px;height:52px;
            background:rgba(124,111,224,0.15);
            border:1px solid rgba(124,111,224,0.3);
            border-radius:12px;
            display:flex;align-items:center;justify-content:center;
            margin:0 auto 20px;
        }
        .icon-box svg{opacity:.8;}

        .brand{text-align:center;margin-bottom:24px;}
        .brand h1{
            font-family:'Space Mono',monospace;
            font-size:22px;font-weight:700;
            letter-spacing:4px;
            color:#e8e9f3;
            margin-bottom:6px;
        }
        .brand p{
            font-size:10px;letter-spacing:2.5px;
            color:rgba(139,143,168,0.7);text-transform:uppercase;
        }

        /* Role tabs */
        .role-tabs{
            display:grid;grid-template-columns:1fr 1fr;
            background:#0d0e1a;border:1px solid rgba(255,255,255,0.07);
            border-radius:10px;padding:4px;gap:4px;margin-bottom:20px;
        }
        .role-tab{
            padding:9px;border-radius:7px;text-align:center;
            font-size:12px;font-weight:500;letter-spacing:.5px;
            cursor:pointer;transition:all .2s;color:#4a4d6a;
            display:flex;align-items:center;justify-content:center;gap:7px;
            border:none;background:transparent;font-family:'Inter',sans-serif;
        }
        .role-tab.active{
            background:#1a1b2e;
            color:#e8e9f3;
            box-shadow:0 0 0 1px rgba(124,111,224,0.4);
        }
        .role-tab svg{opacity:.7;}
        .role-tab.active svg{opacity:1;}

        /* Inputs */
        .input-group{margin-bottom:12px;position:relative;}
        .input-icon{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            color:#4a4d6a;
        }
        .input-group input{
            width:100%;background:#0d0e1a;
            border:1px solid rgba(255,255,255,0.08);
            border-radius:8px;padding:12px 14px 12px 42px;
            color:#e8e9f3;font-family:'Inter',sans-serif;font-size:13px;
            outline:none;transition:border-color .2s;
            letter-spacing:.3px;
        }
        .input-group input::placeholder{color:#3a3d52;}
        .input-group input:focus{border-color:rgba(124,111,224,0.6);}

        /* Submit btn */
        .btn-submit{
            width:100%;margin-top:8px;
            background:linear-gradient(135deg,#7c6fe0,#5b4fcf);
            border:none;border-radius:8px;
            padding:13px;
            color:#fff;font-family:'Inter',sans-serif;
            font-size:13px;font-weight:600;letter-spacing:1.5px;
            text-transform:uppercase;cursor:pointer;
            transition:all .2s;
            display:flex;align-items:center;justify-content:center;gap:8px;
        }
        .btn-submit:hover{background:linear-gradient(135deg,#9080f0,#7c6fe0);transform:translateY(-1px);}
        .btn-submit:active{transform:translateY(0);}

        /* Footer status */
        .card-footer{
            display:flex;justify-content:space-between;align-items:center;
            margin-top:24px;padding-top:16px;
            border-top:1px solid rgba(255,255,255,0.05);
        }
        .status-dot{
            display:flex;align-items:center;gap:6px;
            font-size:10px;letter-spacing:1px;color:#4a4d6a;
        }
        .status-dot::before{
            content:'';width:6px;height:6px;border-radius:50%;background:#3dd68c;
            box-shadow:0 0 6px #3dd68c;
        }
        .version{font-size:10px;letter-spacing:1px;color:#2a2d3a;font-family:'Space Mono',monospace;}

        .error-msg{
            background:rgba(239,69,69,0.1);border:1px solid rgba(239,69,69,0.3);
            border-radius:6px;padding:9px 12px;
            font-size:12px;color:#ef4545;margin-bottom:12px;
        }
    </style>
</head>
<body>
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="card">
    {{-- Icon --}}
    <div class="icon-box">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7c6fe0" stroke-width="1.5">
            <rect x="2" y="3" width="20" height="14" rx="2"/>
            <path d="M8 21h8M12 17v4"/>
            <path d="M9 8h6M12 6v6"/>
        </svg>
    </div>

    <div class="brand">
        <h1>GAMEZONE</h1>
        <p>Operator Authorization</p>
    </div>

    {{-- Role selector --}}
    <div class="role-tabs" id="role-tabs">
        <button class="role-tab" id="tab-admin" onclick="setRole('admin')">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.27 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
            Admin
        </button>
        <button class="role-tab active" id="tab-kasir" onclick="setRole('kasir')">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            Cashier
        </button>
    </div>

    {{-- Error --}}
    @if ($errors->any())
    <div class="error-msg">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="input-group">
            <span class="input-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <input type="email" name="email" value="{{ old('email') }}"
                placeholder="Operator ID (e.g. admin@gamezone.com)" required autofocus>
        </div>

        <div class="input-group">
            <span class="input-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </span>
            <input type="password" name="password" placeholder="• • • • • • • •" required>
        </div>

        <button type="submit" class="btn-submit">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            Initialize Session
        </button>
    </form>

    <div class="card-footer">
        <div class="status-dot">CORE SYSTEMS ONLINE</div>
        <div class="version">V.2.4.1</div>
    </div>
</div>

<script>
function setRole(role) {
    document.getElementById('tab-admin').className = 'role-tab' + (role==='admin'?' active':'');
    document.getElementById('tab-kasir').className = 'role-tab' + (role==='kasir'?' active':'');
}
</script>
</body>
</html>