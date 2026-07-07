---
name: magang-logbook-dev
description: Panduan pengembangan aplikasi Logbook Magang Harian Taruna PKTJ menggunakan CI4 dan Bootstrap 5.
---

# Panduan Pengembangan Logbook Magang Harian Taruna PKTJ

Anda adalah AI Assistant utama untuk pengembangan aplikasi "Logbook Magang Harian Taruna PKTJ".

## 1. Tech Stack
- **Framework:** CodeIgniter 4 (CI4)
- **Bahasa:** PHP 8.2
- **Lingkungan:** Laragon (Lokal) & cPanel (Produksi)
- **Frontend:** Bootstrap 5 (via CDN)

## 2. Aturan Wajib Penulisan Kode (CRITICAL)
- **FULL CODE UTUH:** Setiap kali Anda diminta untuk membuat atau memodifikasi file kode, Anda **WAJIB** memberikan output kode secara penuh dan utuh (1 halaman penuh/full file) dari awal sampai akhir. 
- **DILARANG KERAS** memotong kode, menggunakan komentar seperti `// ... kode lainnya ...`, atau hanya memberikan sebagian kode. Hal ini agar user bisa langsung melakukan *copy-paste* ke dalam file proyek tanpa kebingungan.
- Jangan menghilangkan bagian kode yang tidak diubah, tulis ulang seluruhnya.

## 3. Panduan Arsitektur MVC CodeIgniter 4
- **Model:** Gunakan Model CI4 (`CodeIgniter\Model`) untuk semua interaksi database. Tentukan `$table`, `$primaryKey`, `$allowedFields`, dan fitur CI4 lainnya (seperti `$useTimestamps`).
- **View:** Pisahkan struktur HTML menjadi *layout* (contoh: header, footer, sidebar) dan *content*. Gunakan fitur *View Layouts* CI4 (`$this->extend()`, `$this->section()`).
- **Controller:** Pastikan controller hanya bertugas menerima *request*, memanggil *Model* atau *Library* yang sesuai, dan mengembalikan *View* atau *JSON response*. Jangan letakkan logika bisnis yang rumit di dalam Controller; pindahkan ke Model atau Service jika diperlukan.
- **Routing:** Definisikan semua route secara eksplisit di `app/Config/Routes.php`. Hindari auto-routing demi keamanan dan keterbacaan.

## 4. Standar Penggunaan Bootstrap 5
- **CDN:** Gunakan versi Bootstrap 5 CDN terbaru di bagian header/footer layout.
- **Komponen:** Gunakan *class* bawaan Bootstrap 5 (seperti `container`, `row`, `col-`, `card`, `btn`, `table`, `form-control`) semaksimal mungkin sebelum menulis custom CSS.
- **Responsif:** Pastikan setiap antarmuka dirancang secara *mobile-first*. Gunakan *grid system* Bootstrap dengan baik (misal: `col-12 col-md-6 col-lg-4`).
- **Icon:** Gunakan FontAwesome atau Bootstrap Icons (via CDN) jika membutuhkan ikon.

## 5. Alur Kerja (Workflow)
- Pahami konteks permintaan (fitur apa yang akan dibuat/diperbaiki).
- Rencanakan perubahan pada komponen MVC yang relevan.
- Berikan kode lengkap untuk masing-masing file yang perlu dibuat atau diubah, sesuai dengan Aturan Wajib Penulisan Kode.

## 6. Aturan Domain & Terminologi (Khusus Aplikasi Logbook Ini)
- **Desain UI/UX:** Gunakan layout *Top Menu* (tanpa *sidebar*) untuk optimasi lebar layar. Warna dominan: Biru (`#0d47a1`) -> Kuning -> Abu-abu. Background putih. Antarmuka harus "Simple" dan "Fungsional".
- **Istilah:** Gunakan **NOTAR** (Nomor Taruna), dilarang menggunakan "NIT".
- **Penamaan Kelas Dinamis:** Selalu gabungkan singkatan Prodi dan Kelas untuk *display*. Contoh: "Teknologi Otomotif" Kelas "A" menjadi **"TO A"**, "Teknologi Rekayasa Otomotif" Kelas "B" menjadi **"TRO B"**, "Rekayasa Sistem Transportasi Jalan" menjadi **"RSTJ"**.
- **Format Tanggal:** Gunakan pelokalan Bahasa Indonesia di sisi *frontend* (misal: "07 Jul 2026").
- **File Upload:** Untuk menghemat ruang *server hosting* (cPanel), **DILARANG** membuat fitur *upload file fisik* ke lokal folder. Sebagai gantinya, wajib menggunakan metode **"Input Link Google Drive"** dengan tipe data *string URL*.
- **Aturan Bisnis Logbook:** 
  - Tidak boleh ada laporan (logbook) ganda di tanggal yang sama untuk satu *user*.
  - Status logbook meliputi: `pending`, `disetujui`, `revisi`, `ditolak`.
  - Logbook hanya bisa diedit dan dihapus oleh Taruna jika statusnya BUKAN "disetujui" (contoh: pending, revisi, ditolak bisa diedit/dihapus).

## 7. Standar Clean Code & Maintainability
- **Pemisahan Gaya (CSS):** DILARANG menggunakan *inline style* (seperti `style="color:red;"`) atau meletakkan blok `<style>` berukuran besar di dalam file *View*. Semua custom styling wajib disatukan ke dalam `public/css/style.css` menggunakan *class*.
- **DRY (Don't Repeat Yourself):** Jangan menduplikasi kode. Jika logika yang sama dipakai berulang kali, pindahkan menjadi satu fungsi utuh di Model, Controller, atau Helper.
- **Keterbacaan:** Berikan komentar singkat yang informatif dalam Bahasa Indonesia pada baris kode yang kompleks atau memuat alur krusial, demi kemudahan *maintainability* di masa depan.

## 8. Manajemen Role & Hierarki Sistem (Update Terakhir)
- **Daftar Role Valid (Strict ENUM):** `superadmin`, `admin_prodi`, `pejabat`, `pembimbing`, `taruna`. Segala bentuk pencocokan *role* di controller/view **WAJIB** menggunakan `strtolower(session()->get('role'))` untuk menghindari *bug case-sensitive*.
- **Akses Data Pengguna (CRUD):** 
  - `superadmin`: Akses penuh ke semua data pengguna lintas prodi.
  - `admin_prodi`: Hanya bisa membuat/mengedit akun Dosen dan Taruna yang berada dalam **Prodi yang sama** dengan Admin tersebut.
- **Relasi Pembimbing (1-to-Many):** Satu dosen pembimbing dapat membimbing lebih dari satu Taruna. Taruna terkait direlasikan melalui kolom `pembimbing_id` di tabel `users`.
- **Akses Laporan & Bimbingan:**
  - `pejabat` dan `admin_prodi`: Memiliki akses menu **Laporan Global** untuk memantau rekapitulasi data.
  - `pembimbing`: Memiliki akses ke menu **Validasi Logbook** dan **Taruna Bimbingan** untuk memantau anak didiknya.
