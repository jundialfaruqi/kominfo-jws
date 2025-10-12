<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">Edit Group Category</h3>
                            <div class="card-actions d-flex gap-2">
                                @can('delete-group-category')
                                    <button type="button" class="btn btn-danger py-2 px-2 rounded-3 shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 7h16" />
                                            <path d="M10 11v6" />
                                            <path d="M14 11v6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-3h6v3" />
                                        </svg>
                                        Hapus
                                    </button>
                                @endcan
                                <a wire:navigate href="{{ route('group-category.index') }}"
                                    class="btn py-2 px-2 rounded-3 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-back">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 11l-4 4l4 4" />
                                        <path d="M5 15h11a3 3 0 0 0 0 -6h-2" />
                                    </svg>
                                    Kembali
                                </a>
                            </div>
                        </div>

                        <form wire:submit.prevent="update">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Group</label>
                                            <input type="text" class="form-control rounded-3" wire:model.defer="name"
                                                placeholder="Masukkan nama group">
                                            @error('name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    @if ($isAdmin)
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Profil Masjid</label>
                                                <select class="form-select rounded-3" wire:model.defer="profilId">
                                                    <option value="">-- Pilih Profil --</option>
                                                    @foreach ($profilList as $profil)
                                                        <option value="{{ $profil->id }}">{{ $profil->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('profilId')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card-footer d-flex gap-2 rounded-bottom-4">
                                <button type="submit" class="btn btn-primary rounded-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                        <path d="M6 4v8h8v-8z" />
                                        <path d="M10 12v6" />
                                        <path d="M9 16h2" />
                                    </svg>
                                    Perbarui
                                </button>
                                <a wire:navigate href="{{ route('group-category.index') }}" class="btn rounded-3">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div wire:ignore.self class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog"
        aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="modal-title text-danger">Apakah anda yakin ingin menghapus Group Category untuk Profil:
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-building-mosque">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 21h18" />
                            <path d="M5 21v-9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v9" />
                            <path d="M13 21v-9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v9" />
                            <path d="M9 21v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            <path d="M9.5 8.5l5 -5" />
                            <path d="M9.5 3.5l5 5" />
                            <path d="M12 3v5" />
                        </svg>
                        <span class="fw-bold">
                            {{ $deleteGroupName }}
                        </span>?
                    </div>
                    <div class="pt-3">Pilih <b>Ya, Hapus</b> untuk melanjutkan</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto"
                        data-bs-dismiss="modal">Batal</button>
                    <button wire:loading.attr="disabled" wire:click="destroyGroup" type="button"
                        class="btn btn-danger">
                        <span wire:loading.remove wire:target="destroyGroup">
                            Ya, Hapus
                        </span>
                        <span wire:loading wire:target="destroyGroup">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Menghapus...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            $wire.on('closeDeleteModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                if (modal) {
                    modal.hide();
                }
            });
        </script>
    @endscript
</div>
