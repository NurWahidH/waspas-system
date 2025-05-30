<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tabel alternatif
        Schema::create('alternatif', function (Blueprint $table) {
            $table->id();
            $table->string('nama_alternatif');
            $table->timestamps();
        });

        // 2. Tabel kriteria
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kriteria');
            $table->string('nama_kriteria');
            $table->decimal('bobot', 5, 3);
            $table->enum('jenis', ['Benefit', 'Cost']);
            $table->timestamps();
        });

        // 3. Tabel sub_kriteria
        Schema::create('sub_kriteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
            $table->string('nama_sub_kriteria');
            $table->decimal('nilai', 5, 2);
            $table->timestamps();
        });

        // 4. Tabel penilaian
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternatif_id')->constrained('alternatif')->onDelete('cascade');
            $table->foreignId('kriteria_id')->constrained('kriteria')->onDelete('cascade');
            $table->foreignId('sub_kriteria_id')->constrained('sub_kriteria')->onDelete('cascade');
            $table->decimal('nilai', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
        Schema::dropIfExists('sub_kriteria');
        Schema::dropIfExists('kriteria');
        Schema::dropIfExists('alternatif');
    }
};
