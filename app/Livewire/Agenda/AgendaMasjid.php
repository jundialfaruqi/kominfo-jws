<?php

namespace App\Livewire\Agenda;

use App\Models\Agenda;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Agenda Masjid')]

class AgendaMasjid extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';
    public $deleteAgendaId = null;
    public $deleteAgendaName = '';

    public function delete($id)
    {
        $agenda = Agenda::where('id', $id)->where('id_user', Auth::id())->first();
        if ($agenda) {
            $this->deleteAgendaId = $agenda->id;
            $this->deleteAgendaName = $agenda->name;
        } else {
            $this->dispatch('error', 'Agenda tidak ditemukan atau bukan milik Anda');
        }
    }

    public function destroyAgenda()
    {
        try {
            if (!$this->deleteAgendaId) {
                $this->dispatch('error', 'Agenda tidak valid');
                return;
            }
            $agenda = Agenda::where('id', $this->deleteAgendaId)->where('id_user', Auth::id())->first();
            if (!$agenda) {
                $this->dispatch('error', 'Agenda tidak ditemukan atau bukan milik Anda');
                return;
            }
            $agenda->delete();
            $this->dispatch('success', 'Agenda berhasil dihapus');
            session()->flash('success', 'Agenda berhasil dihapus');
            return redirect()->route('agenda-masjid.index');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Gagal menghapus agenda: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $agendaMasjids = Agenda::query()
            ->select(['id', 'id_user', 'id_masjid', 'date', 'name', 'aktif'])
            ->where('id_user', Auth::id())
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', $term)
                        ->orWhere('date', 'like', $term)
                    ;
                });
            })
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->with(['profilMasjid'])
            ->paginate($this->paginate);

        $today = Carbon::now('Asia/Jakarta')->startOfDay();
        $agendaMasjids->getCollection()->transform(function ($agenda) use ($today) {
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

        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();
        $baruMingguIniUser = Agenda::where('id_user', Auth::id())
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $perubahan24JamTerakhir = Agenda::where('id_user', Auth::id())
            ->where('updated_at', '>=', Carbon::now()->subHours(24))
            ->count();
        $agendaAktifMingguIniUser = Agenda::where('id_user', Auth::id())
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('aktif', true)
            ->count();

        return view('livewire.agenda.agenda-masjid', [
            'agendaMasjids' => $agendaMasjids,
            'totalAgenda' => Agenda::where('id_user', Auth::id())->count(),
            'baruMingguIni' => $baruMingguIniUser,
            'perubahan24JamTerakhir' => $perubahan24JamTerakhir,
            'agendaAktifMingguIni' => $agendaAktifMingguIniUser,
            'agenda7HariKedepan' => Agenda::where('id_user', Auth::id())
                ->whereBetween('date', [Carbon::today()->toDateString(), Carbon::today()->addDays(7)->toDateString()])
                ->count(),
            'agendaTidakAktif' => Agenda::where('id_user', Auth::id())
                ->where('aktif', false)
                ->count(),
        ]);
    }
}
