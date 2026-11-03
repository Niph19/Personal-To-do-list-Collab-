<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Daftar — Tempo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root{--g:#f2f4f7;--s:#fff;--b:#e2e5e9;--t:#101214;--m:#5f6873;--p:#16191d;--bad:#b42328;font-family:Geist,system-ui,sans-serif;color:var(--t)}
        *{box-sizing:border-box}
        body{margin:0;background:var(--g);font-size:14px}
        button,input{font:inherit}
        button{cursor:pointer}
        button:focus-visible,input:focus-visible{outline:3px solid #8cb8f5;outline-offset:2px}
        .auth{min-height:100vh;display:grid;place-items:center;padding:20px}
        .auth-card{background:var(--s);border:1px solid var(--b);border-radius:8px;width:min(100%,430px);padding:32px}
        .brand{display:flex;align-items:center;gap:9px;font-size:18px;font-weight:600}
        .mark{display:grid;place-items:center;width:27px;height:27px;border-radius:7px;background:var(--t);color:#fff}
        h1{font-size:24px;margin:40px 0 6px}
        .intro{margin:0 0 24px;color:var(--m)}
        .field{display:grid;gap:7px;margin:0 0 15px}
        label{font-weight:500}
        input{height:40px;width:100%;border:1px solid #cdd2d8;border-radius:6px;padding:0 11px}
        .btn{min-height:36px;padding:0 13px;border:1px solid transparent;border-radius:6px;font-weight:500;width:100%;margin-top:6px}
        .primary{background:var(--p);color:#fff}
        .text{border:0;background:none;padding:0;color:var(--t);font-weight:600;text-decoration:underline;cursor:pointer}
        .switch{text-align:center;color:var(--m);margin:22px 0 0}
        .message{min-height:18px;margin:0 0 10px;color:var(--bad);font-size:12px}
        .toast{position:fixed;right:24px;bottom:24px;padding:11px 14px;border-radius:6px;background:var(--t);color:#fff;opacity:0;transform:translateY(8px);transition:.18s}
        .toast.show{opacity:1;transform:none}
    </style>
</head>
<body>
    <section class="auth">
        <div class="auth-card">
            <div class="brand"><span class="mark">✓</span>Tempo</div>
            <form id="register-form">
                <h1>Buat akun baru</h1>
                <p class="intro">Mulai susun tugas yang penting untukmu.</p>
                <p class="message" id="reg-msg" aria-live="polite"></p>
                <div class="field">
                    <label for="name">Nama</label>
                    <input id="name" required>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" required>
                </div>
                <div class="field">
                    <label for="password">Kata sandi</label>
                    <input id="password" type="password" minlength="8" required>
                </div>
                <button type="submit" class="btn primary">Buat akun</button>
                <p class="switch">Sudah punya akun? <a href="/login" class="text">Masuk</a></p>
            </form>
        </div>
    </section>
    
    <div class="toast" id="toast" role="status"></div>
    
    <script src="/js/auth-guard.js"></script>
    <script src="/js/api.js"></script>
    <script>
        // Guard: redirect jika sudah login
        AuthGuard.requireGuest();
        
        const $ = s => document.querySelector(s);
        const notice = s => {
            $('#toast').textContent = s;
            $('#toast').classList.add('show');
            setTimeout(() => $('#toast').classList.remove('show'), 2200);
        };
        
        $('#register-form').onsubmit = async (e) => {
            e.preventDefault();
            $('#reg-msg').textContent = '';
            
            const name = $('#name').value.trim();
            const email = $('#email').value.trim();
            const password = $('#password').value;
            
            if (!name || !email || password.length < 8) {
                $('#reg-msg').textContent = 'Isi semua data. Kata sandi minimal 8 karakter.';
                return;
            }
            
            try {
                // Call API register
                await API.post('/register', {
                    name,
                    email,
                    password,
                    password_confirmation: password
                });
                
                notice('Akun berhasil dibuat. Silakan masuk.');
                
                // Redirect ke login setelah sukses
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1500);
                
            } catch (error) {
                $('#reg-msg').textContent = error.message;
            }
        };
    </script>
</body>
</html>
