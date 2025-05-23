<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    /** @use HasFactory<\Database\Factories\PetugasFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hari',
        'khatib',
        'imam',
        'muadzin',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
