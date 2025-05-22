<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalSholat extends Model
{
    /** @use HasFactory<\Database\Factories\JadwalSholatFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_jadwal',
        'dzuhur',
        'ashar',
        'maghrib',
        'isya',
        'tanggal',
        'bulan',
        'tahun',
        'azan',
        'iqomah'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
