<?php

namespace App\Livewire\Agenda;

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Agenda;
use App\Models\User;
use App\Models\Profil;
use App\Events\ContentUpdatedEvent;

#[Title('Buat Agenda Baru Untuk Masjid')]

class AgendaAllCreate extends Component
{
    public $userId = '';
    public $date = '';
    public $name = '';
    public $aktif = false;

    public $selectedMasjidName = '';
    public $cannotSubmitReason = '';

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'date' => 'required|date',
        'name' => 'required|string|max:23',
        'aktif' => 'boolean',
    ];

    protected $messages = [
        'userId.required' => 'User wajib dipilih',
        'userId.exists' => 'User tidak ditemukan',
        'date.required' => 'Tanggal agenda wajib diisi',
        'date.date' => 'Format tanggal tidak valid',
        'name.required' => 'Nama agenda wajib diisi',
        'name.max' => 'Nama agenda terlalu panjang hanya boleh 23 Karakter',
    ];

    public function updatedUserId()
    {
        $this->refreshMasjidBinding();
    }

    protected function refreshMasjidBinding(): void
    {
        $this->selectedMasjidName = '';
        $this->cannotSubmitReason = '';

        if (!$this->userId) {
            return;
        }

        $u = User::with('profil')->find($this->userId);
        $masjid = optional($u)->profil;
        if ($masjid) {
            $this->selectedMasjidName = $masjid->name;
        } else {
            $this->cannotSubmitReason = 'User yang dipilih belum terhubung ke Profil Masjid.';
        }
    }

    public function save()
    {
        // Validasi: biarkan Livewire mengisi $errors agar tampil di bawah input
        $this->validate();

        // Cek keterhubungan profil masjid untuk user yang dipilih
        $u = User::with('profil')->find($this->userId);
        $masjidId = optional($u->profil)->id;
        if (!$masjidId) {
            $this->cannotSubmitReason = 'User yang dipilih belum terhubung ke Profil Masjid.';
            return;
        }

        try {
            $agenda = new Agenda();
            $agenda->id_user = (int) $this->userId;
            $agenda->id_masjid = $masjidId;
            $agenda->date = $this->date;
            $agenda->name = $this->name;
            $agenda->aktif = (bool) $this->aktif;
            $agenda->save();

            $profil = Profil::find($masjidId);
            if ($profil) event(new ContentUpdatedEvent($profil->slug, 'agenda'));

            $this->dispatch('success', 'Agenda Baru berhasil ditambahkan');
            session()->flash('success', 'Agenda Baru berhasil ditambahkan');
            return redirect()->route('agenda-all.index');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan saat menyimpan agenda: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $users = User::whereHas('profil')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('livewire.agenda.agenda-all-create', [
            'users' => $users,
        ]);
    }
}
