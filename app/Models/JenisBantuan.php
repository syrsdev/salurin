<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisBantuan extends Model
{
     protected $table = 'jenis_bantuan';

    protected $fillable = ['nama_bantuan', 'nominal', 'deskripsi'];

    public function penyalurans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Penyaluran::class, 'id_bantuan');
    }

    protected function casts(): array
    {
        return ['nominal' => 'decimal:2'];
    }
}
