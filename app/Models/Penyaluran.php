<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penyaluran extends Model
{
    protected $table = 'penyaluran';

    protected $fillable = [
        'id_penerima',
        'id_bantuan',
        'id_periode',
        'id_pengguna',
        'status_penyaluran',
        'tgl_penyaluran',
        'catatan',
    ];

    public const STATUS = [
        'terdaftar'    => 'Terdaftar',
        'diverifikasi' => 'Diverifikasi',
        'dijadwalkan'  => 'Dijadwalkan',
        'cair'         => 'Cair',
    ];

    protected function casts(): array
    {
        return [
            'tgl_penyaluran' => 'date:Y-m-d',
        ];
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(Penerima::class, 'id_penerima');
    }
}
