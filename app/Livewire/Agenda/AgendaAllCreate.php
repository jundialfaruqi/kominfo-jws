<?php

namespace App\Livewire\Agenda;

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Agenda;
use App\Models\User;

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
        'name' => 'required|string|max:255',
        'aktif' => 'boolean',
    ];

    protected $messages = [
        'userId.required' => 'User wajib dipilih',
        'userId.exists' => 'User tidak ditemukan',
        'date.required' => 'Tanggal agenda wajib diisi',
        'date.date' => 'Format tanggal tidak valid',
        'name.required' => 'Nama agenda wajib diisi',
        'name.max' => 'Nama agenda terlalu panjang',
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
        try {
            $this->validate();

            $u = User::with('profil')->find($this->userId);
            $masjidId = optional($u->profil)->id;
            if (!$masjidId) {
                $this->dispatch('error', 'User yang dipilih belum terhubung ke Profil Masjid.');
                $this->cannotSubmitReason = 'User yang dipilih belum terhubung ke Profil Masjid.';
                return;
            }

            $agenda = new Agenda();
            $agenda->id_user = (int) $this->userId;
            $agenda->id_masjid = $masjidId;
            $agenda->date = $this->date;
            $agenda->name = $this->name;
            $agenda->aktif = (bool) $this->aktif;
            $agenda->save();

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
