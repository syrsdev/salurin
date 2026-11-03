<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditTrail extends Model
{
    protected $table = 'audit_trail';

    protected $fillable = [
        'id_pengguna',
        'nama_tabel',
        'id_record',
        'aksi',
        'data_lama',
        'data_baru',
        'waktu',
    ];

    protected function casts(): array
    {
        return [
            'data_lama' => 'array',
            'data_baru' => 'array',
            'waktu'     => 'datetime',
        ];
    }

    // Pintasan pencatatan: AuditTrail::catat('penerima', 5, 'update', $lama, $baru);
    public static function catat(string $tabel, int $idRecord, string $aksi, ?array $lama, ?array $baru): self
    {
        return static::create([
            'id_pengguna' => Auth::id(),
            'nama_tabel'  => $tabel,
            'id_record'   => $idRecord,
            'aksi'        => $aksi,
            'data_lama'   => $lama,
            'data_baru'   => $baru,
            'waktu'       => now(),
        ]);
    }
}
