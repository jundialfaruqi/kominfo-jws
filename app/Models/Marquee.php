<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marquee extends Model
{
    /** @use HasFactory<\Database\Factories\MarqueeFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'marquee1',
        'marquee2',
        'marquee3',
        'marquee4',
        'marquee5',
        'marquee6',
        'marquee_speed',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
