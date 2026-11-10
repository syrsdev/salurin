<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';

    protected $fillable = [
        'kode_tiket',
        'id_penerima',
        'id_penyaluran',
        'ditangani_oleh',
        'id_wilayah',
        'kategori',
        'isi_pengaduan',
        'status_pengaduan',
        'tgl_pengaduan',
        'tgl_tanggapan',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pengaduan' => 'datetime',
            'tgl_tanggapan' => 'datetime',
        ];
    }

    public const KATEGORI = [
        'belum_cair' => 'Bantuan belum cair',
        'salah_sasaran' => 'Salah sasaran',
        'data_salah' => 'Data salah',
        'lainnya' => 'Lainnya',
    ];

    public const STATUS = [
        'baru' => 'Baru',
        'diproses' => 'Diproses',
        'eskalasi' => 'Eskalasi',
        'selesai' => 'Selesai',
    ];

    // Kode tiket unik per bulan, contoh: ADU-2610-001
    public static function buatKodeTiket(): string
    {
        $prefix = 'ADU-'.now()->format('ym').'-';

        $terakhir = static::where('kode_tiket', 'like', $prefix.'%')
            ->orderByDesc('kode_tiket')
            ->value('kode_tiket');

        $urutan = $terakhir ? ((int) substr($terakhir, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);
    }

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(wilayah::class, 'id_wilayah');
    }

    public function tanggapans(): HasMany
    {
        return $this->hasMany(Tanggapan::class, 'id_pengaduan');
    }
}
