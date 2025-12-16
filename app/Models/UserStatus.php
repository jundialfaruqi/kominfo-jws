<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserStatus extends Model
{
    protected $fillable = [
        'user_id',
        'caption',
        'media_url',
        'media_type',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::deleting(function ($status) {
            if ($status->media_url) {
                // Remove /storage/ prefix if present to get relative path in public disk
                $path = str_replace('/storage/', '', $status->media_url);
                Storage::disk('public')->delete($path);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function viewers()
    {
        return $this->belongsToMany(User::class, 'status_views', 'user_status_id', 'user_id')
            ->withPivot('viewed_at');
    }
}
