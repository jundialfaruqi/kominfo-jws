<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleImageUploadController extends Controller
{
    /**
     * Upload an article content image.
     * Layer 2 server safety: validates file size (max 2MB) and image types.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:webp,jpeg,jpg,png|max:2048',
        ], [
            'image.required' => 'File gambar wajib dipilih.',
            'image.image'    => 'File yang diunggah harus berupa gambar.',
            'image.mimes'    => 'Format gambar harus WebP atau JPEG/PNG.',
            'image.max'      => 'Ukuran gambar maksimal adalah 2MB.',
        ]);

        $file = $request->file('image');

        // Target folder format: artikel/[dd-mm-yy]
        $folder = 'artikel/' . date('d-m-y');

        // Extension: prioritize client extension if valid, default to webp
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['webp', 'jpeg', 'jpg', 'png'])) {
            $extension = 'webp';
        }

        // Random string filename
        $filename = Str::random(32) . '.' . $extension;

        // Store to public storage
        $path = $file->storeAs($folder, $filename, 'public');

        return response()->json([
            'status' => 'success',
            'url'    => '/storage/' . $path,
        ]);
    }
}

