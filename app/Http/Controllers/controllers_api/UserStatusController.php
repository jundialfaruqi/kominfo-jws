<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserStatus;
use App\Events\UserStatusCreated;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserStatusController extends Controller
{
    public function index()
    {
        // Return active statuses (not expired)
        // Active means expires_at > now.
        $statuses = UserStatus::with('user')
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $statuses
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'caption' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,qt|max:20480', // 20MB max
        ]);

        $path = null;
        $type = 'text';

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $mime = $file->getMimeType();
            $type = str_starts_with($mime, 'video') ? 'video' : 'image';
            $path = $file->store('statuses', 'public');
            $path = Storage::url($path);
        }

        $status = UserStatus::create([
            'user_id' => $request->user()->id,
            'caption' => $request->caption,
            'media_url' => $path,
            'media_type' => $type,
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        // Broadcast event
        broadcast(new UserStatusCreated($status))->toOthers();

        return response()->json([
            'success' => true,
            'data' => $status->load('user'),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $status = UserStatus::findOrFail($id);

        if ($status->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $status->delete();

        return response()->json(['success' => true, 'message' => 'Status deleted']);
    }

    public function view(Request $request, $id)
    {
        $status = UserStatus::findOrFail($id);
        $userId = $request->user()->id;

        // Check if already viewed
        $alreadyViewed = $status->viewers()
            ->where('user_id', $userId)
            ->exists();

        if (!$alreadyViewed) {
            $status->viewers()->attach($userId, ['viewed_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    public function viewers(Request $request, $id)
    {
        $status = UserStatus::findOrFail($id);

        if ($status->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $viewers = $status->viewers()->orderBy('status_views.viewed_at', 'desc')->get();

        return response()->json(['success' => true, 'data' => $viewers]);
    }
}
