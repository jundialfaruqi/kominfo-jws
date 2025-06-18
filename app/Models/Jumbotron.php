<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jumbotron extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jumbo1',
        'jumbo2',
        'jumbo3',
        'jumbo4',
        'jumbo5',
        'jumbo6',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
