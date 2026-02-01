<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
  use HasFactory;

  protected $table = 'siswa';

  protected $fillable = [
    'nama',
    'nisn',
    'id_fingerprint',
    'waktu_daftar'
  ];

  protected $casts = [
    'waktu_daftar' => 'datetime',
  ];

  // Mutator untuk auto capitalize nama
  public function setNamaAttribute($value)
  {
    $this->attributes['nama'] = strtoupper($value);
  }

  // Relasi ke absensi
  public function absensi()
  {
    return $this->hasMany(Absensi::class);
  }
}
