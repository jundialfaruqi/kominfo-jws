<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevicePairing extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'pairing_code',
        'profil_id',
        'status',
    ];

    public function profil()
    {
        return $this->belongsTo(Profil::class);
    }
}
