<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'terdaftar' => 'Terdaftar',
        'diverifikasi' => 'Diverifikasi',
        'dijadwalkan' => 'Dijadwalkan',
        'cair' => 'Cair',
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

    public function jenisBantuan(): BelongsTo
    {
        return $this->belongsTo(JenisBantuan::class, 'id_bantuan');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'id_periode');
    }

    public function buktiPenyalurans(): HasMany
    {
        return $this->hasMany(BuktiPenyaluran::class, 'id_penyaluran');
    }

    public function canTransitionTo(string $status): bool
    {
        $statuses = array_keys(self::STATUS);
        $currentPosition = array_search($this->status_penyaluran, $statuses, true);
        $nextPosition = array_search($status, $statuses, true);

        return $currentPosition !== false
            && $nextPosition !== false
            && $nextPosition >= $currentPosition;
    }
}
