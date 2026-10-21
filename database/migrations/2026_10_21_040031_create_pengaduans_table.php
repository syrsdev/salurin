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
            $table->foreignId('id_penerima')->constrained('penerima');
            $table->foreignId('id_penyaluran')->constrained('penyaluran');
            $table->foreignId('ditangani_oleh')->constrained('users');

            // nilai: belum_cair | salah_sasaran | data_salah | lainnya
            $table->string('kategori', 50)->nullable();
            $table->text('isi_pengaduan');
            // nilai: baru | diproses | eskalasi | selesai
            $table->string('status_pengaduan', 20)->default('baru');
            $table->dateTime('tgl_pengaduan')->useCurrent();
            $table->dateTime('tgl_tanggapan')->nullable();

            // $table->foreign('id_penerima')
            //     ->references('id_penerima')->on('penerima')
            //     ->nullOnDelete();
            // $table->foreign('id_penyaluran')
            //     ->references('id_penyaluran')->on('penyaluran')
            //     ->nullOnDelete();
            // $table->foreign('ditangani_oleh')
            //     ->references('id')->on('users')
            //     ->nullOnDelete();
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