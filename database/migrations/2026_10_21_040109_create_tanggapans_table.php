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
        Schema::create('tanggapan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pengaduan')->constrained('pengaduan');
            $table->foreignId('id_pengguna')->constrained('users');
            $table->text('isi_tanggapan');
            $table->dateTime('waktu')->useCurrent();

            // $table->foreign('id_pengaduan')
            //     ->references('id_pengaduan')->on('pengaduan')
            //     ->cascadeOnDelete();
            // $table->foreign('id_pengguna')
            //     ->references('id')->on('users')
            //     ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanggapan');
    }
};