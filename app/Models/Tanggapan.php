<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tanggapan extends Model
{
    protected $table = 'tanggapan';

    protected $fillable = [
        'id_pengaduan',
        'id_pengguna',
        'isi_tanggapan',
        'waktu',
    ];

    protected function casts(): array
    {
        return [
            'waktu' => 'datetime',
        ];
    }

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }
}
