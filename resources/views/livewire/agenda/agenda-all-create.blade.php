<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card rounded-4 shadow-sm border-0">
                <div class="card-header bg-dark rounded-top-4 text-white">
                    <h3 class="card-title ">
                        Lengkapi isian form berikut
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
                                    wire:model.live="name" maxlength="23" placeholder="Masukkan nama agenda" />

                                @php
                                    $length = strlen($name ?? '');
                                    $isMax = $length >= 23;
                                @endphp

                                <div class="text-end mt-1">
                                    <small class="{{ $isMax ? 'text-danger fw-semibold' : 'text-muted' }}">
                                        {{ $length }} / 23 karakter
                                    </small>
                                </div>

                                <div class="d-flex justify-content-between mt-1">
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-send-2 me-0">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M4.698 4.034l16.302 7.966l-16.302 7.966a.503 .503 0 0 1 -.546 -.124a.555 .555 0 0 1 -.12 -.568l2.468 -7.274l-2.468 -7.274a.555 .555 0 0 1 .12 -.568a.503 .503 0 0 1 .546 -.124z" />
                                    <path d="M6.5 12h14.5" />
                                </svg>
                                Simpan
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
