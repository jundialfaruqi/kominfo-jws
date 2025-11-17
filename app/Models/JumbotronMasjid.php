<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JumbotronMasjid extends Model
{
    /** @use HasFactory<\Database\Factories\JumbotronMasjidFactory> */
    use HasFactory;

    protected $fillable = [
        'masjid_id',
        'created_by',
        'jumbotron_masjid_1',
        'jumbotron_masjid_2',
        'jumbotron_masjid_3',
        'jumbotron_masjid_4',
        'jumbotron_masjid_5',
        'jumbotron_masjid_6',
        'aktif',
    ];

    public function profilMasjid()
    {
        return $this->belongsTo(Profil::class, 'masjid_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
