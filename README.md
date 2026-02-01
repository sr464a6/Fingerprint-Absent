# Sistem Absensi Siswa dengan Fingerprint ESP32

Aplikasi web untuk absensi siswa menggunakan sensor fingerprint yang terhubung dengan ESP32. Dibangun dengan Laravel 11 dan Admin-LTE template.

## Fitur

### Web Admin

-   ✅ Login/Logout Authentication
-   ✅ Dashboard dengan statistik absensi harian
-   ✅ **Daftar Siswa** - Kelola data siswa dengan nama auto kapital, NISN, ID fingerprint
-   ✅ **Absensi** - Lihat rekap absensi dengan filter tanggal (DataTables)
-   ✅ **Form Izin/Sakit** - Input izin/sakit dengan upload bukti (optional)
-   ✅ **Enroll Fingerprint** - Daftarkan siswa baru dan trigger ESP32 untuk enroll

### API untuk ESP32

-   ✅ Check enroll - Polling untuk cek siswa baru yang perlu dienroll
-   ✅ Confirm enroll - Konfirmasi fingerprint berhasil didaftarkan
-   ✅ Scan absensi - Kirim data fingerprint untuk absensi
-   ✅ Get fingerprint list - Sync semua data fingerprint

## Teknologi

-   **Backend:** Laravel 11
-   **Frontend:** Admin-LTE 3, Bootstrap 4, jQuery
-   **Database:** MySQL
-   **DataTables:** Server-side processing
-   **API:** RESTful API untuk ESP32
-   **Hardware:** ESP32 + Fingerprint Sensor (R307/AS608)

## Requirements

-   PHP >= 8.2
-   Composer
-   MySQL/MariaDB
-   Node.js & NPM (optional, untuk compile assets)

## Instalasi

### 1. Clone & Install Dependencies

```bash
git clone <repository-url> absensi-siswa
cd absensi-siswa
composer install
```

### 2. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` sesuaikan database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_siswa
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Database Setup

```bash
# Buat database
mysql -u root -p -e "CREATE DATABASE absensi_siswa"

# Jalankan migrations
php artisan migrate

# Seed admin user
php artisan db:seed --class=AdminSeeder

# Setup storage link untuk upload foto
php artisan storage:link
```

### 4. Jalankan Server

```bash
php artisan serve
```

Akses: `http://localhost:8000`

**Login Default:**

-   Email: `admin@absensi.com`
-   Password: `admin123`

## Struktur Database

### Tabel: siswa

| Field          | Type      | Keterangan                                    |
| -------------- | --------- | --------------------------------------------- |
| id             | bigint    | Primary key                                   |
| nama           | varchar   | Nama siswa (auto kapital)                     |
| nisn           | varchar   | NISN (unique)                                 |
| id_fingerprint | int       | ID dari sensor fingerprint (unique, nullable) |
| waktu_daftar   | timestamp | Waktu pendaftaran                             |

### Tabel: absensi

| Field      | Type      | Keterangan                         |
| ---------- | --------- | ---------------------------------- |
| id         | bigint    | Primary key                        |
| siswa_id   | bigint    | Foreign key ke tabel siswa         |
| nama       | varchar   | Nama siswa (copy dari tabel siswa) |
| keterangan | enum      | hadir / izin / sakit               |
| alasan     | text      | Alasan jika izin/sakit (nullable)  |
| foto       | varchar   | Path foto bukti (nullable)         |
| jam        | timestamp | Waktu absensi                      |
| tanggal    | date      | Tanggal absensi                    |

## Penggunaan

### 1. Enroll Siswa Baru

1. Buka menu **Enroll**
2. Isi form:
    - Nama (akan auto kapital)
    - NISN (angka)
3. Klik **Enroll**
4. ESP32 akan mulai proses enroll fingerprint
5. Siswa tempelkan jari sesuai instruksi di LCD
6. Setelah berhasil, ID fingerprint akan tersimpan otomatis

### 2. Absensi dengan Fingerprint

1. ESP32 dalam mode standby
2. Siswa scan jari pada sensor
3. Data otomatis masuk ke tabel absensi
4. Tampil notifikasi di LCD (berhasil/gagal)

### 3. Input Izin/Sakit Manual

1. Buka menu **Izin**
2. Pilih nama siswa dari dropdown
3. Pilih keterangan (Izin/Sakit)
4. Isi alasan
5. Upload foto bukti (optional)
6. Submit - data masuk ke tabel absensi

### 4. Lihat Rekap Absensi

1. Buka menu **Absensi**
2. Pilih tanggal di datepicker
3. Tabel otomatis reload dengan DataTables
4. Lihat detail: nama, keterangan, jam, alasan, foto

## API Endpoints

### Base URL: `/api/fingerprint`

| Method | Endpoint          | Keterangan                               |
| ------ | ----------------- | ---------------------------------------- |
| GET    | `/check-enroll`   | Cek apakah ada siswa yang perlu dienroll |
| POST   | `/confirm-enroll` | Konfirmasi enroll berhasil               |
| POST   | `/scan`           | Kirim data scan fingerprint              |
| GET    | `/list`           | Ambil semua data fingerprint             |

**Dokumentasi lengkap:** Lihat file `API_DOCUMENTATION.md`

## ESP32 Setup

**Komponen:**

-   ESP32 Dev Board
-   Sensor Fingerprint R307/AS608
-   LCD 16x2 I2C (optional)
-   Buzzer (optional)

**Wiring:**

-   Fingerprint TX → ESP32 RX (GPIO 16)
-   Fingerprint RX → ESP32 TX (GPIO 17)
-   LCD SDA → ESP32 GPIO 21
-   LCD SCL → ESP32 GPIO 22

**Sketch Arduino:** Lihat file `ESP32_FINGERPRINT_SKETCH.ino`

## File Penting

-   `API_DOCUMENTATION.md` - Dokumentasi API untuk ESP32
-   `ESP32_FINGERPRINT_SKETCH.ino` - Kode Arduino untuk ESP32
-   `routes/web.php` - Route web admin
-   `routes/api.php` - Route API untuk ESP32
-   `app/Http/Controllers/Api/FingerprintController.php` - Logic API

## Troubleshooting

**Problem:** Login gagal  
**Solusi:** Pastikan sudah run `php artisan db:seed --class=AdminSeeder`

**Problem:** Assets CSS/JS tidak load  
**Solusi:** Pastikan folder `public/adminlte` ada dan berisi file CSS/JS

**Problem:** Upload foto error  
**Solusi:** Jalankan `php artisan storage:link`

**Problem:** DataTables error  
**Solusi:** Clear cache browser atau cek console untuk error

**Problem:** ESP32 tidak bisa connect  
**Solusi:** Pastikan ESP32 dan server dalam satu network, cek IP server

## Credits

-   Laravel 11
-   Admin-LTE 3
-   DataTables
-   Font Awesome
-   Bootstrap 4

## License

MIT License

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
