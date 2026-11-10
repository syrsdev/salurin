<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    protected $table = 'periode';

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date:Y-m-d',
            'tgl_selesai' => 'date:Y-m-d',
        ];
    }
}
