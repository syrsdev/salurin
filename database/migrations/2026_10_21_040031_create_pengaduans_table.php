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
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tiket', 30)->unique();
            $table->foreignId('id_penerima')->nullable()->constrained('penerima')->nullOnDelete();
            $table->foreignId('id_penyaluran')->nullable()->constrained('penyaluran')->nullOnDelete();
            $table->foreignId('ditangani_oleh')->nullable()->constrained('users')->nullOnDelete();

            // nilai: belum_cair | salah_sasaran | data_salah | lainnya
            $table->string('kategori', 50)->nullable();
            $table->text('isi_pengaduan');
            // nilai: baru | diproses | eskalasi | selesai
            $table->string('status_pengaduan', 20)->default('baru');
            $table->dateTime('tgl_pengaduan')->useCurrent();
            $table->dateTime('tgl_tanggapan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};