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
        Schema::create('penyaluran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penerima')->constrained('penerima')->restrictOnDelete();
            $table->foreignId('id_bantuan')->constrained('jenis_bantuan')->restrictOnDelete();
            $table->foreignId('id_periode')->constrained('periode')->restrictOnDelete();
            $table->foreignId('id_pengguna')->constrained('users')->restrictOnDelete();

            // nilai: terdaftar | diverifikasi | dijadwalkan | cair
            $table->string('status_penyaluran', 20)->default('terdaftar');
            $table->date('tgl_penyaluran')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyaluran');
    }
};