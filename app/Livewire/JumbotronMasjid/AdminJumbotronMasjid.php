<?php

namespace App\Livewire\JumbotronMasjid;

use App\Models\JumbotronMasjid;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Title;

#[Title('Data Jumbotron Semua Masjid')]
class AdminJumbotronMasjid extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $paginate = '10';
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedPaginate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = JumbotronMasjid::select('id', 'masjid_id', 'created_by', 'jumbotron_masjid_1', 'jumbotron_masjid_2', 'jumbotron_masjid_3', 'jumbotron_masjid_4', 'jumbotron_masjid_5', 'jumbotron_masjid_6', 'aktif')
            ->with(['profilMasjid', 'user']);

        if (trim($this->search) !== '') {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('profilMasjid', function ($qq) use ($s) {
                    $qq->where('name', 'like', "%$s%");
                })->orWhereHas('user', function ($qq) use ($s) {
                    $qq->where('name', 'like', "%$s%");
                });
            });
        }

        $jumbotronMasjidsData = $query->orderBy('id', 'asc')->paginate($this->paginate);
        return view('livewire.jumbotron-masjid.admin-jumbotron-masjid', [
            'jumbotronMasjidsData' => $jumbotronMasjidsData,
            'search' => $this->search,
        ]);
    }
}
