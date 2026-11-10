<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(wilayah::class, 'id_wilayah');
    }

    public function penyalurans(): HasMany
    {
        return $this->hasMany(Penyaluran::class, 'id_penerima');
    }

    public function scopeMatchingIdentity(Builder $query, string $nik, string $noKk, string $tanggalLahir): void
    {
        $query->where('nik', $nik)
            ->where('no_kk', $noKk)
            ->whereDate('tanggal_lahir', $tanggalLahir);
    }
}
