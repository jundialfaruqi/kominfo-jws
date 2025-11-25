<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'role',
        'photo',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profil()
    {
        return $this->hasOne(Profil::class);
    }

    /**
     * Recalculate and persist the latest activity timestamp for this user
     * based on related tables: petugas, slides, and laporans (via profil).
     */
    public function recalculateLastActivity(): void
    {
        // Petugas and Slides link directly by user_id
        $petugasLast = \App\Models\Petugas::where('user_id', $this->id)->max('updated_at');
        $slidesLast = \App\Models\Slides::where('user_id', $this->id)->max('updated_at');
        $marqueeLast = \App\Models\Marquee::where('user_id', $this->id)->max('updated_at');

        // Laporans belong to Profil (masjid); Profil has user_id
        $profilId = $this->profil?->id;
        $laporansLast = null;
        if ($profilId) {
            $laporansLast = \App\Models\Laporan::where('id_masjid', $profilId)->max('updated_at');
        }

        $candidates = array_filter([$petugasLast, $slidesLast, $marqueeLast, $laporansLast]);
        $latest = null;
        foreach ($candidates as $ts) {
            if ($ts && (!$latest || $ts > $latest)) {
                $latest = $ts;
            }
        }

        // Persist if changed
        if ($latest !== $this->last_activity_at) {
            $this->last_activity_at = $latest; // can be null if no activity
            // Avoid touching other timestamps unintentionally
            $this->timestamps = false;
            $this->save(['timestamps' => false]);
            $this->timestamps = true;
        }
    }

    public function agenda()
    {
        return $this->hasMany(Agenda::class, 'id_user');
    }
}
