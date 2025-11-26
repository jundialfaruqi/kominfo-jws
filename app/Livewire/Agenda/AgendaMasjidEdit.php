<?php

namespace App\Livewire\Agenda;

use App\Events\ContentUpdatedEvent;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Agenda;
use App\Models\Profil;
use Illuminate\Support\Facades\Auth;

#[Title('Ubah Agenda Masjid')]
class AgendaMasjidEdit extends Component
{
    public $agendaId;
    public $date = '';
    public $name = '';
    public $aktif = false;

    protected $rules = [
        'date' => 'required|date',
        'name' => 'required|string|max:255',
        'aktif' => 'boolean',
    ];

    protected $messages = [
        'date.required' => 'Tanggal agenda wajib diisi',
        'date.date' => 'Format tanggal tidak valid',
        'name.required' => 'Nama agenda wajib diisi',
        'name.max' => 'Nama agenda terlalu panjang',
    ];

    public function mount($id)
    {
        $this->agendaId = (int) $id;
        $agenda = Agenda::with('profilMasjid')->find($this->agendaId);
        if (!$agenda || $agenda->id_user !== Auth::id()) {
            $this->dispatch('error', 'Agenda tidak ditemukan atau Anda tidak berhak mengeditnya');
            return $this->redirectRoute('agenda-masjid.index');
        }

        $this->date = $agenda->date;
        $this->name = $agenda->name;
        $this->aktif = (bool) $agenda->aktif;
    }

    public function save()
    {
        $this->validate();

        $agenda = Agenda::find($this->agendaId);
        if (!$agenda || $agenda->id_user !== Auth::id()) {
            $this->dispatch('error', 'Agenda tidak ditemukan atau Anda tidak berhak mengeditnya');
            return $this->redirectRoute('agenda-masjid.index');
        }

        $masjidId = (int) $agenda->id_masjid;

        try {
            $agenda->date = $this->date;
            $agenda->name = $this->name;
            $agenda->aktif = (bool) $this->aktif;
            $agenda->save();

            $profil = Profil::find($masjidId);
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'agenda'));

            $this->dispatch('success', 'Agenda berhasil diperbarui');
            session()->flash('success', 'Agenda berhasil diperbarui');
            return redirect()->route('agenda-masjid.index');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat memperbarui agenda: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.agenda.agenda-masjid-edit');
    }
}
