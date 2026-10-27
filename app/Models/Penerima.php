<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penerima extends Model
{
    protected $table = 'penerima';

    // Tabel penerima pada migration saat ini tidak punya created_at/updated_at.
    public $timestamps = false;

    // Relasi dipersiapkan untuk Issue 15; tidak mengubah struktur database.
    public function wilayah(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(wilayah::class, 'id_wilayah');
    }
}
