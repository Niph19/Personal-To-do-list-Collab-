# KONTRAK API TO DO LIST COLLAB

## Deskripsi

API ini merupakan API untuk To do list Pribadi

## Informasi Umum

- **Base URL:** `https://localhost:8000`
- **Format Respons:** JSON
- **Autentikasi:** Bearer Token (JWT)

## 1. Register

- **Method:** POST
- **URL:** /api/register
- **Autentikasi:** Tidak ada
- **Field & Tipe data:**
  | Nomor | Field | Tipe Data |
  | :---: | :---: | :---: |
  | 1 | username | string |
  | 2 | email | string |
  | 3 | password | string |
  | 4 | password_confirmation | string |

### Request Body

```json
{
    "username": "johndoe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

### API Responses

#### 🟢 201 Created

Respons ketika user berhasil dibuat.

```json
{
    "success": true,
    "message": "User created successfully",
    "data": {
        "id": 1024,
        "username": "johndoe",
        "email": "john@example.com",
        "created_at": "2026-11-03T10:12:00Z"
    }
}
```

#### 🔴 400 Bad Request

Respons jika input tidak valid atau ada data yang kurang.

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": "The email field is required.",
        "password": "Password must be at least 8 characters.",
        "password_confirmation": "Password does not match."
    }
}
```

## 2. Login

- **Method:** POST
- **URL:** /api/login
- **Autentikasi:** Tidak ada
- **Field & Tipe data:**
  | Nomor | Field | Tipe Data |
  | :---: | :---: | :---: |
  | 1 | email | string |
  | 2 | password | string |

### Request Body

Gunakan format JSON untuk menunjukkan struktur data yang dikirim oleh klien.

```json
{
    "email": "john@example.com", // string, required, email format
    "password": "password" // string, required, min 8 chars
}
```

### API Responses

#### 🟢 200 OK

Respons ketika user berhasil login.

```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
        "user": {
            "id": "usr_99812",
            "name": "Budi Santoso",
            "email": "budi@example.com"
        }
    }
}
```

#### 🔴 401 Unauthorized

Respons jika input tidak valid atau ada data yang kurang.

```json
{
    "success": false,
    "message": "Email atau password salah"
}
```

## 3. Profil User

- **Method:** GET
- **URL:** /api/login
- **Autentikasi:** Bearer Token

### API Responses

#### 🟢 200 OK

```json
{
    "success": true,
    "message": "Berhasil mengambil data profil",
    "data": {
        "id": "1234",
        "name": "John Doe",
        "email": "johndoe@example.com",
        "created_at": "2026-11-01T10:00:00Z"
    }
}
```

#### 🔴 401 Unauthorized

```json
{
    "success": false,
    "message": "Token tidak valid atau kadaluwarsa"
}
```

## 4. Logout

- **Method:** POST
- **URL:** /api/logout
- **Autentikasi:** Bearer Token

#### 🟢 200 OK

```json
{
    "success": true,
    "message": "Berhasil Logout"
}
```

## 5. CRUD Tugas

## 5.1 Daftar Semua Tugas

- **Method:** GET
- **URL:** /api/tugas
- **Autentikasi:** Bearer Token

### API Responses

#### 🟢 200 OK

```json
{
    "success": true,
    "message": "Berhasil mengambil seluruh tugas",
    "data": [
        {
            "id": "task_001",
            "title": "Membuat API Contract",
            "deadline": "2026-11-10T23:59:59Z",
            "status": "done"
        },
        {
            "id": "task_002",
            "title": "Integrasi Mocking Frontend",
            "deadline": "2026-11-12T17:00:00Z",
            "status": "todo"
        }
    ]
}
```

## 5.2 Detail Tugas

- **Method:** GET
- **URL:** /api/tugas/{id}
- **Autentikasi:** Bearer Token

### API Responses

#### 🟢 200 OK

```json
{
    "success": true,
    "message": "Berhasil mengambil seluruh tugas",
    "data": {
        "id": "task_001",
        "title": "Membuat API Contract",
        "deadline": "2026-11-10T23:59:59Z",
        "status": "done"
    }
}
```

#### 🔴 404 Not Found

```json
{
    "success": false,
    "message": "Tugas tidak ditemukan"
}
```

## 5.3 Membuat Tugas

- **Method:** POST
- **URL:** /api/tugas
- **Autentikasi:** Bearer Token
- **Field & Tipe data:**
  | Nomor | Field | Tipe Data |
  | :---: | :---: | :---: |
  | 1 | title | string |
  | 2 | deadline | string |
  | 3 | status | string |

### Request Body

```json
{
    "title": "Pitch Deck English",
    "deadline": "2026-11-15T18:00:00Z",
    "status": "todo"
}
```

### API Responses

#### 🟢 201 Created

```json
{
  "success": true,
  "message": "Tugas berhasil dibuat",
  "data": {
    "id": "task_003",
    "title": "Pitch Deck English",
    "deadline": "2026-11-15T18:00:00Z",
    "status": "todo"
  }
}
```

## 5.4 Perbarui Tugas
- **Method:** PUT
- **URL:** /api/tugas/{id}
- **Autentikasi:** Bearer Token
- **Field & Tipe data:**
  | Nomor | Field | Tipe Data |
  | :---: | :---: | :---: |
  | 1 | title | string |
  | 2 | deadline | string |
  | 3 | status | string |

### Request Body

```json
{
    "title": "Proposal Bisnis Bahasa Indonesia",
    "deadline": "2026-11-15T18:00:00Z",
    "status": "done"
}
```

### API Responses

#### 🟢 200 OK

```json
{
  "success": true,
  "message": "Tugas berhasil dibuat",
  "data": {
    "id": "task_003",
    "title": "Proposal Bisnis Bahasa Indonesia",
    "deadline": "2026-11-15T18:00:00Z",
    "status": "todo"
  }
}
```

## 5.5 Hapus Tugas
- **Method:** DELETE
- **URL:** /api/tugas/{id}
- **Autentikasi:** Bearer Token

### API Responses

#### 🟢 200 OK

```json
{
  "success": true,
  "message": "Tugas berhasil dihapus"
}
```

#### 🔴 404 Not Found

```json
{
  "success": false,
  "message": "Tugas tidak dapat ditemukan atau sudah dihapus"
}
```