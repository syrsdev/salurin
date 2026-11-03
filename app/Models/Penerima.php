<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerima extends Model
{
    protected $table = 'penerima';

    // Tabel penerima pada migration saat ini tidak punya created_at/updated_at.
    public $timestamps = false;

    protected $fillable = [
        'nik',
        'no_kk',
        'nama_lengkap',
        'alamat',
        'tanggal_lahir',
        'status_data',
        'alasan_penolakan',
        'tgl_disetujui',
        'id_wilayah',
        'dibuat_oleh',
        'disetujui_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tgl_disetujui' => 'datetime',
        ];
    }

    // Relasi dipersiapkan untuk Issue 15; tidak mengubah struktur database.
    public function wilayah(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(wilayah::class, 'id_wilayah');
    }
}
