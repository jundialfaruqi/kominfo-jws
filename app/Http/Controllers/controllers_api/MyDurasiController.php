<?php

namespace App\Http\Controllers\controllers_api;

use App\Events\ContentUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Models\Durasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyDurasiController extends Controller
{
    public function index(Request $request)
    {
        $durasi = Durasi::where('user_id', Auth::id())->first();

        if (!$durasi) {
            // Return defaults or empty structure if not found
            // Based on Livewire component defaults could be useful, but let's return nulls or defaults
            return response()->json([
                'status' => 'success',
                'data' => null, // Frontend handles defaults
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $durasi,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'adzan_imsak' => 'required|numeric|min:2',
            'adzan_shuruq' => 'required|numeric|min:2',
            'adzan_dhuha' => 'required|numeric|min:2',
            'adzan_shubuh' => 'required|numeric|min:1',
            'iqomah_shubuh' => 'required|numeric|min:1',
            'final_shubuh' => 'required|numeric|min:1',
            'adzan_dzuhur' => 'required|numeric|min:1',
            'iqomah_dzuhur' => 'required|numeric|min:1',
            'final_dzuhur' => 'required|numeric|min:1',
            'jumat_slide' => 'required|numeric|min:1',
            'adzan_ashar' => 'required|numeric|min:1',
            'iqomah_ashar' => 'required|numeric|min:1',
            'final_ashar' => 'required|numeric|min:1',
            'adzan_maghrib' => 'required|numeric|min:1',
            'iqomah_maghrib' => 'required|numeric|min:1',
            'final_maghrib' => 'required|numeric|min:1',
            'adzan_isya' => 'required|numeric|min:1',
            'iqomah_isya' => 'required|numeric|min:1',
            'final_isya' => 'required|numeric|min:1',
            'finance_scroll_speed' => 'nullable|numeric|min:0.1|max:10',
        ]);

        $durasi = Durasi::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        // Trigger update event for JWS display
        event(new ContentUpdatedEvent('durasi', $durasi));

        return response()->json([
            'status' => 'success',
            'message' => 'Pengaturan durasi berhasil disimpan',
            'data' => $durasi,
        ]);
    }
}
