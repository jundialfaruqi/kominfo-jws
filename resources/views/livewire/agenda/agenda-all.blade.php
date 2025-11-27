<div>
    <div class="page-body">
        <div class="container-xl">

            <div class="card mb-3 rounded-4 border-0 overflow-hidden">
                <div class="card-body"
                    style="background: linear-gradient(90deg, #0ea5a3 0%, #1f7ae0 60%, #3b82f6 100%); color: #fff;">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-7 mb-3 mb-md-0">
                            <h1 class="mb-1">Agenda Masjid</h1>
                            <div class="text-white" style="opacity:.9;">
                                Manajemen agenda seluruh masjid dengan ringkasan statistik, pencarian, dan pengelolaan
                            </div>
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="d-flex flex-wrap justify-content-md-end">
                                <div class="text-center"
                                    style="border-right: 1px solid rgba(255, 255, 255, 0.498); padding-right: 1rem; margin-right: 1rem;">
                                    <div class="text-white fw-bold lh-1" style="font-size: 2rem;">
                                        {{ $totalMasjid }}
                                    </div>
                                    <div class="text-white small" style="opacity:.85;">
                                        Total Masjid
                                    </div>
                                </div>
                                <div class="text-center"
                                    style="border-right: 1px solid rgba(255, 255, 255, 0.498); padding-right: 1rem; margin-right: 1rem;">
                                    <div class="text-white fw-bold lh-1" style="font-size: 2rem;">
                                        {{ $totalAgenda }}
                                    </div>
                                    <div class="text-white small" style="opacity:.85;">
                                        Total Agenda
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="text-white fw-bold lh-1" style="font-size: 2rem;">
                                        {{ $baruMingguIni }}
                                    </div>
                                    <div class="text-white small" style="opacity:.85;">
                                        Baru Minggu Ini
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('livewire.agenda.agenda-all-statistic')

            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm border-0">
                        <div class="card-header bg-dark rounded-top-4 text-white">
                            <h3 class="card-title d-none d-md-block">
                                Daftar Agenda
                            </h3>
                            <div class="card-actions">
                                <a wire:navigate href="{{ route('agenda-all.create') }}"
                                    class="btn btn-primary py-2 rounded-4 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-pencil-plus">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                        <path d="M13.5 6.5l4 4" />
                                        <path d="M16 19h6" />
                                        <path d="M19 16v6" />
                                    </svg>
                                    Tambah Agenda Baru
                                </a>
                            </div>
                        </div>
                        {{-- Pagination & Search Controls --}}
                        <div class="card-body border-bottom py-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-auto">
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
                                            class="form-control rounded-end-4" placeholder="Ketik disini"
                                            autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th class="w-1">No.</th>
                                        <th>Masjid & Admin</th>
                                        <th>Tanggal</th>
                                        <th>Nama Agenda</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $currentMasjidId = null; @endphp
                                    @forelse ($agendas as $agenda)
                                        @if ($currentMasjidId !== $agenda->id_masjid)
                                            @php $currentMasjidId = $agenda->id_masjid; @endphp
                                            <tr class="table-active">
                                                <td colspan="6">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="fw-bold">{{ $agenda->profilMasjid->name }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-center text-muted">
                                                {{ $loop->iteration + ($agendas->currentPage() - 1) * $agendas->perPage() }}
                                            </td>
                                            <td class="text-wrap">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="flex-shrink-0">
                                                        @if ($agenda->profilMasjid->slug)
                                                            <a href="{{ route('firdaus', $agenda->profilMasjid->slug) }}"
                                                                class="btn btn-icon bg-blue-lt text-blue-lt-fg rounded-circle border-0 shadow-sm"
                                                                target="_blank">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-external-link">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path
                                                                        d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                                                    <path d="M11 13l9 -9" />
                                                                    <path d="M15 4h5v5" />
                                                                </svg>
                                                            </a>
                                                        @else
                                                            <span class="text-gray-400">-</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div>{{ $agenda->profilMasjid->name }}</div>
                                                        <div class="text-muted">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <path
                                                                    d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                                                <path d="M3 7l9 6l9 -6" />
                                                            </svg>
                                                            {{ $agenda->user->email ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-wrap">
                                                {{ \Carbon\Carbon::parse($agenda->date, 'Asia/Jakarta')->locale('id')->translatedFormat('d F Y') }}
                                            </td>
                                            <td class="text-wrap">{{ $agenda->name }}</td>
                                            <td class="text-wrap">
                                                <span
                                                    class="badge rounded-3 {{ $agenda->aktif ? 'bg-success' : 'bg-danger' }} text-white">
                                                    {{ $agenda->aktif ? 'Aktif' : 'Tidak Aktif' }}
                                                </span>
                                                <div class="small text-muted mt-1">{{ $agenda->days_label }}</div>
                                            </td>
                                            <td class="text-end">
                                                <button wire:click="edit('{{ $agenda->id }}')"
                                                    class="btn py-2 px-2 rounded-4 shadow-sm">
                                                    <span wire:loading.remove
                                                        wire:target="edit('{{ $agenda->id }}')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-0">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path
                                                                d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                            <path
                                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                            <path d="M16 5l3 3" />
                                                        </svg>
                                                        Ubah
                                                    </span>
                                                    <span wire:loading wire:target="edit('{{ $agenda->id }}')">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Loading...</span>
                                                    </span>
                                                </button>
                                                <button wire:click="delete('{{ $agenda->id }}')"
                                                    class="btn py-2 px-2 rounded-4 shadow-sm " data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal">
                                                    <span wire:loading.remove
                                                        wire:target="delete('{{ $agenda->id }}')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-0">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M4 7l16 0" />
                                                            <path d="M10 11l0 6" />
                                                            <path d="M14 11l0 6" />
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                        </svg>
                                                        Hapus
                                                    </span>
                                                    <span wire:loading wire:target="delete('{{ $agenda->id }}')">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Loading...</span>
                                                    </span>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                Belum ada data
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div wire:ignore.self class="modal modal-blur fade" id="deleteModal" tabindex="-1"
                                role="dialog" aria-hidden="true" data-bs-backdrop="static">
                                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body text-center">
                                            <div class="modal-title text-danger">Hapus Agenda</div>
                                            <div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                    <path d="M16 3v4" />
                                                    <path d="M8 3v4" />
                                                    <path d="M4 11h16" />
                                                </svg>
                                                <span class="fw-bold">
                                                    {{ $deleteAgendaName }}
                                                </span>?
                                            </div>
                                            <div class="pt-3">Pilih <b>Ya, Hapus</b> untuk melanjutkan</div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-link link-secondary me-auto"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button wire:loading.attr="disabled" wire:click="destroyAgenda"
                                                type="button" class="btn btn-danger">
                                                <span wire:loading.remove wire:target="destroyAgenda">Ya, Hapus</span>
                                                <span wire:loading wire:target="destroyAgenda">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    Menghapus...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
                            {{ $agendas->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @script
            <script>
                let __sessionToastShown = false;
                const showToastFromSession = () => {
                    if (__sessionToastShown) return;
                    const success = @json(session('success'));
                    const error = @json(session('error'));
                    if (success && window.iziToast) {
                        iziToast.success({
                            title: 'Berhasil',
                            message: success,
                            position: 'topRight'
                        });
                        __sessionToastShown = true;
                    } else if (success) {
                        alert(success);
                        __sessionToastShown = true;
                    }
                    if (error && window.iziToast) {
                        iziToast.error({
                            title: 'Gagal',
                            message: error,
                            position: 'topRight'
                        });
                        __sessionToastShown = true;
                    } else if (error) {
                        alert(error);
                        __sessionToastShown = true;
                    }
                };

                // Support full page load and Livewire SPA navigate
                document.addEventListener('DOMContentLoaded', showToastFromSession);
                document.addEventListener('livewire:navigated', showToastFromSession);

                $wire.on('success', message => {
                    iziToast.success({
                        title: 'Berhasil',
                        message,
                        position: 'topRight'
                    });
                });

                $wire.on('error', message => {
                    iziToast.error({
                        title: 'Gagal',
                        message,
                        position: 'topRight'
                    });
                });
            </script>
        @endscript


    </div>

    {{-- Create Agenda Oleh Admin --}}
    {{-- @include('livewire.agenda.agenda-all-create') --}}

    {{-- Delete Modal --}}
    {{-- @include('livewire.profil.delete') --}}

    {{-- Close Modal --}}
    {{-- @script
            <script>
                $wire.on('closeDeleteModal', () => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    if (modal) {
                        modal.hide();
                    }
                });

                $wire.on('success', message => {
                    iziToast.success({
                        title: 'Berhasil',
                        message,
                        position: 'topRight'
                    });
                });

                $wire.on('error', message => {
                    iziToast.error({
                        title: 'Gagal',
                        message,
                        position: 'topRight'
                    });
                });
            </script>
        @endscript --}}
</div>
