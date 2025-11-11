<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class UserPdfController extends Controller
{
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
}
