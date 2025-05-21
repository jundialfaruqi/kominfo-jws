<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Durasi extends Model
{
    /** @use HasFactory<\Database\Factories\DurasiFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sholat',
        'durasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
