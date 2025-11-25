<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    /** @use HasFactory<\Database\Factories\AgendaFactory> */
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_masjid',
        'date',
        'name',
        'aktif',

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function profilMasjid()
    {
        return $this->belongsTo(Profil::class, 'id_masjid');
    }
}
