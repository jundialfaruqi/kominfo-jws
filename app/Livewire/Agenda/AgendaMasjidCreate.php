<?php

namespace App\Livewire\Agenda;

use App\Events\ContentUpdatedEvent;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Agenda;
use App\Models\Profil;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

#[Title('Buat Agenda Baru')]
class AgendaMasjidCreate extends Component
{
    public $date = '';
    public $name = '';
    public $aktif = false;

    public $selectedMasjidName = '';
    public $cannotSubmitReason = '';

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

    public function mount()
    {
        $this->refreshMasjidBinding();
    }

    protected function refreshMasjidBinding(): void
    {
        $this->selectedMasjidName = '';
        $this->cannotSubmitReason = '';

        $u = User::with('profil')->find(Auth::id());
        $masjid = optional($u)->profil;
        if ($masjid) {
            $this->selectedMasjidName = $masjid->name;
        } else {
            $this->cannotSubmitReason = 'Profil Masjid Anda belum terhubung.';
        }
    }

    public function save()
    {
        // Validasi: biarkan Livewire mengisi $errors agar tampil di bawah input
        $this->validate();

        // Cek profil masjid terhubung; tampilkan pesan di bawah form, jangan iziToast
        $u = User::with('profil')->find(Auth::id());
        $masjidId = optional($u->profil)->id;
        if (!$masjidId) {
            $this->cannotSubmitReason = 'Profil Masjid Anda belum terhubung.';
            return;
        }

        try {
            $agenda = new Agenda();
            $agenda->id_user = (int) Auth::id();
            $agenda->id_masjid = $masjidId;
            $agenda->date = $this->date;
            $agenda->name = $this->name;
            $agenda->aktif = (bool) $this->aktif;
            $agenda->save();

            $profil = Profil::find($masjidId);
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'agenda'));

            $this->dispatch('success', 'Agenda Baru berhasil ditambahkan');
            session()->flash('success', 'Agenda Baru berhasil ditambahkan');
            return redirect()->route('agenda-masjid.index');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan agenda: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.agenda.agenda-masjid-create');
    }
}
