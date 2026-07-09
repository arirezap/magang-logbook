---
name: magang-logbook-dev
description: Panduan pengembangan aplikasi Logbook Magang Harian Taruna PKTJ menggunakan CI4 dan Bootstrap 5.
---

# Panduan Pengembangan Logbook Magang Harian Taruna PKTJ

Anda adalah AI Assistant utama untuk pengembangan aplikasi "Logbook Magang Harian Taruna PKTJ".

## 1. Tech Stack
- **Framework:** CodeIgniter 4 (CI4)
- **Bahasa:** PHP 8.2+ (Kompatibel dengan PHP 8.3+)
- **Lingkungan:** Laragon (Lokal) & cPanel (Produksi)
- **Frontend:** Bootstrap 5 (via CDN)
- **Database:** MySQL

## 2. Aturan Wajib Penulisan Kode (CRITICAL)
- **FULL CODE UTUH:** Setiap kali Anda diminta untuk membuat atau memodifikasi file kode, Anda **WAJIB** memberikan output kode secara penuh dan utuh (1 halaman penuh/full file) dari awal sampai akhir. 
- **DILARANG KERAS** memotong kode, menggunakan komentar seperti `// ... kode lainnya ...`, atau hanya memberikan sebagian kode. Hal ini agar user bisa langsung melakukan *copy-paste* ke dalam file proyek tanpa kebingungan.
- Jangan menghilangkan bagian kode yang tidak diubah, tulis ulang seluruhnya.

## 3. Panduan Arsitektur MVC & Keamanan Aplikasi (CRITICAL SECURITY)
Keamanan aplikasi adalah prioritas utama untuk mencegah serangan dari peretas (hacker). Ikuti standar keamanan berikut:
- **Pencegahan XSS (Cross-Site Scripting):** Semua output variabel di dalam View wajib dibungkus dengan helper `esc()`. Contoh: `<?= esc($log['kegiatan']) ?>`. Hindari menampilkan data mentah secara langsung.
- **Pencegahan SQL Injection:** Selalu gunakan Query Builder bawaan CodeIgniter 4 untuk berinteraksi dengan database karena otomatis menerapkan *parameter binding* (misal: `$builder->where()`, `$builder->like()`). Dilarang menulis query SQL manual yang menggabungkan string secara langsung dengan input user.
- **CSRF (Cross-Site Request Forgery) Protection:** Pastikan proteksi CSRF diaktifkan pada sistem. Semua form pengiriman data POST wajib menyertakan token CSRF (gunakan `<?= csrf_field() ?>` di setiap elemen form).
- **Autentikasi & Otorisasi Ketat:**
  - Gunakan filter auth (`['filter' => 'auth']`) di `Config/Routes.php` untuk memproteksi rute yang membutuhkan login.
  - Setiap fungsi di Controller harus memeriksa hak akses berdasarkan role pengguna. Lakukan pengecekan ketat menggunakan `strtolower(session()->get('role'))` sebelum memproses data.
- **Penanganan Input:** Selalu lakukan validasi input di Controller menggunakan library `Validation` bawaan CI4 sebelum memasukkannya ke database atau mengolah logika bisnis.

## 4. Standar Penggunaan Bootstrap 5
- **CDN:** Gunakan versi Bootstrap 5 CDN terbaru di bagian header/footer layout.
- **Komponen:** Gunakan *class* bawaan Bootstrap 5 (seperti `container`, `row`, `col-`, `card`, `btn`, `table`, `form-control`) semaksimal mungkin sebelum menulis custom CSS.
- **Responsif:** Pastikan setiap antarmuka dirancang secara *mobile-first*. Gunakan *grid system* Bootstrap dengan baik (misal: `col-12 col-md-6 col-lg-4`).
- **Icon:** Gunakan FontAwesome atau Bootstrap Icons (via CDN) jika membutuhkan ikon.

## 5. Alur Kerja (Workflow) & Setup Proyek
Untuk setup awal proyek hasil kloning:
1. Salin file `env` menjadi `.env` dan sesuaikan variabel konfigurasi lokal (lingkungan `development`, baseUrl, dan kredensial database).
2. Jalankan `composer install` untuk mengunduh dependencies.
3. Jalankan migrasi database menggunakan perintah `php spark migrate`.
4. Jalankan seeder database untuk data awal menggunakan `php spark db:seed MainSeeder`, `php spark db:seed SuperadminSeeder`, `php spark db:seed MassiveSeeder`, dan `php spark db:seed LogbookSeeder`.

## 6. Aturan Domain, Fitur & Terminologi (Khusus Aplikasi Logbook Ini)
- **Desain UI/UX:** Integrasikan standar dari skill [ui-ux-pro-max](file:///d:/laragon/www/magang/.agents/skills/ui-ux-pro-max/SKILL.md) dan [ui-styling](file:///d:/laragon/www/magang/.agents/skills/ui-styling/SKILL.md). Gunakan layout *Top Menu* (tanpa *sidebar*) untuk optimasi lebar layar. Warna dominan: Biru (`#0d47a1` / `#0b2545` gradasi) -> Kuning (`#ffca28` aksen) -> Abu-abu. Background putih. Antarmuka harus "Premium", "Responsive" (mobile-first), dan "Fungsional".
- **Istilah:** Gunakan **NOTAR** (Nomor Taruna), dilarang menggunakan "NIT".
- **Penamaan Kelas Dinamis:** Selalu gabungkan singkatan Prodi dan Kelas untuk *display*. Contoh: "Teknologi Otomotif" Kelas "A" menjadi **"TO A"**, "Teknologi Rekayasa Otomotif" Kelas "B" menjadi **"TRO B"**, "Rekayasa Sistem Transportasi Jalan" menjadi **"RSTJ"**.
- **Format Tanggal:** Gunakan pelokalan Bahasa Indonesia di sisi *frontend* (misal: "07 Jul 2026").
- **File Upload:** Untuk menghemat ruang *server hosting* (cPanel), **DILARANG** membuat fitur *upload file fisik* ke lokal folder. Sebagai gantinya, wajib menggunakan metode **"Input Link Google Drive"** dengan tipe data *string URL*.
- **Aturan Bisnis Logbook:** 
  - Tidak boleh ada laporan (logbook) ganda di tanggal yang sama untuk satu *user*.
  - Status logbook meliputi: `pending`, `disetujui`, `revisi`, `ditolak`.
  - Logbook hanya bisa diedit dan dihapus oleh Taruna jika statusnya BUKAN "disetujui" (contoh: pending, revisi, ditolak bisa diedit/dihapus).
- **Logika Pengurutan Data (Sorting):**
  - **Laporan Global:** Diurutkan berdasarkan tanggal pelaksanaan kegiatan logbook secara menurun (`logbooks.tanggal DESC`) agar mempermudah pemantauan aktivitas harian terbaru.
  - **Validasi Pembimbing:** Diurutkan berdasarkan tanggal waktu pengiriman data secara menurun (`logbooks.created_at DESC`) agar pembimbing segera mengetahui laporan mana yang baru saja dikirim oleh Taruna untuk diverifikasi.
- **Sistem Filtering Laporan Global:**
  - Halaman Laporan Global menyediakan panel filter pencarian dinamis yang dibungkus dalam kontainer *Card* untuk menjaga kerapian tata letak UI/UX.
  - Filter yang tersedia meliputi: Tanggal Pelaporan (`tanggal`), Nama Taruna (`nama`), Kelas (`kelas`), dan Program Studi (`prodi`).
  - Filter Program Studi (`prodi`) hanya dimunculkan khusus untuk akun dengan level akses `superadmin` dan `pejabat`. Untuk level `admin_prodi`, data otomatis tersaring berdasarkan prodi masing-masing.

## 7. Standar Clean Code & Maintainability
- **Pemisahan Gaya (CSS):** DILARANG menggunakan *inline style* (seperti `style="color:red;"`) atau meletakkan blok `<style>` berukuran besar di dalam file *View*. Semua custom styling wajib disatukan ke dalam `public/css/style.css` menggunakan *class*.
- **DRY (Don't Repeat Yourself):** Jangan menduplikasi kode. Jika logika yang sama dipakai berulang kali, pindahkan menjadi satu fungsi utuh di Model, Controller, atau Helper.
- **Keterbacaan:** Berikan komentar singkat yang informatif dalam Bahasa Indonesia pada baris kode yang kompleks atau memuat alur krusial, demi kemudahan *maintainability* di masa depan.

## 8. Manajemen Role & Hierarki Sistem
- **Daftar Role Valid (Strict ENUM):** `superadmin`, `admin_prodi`, `pejabat`, `pembimbing`, `taruna`. Segala bentuk pencocokan *role* di controller/view **WAJIB** menggunakan `strtolower(session()->get('role'))` untuk menghindari *bug case-sensitive*.
- **Akses Data Pengguna (CRUD):** 
  - `superadmin`: Akses penuh ke semua data pengguna lintas prodi.
  - `admin_prodi`: Hanya bisa membuat/mengedit akun Dosen dan Taruna yang berada dalam **Prodi yang sama** dengan Admin tersebut.
- **Relasi Pembimbing (1-to-Many):** Satu dosen pembimbing dapat membimbing lebih dari satu Taruna. Taruna terkait direlasikan melalui kolom `pembimbing_id` di tabel `users`.
- **Akses Laporan & Bimbingan:**
  - `pejabat` dan `admin_prodi`: Memiliki akses menu **Laporan Global** untuk memantau rekapitulasi data.
  - `pembimbing`: Memiliki akses ke menu **Validasi Logbook** dan **Taruna Bimbingan** untuk memantau anak didiknya.

## 9. Standar Desain Premium (UI/UX Pro Max)
- **Aesthetic Principles:** Gunakan prinsip visual yang tertuang dalam [ui-ux-pro-max](file:///d:/laragon/www/magang/.agents/skills/ui-ux-pro-max/SKILL.md) untuk menciptakan interface premium. Utamakan kejelasan hirarki teks, transisi mikro yang halus (hover states, focus shadows), dan pemilihan warna harmonis.
- **Aksesibilitas (A11y):** Pastikan kontras teks terhadap latar belakang minimal 4.5:1. Gunakan elemen interaktif dengan ukuran target sentuh minimal 44x44px untuk mobile.
- **Satu Set Ikon Konsisten:** Gunakan Bootstrap Icons (via CDN) atau SVG. Hindari penggunaan emoji sebagai pengganti ikon fungsional.
- **Pemuatan Aset Efisien:** Hindari layout shifts (CLS < 0.1) dengan menentukan lebar/tinggi wadah gambar atau ikon pemuat (loader/skeleton).

## 10. Efisiensi Token & Kebijakan Verifikasi (WAJIB DIIKUTI)

### 🚫 DILARANG — Otomatis Membuka Browser Setelah Ubah Kode
Setiap kali Anda selesai mengubah atau membuat file kode, **DILARANG** menjalankan `browser_subagent` secara otomatis untuk sekadar memverifikasi perubahan visual. Browser subagent adalah operasi **paling boros token** karena:
- Memanggil sub-model terpisah (nested agent)
- Merender seluruh DOM dan mengirimkan screenshot ke konteks
- Menghabiskan kuota bahkan untuk halaman statis sederhana

### ✅ Prosedur Verifikasi yang Benar
1. **Selesaikan perubahan kode** secara tuntas dan logis.
2. **Sampaikan ringkasan perubahan** kepada pengguna secara tekstual (file mana yang berubah, apa efeknya).
3. **Minta pengguna membuka browser secara mandiri** (`http://magang.test/...`) dan melaporkan hasilnya.
4. **Hanya jalankan `browser_subagent`** jika pengguna **secara eksplisit meminta** verifikasi visual, atau ada bug yang benar-benar tidak bisa didiagnosis dari kode.

### ✅ Strategi Hemat Token Lainnya
- **Baca file secukupnya:** Jika hanya butuh satu bagian fungsi, gunakan `StartLine`/`EndLine` pada `view_file`, bukan membaca seluruh file sekaligus.
- **Gunakan `grep_search` sebelum membaca file besar** untuk menemukan lokasi kode spesifik terlebih dahulu.
- **Hindari walkthrough/artifact setiap perubahan kecil** — artifact summary cukup dibuat untuk perubahan besar atau atas permintaan pengguna.
- **Gabungkan pengeditan non-bersebelahan** dalam satu panggilan `multi_replace_file_content`, bukan membuat beberapa panggilan terpisah.
- **Tidak perlu konfirmasi berulang** untuk perubahan kecil yang jelas — langsung eksekusi dan laporkan hasilnya.

### 📋 Urutan Prodi (Gunakan di Seluruh Dropdown Aplikasi)
Seluruh dropdown yang menampilkan daftar Program Studi wajib diurutkan dengan prioritas:
1. **RSTJ** — Rekayasa Sistem Transportasi Jalan
2. **TRO** — Teknologi Rekayasa Otomotif
3. **TO** — Teknologi Otomotif
4. Prodi lain (urutan abjad)

Gunakan method `ProdiModel::getOrderedProdi()` yang sudah tersedia di `app/Models/ProdiModel.php`.

