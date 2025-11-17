<?php

namespace App\Livewire\JumbotronMasjid;

use App\Models\JumbotronMasjid as ModelsJumbotronMasjid;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Title('Jumbotron Masjid')]

class JumbotronMasjid extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    public $paginate;

    public function render()
    {
        $userId = Auth::id();
        $jumbotronMasjids = ModelsJumbotronMasjid::with('profilMasjid')
            ->select(
                'id',
                'masjid_id',
                'created_by',
                'jumbotron_masjid_1',
                'jumbotron_masjid_2',
                'jumbotron_masjid_3',
                'jumbotron_masjid_4',
                'jumbotron_masjid_5',
                'jumbotron_masjid_6',
                'aktif',
            )
            ->where('created_by', $userId)
            ->whereHas('profilMasjid', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });

        $perPage = $this->paginate ?: 10;
        $jumbotronMasjidsData = $jumbotronMasjids->orderBy('id', 'asc')->paginate($perPage);

        return view('livewire.jumbotron-masjid.jumbotron-masjid', [
            'jumbotronMasjidsData' => $jumbotronMasjidsData,
        ]);
    }
}
