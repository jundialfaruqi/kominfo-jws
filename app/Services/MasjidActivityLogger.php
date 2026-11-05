<?php

namespace App\Services;

use App\Models\MasjidActivityLog;
use App\Models\Profil;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MasjidActivityLogger
{
    /**
     * Cache apakah tabel masjid_activity_logs sudah ada di database
     * agar tidak memicu error sebelum migrasi dijalankan.
     */
    private static ?bool $tableExists = null;

    /**
     * Menyimpan signature log dalam siklus request yang sama untuk mencegah duplikasi log ganda.
     *
     * @var array<string, int>
     */
    private static array $recentSignatures = [];

    /**
     * Rekam aktivitas masjid secara aman (fail-safe).
     * Tidak akan pernah melempar exception yang mengganggu API atau WebSocket.
     */
    public static function log(
        string $module,
        string $action,
        string $description,
        Profil|int|null $profil = null,
        User|int|null $user = null,
        ?string $fallbackMasjidName = null
    ): void {
        try {
            if (!self::ensureTableExists()) {
                return;
            }

            $resolvedProfil = null;
            if ($profil instanceof Profil) {
                $resolvedProfil = $profil;
            } elseif (is_numeric($profil) && (int) $profil > 0) {
                $resolvedProfil = Profil::select('id', 'user_id', 'name')->find((int) $profil);
            }

            $resolvedUserId = null;
            if ($user instanceof User) {
                $resolvedUserId = $user->id;
            } elseif (is_numeric($user) && (int) $user > 0) {
                $resolvedUserId = (int) $user;
            }

            // Jika profil belum ditemukan tapi ada user_id, cari profil milik user tersebut
            if (!$resolvedProfil && $resolvedUserId) {
                $resolvedProfil = Profil::select('id', 'user_id', 'name')
                    ->where('user_id', $resolvedUserId)
                    ->first();
            }

            // Jika user_id belum ditentukan tapi ada profil, ambil user_id dari profil atau Auth
            if (!$resolvedUserId) {
                $resolvedUserId = $resolvedProfil?->user_id ?: Auth::id();
            }

            $profilId = $resolvedProfil?->id;
            $masjidName = trim((string) ($resolvedProfil?->name ?: $fallbackMasjidName));

            // Hanya rekam jika terhubung ke profil masjid/OPD yang valid
            if ($masjidName === '') {
                return;
            }

            $description = trim($description);
            if ($description === '') {
                return;
            }

            // Cegah duplikasi identik dalam rentang 2 detik pada proses PHP yang sama
            $signature = md5(($profilId ?? '0') . '|' . $module . '|' . $action . '|' . $description);
            $nowTs = time();
            if (isset(self::$recentSignatures[$signature]) && ($nowTs - self::$recentSignatures[$signature]) <= 2) {
                return;
            }
            self::$recentSignatures[$signature] = $nowTs;

            MasjidActivityLog::create([
                'profil_id'   => $profilId,
                'user_id'     => $resolvedUserId,
                'masjid_name' => $masjidName,
                'module'      => $module,
                'action'      => $action,
                'description' => $description,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal mencatat MasjidActivityLog: ' . $e->getMessage(), [
                'module'      => $module,
                'action'      => $action,
                'description' => $description,
            ]);
        }
    }

    /**
     * Cek keberadaan tabel satu kali per siklus hidup proses.
     */
    private static function ensureTableExists(): bool
    {
        if (self::$tableExists !== null) {
            return self::$tableExists;
        }

        try {
            self::$tableExists = Schema::hasTable('masjid_activity_logs');
        } catch (\Throwable) {
            self::$tableExists = false;
        }

        return self::$tableExists;
    }
}

