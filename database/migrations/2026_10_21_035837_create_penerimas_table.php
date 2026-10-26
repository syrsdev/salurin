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
        Schema::create('penerima', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->string('no_kk', 16)->nullable();
            $table->string('nama_lengkap', 100);
            $table->text('alamat')->nullable();
            $table->date('tanggal_lahir')->nullable();
            // nilai: menunggu | disetujui | ditolak
            $table->string('status_data', 20)->default('menunggu');
            $table->dateTime('tgl_disetujui')->nullable();

            $table->foreignId('id_wilayah')->constrained('wilayah')->restrictOnDelete();
            $table->foreignId('dibuat_oleh')->constrained('users')->restrictOnDelete();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerima');
    }
};