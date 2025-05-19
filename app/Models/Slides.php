<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slides extends Model
{
    /** @use HasFactory<\Database\Factories\SlidesFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slide1',
        'slide2',
        'slide3',
        'slide4',
        'slide5',
        'slide6',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
