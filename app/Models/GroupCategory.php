<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupCategory extends Model
{
    /** @use HasFactory<\Database\Factories\GroupCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'id_masjid',
    ];

    public function profil()
    {
        return $this->belongsTo(Profil::class, 'id_masjid');
    }
}
