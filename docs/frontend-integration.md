# Frontend Integration - Bearer Token & User-Specific Tasks

## Struktur FE Yang Sudah Dikerjakan

### 1. **Routes & Views Terpisah**
```
GET  /              → redirect ke /login
GET  /login         → auth/login.blade.php
GET  /register      → auth/register.blade.php
GET  /dashboard     → dashboard.blade.php
```

### 2. **Token Management (auth-guard.js)**

Client-side token guard untuk:
- ✅ Simpan token setelah login (`localStorage`)
- ✅ Simpan user data (`sessionStorage`)
- ✅ Auto-redirect ke `/login` jika no token (guard dashboard)
- ✅ Auto-redirect ke `/dashboard` jika sudah login (guard login/register)
- ✅ Clear token saat logout

**Cara kerja:**
```javascript
// Login page
AuthGuard.requireGuest(); // redirect ke dashboard jika sudah login

// Dashboard page
AuthGuard.requireAuth(); // redirect ke login jika no token
```

### 3. **API Helper (api.js)**

Otomatis attach Bearer token di setiap request:
```javascript
// Setiap request otomatis kirim:
Authorization: Bearer <token>

// Usage:
await API.get('/tugas')        // GET dengan token
await API.post('/tugas', data) // POST dengan token
await API.put('/tugas/1', data)
await API.delete('/tugas/1')
```

**Auto-handle 401 Unauthorized:**
- Token expired/invalid → clear token → redirect `/login`

### 4. **Pemanggilan API dan mode mock**

Set di `api.js`:
```javascript
API.useMock = false // API Laravel aktif
API.useMock = true  // Demo mock per akun browser
```

FE mengirim `name` saat register, membaca `access_token` dari login, dan mengirim token sebagai Bearer. Mock tasks disimpan per user ID/email.

### 5. **Database Structure**

Migration `tugas` table:
```php
id          - bigint auto increment
user_id     - foreign key ke users (onDelete cascade)
title       - string
deadline    - datetime (ISO 8601)
status      - enum('todo', 'done')
created_at
updated_at
```

**User-specific tasks:** BE harus filter by `auth()->user()->id`

## Cara Integrasi Setelah BE Push

### Step 1: Pull dari BE
```bash
git pull origin main
```

### Step 2: Check BE API Endpoints
BE harus provide:
```
POST   /api/login     → { user, access_token }
POST   /api/register  → body { name, email, password, password_confirmation }
GET    /api/profile   → profile (Bearer token)
POST   /api/logout    → revoke current token (Bearer token)
GET    /api/tugas     → authenticated user's tasks (Bearer token)
POST   /api/tugas     → create task for authenticated user
PUT    /api/tugas/:id → update task owned by authenticated user
DELETE /api/tugas/:id → delete task owned by authenticated user
```

### Step 3: Ganti Mock Mode
`public/js/api.js` uses live API mode (`useMock: false`). Set it to `true` only when demoing without API.

### Step 4: Test Flow
1. Buka http://localhost:8000 → redirect ke `/login`
2. Register → redirect ke `/login`
3. Login → simpan token → redirect ke `/dashboard`
4. Dashboard:
   - Load tasks (otomatis kirim Bearer token)
   - Add/Edit/Delete task (token otomatis)
   - Toggle status (token otomatis)
5. Logout → clear token → redirect ke `/login`
6. Coba akses `/dashboard` tanpa login → redirect ke `/login`

### Step 5: BE Must Handle

**Auth Middleware:**
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tugas', [TaskController::class, 'index']);
    Route::post('/tugas', [TaskController::class, 'store']);
    // dst...
});
```

**Kepemilikan tugas:**
```php
// Routes require auth:sanctum; TaskApiController scopes every query/create via $request->user()->tugas().
```

## Frontend Controllers (Proxy)

FE punya controller di `app/Http/Controllers/Frontend/`:
- `AuthController` - proxy login/register/logout ke BE
- `TaskController` - proxy CRUD tasks ke BE

**Ini optional** - JS bisa langsung call API BE tanpa lewat FE controller. Tapi kalau pakai proxy:
- FE controller forward request + token ke BE
- Response BE diteruskan ke JS

## Token Security

✅ Token stored in `localStorage` (persistent across tabs)
✅ User data in `sessionStorage` (per-tab)
✅ Token sent in `Authorization: Bearer` header
✅ Auto-clear on 401 Unauthorized
✅ Client-side guard redirect jika no token

**Note:** Client-side guard bisa di-bypass (inspect element), tapi request tetap butuh valid token dari BE. Real security di BE middleware.

## File Structure

```
public/
  js/
    auth-guard.js   - Token management & route guard
    api.js          - API helper dengan Bearer token
    dashboard.js    - Dashboard logic
  css/              - (optional untuk extract CSS)

resources/views/
  auth/
    login.blade.php
    register.blade.php
  dashboard.blade.php

app/Http/Controllers/Frontend/
  AuthController.php   - (optional proxy)
  TaskController.php   - (optional proxy)

routes/
  web.php      - /login, /register, /dashboard
  frontend.php - /api/* proxy routes (optional)
```

## Mock Data Format

`public/mock/tasks.json`:
```json
[
  {
    "id": "task_001",
    "title": "Contoh tugas",
    "deadline": "2026-11-10T23:59:59Z",
    "status": "todo"
  }
]
```

## Checklist Integrasi

## Perbedaan implementasi dengan `docs/kontrak-api.md`

- Implementasi register menerima `name`, sedangkan kontrak lama menyebut `username`.
- Login mengembalikan `access_token` dan `user` di tingkat teratas, bukan `data.token`.
- Profil tersedia di `GET /api/profile`; kontrak lama menuliskan `GET /api/login`.
- Logout memakai `POST /api/logout` dan sekarang butuh Bearer token.

FE mengikuti implementasi API yang ada. Sepakati dan perbarui dokumen kontrak bersama BE sebelum merge.

- [ ] BE push API endpoints sesuai kontrak
- [ ] BE implement Sanctum auth middleware
- [ ] BE filter tasks by `auth()->user()->id`
- [ ] FE ganti `useMock: false` di `api.js`
- [ ] Test: register → login → dashboard → CRUD tasks → logout
- [ ] Verify: User A tidak bisa lihat tasks User B

## Troubleshooting

**Error: "The route api/register could not be found"**
→ BE belum implement endpoint, tetap pakai `useMock: true`

**Tasks tidak muncul setelah login**
→ Check Console Network tab, lihat response API
→ Pastikan BE return array tasks di `data` field

**401 Unauthorized di dashboard**
→ Token invalid/expired
→ Clear localStorage & login ulang
→ Check BE middleware `auth:sanctum`

**User bisa lihat tasks user lain**
→ BE belum filter by user_id
→ Fix di BE: `auth()->user()->tugas`
