<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    /** @use HasFactory<\Database\Factories\ProfilFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'logo_masjid',
        'logo_pemerintah',
        'slug',
    ];
    protected $hidden = [
        'logo_masjid_url',
        'logo_pemerintah_url',
    ];

    protected $appends = [
        'logo_masjid_url',
        'logo_pemerintah_url',
    ];

    // Additional Method
    public function getLogoMasjidUrlAttribute()
    {
        return $this->logo_masjid ? asset($this->logo_masjid) : asset('images/other/logo-masjid-default.png');
    }
    public function getLogoPemerintahUrlAttribute()
    {
        return $this->logo_pemerintah ? asset($this->logo_pemerintah) : asset('images/other/logo-masjid-default.png');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Relasi: Satu profil memiliki banyak laporan.
     */
    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'id_masjid');
    }

    /**
     * Relasi: Satu profil memiliki banyak group categories.
     */
    public function groupCategories()
    {
        return $this->hasMany(GroupCategory::class, 'id_masjid');
    }

    public function agenda()
    {
        return $this->hasMany(Agenda::class, 'id_masjid');
    }
}
