<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('siswa', function (Blueprint $table) {
      $table->id();
      $table->string('nama');
      $table->string('nisn')->unique();
      $table->integer('id_fingerprint')->nullable()->unique();
      $table->timestamp('waktu_daftar')->useCurrent();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('siswa');
  }
};
