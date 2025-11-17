<div>
    <div>
        <div class="page-body">
            <div class="container-xl">
                <div class="row row-cards">
                    <div class="col-12">
                        <div class="card rounded-4 shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Jumbotron Masjid</h3>
                                <div class="w-50">
                                    <input type="text" class="form-control rounded-3"
                                        placeholder="Cari admin atau nama masjid" wire:model.live="search">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 text-muted">Total: {{ $jumbotronMasjidsData->total() }}</div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nama Masjid & Admin</th>
                                                <th>Status</th>
                                                <th>Jumbotron Masjid 1</th>
                                                <th>Jumbotron Masjid 2</th>
                                                <th>Jumbotron Masjid 3</th>
                                                <th>Jumbotron Masjid 4</th>
                                                <th>Jumbotron Masjid 5</th>
                                                <th>Jumbotron Masjid 6</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($jumbotronMasjidsData->count())
                                                @foreach ($jumbotronMasjidsData as $jm)
                                                    <tr class="align-middle">
                                                        <td class="text-wrap">
                                                            <div>
                                                                {{ optional($jm->profilMasjid)->name }}
                                                            </div>
                                                            <div class="small text-muted">
                                                                {{ optional($jm->user)->name }}
                                                            </div>
                                                            <div class="small text-muted">
                                                                {{ optional($jm->user)->email }}
                                                            </div>
                                                        </td>
                                                        <td>{{ $jm->aktif ? 'Aktif' : 'Tidak Aktif' }}</td>
                                                        <td class="small">
                                                            @if ($jm->jumbotron_masjid_1)
                                                                <a href="{{ asset($jm->jumbotron_masjid_1) }}"
                                                                    target="_blank">{{ basename($jm->jumbotron_masjid_1) }}</a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="small">
                                                            @if ($jm->jumbotron_masjid_2)
                                                                <a href="{{ asset($jm->jumbotron_masjid_2) }}"
                                                                    target="_blank">{{ basename($jm->jumbotron_masjid_2) }}</a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="small">
                                                            @if ($jm->jumbotron_masjid_3)
                                                                <a href="{{ asset($jm->jumbotron_masjid_3) }}"
                                                                    target="_blank">{{ basename($jm->jumbotron_masjid_3) }}</a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="small">
                                                            @if ($jm->jumbotron_masjid_4)
                                                                <a href="{{ asset($jm->jumbotron_masjid_4) }}"
                                                                    target="_blank">{{ basename($jm->jumbotron_masjid_4) }}</a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="small">
                                                            @if ($jm->jumbotron_masjid_5)
                                                                <a href="{{ asset($jm->jumbotron_masjid_5) }}"
                                                                    target="_blank">{{ basename($jm->jumbotron_masjid_5) }}</a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="small">
                                                            @if ($jm->jumbotron_masjid_6)
                                                                <a href="{{ asset($jm->jumbotron_masjid_6) }}"
                                                                    target="_blank">{{ basename($jm->jumbotron_masjid_6) }}</a>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="8" class="text-center">Tidak ada data</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                {{ $jumbotronMasjidsData->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
