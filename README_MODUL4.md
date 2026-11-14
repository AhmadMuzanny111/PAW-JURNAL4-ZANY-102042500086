# Starter Code Praktikum Modul 4 — CeLOE LMS

Selamat datang di **Praktikum Modul 4 Pengembangan Aplikasi Website (CeLOE LMS)**!  
Repositori ini adalah **Starter Code** yang telah dilengkapi fitur dasar Modul 3. Tugas Anda pada Modul 4 adalah melengkapi fitur **Proteksi Route dengan Middleware**, **Filter & Pengurutan Katalog Mata Kuliah**, **Penilaian Tugas (Grading)**, serta **Ekspor Rekap Nilai ke CSV** dengan menyelesaikan **TODO 1 hingga TODO 8**.

---

## Persiapan & Perintah Artisan

1. Pastikan konfigurasi database pada file `.env` sudah sesuai.
2. Jalankan migrasi dan seeder awal untuk memastikan aplikasi berjalan dengan baik:
   ```bash
   php artisan migrate:fresh --seed
   ```
3. Jalankan server lokal Laravel:
   ```bash
   php artisan serve
   ```
4. Buka aplikasi di browser melalui alamat `http://127.0.0.1:8000`.

> **Catatan:** Setiap kali Anda selesai mengerjakan **TODO 1** (Migration) dan **TODO 8** (Seeder), jalankan kembali perintah `php artisan migrate:fresh --seed` agar perubahan skema tabel dan data awal diterapkan ke database.

---

## Akun Login Default (Seeder)

Gunakan kredensial berikut untuk menguji fitur dari sisi **Dosen** maupun **Mahasiswa**:

| Peran | Email | Password | Nomor Induk (NIP / NIM) |
| :--- | :--- | :--- | :--- |
| **Dosen** | `dosen@celoe.test` | `dosen123` | `198501012015041001` |
| **Mahasiswa** | `mahasiswa@celoe.test` | `mahasiswa123` | `1301210001` |

---

## Urutan Pengerjaan Tugas (`TODO 1` – `TODO 8`)

Kerjakan bagian-bagian yang ditandai komentar `TODO` secara berurutan pada 8 file berikut:

### 1. `TODO 1` — Migration Kolom Penilaian
- **File:** `database/migrations/2026_10_25_000001_add_grading_to_submissions_table.php`
- **Instruksi:**
  - Pada method `up()`, tambahkan kolom `nilai` (`unsignedTinyInteger`, `nullable`) dan `catatan_dosen` (`text`, `nullable`) setelah kolom `jawaban`.
  - Pada method `down()`, hapus kedua kolom tersebut menggunakan `$table->dropColumn(['nilai', 'catatan_dosen'])`.

### 2. `TODO 2` — Model & Accessor Status Nilai
- **File:** `app/Models/Submission.php`
- **Instruksi:**
  - Tambahkan `'nilai'` dan `'catatan_dosen'` ke dalam properti `$fillable`.
  - Lengkapi method accessor `getStatusNilaiAttribute()` dengan ketentuan:
    - Jika `$this->nilai === null`, kembalikan `'Belum Dinilai'`.
    - Jika `$this->nilai >= 70`, kembalikan `'Lulus'`.
    - Jika `< 70`, kembalikan `'Perlu Perbaikan'`.

### 3. `TODO 3` — Route Middleware & Aktivasi Endpoint Baru
- **File:** `routes/web.php`
- **Instruksi:**
  - Bungkus seluruh route terproteksi menggunakan `Route::middleware(['auth'])->group(...)`.
  - Kelompokkan route khusus dosen ke dalam `Route::middleware(['role:dosen'])->group(...)`.
  - Kelompokkan route khusus mahasiswa ke dalam `Route::middleware(['role:mahasiswa'])->group(...)`.
  - Hapus tanda komentar (`//`) pada route `submissions.grade` dan `assignments.export-csv` di dalam grup dosen.

### 4. `TODO 4` — Filter Semester & Sorting Katalog Mata Kuliah
- **File:** `app/Http/Controllers/MataKuliahController.php`
- **Instruksi:**
  - Pada method `index()`, ambil query string `semester` (`$request->query('semester')`) dan `sort` (`$request->query('sort', 'nama_asc')`).
  - Jika `$semester` diisi, tambahkan filter `$query->where('semester', $semester)`.
  - Terapkan pengurutan berdasarkan nilai `$sort`:
    - `'nama_desc'` -> `orderBy('nama_mk', 'desc')`
    - `'sks_desc'` -> `orderBy('sks', 'desc')`
    - `'sks_asc'` -> `orderBy('sks', 'asc')`
    - `default` (`'nama_asc'`) -> `orderBy('nama_mk', 'asc')`

### 5. `TODO 5a` & `TODO 5b` — Logika Penilaian & Export CSV
- **File:** `app/Http/Controllers/SubmissionController.php`
- **Instruksi:**
  - **`TODO 5a` (`grade`):**
    1. Pastikan dosen yang login adalah pengampu mata kuliah (`$submission->assignment->course->dosen_id === Auth::id()`), jika bukan gagalkan dengan `abort(403)`.
    2. Validasi input `nilai` (`required|integer|min:0|max:100`) dan `catatan_dosen` (`nullable|string|max:1000`).
    3. Perbarui data submission menggunakan `$submission->update(...)` lalu kembalikan `back()->with('success', ...)`.
  - **`TODO 5b` (`exportCsv`):**
    1. Pastikan dosen yang login adalah pengampu mata kuliah tugas tersebut, jika bukan `abort(403)`.
    2. Gunakan `response()->streamDownload(...)` serta `fopen('php://output', 'w')` dan `fputcsv()` untuk mengunduh daftar nilai mahasiswa dalam format CSV.

### 6. `TODO 6` — UI Dropdown Filter Semester & Sort
- **File:** `resources/views/courses/index.blade.php`
- **Instruksi:**
  - Tambahkan elemen `<select name="semester">` yang menampilkan daftar `$semesters` di dalam form pencarian.
  - Tambahkan elemen `<select name="sort">` dengan opsi `nama_asc`, `nama_desc`, `sks_desc`, dan `sks_asc`.

### 7. `TODO 7a`, `TODO 7b`, & `TODO 7c` — UI Rekapitulasi & Form Penilaian Tugas
- **File:** `resources/views/assignments/submissions.blade.php`
- **Instruksi:**
  - **`TODO 7a`:** Tampilkan kartu statistik ringkasan untuk **Total Mahasiswa**, **Sudah Mengumpulkan**, dan **Rata-rata Nilai**.
  - **`TODO 7b`:** Tambahkan tombol **Export CSV** yang mengarah ke `route('assignments.export-csv', $assignment)`.
  - **`TODO 7c`:** Tambahkan form ber-method `PATCH` menuju `route('submissions.grade', $sub->id)` berisi input `nilai` (0–100), input `catatan_dosen`, dan tombol simpan pada baris mahasiswa yang sudah mengumpulkan tugas.

### 8. `TODO 8` — Penambahan Data Seeder Modul 4
- **File:** `database/seeders/DatabaseSeeder.php`
- **Instruksi:**
  - Tambahkan minimal 2 mata kuliah lagi dengan semester dan SKS berbeda, 1 akun mahasiswa tambahan, minimal 2 tugas perkuliahan (`Assignment`), serta contoh pengumpulan tugas (`Submission`) untuk menguji fitur filter, sorting, penilaian, dan export CSV.
  - Jalankan `php artisan migrate:fresh --seed` untuk memuat seluruh data seeder baru.

