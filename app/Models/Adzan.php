<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adzan extends Model
{
    /** @use HasFactory<\Database\Factories\AdzanFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'adzan1',
        'adzan2',
        'adzan3',
        'adzan4',
        'adzan5',
        'adzan6',
        'adzan7',
        'adzan8',
        'adzan9',
        'adzan10',
        'adzan11',
        'adzan12',
        'adzan13',
        'adzan14',
        'adzan15',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
