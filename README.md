# Sistem Absensi Siswa dengan Fingerprint ESP32

Aplikasi web untuk absensi siswa menggunakan sensor fingerprint yang terhubung dengan ESP32. Dibangun dengan Laravel 11 dan Admin-LTE template.

## Fitur

### Web Admin<div align="center">

<!-- ANIMASI JUDUL PROYEK -->
<a href="https://github.com/sr464a6">
  <img src="https://readme-typing-svg.demolab.com?font=Plus+Jakarta+Sans&weight=700&size=32&duration=3200&pause=1200&color=3B82F6&center=true&vCenter=true&width=720&height=70&lines=Sistem+Absensi+Siswa+Fingerprint+ESP32;Laravel+11+%E2%80%A2+AdminLTE+3+%E2%80%A2+IoT+Integration" alt="Sistem Absensi Siswa ESP32" />
</a>

## IoT & Web-Based Student Attendance System

> *"Aplikasi web untuk absensi siswa menggunakan sensor fingerprint yang terhubung dengan **ESP32**. Dibangun dengan **Laravel 11** dan **AdminLTE 3**."*

<br/>

<!-- BADGES INFO (NUANSA BIRU) -->
<p align="center">
  <img src="https://img.shields.io/badge/Hardware-ESP32_%2B_R307%2FAS608-2563EB?style=for-the-badge" alt="Hardware ESP32" />
  &nbsp;
  <img src="https://img.shields.io/badge/Backend-Laravel_11-3B82F6?style=for-the-badge" alt="Laravel 11" />
  &nbsp;
  <img src="https://img.shields.io/badge/API-RESTful_JSON-0EA5E9?style=for-the-badge" alt="REST API" />
</p>

<br/>

<!-- TECH STACK (LOGO SAJA) -->
<p align="center">
  <img src="https://skillicons.dev/icons?i=laravel,php,mysql,bootstrap,jquery,arduino,git,github" alt="Tech Stack Logos" />
</p>

</div>

---

### ✨ Key Features

<table>
  <tr>
    <td width="50%" valign="top">
      <h4>🖥️ Web Admin Dashboard</h4>
      <ul>
        <li><b>Authentication:</b> Login/Logout sistem admin</li>
        <li><b>Dashboard Statistik:</b> Pantau ringkasan kehadiran siswa harian</li>
        <li><b>Daftar Siswa:</b> Kelola data siswa (nama auto kapital, NISN, ID fingerprint)</li>
        <li><b>Rekap Absensi:</b> Filter tanggal otomatis dengan server-side <b>DataTables</b></li>
      </ul>
    </td>
    <td width="50%" valign="top">
      <h4>🔌 ESP32 RESTful API</h4>
      <ul>
        <li><b>Check Enroll:</b> Polling pengecekan siswa baru untuk pendaftaran sidik jari</li>
        <li><b>Confirm Enroll:</b> Konfirmasi otomatis saat sidik jari berhasil disimpan</li>
        <li><b>Scan Absensi:</b> Pengiriman data scan sidik jari real-time ke server</li>
        <li><b>Sync List:</b> Sinkronisasi seluruh daftar ID fingerprint</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td width="50%" valign="top">
      <h4>👆 Remote Fingerprint Enrollment</h4>
      <ul>
        <li>Trigger proses pendaftaran sidik jari langsung dari halaman Web Admin</li>
        <li>Instruksi interaktif tampil langsung pada layar <b>LCD 16x2 I2C</b> perangkat</li>
        <li>ID fingerprint tersimpan otomatis ke database setelah scan berhasil</li>
      </ul>
    </td>
    <td width="50%" valign="top">
      <h4>📝 Form Izin & Sakit</h4>
      <ul>
        <li>Input manual status <b>Izin</b> atau <b>Sakit</b> berdasarkan daftar siswa</li>
        <li>Dilengkapi kolom alasan dan fitur <b>upload foto bukti</b> (opsional)</li>
        <li>Data langsung terintegrasi dengan laporan rekap harian</li>
      </ul>
    </td>
  </tr>
</table>

---

### 🚀 Installation, Hardware & API Guide

<!-- TOMBOL KLIK BUKA-TUTUP 1: CARA CLONE & INSTALASI -->
<details>
  <summary><b>📄 How to Run and Cloning (Web & Database Setup)</b></summary>
  <br/>

  <blockquote>
    <h4>📋 Panduan Instalasi Lokal</h4>
    <p><i>Pastikan <b>PHP >= 8.2</b>, <b>Composer</b>, <b>MySQL/MariaDB</b>, serta <b>Node.js & NPM</b> (opsional) sudah terpasang.</i></p>

    1. Clone Repository & Install Dependencies
    git clone <repository-url> absensi-siswa
    cd absensi-siswa
    composer install

    2. Setup Environment (.env)
    cp .env.example .env
    php artisan key:generate

    Sesuaikan konfigurasi database di file .env:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=absensi_siswa
    DB_USERNAME=root
    DB_PASSWORD=

    3. Database Setup & Seeding
    mysql -u root -p -e "CREATE DATABASE absensi_siswa"
    php artisan migrate
    php artisan db:seed --class=AdminSeeder
    php artisan storage:link

    4. Jalankan Server Lokal
    php artisan serve
    (Akses di browser: http://localhost:8000)

    🔑 Login Default Admin:
    • Email    : admin@absensi.com
    • Password : admin123
  </blockquote>

</details>

<br/>

<!-- TOMBOL KLIK BUKA-TUTUP 2: WIRING ESP32, DATABASE & API -->
<details>
  <summary><b>🔌 Hardware Wiring, API Endpoints & Database Structure</b></summary>
  <br/>

  <blockquote>
    <h4>🛠️ Komponen & Wiring ESP32</h4>
    <p><b>Komponen:</b> ESP32 Dev Board, Sensor Fingerprint R307/AS608, LCD 16x2 I2C (opsional), Buzzer (opsional).</p>
    <ul>
      <li><b>Fingerprint TX</b> → ESP32 RX (<code>GPIO 16</code>)</li>
      <li><b>Fingerprint RX</b> → ESP32 TX (<code>GPIO 17</code>)</li>
      <li><b>LCD SDA</b> → ESP32 <code>GPIO 21</code></li>
      <li><b>LCD SCL</b> → ESP32 <code>GPIO 22</code></li>
    </ul>

    <hr/>

    <h4>🌐 API Endpoints (Base URL: <code>/api/fingerprint</code>)</h4>
    <ul>
      <li><code>GET /check-enroll</code> — Cek apakah ada siswa baru yang perlu dienroll</li>
      <li><code>POST /confirm-enroll</code> — Konfirmasi enroll fingerprint berhasil</li>
      <li><code>POST /scan</code> — Kirim data scan fingerprint untuk absensi</li>
      <li><code>GET /list</code> — Ambil & sinkronisasi semua data fingerprint</li>
    </ul>

    <hr/>

    <h4>🗄️ Struktur Tabel Database</h4>
    <ul>
      <li><b>Tabel <code>siswa</code>:</b> <code>id</code>, <code>nama</code> (auto kapital), <code>nisn</code> (unique), <code>id_fingerprint</code> (unique, nullable), <code>waktu_daftar</code></li>
      <li><b>Tabel <code>absensi</code>:</b> <code>id</code>, <code>siswa_id</code>, <code>nama</code>, <code>keterangan</code> (hadir/izin/sakit), <code>alasan</code>, <code>foto</code>, <code>jam</code>, <code>tanggal</code></li>
    </ul>

    <hr/>

    <h4>📂 File Penting & Cara Penggunaan</h4>
    <ul>
      <li><code>ESP32_FINGERPRINT_SKETCH.ino</code> — Kode Arduino IDE untuk ESP32</li>
      <li><code>API_DOCUMENTATION.md</code> — Dokumentasi lengkap payload & response API</li>
      <li><code>app/Http/Controllers/Api/FingerprintController.php</code> — Logic endpoint ESP32</li>
      <li><b>Alur Enroll:</b> Buka menu <i>Enroll</i> → Isi Nama & NISN → Klik Enroll → Tempelkan jari sesuai instruksi LCD ESP32.</li>
      <li><b>Alur Absensi:</b> Siswa scan jari saat ESP32 standby → Data otomatis masuk ke rekap & muncul status di LCD.</li>
    </ul>
  </blockquote>

</details>

<br/>

<!-- TOMBOL KLIK BUKA-TUTUP 3: TROUBLESHOOTING -->
<details>
  <summary><b>🛠️ Troubleshooting Cepat</b></summary>
  <br/>

  <blockquote>
    <ul>
      <li><b>Login gagal:</b> Pastikan sudah menjalankan <code>php artisan db:seed --class=AdminSeeder</code>.</li>
      <li><b>Assets CSS/JS AdminLTE tidak load:</b> Pastikan folder <code>public/adminlte</code> tersedia dan berisi file asset.</li>
      <li><b>Upload foto izin/sakit error:</b> Jalankan perintah <code>php artisan storage:link</code>.</li>
      <li><b>ESP32 gagal terhubung ke server:</b> Pastikan ESP32 dan komputer/server berada dalam satu jaringan Wi-Fi yang sama dan alamat IP server di sketch sudah sesuai.</li>
    </ul>
  </blockquote>

</details>

---

### 📄 License

This project is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).

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
