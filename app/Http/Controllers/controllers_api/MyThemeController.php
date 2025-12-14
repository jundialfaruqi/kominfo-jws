<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theme;
use App\Models\Profil;

class MyThemeController extends Controller
{
    public function list()
    {
        try {
            $themes = Theme::select('id', 'name', 'preview_image')->orderBy('name', 'asc')->get();
            $data = $themes->map(function ($t) {
                return [
                    'id' => (int) $t->id,
                    'name' => $t->name,
                    'preview_image' => $t->preview_image ? asset($t->preview_image) : asset('images/other/default-theme.jpg'),
                ];
            });
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendapatkan daftar tema',
                'data' => $data
            ]);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    public function set(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            if (in_array($user->role, ['Admin', 'Super Admin'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
            }

            $themeId = $request->input('theme_id');
            if ($themeId === null || $themeId === '' || strtolower((string) $themeId) === 'null') {
                $user->theme_id = null;
                $user->save();
            } else {
                $theme = Theme::find($themeId);
                if (!$theme) {
                    return response()->json(['success' => false, 'message' => 'Tema tidak valid'], 422);
                }
                $user->theme_id = $theme->id;
                $user->save();
            }

            $slug = Profil::where('user_id', $user->id)->value('slug');
            if ($slug) {
                event(new \App\Events\ContentUpdatedEvent($slug, 'theme'));
            }

            $selected = $user->theme_id ? Theme::find($user->theme_id) : null;
            $data = $selected ? [
                'theme_id' => (int) $selected->id,
                'theme_name' => $selected->name,
            ] : null;

            return response()->json([
                'success' => true,
                'message' => 'Tema berhasil disimpan',
                'data' => $data
            ]);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }
}

