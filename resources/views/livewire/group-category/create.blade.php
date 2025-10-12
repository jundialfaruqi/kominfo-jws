<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">Tambah Group Category</h3>
                        </div>
                        <form wire:submit.prevent="save">
                            <div class="card-body">
                                @if ($isAdmin)
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-2">
                                            <label class="form-label required">Profil Masjid</label>
                                        </div>
                                        <div class="col-md-10">
                                            <select
                                                class="form-select rounded-3 @error('profilId') is-invalid @enderror"
                                                wire:model="profilId">
                                                <option value="">Pilih Profil Masjid</option>
                                                @foreach ($profiles as $p)
                                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('profilId')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endif

                                <div class="row g-2 mb-3">
                                    <div class="col-md-2">
                                        <label class="form-label required">Nama Group</label>
                                    </div>
                                    <div class="col-md-10">
                                        <input type="text"
                                            class="form-control rounded-3 @error('name') is-invalid @enderror"
                                            wire:model="name" placeholder="Masukkan nama group" />
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between rounded-bottom-4">
                                <a href="{{ route('group-category.index') }}"
                                    class="btn btn-outline-secondary rounded-3">Kembali</a>
                                <button type="submit" class="btn py-2 px-2 rounded-3 shadow-sm">
                                    <span wire:loading.remove wire:target="save">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                            <path d="M12 16v-4" />
                                            <path d="M8 8h8v4h-8z" />
                                        </svg>
                                        Simpan
                                    </span>
                                    <span wire:loading wire:target="save">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        <span class="small">Menyimpan...</span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
