<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard — Tempo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root{--g:#f2f4f7;--s:#fff;--b:#e2e5e9;--r:#edf0f2;--t:#101214;--m:#5f6873;--p:#16191d;--bad:#b42328;font-family:Geist,system-ui,sans-serif;color:var(--t)}
        *{box-sizing:border-box}
        body{margin:0;background:var(--g);font-size:14px}
        button,input{font:inherit}
        button{cursor:pointer}
        button:focus-visible,input:focus-visible{outline:3px solid #8cb8f5;outline-offset:2px}
        .app{min-height:100vh;display:grid;grid-template-columns:256px minmax(0,1fr)}
        .side{padding:28px 12px 16px;display:flex;flex-direction:column;border-right:1px solid var(--b);background:var(--s)}
        .brand{display:flex;align-items:center;gap:9px;font-size:18px;font-weight:600}
        .mark{display:grid;place-items:center;width:27px;height:27px;border-radius:7px;background:var(--t);color:#fff}
        .space{padding:9px 10px;margin:24px 0 30px;background:#e9ecef;border-radius:6px;font-weight:500}
        .nav-title{margin:0 10px 10px;color:var(--m);font-size:12px}
        .nav{height:34px;width:100%;border:0;border-radius:6px;padding:0 10px;background:none;color:var(--m);text-align:left}
        .nav:hover,.nav.active{background:#e5e8eb;color:var(--t)}
        .bottom{margin-top:auto}
        .user{display:flex;gap:9px;align-items:center;padding:12px 10px}
        .avatar{display:grid;place-items:center;width:28px;height:28px;border-radius:50%;background:#dfe6ef;color:#314a6d;font-weight:600;font-size:12px}
        .email{font-size:12px;color:var(--m)}
        main{padding:34px 40px 56px;min-width:0}
        .head{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;margin-bottom:26px}
        h1{margin:0;font-size:24px;letter-spacing:-.02em}
        .sub{margin:7px 0 0;color:var(--m)}
        .actions{display:flex;gap:8px;align-items:center}
        .updated{font-size:12px;color:var(--m)}
        .btn{min-height:36px;padding:0 13px;border:1px solid transparent;border-radius:6px;font-weight:500}
        .primary{background:var(--p);color:#fff}
        .secondary{background:#fff;border-color:#cdd2d8}
        .summary{display:grid;grid-template-columns:repeat(3,1fr);background:var(--s);border:1px solid var(--b);border-radius:8px;margin-bottom:24px}
        .metric{padding:17px 20px}
        .metric+.metric{border-left:1px solid var(--b)}
        .metric small{color:var(--m)}
        .metric strong{display:block;font-size:28px;font-weight:500;margin-top:3px;font-variant-numeric:tabular-nums}
        .grid{display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:12px;align-items:start}
        .panel{background:var(--s);border:1px solid var(--b);border-radius:8px}
        .panel-head{display:flex;justify-content:space-between;gap:16px;align-items:center;padding:18px 20px 14px}
        .panel h2{font-size:16px;margin:0}
        .filters{display:flex;padding:3px;gap:3px;background:#eef0f2;border-radius:6px}
        .filter{border:0;background:transparent;border-radius:4px;padding:5px 8px;color:var(--m);font-size:12px}
        .filter.active{background:#fff;color:var(--t)}
        .tasks{padding:0 12px 12px}
        .task{display:grid;grid-template-columns:20px minmax(0,1fr) auto auto auto;gap:12px;align-items:center;min-height:62px;padding:9px 8px;border-radius:6px}
        .task+.task{border-top:1px solid var(--r)}
        .task:hover{outline:1px solid var(--b)}
        .check{appearance:none;width:18px;height:18px;border:1.5px solid #89919b;border-radius:50%;margin:0;cursor:pointer}
        .check:checked{background:var(--t);border-color:var(--t);box-shadow:inset 0 0 0 4px #fff}
        .title{font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .done .title{text-decoration:line-through;color:var(--m)}
        time{font-size:12px;color:var(--m);white-space:nowrap}
        .late{color:var(--bad)}
        .action-btn{height:28px;padding:0 10px;border:0;border-radius:5px;background:none;color:var(--m);font-size:12px;font-weight:500}
        .action-btn:hover{background:#eef0f2}
        .delete-btn:hover{color:var(--bad)}
        .side-panel{padding:20px}
        .side-panel+.side-panel{margin-top:12px}
        .copy{font-size:13px;margin:6px 0 16px;color:var(--m)}
        .field{display:grid;gap:7px;margin:0 0 15px}
        label{font-weight:500}
        input{height:40px;width:100%;border:1px solid #cdd2d8;border-radius:6px;padding:0 11px}
        .deadlines{padding:0;margin:0;list-style:none}
        .deadlines li{display:flex;justify-content:space-between;gap:10px;min-height:40px;align-items:center;border-top:1px solid var(--r)}
        .deadlines li:first-child{border:0}
        .deadlines span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .empty,.loading,.error{padding:34px 20px;text-align:center;color:var(--m)}
        .error{color:var(--bad)}
        .text{border:0;background:none;padding:0;color:var(--t);font-weight:600;text-decoration:underline;cursor:pointer}
        .toast{position:fixed;right:24px;bottom:24px;padding:11px 14px;border-radius:6px;background:var(--t);color:#fff;opacity:0;transform:translateY(8px);transition:.18s}
        .toast.show{opacity:1;transform:none}
        .backdrop{position:fixed;inset:0;display:grid;place-items:center;padding:20px;background:#10121455}
        .modal{width:min(100%,440px);background:#fff;border-radius:8px;padding:24px}
        .modal h2{margin:0 0 20px;font-size:18px}
        .message{min-height:18px;margin:0 0 10px;color:var(--bad);font-size:12px}
        .modal-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:20px}
        @media(max-width:900px){.app{grid-template-columns:1fr}.side{display:none}main{padding:24px}.grid{grid-template-columns:1fr}}
        @media(max-width:600px){main{padding:20px 16px 40px}.head{display:block}.actions{margin-top:14px}.summary{grid-template-columns:1fr 1fr}.metric:last-child{grid-column:1/-1;border-top:1px solid var(--b)}.metric+.metric{border-left:0}.metric:nth-child(2){border-left:1px solid var(--b)}.task{grid-template-columns:20px minmax(0,1fr) auto auto}.task time{grid-column:2/5;grid-row:2;margin-top:4px}.task .action-btn{grid-row:1;font-size:11px;padding:0 8px;height:24px}}
    </style>
</head>
<body>
    <div class="app">
        <aside class="side">
            <div class="brand"><span class="mark">✓</span>Tempo</div>
            <div class="space">Ruang pribadi</div>
            <p class="nav-title">Pekerjaan</p>
            <button class="nav active" data-filter="all">☷ &nbsp; Semua tugas</button>
            <button class="nav" data-filter="todo">○ &nbsp; Belum selesai</button>
            <button class="nav" data-filter="done">✓ &nbsp; Selesai</button>
            <div class="bottom">
                <button class="nav" id="logout">↪ &nbsp; Keluar</button>
                <div class="user">
                    <span class="avatar" id="initial">U</span>
                    <div>
                        <div id="name">User</div>
                        <div class="email" id="email">user@example.com</div>
                    </div>
                </div>
            </div>
        </aside>
        <main>
            <header class="head">
                <div>
                    <h1>Tugas saya</h1>
                    <p class="sub" id="finding">Memuat tugas…</p>
                </div>
                <div class="actions">
                    <span class="updated" id="updated"></span>
                    <button class="btn primary" id="add">Tambah tugas</button>
                </div>
            </header>
            <section class="summary">
                <div class="metric"><small>Total tugas</small><strong id="total">—</strong></div>
                <div class="metric"><small>Belum selesai</small><strong id="todo-count">—</strong></div>
                <div class="metric"><small>Selesai</small><strong id="done-count">—</strong></div>
            </section>
            <div class="grid">
                <section class="panel">
                    <header class="panel-head">
                        <h2>Daftar tugas</h2>
                        <div class="filters">
                            <button class="filter active" data-filter="all">Semua</button>
                            <button class="filter" data-filter="todo">Belum selesai</button>
                            <button class="filter" data-filter="done">Selesai</button>
                        </div>
                    </header>
                    <div class="tasks" id="tasks" aria-live="polite">
                        <div class="loading">Memuat tugas…</div>
                    </div>
                </section>
                <aside>
                    <section class="panel side-panel">
                        <h2>Tambah tugas cepat</h2>
                        <p class="copy">Tulis yang perlu dikerjakan, lalu atur tenggatnya.</p>
                        <form id="quick-form">
                            <div class="field">
                                <label for="q-title">Judul tugas</label>
                                <input id="q-title" required placeholder="Contoh: Kirim revisi desain">
                            </div>
                            <div class="field">
                                <label for="q-date">Tenggat</label>
                                <input id="q-date" type="date" required>
                            </div>
                            <button class="btn primary">Simpan tugas</button>
                        </form>
                    </section>
                    <section class="panel side-panel">
                        <h2>Tenggat terdekat</h2>
                        <ul class="deadlines" id="deadlines">
                            <li>Memuat…</li>
                        </ul>
                    </section>
                </aside>
            </div>
        </main>
    </div>
    
    <div class="toast" id="toast" role="status"></div>
    
    <template id="modal-tpl">
        <div class="backdrop">
            <form class="modal" id="editor">
                <h2 id="modal-title">Tambah tugas</h2>
                <div class="field">
                    <label for="e-title">Judul tugas</label>
                    <input id="e-title" required>
                </div>
                <div class="field">
                    <label for="e-date">Tenggat</label>
                    <input id="e-date" type="date" required>
                </div>
                <p class="message" id="edit-msg"></p>
                <div class="modal-actions">
                    <button type="button" class="btn secondary" id="delete" hidden>Hapus</button>
                    <button type="button" class="btn secondary" id="cancel">Batal</button>
                    <button class="btn primary">Simpan</button>
                </div>
            </form>
        </div>
    </template>
    
    <script src="/js/auth-guard.js"></script>
    <script src="/js/api.js"></script>
    <script src="/js/dashboard.js"></script>
</body>
</html>
