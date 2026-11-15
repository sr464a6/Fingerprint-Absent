<div align="center">

# Sistem Absensi Siswa dengan Fingerprint ESP32

> *"Aplikasi web absensi siswa terintegrasi sensor sidik jari (R307/AS608) dan mikrokontroler **ESP32** berbasis **Laravel 11** & **AdminLTE 3**."*

<br/>

<!-- ANIMASI HARDWARE & PROSES ABSENSI FINGERPRINT -->
<img src="https://readme-typing-svg.demolab.com?font=Fira+Code&weight=600&size=18&duration=2800&pause=1000&color=3B82F6&background=0F172A&center=true&vCenter=true&width=680&height=65&lines=%5BESP32%5D+Sensor+R307+Standby...+%F0%9F%91%86;%5BSCAN%5D+Verifying+Fingerprint+ID+%2301...+%F0%9F%94%8D;%5BAPI%5D+POST+%2Fapi%2Ffingerprint%2Fscan+-%3E+200+OK+%E2%9C%85;%5BLCD+16x2%5D+Absensi+Berhasil+Tercatat!+%F0%9F%8E%93" alt="Animasi Proses Absensi Fingerprint ESP32" />

<br/><br/>

<!-- TECH STACK (LOGO SAJA - HANYA SATU BARIS) -->
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
        <li>Trigger pendaftaran sidik jari langsung dari halaman Web Admin</li>
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

<!-- DROPDOWN 1: HOW TO RUN AND CLONING -->
<details>
  <summary><b>📄 How to Run and Cloning</b></summary>
  <br/>

  <blockquote>
    <h4>📋 Panduan Instalasi Lokal</h4>
    <p><i>Pastikan <b>PHP >= 8.2</b>, <b>Composer</b>, <b>MySQL/MariaDB</b>, serta <b>Node.js & NPM</b> (opsional) sudah terpasang.</i></p>

    1. Clone Repository & Install Dependencies
    git clone https://github.com/sr464a6/Fingerprint-Absent.git
    cd Fingerprint-Absent
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

<!-- DROPDOWN 2: HARDWARE WIRING, API & DATABASE -->
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

    <h4>📂 File Penting & Alur Penggunaan</h4>
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

<!-- DROPDOWN 3: TROUBLESHOOTING -->
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
