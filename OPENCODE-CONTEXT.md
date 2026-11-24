# Konteks OpenCode — CameraService (Dinamika Kamera)

## Ringkasan aplikasi

Aplikasi lokal untuk bengkel servis kamera **Dinamika Kamera**, Purwokerto.

Stack:
- Laravel 13
- Livewire 4
- Tailwind 4
- SQLite
- Vite
- Chart.js
- Dompdf untuk PDF lokal

Bahasa UI: Indonesia.

Prinsip penting:
- Fungsi inti harus tetap bisa dipakai offline.
- WhatsApp hanya membuka `wa.me` dengan pesan siap kirim. Tidak ada pengiriman otomatis.
- PDF dibuat lokal, tidak bergantung hosting/cloud.

## Status proyek

- Fitur utama sudah lengkap: dashboard, pelanggan, penerimaan servis, daftar servis,
  katalog, estimasi, pengerjaan, pembayaran, pengeluaran, laporan, tanda terima,
  bukti pembayaran, PDF, dan WhatsApp.
- Test terakhir: 30 tests, 117 assertions, semua lulus.
- Logo: `public/images/logo.jpg`.
- Branch utama: `master`.

## File yang TIDAK boleh di-commit

- `.env`
- `database/database.sqlite`
- `database/backups/`
- `vendor/`
- `node_modules/`
- `public/build/`

Semua itu sudah masuk `.gitignore` dan harus disiapkan lokal di komputer tujuan.

## Script yang tersedia

- `start-cameraservice.bat` — membersihkan server PHP lama di port 8000,
  menjalankan CameraService, lalu membuka browser.
- `check-requirements.ps1` — memeriksa PHP, extension, vendor, asset, database,
  dan port 8000.
- `backup-database.bat` — menyalin database SQLite ke `database/backups/`
  dengan nama bertanggal.

## Menjalankan aplikasi

Cara paling mudah:

```bat
start-cameraservice.bat
```

Atau manual:

```powershell
cd <folder project>
php artisan serve --host=127.0.0.1 --port=8000
```

Lalu buka:

```text
http://127.0.0.1:8000
```

Gunakan `127.0.0.1`, bukan `localhost`, untuk menghindari konflik IPv6.

## Setup di komputer baru

Konfigurasi diatur terakhir dan tidak boleh di-commit.

1. Install PHP 8.3+ dengan extension:
   `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `curl`,
   `dom`, `xml`, `tokenizer`, `xmlwriter`, `gd`.

2. Copy folder project. `vendor/` dan `public/build/` boleh ikut dicopy,
   atau dibuat ulang:

   ```powershell
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   ```

3. Buat `.env` dari `.env.example`, lalu:

   ```powershell
   php artisan key:generate
   php artisan migrate --force
   php artisan optimize
   ```

4. Di `.env`, sesuaikan:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=http://127.0.0.1:8000`
   - `DB_CONNECTION=sqlite`
   - hapus/ubah `DB_DATABASE` agar memakai `database/database.sqlite`

Jangan pernah commit `.env`.

## Catatan penting untuk agen

- `AGENTS.md` berisi panduan Laravel Boost. Jangan jalankan `php artisan boost:install`
  kecuali benar-benar diminta.
- Jangan mengubah `.env`, `config/`, atau file konfigurasi lain kecuali diminta.
- Jika mengubah CSS/JS, jalankan `npm run build`.
- Jika mengubah `.env`, jalankan `php artisan optimize:clear`.
- Jika membuka aplikasi lewat jaringan/LAN, tambahkan authentication terlebih
  dahulu. Saat ini belum ada login.
- Port 8000 pernah bentrok dengan project lain. `start-cameraservice.bat`
  membersihkan server PHP lama di port tersebut.
- Backup cukup dengan menyalin `database/database.sqlite` atau menjalankan
  `backup-database.bat`.

## Struktur fitur utama

- `app/Livewire/Dashboard.php` — ringkasan dan modal tambah servis.
- `app/Livewire/ServiceIntake.php` — form penerimaan servis.
- `app/Livewire/Concerns/ManagesServiceIntake.php` — logika form intake bersama.
- `resources/views/livewire/partials/service-intake-form.blade.php` — partial form intake.
- `app/Livewire/ServiceList.php` — daftar servis.
- `app/Livewire/ServiceDetail.php` — detail servis.
- `app/Livewire/ServiceEstimate.php` — estimasi biaya.
- `app/Livewire/ServiceWork.php` — pengerjaan perangkat.
- `app/Livewire/ServicePayment.php` — pembayaran servis.
- `app/Livewire/Payments.php` — daftar pembayaran.
- `app/Livewire/Catalogs.php` — katalog harga.
- `app/Livewire/Expenses.php` — pengeluaran.
- `app/Livewire/Reports.php` — laporan.
- `app/Support/WhatsappMessage.php` — pembuat pesan WhatsApp.
- `resources/views/receipts/` — tanda terima dan bukti pembayaran.

## Verifikasi

```powershell
php artisan test
composer check-platform-reqs --no-dev
php artisan route:list
curl.exe -I http://127.0.0.1:8000
```

## Prompt singkat untuk OpenCode

```text
Baca OPENCODE-CONTEXT.md di root project. Jangan ubah .env, config/, composer.json,
atau file konfigurasi lain kecuali saya minta. Bantu verifikasi aplikasi CameraService:
cek composer check-platform-reqs, jalankan migration bila perlu, build asset bila perlu,
lalu jalankan start-cameraservice.bat dan pastikan http://127.0.0.1:8000 menampilkan
Dashboard Dinamika Kamera.
```
