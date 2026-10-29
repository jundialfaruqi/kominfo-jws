<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm border-0">
                        <div class="card-header bg-dark text-white rounded-top-4">
                            <h3 class="card-title d-none d-md-block">
                                Daftar Jumbotron Masjid
                            </h3>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-auto d-flex gap-2">
                                    <div class="input-group align-items-center rounded-4 w-100 w-md-auto">
                                        <span class="input-group-text rounded-start-4 gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-table-row">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                                                <path d="M9 3l-6 6" />
                                                <path d="M14 3l-7 7" />
                                                <path d="M19 3l-7 7" />
                                                <path d="M21 6l-4 4" />
                                                <path d="M3 10h18" />
                                                <path d="M10 10v11" />
                                            </svg>
                                            Tampilkan Baris
                                        </span>
                                        <select wire:model.live="paginate"
                                            class="form-select form-select rounded-end-4">
                                            <option>5</option>
                                            <option>10</option>
                                            <option>25</option>
                                            <option>50</option>
                                            <option>100</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-auto ms-md-auto">
                                    <div class="input-group align-items-center rounded-4 w-100 w-md-auto">
                                        <span class="input-group-text rounded-start-4 gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                <path d="M21 21l-6 -6" />
                                            </svg>
                                            Cari
                                        </span>
                                        <input wire:model.live="search" type="text"
                                            class="form-control rounded-end-4" placeholder="Cari admin atau nama masjid"
                                            autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th class="w-1">No.</th>
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
                                                <td class="text-center text-muted">
                                                    {{ $loop->iteration + ($jumbotronMasjidsData->currentPage() - 1) * $jumbotronMasjidsData->perPage() }}
                                                </td>
                                                <td class="text-wrap">
                                                    <div class="fw-bold">
                                                        {{ optional($jm->profilMasjid)->name ?? '-' }}
                                                    </div>
                                                    <div class="small text-muted">
                                                        {{ optional($jm->user)->name }}
                                                    </div>
                                                    <div class="small text-muted">
                                                        {{ optional($jm->user)->email }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $jm->aktif ? 'bg-green-lt' : 'bg-red-lt' }}">
                                                        {{ $jm->aktif ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </td>
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
                                            <td colspan="9" class="text-center text-muted py-4">Tidak ada data</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
                            {{ $jumbotronMasjidsData->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
