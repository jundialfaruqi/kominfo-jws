<?php

namespace App\Observers;

use App\Models\Laporan;
use App\Models\Profil;
use App\Models\User;
use App\Services\MasjidActivityLogger;

class LaporanObserver
{
    public function saved(Laporan $laporan): void
    {
        $profil = Profil::find($laporan->id_masjid);
        if ($profil && $profil->user_id) {
            $user = User::find($profil->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }

        if (!$profil) {
            return;
        }

        $nominal = 'Rp ' . number_format((int) $laporan->saldo, 0, ',', '.');
        $jenisLabel = $laporan->is_opening
            ? 'Saldo Awal'
            : ($laporan->jenis === 'masuk' ? 'Pemasukan' : 'Pengeluaran');
        $uraian = trim((string) $laporan->uraian) ?: '-';

        if ($laporan->wasRecentlyCreated) {
            MasjidActivityLogger::log(
                module: 'laporan_keuangan',
                action: 'create',
                description: 'Membuat laporan keuangan (' . $jenisLabel . '): "' . $uraian . '" sebesar ' . $nominal,
                profil: $profil,
                user: $profil->user_id
            );
            return;
        }

        $businessFields = ['tanggal', 'uraian', 'jenis', 'saldo', 'is_opening', 'id_group_category'];
        if ($laporan->wasChanged($businessFields)) {
            MasjidActivityLogger::log(
                module: 'laporan_keuangan',
                action: 'update',
                description: 'Mengedit laporan keuangan (' . $jenisLabel . '): "' . $uraian . '" (' . $nominal . ')',
                profil: $profil,
                user: $profil->user_id
            );
        }
    }

    public function deleted(Laporan $laporan): void
    {
        $profil = Profil::find($laporan->id_masjid);
        if ($profil && $profil->user_id) {
            $user = User::find($profil->user_id);
            if ($user) {
                $user->recalculateLastActivity();
            }
        }

        if (!$profil) {
            return;
        }

        $nominal = 'Rp ' . number_format((int) $laporan->saldo, 0, ',', '.');
        $jenisLabel = $laporan->is_opening
            ? 'Saldo Awal'
            : ($laporan->jenis === 'masuk' ? 'Pemasukan' : 'Pengeluaran');
        $uraian = trim((string) $laporan->uraian) ?: '-';

        MasjidActivityLogger::log(
            module: 'laporan_keuangan',
            action: 'delete',
            description: 'Menghapus laporan keuangan (' . $jenisLabel . '): "' . $uraian . '" (' . $nominal . ')',
            profil: $profil,
            user: $profil->user_id
        );
    }
}
