<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Durasi extends Model
{
    use HasFactory;

    protected $table = 'durasi';

    protected $fillable = [
        'user_id',
        'adzan_imsak',
        'adzan_shuruq',
        'adzan_dhuha',
        'adzan_shubuh',
        'iqomah_shubuh',
        'final_shubuh',
        'adzan_dzuhur',
        'iqomah_dzuhur',
        'final_dzuhur',
        'jumat_slide',
        'adzan_ashar',
        'iqomah_ashar',
        'final_ashar',
        'adzan_maghrib',
        'iqomah_maghrib',
        'final_maghrib',
        'adzan_isya',
        'iqomah_isya',
        'final_isya',
        'finance_scroll_speed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
