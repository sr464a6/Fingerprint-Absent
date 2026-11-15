<div align="center">

# Sistem Absensi Siswa dengan Fingerprint ESP32

> *"Aplikasi web absensi siswa terintegrasi sensor sidik jari (R307/AS608) dan mikrokontroler **ESP32** berbasis **Laravel 11** & **AdminLTE 3**."*

<br/>

<img src="https://readme-typing-svg.demolab.com?font=Fira+Code&weight=600&size=18&duration=2800&pause=1000&color=3B82F6&background=0F172A&center=true&vCenter=true&width=680&height=65&lines=%5BESP32%5D+Sensor+R307+Standby...+%F0%9F%91%86;%5BSCAN%5D+Verifying+Fingerprint+ID+%2301...+%F0%9F%94%8D;%5BAPI%5D+POST+%2Fapi%2Ffingerprint%2Fscan+-%3E+200+OK+%E2%9C%85;%5BLCD+16x2%5D+Absensi+Berhasil+Tercatat!+%F0%9F%8E%93" alt="Animasi Proses Absensi Fingerprint ESP32" />

<br/><br/>

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

<details>
<summary><b>📄 How to Run and Cloning</b></summary>

<br/>

#### 📋 Panduan Instalasi Lokal
*Pastikan **PHP >= 8.2**, **Composer**, **MySQL/MariaDB**, serta **Node.js & NPM** (opsional) sudah terpasang.*

**1. Clone Repository & Install Dependencies**
```bash
git clone [https://github.com/sr464a6/Fingerprint-Absent.git](https://github.com/sr464a6/Fingerprint-Absent.git)
cd Fingerprint-Absent
composer install
