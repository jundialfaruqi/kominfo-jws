<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserPdfController extends Controller
{
    private const TEMP_PDF_DIR = 'app/private/user-account-pdfs';
    private const TEMP_PDF_TTL_MINUTES = 15;

    /**
     * Export users to PDF filtered by Spatie Role.
     */
    public function downloadByRole(Request $request)
    {
        $roleName = $request->input('role');

        if (empty($roleName)) {
            return back()->with('error', 'Role harus dipilih untuk export PDF.');
        }

        $roleExists = Role::where('name', $roleName)->exists();
        if (!$roleExists) {
            return back()->with('error', 'Role tidak ditemukan.');
        }

        $users = User::with(['profil', 'roles'])
            ->role($roleName)
            ->orderBy('name')
            ->get();

        $viewData = [
            'roleName' => $roleName,
            'users' => $users,
            'exportDateLabel' => Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'),
        ];

        $dom = DomPDF::loadView('livewire.admin.user.user-pdf', $viewData)->setPaper('a4', 'portrait');

        $filename = 'users-role-' . str_replace(' ', '-', strtolower($roleName)) . '-' . Carbon::now('Asia/Jakarta')->format('Y-m-d') . '.pdf';
        return $dom->stream($filename);
    }

    /**
     * Generate per-user formal account PDF while streaming realtime progress steps.
     */
    public function generateAccountPdf(Request $request, User $user): StreamedResponse
    {
        $actor = Auth::user();
        if (!$actor || !$this->canExportAccountPdf($actor, $user)) {
            abort(403, 'Anda tidak memiliki izin untuk mengunduh PDF akun ini.');
        }

        $user->load(['profil', 'roles']);

        return response()->stream(function () use ($actor, $user) {
            $emit = static function (array $payload): void {
                echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                @flush();
            };

            try {
                $emit([
                    'step' => 1,
                    'progress' => 15,
                    'message' => 'Memvalidasi data akun pengguna & profil masjid...',
                ]);

                $this->cleanupExpiredTempPdfs();

                $emit([
                    'step' => 2,
                    'progress' => 35,
                    'message' => 'Menyiapkan kop surat resmi & logo Pemko WebP...',
                ]);

                $logoDataUri = $this->resolveKopLogoWebpDataUri();

                $emit([
                    'step' => 3,
                    'progress' => 60,
                    'message' => 'Menyusun informasi akun & aturan penggunaan aplikasi JWS...',
                ]);

                $displayUrl = $user->profil?->slug ? route('firdaus', $user->profil->slug) : null;
                $viewData = [
                    'user' => $user,
                    'defaultPassword' => 'jwskominfopku@123',
                    'loginUrl' => route('login'),
                    'displayUrl' => $displayUrl,
                    'logoDataUri' => $logoDataUri,
                    'exportDateLabel' => Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'),
                ];

                $emit([
                    'step' => 4,
                    'progress' => 85,
                    'message' => 'Merender dokumen PDF di server...',
                ]);

                $pdfBinary = DomPDF::loadView('livewire.admin.user.account-pdf', $viewData)
                    ->setPaper('a4', 'portrait')
                    ->output();

                $dir = storage_path(self::TEMP_PDF_DIR);
                File::ensureDirectoryExists($dir);

                $token = (string) Str::uuid();
                $filePath = $dir . DIRECTORY_SEPARATOR . $token . '.pdf';
                File::put($filePath, $pdfBinary);

                $slugName = Str::slug($user->profil?->name ?: $user->name, '-');
                $filename = 'akun-jws-' . ($slugName !== '' ? $slugName : 'user-' . $user->id) . '.pdf';

                Cache::put(
                    $this->cacheKey($token),
                    [
                        'path' => $filePath,
                        'filename' => $filename,
                        'owner_id' => $actor->id,
                        'target_user_id' => $user->id,
                    ],
                    now()->addMinutes(self::TEMP_PDF_TTL_MINUTES)
                );

                $emit([
                    'step' => 5,
                    'progress' => 100,
                    'done' => true,
                    'message' => 'File PDF selesai dibuat dan siap diunduh.',
                    'filename' => $filename,
                    'size_kb' => max(1, (int) ceil(strlen($pdfBinary) / 1024)),
                    'download_url' => route('admin.user.account-pdf.download', [
                        'user' => $user->id,
                        'token' => $token,
                    ]),
                ]);
            } catch (\Throwable $e) {
                $emit([
                    'error' => true,
                    'message' => 'Gagal membuat file PDF: ' . $e->getMessage(),
                ]);
            }
        }, 200, [
            'Content-Type' => 'application/x-ndjson; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Download a previously generated per-user PDF from local temporary storage.
     */
    public function downloadAccountPdf(Request $request, User $user, string $token)
    {
        $actor = Auth::user();
        if (!$actor || !$this->canExportAccountPdf($actor, $user)) {
            abort(403, 'Anda tidak memiliki izin untuk mengunduh PDF akun ini.');
        }

        if (!Str::isUuid($token)) {
            abort(404, 'Token unduhan tidak valid.');
        }

        $meta = Cache::get($this->cacheKey($token));
        if (
            !is_array($meta) ||
            (int) ($meta['owner_id'] ?? 0) !== (int) $actor->id ||
            (int) ($meta['target_user_id'] ?? 0) !== (int) $user->id
        ) {
            abort(404, 'File PDF kedaluwarsa atau tidak ditemukan. Silakan generate ulang.');
        }

        $path = $meta['path'] ?? '';
        if ($path === '' || !File::exists($path)) {
            abort(404, 'File PDF tidak ditemukan. Silakan generate ulang.');
        }

        $filename = $meta['filename'] ?? ('akun-jws-user-' . $user->id . '.pdf');

        return response()->download($path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function canExportAccountPdf(User $actor, User $target): bool
    {
        if ($actor->role === 'Super Admin') {
            return true;
        }

        if ($actor->role === 'Admin') {
            return $target->role === 'User';
        }

        return false;
    }

    private function cacheKey(string $token): string
    {
        return 'user_account_pdf_' . $token;
    }

    private function cleanupExpiredTempPdfs(): void
    {
        $dir = storage_path(self::TEMP_PDF_DIR);
        if (!File::isDirectory($dir)) {
            return;
        }

        $cutoff = Carbon::now()->subMinutes(self::TEMP_PDF_TTL_MINUTES)->getTimestamp();
        foreach (File::files($dir) as $file) {
            if ($file->getMTime() < $cutoff) {
                @File::delete($file->getPathname());
            }
        }
    }

    private function resolveKopLogoWebpDataUri(): ?string
    {
        $webpPath = public_path('theme/static/logo-pemko.webp');
        $pngPath = public_path('theme/static/logo.png');

        if (!File::exists($webpPath) && File::exists($pngPath) && function_exists('imagecreatefrompng') && function_exists('imagewebp')) {
            $img = @imagecreatefrompng($pngPath);
            if ($img) {
                $srcW = imagesx($img);
                $srcH = imagesy($img);
                $targetW = 260;
                $targetH = max(1, (int) round($srcH * ($targetW / max(1, $srcW))));
                $out = imagecreatetruecolor($targetW, $targetH);
                imagealphablending($out, false);
                imagesavealpha($out, true);
                $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
                imagefilledrectangle($out, 0, 0, $targetW, $targetH, $transparent);
                imagecopyresampled($out, $img, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);
                @imagewebp($out, $webpPath, 85);
                imagedestroy($out);
                imagedestroy($img);
            }
        }

        if (File::exists($webpPath)) {
            return 'data:image/webp;base64,' . base64_encode(File::get($webpPath));
        }

        if (File::exists($pngPath)) {
            return 'data:image/png;base64,' . base64_encode(File::get($pngPath));
        }

        return null;
    }
}
