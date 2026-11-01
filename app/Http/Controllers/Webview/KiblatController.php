<?php

namespace App\Http\Controllers\Webview;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KiblatController extends Controller
{
    /**
     * Menampilkan halaman Webview Arah Kiblat & Jadwal Sholat.
     */
    public function index(): View
    {
        return view('webview.kiblat.index');
    }
}

