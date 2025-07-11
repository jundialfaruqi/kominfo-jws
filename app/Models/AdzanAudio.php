<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdzanAudio extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'audioadzan',
        'adzanshubuh',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
