<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel users untuk authentication
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nama_lengkap');
                $table->string('email')->unique();
                $table->string('username')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 1. Tabel alternatif
        if (!Schema::hasTable('alternatif')) {
            Schema::create('alternatif', function (Blueprint $table) {
                $table->id();
                $table->string('nama_alternatif');
                $table->timestamps();
            });
        }

        // 2. Tabel kriteria
        if (!Schema::hasTable('kriteria')) {
            Schema::create('kriteria', function (Blueprint $table) {
                $table->id();
                $table->string('kode_kriteria');
                $table->string('nama_kriteria');
                $table->decimal('bobot', 5, 3);
                $table->enum('jenis', ['Benefit', 'Cost']);
                $table->timestamps();
            });
        }

        // 3. Tabel sub_kriteria
        if (!Schema::hasTable('sub_kriteria')) {
            Schema::create('sub_kriteria', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
                $table->string('nama_sub_kriteria');
                $table->decimal('nilai', 5, 2);
                $table->timestamps();
            });
        }

        // 4. Tabel penilaian
        if (!Schema::hasTable('penilaian')) {
            Schema::create('penilaian', function (Blueprint $table) {
                $table->id();
                $table->foreignId('alternatif_id')->constrained('alternatif')->onDelete('cascade');
                $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
                $table->foreignId('sub_kriteria_id')->constrained('sub_kriteria')->onDelete('cascade');
                $table->decimal('nilai', 8, 2);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
        Schema::dropIfExists('sub_kriteria');
        Schema::dropIfExists('kriteria');
        Schema::dropIfExists('alternatif');
        Schema::dropIfExists('users');
    }
};