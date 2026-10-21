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
        Schema::create('audit_trail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengguna')->constrained('users');
            $table->string('nama_tabel', 50);
            $table->unsignedInteger('id_record');
            // nilai: create | update | delete
            $table->string('aksi', 20);
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->dateTime('waktu')->useCurrent();

            // restrict: log audit tidak boleh hilang walau akun pengguna dihapus
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
        Schema::dropIfExists('audit_trail');
    }
};