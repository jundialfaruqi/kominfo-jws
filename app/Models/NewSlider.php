<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewSlider extends Model
{
    /** @use HasFactory<\Database\Factories\NewSliderFactory> */
    use HasFactory;
    protected $guarded = ['id'];

    public function masjid()
    {
        return $this->belongsTo(\App\Models\Profil::class, 'masjid_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}
