<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BuktiPenyaluran extends Model
{
    protected $table = 'bukti_penyaluran';

    protected $appends = ['url'];

    protected $fillable = [
        'id_penyaluran',
        'path_file',
        'tgl_unggah',
    ];

    protected function casts(): array
    {
        return [
            'tgl_unggah' => 'datetime',
        ];
    }

    public function penyaluran(): BelongsTo
    {
        return $this->belongsTo(Penyaluran::class, 'id_penyaluran');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path_file);
    }
}
