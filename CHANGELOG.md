# Changelog

Semua perubahan penting pada proyek ini akan didokumentasikan dalam file ini.

Format pencatatan mengacu pada [Keep a Changelog](https://keepachangelog.com/id/1.0.0/) dan mengikuti [Semantic Versioning](https://semver.org/lang/id/).

---

## [0.1.0] - 2026-10-03

Rilis awal sistem manajemen pengguna, peran, hak akses, dan log aktivitas dengan antarmuka DataTable modern serta arsitektur backend Service-Repository.

### ✨ Fitur Utama (Features)

- **Manajemen Pengguna (Users CRUD)**:
  - Pengelolaan data pengguna (Nama, Username, Email, Password, dan Peran).
  - Fitur pencarian *real-time*, paginasi bernomor, dan pengaturan jumlah baris data per halaman.
  - Avatar inisial dinamis dan modal konfirmasi hapus pengguna.

- **Manajemen Peran & Hak Akses (Roles & Permissions)**:
  - Pembuatan dan pembaruan peran dengan multi-select hak akses (*permissions*).
  - Proteksi otomatis untuk peran `super admin` (akses penuh dan tidak dapat dihapus).
  - Tabel katalog seluruh hak akses (*permissions*) yang terdaftar dalam sistem.

- **Audit & Log Aktivitas (Activity Logs)**:
  - Pencatatan otomatis setiap aksi penting (Create, Update, Delete) beserta detail perubahan data.
  - Pencatatan informasi audit perangkat (IP Address dan User Agent/Browser).
  - Panel filter pencarian berdasarkan pengguna dan rentang tanggal.

- **Sistem Notifikasi Toast**:
  - Integrasi notifikasi toast Sonner dengan indikator warna (*rich colors*: hijau untuk sukses, merah untuk gagal/error) yang terhubung langsung dengan session flash Laravel Inertia.

- **Standardisasi UI / UX**:
  - Breadcrumbs dan judul halaman langsung terintegrasi di dalam *body content*.
  - Form pembuatan dan pengeditan dengan layout kartu *full-width*.
  - Seluruh antarmuka, pesan validasi, dan notifikasi menggunakan Bahasa Indonesia.

### 🏗️ Arsitektur & Backend

- **Service & Repository Pattern**:
  - Pemisahan *business logic* dan *query execution* pada modul User, Role, Permission, dan Activity Log.
- **Form Request Validation**:
  - Validasi input dan filter paginasi dikelompokkan dalam sub-folder fitur masing-masing (`app/Http/Requests/`).
- **Error Handling & Keamanan**:
  - Pengecekan keberadaan data sebelum operasi database.
  - Proteksi transaksi database dengan blok `try-catch` dan pencatatan log error otomatis.
- **Unit Testing**:
  - Pengujian unit untuk `UserService`, `RoleService`, dan `PermissionService`.

---
