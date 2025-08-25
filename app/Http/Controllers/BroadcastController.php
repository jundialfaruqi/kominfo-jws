<?php

namespace App\Http\Controllers;

use App\Events\ServerTimeUpdated;
use App\Events\AudioUpdated;
use App\Events\ContentUpdated;
use App\Events\AdzanUpdated;
use App\Events\ProfileUpdated;
use App\Models\Profil;
use App\Models\Audios;
use App\Models\Marquee;
use App\Models\Slides;
use App\Models\Jumbotron;
use App\Models\Petugas;
use App\Models\Adzan;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DateTime;
use DateTimeZone;

class BroadcastController extends Controller
{
    /**
     * Broadcast server time updates
     */
    public function broadcastServerTime()
    {
        try {
            // Coba API utama (Pekanbaru)
            $response = Http::timeout(5)->get('https://superapp.pekanbaru.go.id/api/server-time');

            if ($response->successful()) {
                $serverTime = $response['serverTime'];
                $serverDateTime = new DateTime($serverTime, new DateTimeZone('UTC'));
                $serverDateTime->setTimezone(new DateTimeZone('Asia/Jakarta'));

                $timeData = [
                    'timestamp' => $serverDateTime->getTimestamp() * 1000,
                    'serverTime' => $serverDateTime->format('Y-m-d H:i:s'),
                    'source' => 'pekanbaru'
                ];

                ServerTimeUpdated::dispatch($timeData);
                return response()->json(['success' => true, 'data' => $timeData]);
            }
        } catch (\Exception $e) {
            // Fallback to local time
            $serverDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            $timeData = [
                'timestamp' => $serverDateTime->getTimestamp() * 1000,
                'serverTime' => $serverDateTime->format('Y-m-d H:i:s'),
                'source' => 'local'
            ];

            ServerTimeUpdated::dispatch($timeData);
            return response()->json(['success' => true, 'data' => $timeData]);
        }
    }

    /**
     * Broadcast audio updates for a specific slug
     */
    public function broadcastAudio($slug)
    {
        $profil = Profil::where('slug', $slug)->first();
        if ($profil) {
            $audio = Audios::where('user_id', $profil->user_id)->first();
            if ($audio && $audio->status) {
                $audioComponent = new \App\Livewire\Audios\Audio();

                $audioData = [
                    'audio1' => $audio->audio1 ? $audioComponent->generateLocalUrl($audio->audio1) : null,
                    'audio2' => $audio->audio2 ? $audioComponent->generateLocalUrl($audio->audio2) : null,
                    'audio3' => $audio->audio3 ? $audioComponent->generateLocalUrl($audio->audio3) : null,
                    'status' => $audio->status,
                    'slug' => $slug
                ];

                AudioUpdated::dispatch($audioData);
                return response()->json(['success' => true, 'data' => $audioData]);
            }
        }
        return response()->json(['success' => false, 'message' => 'Resource not found'], 404);
    }

    /**
     * Broadcast marquee updates for a specific slug
     */
    public function broadcastMarquee($slug)
    {
        $profil = Profil::where('slug', $slug)->first();
        if ($profil) {
            $marquee = Marquee::where('user_id', $profil->user_id)->first();
            if ($marquee) {
                $marqueeData = [
                    'marquee1' => $marquee->marquee1,
                    'marquee2' => $marquee->marquee2,
                    'marquee3' => $marquee->marquee3,
                    'marquee4' => $marquee->marquee4,
                    'marquee5' => $marquee->marquee5,
                    'slug' => $slug
                ];

                ContentUpdated::dispatch('marquee', $marqueeData);
                return response()->json(['success' => true, 'data' => $marqueeData]);
            }
        }
        return response()->json(['success' => false, 'message' => 'Resource not found'], 200);
    }

    /**
     * Broadcast slides updates for a specific slug
     */
    public function broadcastSlides($slug)
    {
        $profil = Profil::where('slug', $slug)->first();
        if ($profil) {
            $slides = Slides::where('user_id', $profil->user_id)->first();
            if ($slides) {
                $slidesData = [
                    'slide1' => $slides->slide1,
                    'slide2' => $slides->slide2,
                    'slide3' => $slides->slide3,
                    'slide4' => $slides->slide4,
                    'slide5' => $slides->slide5,
                    'slide6' => $slides->slide6,
                    'slug' => $slug
                ];

                ContentUpdated::dispatch('slides', $slidesData);
                return response()->json(['success' => true, 'data' => $slidesData]);
            }
        }
        return response()->json(['success' => false, 'message' => 'Resource not found'], 200);
    }

    /**
     * Broadcast jumbotron updates
     */
    public function broadcastJumbotron()
    {
        $jumbotron = Jumbotron::where('is_active', true)->first();
        if ($jumbotron) {
            $jumbotronData = [
                'jumbo1' => $jumbotron->jumbo1 ?? '',
                'jumbo2' => $jumbotron->jumbo2 ?? '',
                'jumbo3' => $jumbotron->jumbo3 ?? '',
                'jumbo4' => $jumbotron->jumbo4 ?? '',
                'jumbo5' => $jumbotron->jumbo5 ?? '',
                'jumbo6' => $jumbotron->jumbo6 ?? '',
                'is_active' => $jumbotron->is_active,
            ];

            ContentUpdated::dispatch('jumbotron', $jumbotronData);
            return response()->json(['success' => true, 'data' => $jumbotronData]);
        }
        return response()->json(['success' => false, 'message' => 'Resource not found'], 200);
    }

    /**
     * Broadcast petugas updates for a specific slug
     */
    public function broadcastPetugas($slug)
    {
        $profil = Profil::where('slug', $slug)->first();
        if ($profil) {
            $petugas = Petugas::where('user_id', $profil->user_id)->first();
            if ($petugas) {
                $petugasData = [
                    'hari' => $petugas->hari,
                    'khatib' => $petugas->khatib,
                    'imam' => $petugas->imam,
                    'muadzin' => $petugas->muadzin,
                    'slug' => $slug
                ];

                ContentUpdated::dispatch('petugas', $petugasData);
                return response()->json(['success' => true, 'data' => $petugasData]);
            }
        }
        return response()->json(['success' => false, 'message' => 'Resource not found'], 200);
    }

    /**
     * Broadcast adzan updates for a specific slug
     */
    public function broadcastAdzan($slug)
    {
        $profil = Profil::where('slug', $slug)->first();
        if ($profil) {
            $adzan = Adzan::where('user_id', $profil->user_id)->first();
            if ($adzan) {
                $adzanData = [
                    'adzan1' => $adzan->adzan1,
                    'adzan2' => $adzan->adzan2,
                    'adzan3' => $adzan->adzan3,
                    'adzan4' => $adzan->adzan4,
                    'adzan5' => $adzan->adzan5,
                    'adzan6' => $adzan->adzan6,
                    'adzan15' => $adzan->adzan15,
                    'adzan7' => $adzan->adzan7,
                    'adzan8' => $adzan->adzan8,
                    'adzan9' => $adzan->adzan9,
                    'adzan10' => $adzan->adzan10,
                    'adzan11' => $adzan->adzan11,
                    'adzan12' => $adzan->adzan12,
                    'slug' => $slug
                ];

                AdzanUpdated::dispatch($adzanData);
                return response()->json(['success' => true, 'data' => $adzanData]);
            }
        }
        return response()->json(['success' => false, 'message' => 'Resource not found'], 200);
    }

    /**
     * Broadcast profile updates for a specific slug
     */
    public function broadcastProfile($slug)
    {
        $profil = Profil::where('slug', $slug)->first();
        if ($profil) {
            $profileData = [
                'nama_masjid' => $profil->nama_masjid,
                'alamat' => $profil->alamat,
                'kota' => $profil->kota,
                'provinsi' => $profil->provinsi,
                'slug' => $profil->slug,
                'logo' => $profil->logo,
                'background' => $profil->background
            ];

            ProfileUpdated::dispatch($profileData);
            return response()->json(['success' => true, 'data' => $profileData]);
        }
        return response()->json(['success' => false, 'message' => 'Resource not found'], 200);
    }

    /**
     * Broadcast theme check updates for a specific slug
     */
    public function broadcastThemeCheck($slug)
    {
        $profil = Profil::where('slug', $slug)->first();
        if (!$profil) {
            return response()->json(['success' => false, 'message' => 'Profile not found'], 404);
        }

        $user = User::find($profil->user_id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $theme = Theme::find($user->theme_id);
        if (!$theme) {
            return response()->json(['success' => false, 'message' => 'Theme not found'], 404);
        }

        $updatedAt = $theme->updated_at ? $theme->updated_at->timestamp : now()->timestamp;

        $themeData = [
            'theme_id' => $user->theme_id,
            'updated_at' => $updatedAt,
            'css_file' => $theme->css_file ? asset($theme->css_file) : asset('css/style.css'),
            'slug' => $slug
        ];

        ContentUpdated::dispatch('theme', $themeData);
        return response()->json(['success' => true, 'data' => $themeData]);
    }
}