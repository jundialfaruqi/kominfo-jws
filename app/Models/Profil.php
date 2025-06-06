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

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
