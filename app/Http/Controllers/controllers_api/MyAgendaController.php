<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agenda;
use App\Models\Profil;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Events\ContentUpdatedEvent;
use Carbon\Carbon;

class MyAgendaController extends Controller
{
    public function list(Request $request)
    {
        $query = Agenda::query()
            ->select(['id', 'id_user', 'id_masjid', 'date', 'name', 'aktif'])
            ->where('id_user', Auth::id());

        if ($request->has('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('date', 'like', $term);
            });
        }

        $query->orderBy('date', 'asc')->orderBy('id', 'asc');

        $perPage = $request->input('per_page', 10);
        $agendas = $query->paginate($perPage);

        $today = Carbon::now('Asia/Jakarta')->startOfDay();
        $agendas->getCollection()->transform(function ($agenda) use ($today) {
            $agendaDate = Carbon::parse($agenda->date, 'Asia/Jakarta')->startOfDay();
            if ($agendaDate->isSameDay($today)) {
                $agenda->days_label = 'Sekarang';
            } elseif ($agendaDate->gt($today)) {
                $days = $today->diffInDays($agendaDate);
                $agenda->days_label = $days . ' Hari Lagi';
            } else {
                $agenda->days_label = 'Sudah lewat';
            }
            return $agenda;
        });

        return response()->json($agendas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'aktif' => 'boolean',
        ]);

        $user = User::with('profil')->find(Auth::id());
        $masjidId = optional($user->profil)->id;

        if (!$masjidId) {
            return response()->json(['message' => 'Profil Masjid Anda belum terhubung.'], 400);
        }

        $agenda = new Agenda();
        $agenda->id_user = Auth::id();
        $agenda->id_masjid = $masjidId;
        $agenda->date = $request->date;
        $agenda->name = $request->name;
        $agenda->aktif = $request->boolean('aktif');
        $agenda->save();

        $profil = Profil::find($masjidId);
        if ($profil) event(new ContentUpdatedEvent($profil->slug, 'agenda'));

        return response()->json(['message' => 'Agenda berhasil ditambahkan', 'data' => $agenda], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'aktif' => 'boolean',
        ]);

        $agenda = Agenda::where('id', $id)->where('id_user', Auth::id())->first();

        if (!$agenda) {
            return response()->json(['message' => 'Agenda tidak ditemukan atau bukan milik Anda'], 404);
        }

        $agenda->date = $request->date;
        $agenda->name = $request->name;
        $agenda->aktif = $request->boolean('aktif');
        $agenda->save();

        $profil = Profil::find($agenda->id_masjid);
        if ($profil) event(new ContentUpdatedEvent($profil->slug, 'agenda'));

        return response()->json(['message' => 'Agenda berhasil diperbarui', 'data' => $agenda]);
    }

    public function destroy($id)
    {
        $agenda = Agenda::where('id', $id)->where('id_user', Auth::id())->first();

        if (!$agenda) {
            return response()->json(['message' => 'Agenda tidak ditemukan atau bukan milik Anda'], 404);
        }

        $masjidId = $agenda->id_masjid;
        $agenda->delete();

        $profil = Profil::find($masjidId);
        if ($profil) event(new ContentUpdatedEvent($profil->slug, 'agenda'));

        return response()->json(['message' => 'Agenda berhasil dihapus']);
    }
}
