<?php

namespace App\Livewire\Agenda;

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Agenda;
use App\Models\Profil;
use Carbon\Carbon;

#[Title('Semua Agenda')]
class AgendaAll extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = 10;
    public $search = '';
    public $deleteAgendaId = null;
    public $deleteAgendaName = '';

    public function render()
    {
        $agendas = Agenda::query()
            ->select(['id', 'id_user', 'id_masjid', 'date', 'name', 'aktif'])
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', $term)
                        ->orWhere('date', 'like', $term)
                        ->orWhereHas('user', function ($uq) use ($term) {
                            $uq->where('name', 'like', $term);
                        })
                        ->orWhereHas('profilMasjid', function ($pq) use ($term) {
                            $pq->where('name', 'like', $term);
                        });
                });
            })
            ->orderBy('id_masjid', 'asc')
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->with(['user', 'profilMasjid'])
            ->paginate($this->paginate);

        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();
        $baruMingguIni = Agenda::whereBetween('created_at', [$start, $end])->count();
        $perubahan24JamTerakhir = Agenda::where('updated_at', '>=', Carbon::now()->subHours(24))->count();
        $agendaAktifMingguIni = Agenda::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('aktif', true)
            ->count();

        $totalMasjid = Profil::count();
        $masjidIdsDenganAgenda = Agenda::select('id_masjid')->distinct()->pluck('id_masjid')->filter()->all();
        $masjidTanpaAgenda = $totalMasjid > 0
            ? Profil::whereNotIn('id', $masjidIdsDenganAgenda)->count()
            : 0;
        $masjidPerluDilengkapi = $totalMasjid > 0
            ? (int) round(($masjidTanpaAgenda / $totalMasjid) * 100)
            : 0;
        $masjidDenganAgenda = $totalMasjid > 0
            ? Profil::whereIn('id', $masjidIdsDenganAgenda)->count()
            : 0;
        $masjidDenganAgendaPercent = $totalMasjid > 0
            ? (int) round(($masjidDenganAgenda / $totalMasjid) * 100)
            : 0;

        return view('livewire.agenda.agenda-all', [
            'agendas' => $agendas,
            'totalMasjid' => $totalMasjid,
            'totalAgenda' => Agenda::count(),
            'baruMingguIni' => $baruMingguIni,
            'perubahan24JamTerakhir' => $perubahan24JamTerakhir,
            'masjidPerluDilengkapi' => $masjidPerluDilengkapi,
            'masjidDenganAgendaPercent' => $masjidDenganAgendaPercent,
            'agendaAktifMingguIni' => $agendaAktifMingguIni,
        ]);
    }

    public function edit($id)
    {
        return redirect()->route('agenda-all.edit', ['id' => $id]);
    }

    public function delete($id)
    {
        $agenda = Agenda::find($id);
        if ($agenda) {
            $this->deleteAgendaId = $agenda->id;
            $this->deleteAgendaName = $agenda->name;
        } else {
            $this->dispatch('error', 'Agenda tidak ditemukan');
        }
    }

    public function destroyAgenda()
    {
        try {
            if (!$this->deleteAgendaId) {
                $this->dispatch('error', 'Agenda tidak valid');
                return;
            }
            $agenda = Agenda::find($this->deleteAgendaId);
            if (!$agenda) {
                $this->dispatch('error', 'Agenda tidak ditemukan');
                return;
            }
            $agenda->delete();
            $this->dispatch('success', 'Agenda berhasil dihapus');
            session()->flash('success', 'Agenda berhasil dihapus');
            return redirect()->route('agenda-all.index');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Gagal menghapus agenda: ' . $e->getMessage());
        }
    }
}
