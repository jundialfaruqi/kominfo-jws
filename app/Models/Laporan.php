<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    /** @use HasFactory<\Database\Factories\LaporanFactory> */
    use HasFactory;

    protected $fillable = [
        'id_masjid',
        'tanggal',
        'uraian',
        'jenis',
        'saldo',
        // tambahkan flag saldo awal
        'is_opening',
        'id_group_category',
    ];

    protected $casts = [
        'is_opening' => 'boolean',
        'saldo' => 'integer',
        'running_balance' => 'integer',
    ];

    /**
     * Relasi: Setiap laporan dimiliki oleh satu profil masjid.
     */
    public function profil()
    {
        return $this->belongsTo(Profil::class, 'id_masjid');
    }

    /**
     * Relasi: Setiap laporan bisa terkait ke satu GroupCategory.
     */
    public function groupCategory()
    {
        return $this->belongsTo(GroupCategory::class, 'id_group_category');
    }
}
