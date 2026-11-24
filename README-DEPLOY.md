# Panduan Deploy CameraService

Aplikasi servis kamera **Dinamika Kamera, Purwokerto**.

## Ringkasan

- Laravel 13
- Livewire 4
- Tailwind 4
- SQLite
- Vite
- Chart.js
- Dompdf untuk PDF lokal

Aplikasi dirancang untuk berjalan lokal/offline. WhatsApp hanya membuka `wa.me`
dengan pesan siap kirim dan tidak mengirim pesan secara otomatis.

## Yang tidak ikut Git

File/folder berikut sengaja diabaikan dan harus disiapkan di komputer tujuan:

- `.env`
- `database/database.sqlite`
- `vendor/`
- `node_modules/`
- `public/build/`

Logo sudah ikut repository di `public/images/logo.jpg`.

## Kebutuhan komputer tujuan

- Windows 10/11
- PHP 8.3 atau lebih baru
- Extension PHP:
  - `pdo_sqlite`
  - `sqlite3`
  - `mbstring`
  - `openssl`
  - `fileinfo`
  - `curl`
  - `dom`
  - `xml`
  - `tokenizer`
  - `xmlwriter`
  - `gd`
- Laragon, Laravel Herd, atau PHP yang sudah masuk `PATH`
- Node.js hanya diperlukan jika ingin membangun ulang asset frontend

## Cara cepat di komputer tujuan

1. Copy folder project, misalnya ke:

   ```text
   C:\CameraService
   ```

2. Jalankan pengecekan:

   ```powershell
   powershell -ExecutionPolicy Bypass -File check-requirements.ps1
   ```

3. Jika `vendor` belum ada:

   ```powershell
   composer install --no-dev --optimize-autoloader
   ```

4. Jika `public/build` belum ada:

   ```powershell
   npm install
   npm run build
   ```

5. Buat `.env` dari `.env.example`, lalu sesuaikan:

   ```env
   APP_NAME=CameraService
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=http://127.0.0.1:8000
   APP_LOCALE=id
   DB_CONNECTION=sqlite
   ```

   Hapus atau sesuaikan `DB_DATABASE` agar memakai:

   ```text
   database/database.sqlite
   ```

6. Generate key dan migrasi:

   ```powershell
   php artisan key:generate
   php artisan migrate --force
   php artisan optimize
   ```

7. Jalankan aplikasi dengan double-click:

   ```text
   start-cameraservice.bat
   ```

   Atau manual:

   ```powershell
   php artisan serve --host=127.0.0.1 --port=8000
   ```

8. Buka:

   ```text
   http://127.0.0.1:8000
   ```

Gunakan `127.0.0.1`, bukan `localhost`, untuk menghindari konflik IPv6.

## Membawa data lama

Jika ingin membawa data dari komputer lama:

1. Tutup aplikasi di kedua komputer.
2. Copy file:

   ```text
   database/database.sqlite
   ```

   dari komputer lama ke folder `database/` komputer tujuan.

3. Jalankan migrasi untuk memastikan skema terbaru:

   ```powershell
   php artisan migrate --force
   ```

## Backup

Gunakan:

```text
backup-database.bat
```

Hasil backup disimpan di:

```text
database/backups/
```

Folder tersebut tidak akan ikut Git karena file `*.sqlite*` diabaikan.

## Troubleshooting port 8000

Jika yang muncul project lain, biasanya ada server PHP lama yang masih berjalan
di port `8000`.

`start-cameraservice.bat` sudah mencoba membersihkan server PHP lama di port
tersebut. Jika port masih dipakai aplikasi non-PHP, tutup aplikasi tersebut atau
jalankan CameraService di port lain:

```powershell
php artisan serve --host=127.0.0.1 --port=8001
```

Cek port:

```powershell
Get-NetTCPConnection -LocalPort 8000 -State Listen
```

## Akses dari komputer lain / LAN

Saat ini aplikasi belum memiliki login.

- Untuk satu komputer toko: aman dipakai lokal.
- Untuk diakses dari komputer/perangkat lain lewat jaringan: tambahkan
  authentication terlebih dahulu, lalu jalankan server dengan host yang sesuai.

Jangan membuka aplikasi ke internet tanpa login dan konfigurasi keamanan.

## Setelah mengubah kode

Jika mengubah CSS/JS:

```powershell
npm run build
```

Jika mengubah `.env`:

```powershell
php artisan optimize:clear
```

## Verifikasi

```powershell
php artisan test
composer check-platform-reqs --no-dev
php artisan route:list
```

## Untuk OpenCode di komputer tujuan

Baca file:

```text
OPENCODE-CONTEXT.md
```

File tersebut berisi ringkasan proyek, aturan kerja, dan langkah verifikasi.
