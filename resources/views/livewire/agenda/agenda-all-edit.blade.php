<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card rounded-4 shadow-sm border-0">
                <div class="card-header bg-dark rounded-top-4 text-white">
                    <h3 class="card-title ">
                        Ubah data agenda
                    </h3>
                    <div class="card-actions">
                        <a wire:navigate href="{{ route('agenda-all.index') }}"
                            class="btn btn-primary py-2 rounded-4 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-narrow-left-dashed">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 12h6m3 0h1.5m3 0h.5" />
                                <path d="M5 12l4 4" />
                                <path d="M5 12l4 -4" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
                <form wire:submit.prevent="save">
                    <div class="card-body py-3">
                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <label class="form-label required">Pilih User</label>
                            </div>
                            <div class="col-md-9">
                                <select class="form-select rounded-3 @error('userId') is-invalid @enderror"
                                    wire:model.live="userId">
                                    <option value="">Pilih User</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                                @error('userId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if (!empty($selectedMasjidName))
                                    <div class="mt-3">
                                        Nama Masjid :
                                        <span class="bg-azure p-2 rounded-3 text-white fw-bold">
                                            {{ $selectedMasjidName }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <label class="form-label required">Tanggal Agenda</label>
                            </div>
                            <div class="col-md-9">
                                <input type="date" class="form-control rounded-3 @error('date') is-invalid @enderror"
                                    wire:model="date" />
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <label class="form-label required">Nama Agenda</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror"
                                    wire:model="name" placeholder="Masukkan nama agenda" />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                            </div>
                            <div class="col-md-9 d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="aktifSwitch"
                                        wire:model="aktif">
                                    <label class="form-check-label" for="aktifSwitch">Aktif</label>
                                </div>
                            </div>
                        </div>

                        @if ($cannotSubmitReason)
                            <div class="alert alert-danger rounded-3">{{ $cannotSubmitReason }}</div>
                        @endif
                    </div>
                    <div class="card-footer rounded-bottom-4 shadow-sm d-flex justify-content-end gap-2">
                        <a wire:navigate href="{{ route('agenda-all.index') }}"
                            class="btn btn-outline-secondary rounded-4">Batal</a>
                        <button type="submit" class="btn btn-primary py-2 px-3 rounded-4 shadow-sm"
                            @disabled($cannotSubmitReason)>
                            <span wire:loading.remove wire:target="save">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy me-0">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                    <path d="M12 16v-4" />
                                    <path d="M8 8h8v-4h-8z" />
                                </svg>
                                Simpan Perubahan
                            </span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">Menyimpan...</span>
                            </span>
                        </button>
                    </div>
                </form>
                @script
                    <script>
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
        </div>
    </div>
</div>
