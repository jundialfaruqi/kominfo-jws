<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasjidActivityLog extends Model
{
    use HasFactory;

    protected $table = 'masjid_activity_logs';

    protected $fillable = [
        'profil_id',
        'user_id',
        'masjid_name',
        'module',
        'action',
        'description',
    ];

    public function profil()
    {
        return $this->belongsTo(Profil::class, 'profil_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

