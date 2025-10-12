@if ($showForm)
    <div class="card-body">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="mb-2 d-flex align-items-center">
                    <input id="isOpening" type="checkbox" class="form-check-input me-2" wire:model="isOpening">
                    <label for="isOpening" class="form-check-label">Saldo awal</label>
                </div>
                <div class="text-danger small">*Centang Saldo Awal jika ingin menginput saldo awal</div>
            </div>
            @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Profil Masjid</label>
                        <select wire:model.live="idMasjid" class="form-select @error('idMasjid') is-invalid @enderror">
                            <option value="">Pilih Profil Masjid</option>
                            @foreach ($profils as $profil)
                                <option value="{{ $profil->id }}">{{ $profil->name }}</option>
                            @endforeach
                        </select>
                        @error('idMasjid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @else
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Profil Masjid</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->profil?->name }}" disabled>
                    </div>
                </div>
            @endif
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" wire:model="tanggal"
                        class="form-control @error('tanggal') is-invalid @enderror">
                    @error('tanggal')
                    @enderror
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Group Category</label>
                    <select wire:model="idGroupCategory" wire:key="group-category-{{ $idMasjid ?? 'none' }}"
                        class="form-select @error('idGroupCategory') is-invalid @enderror">
                        <option value="">Pilih Group Category</option>
                        @foreach ($groupCategories as $gc)
                            <option value="{{ $gc->id }}">{{ $gc->name }}</option>
                        @endforeach
                    </select>
                    @error('idGroupCategory')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Uraian</label>
                    <textarea wire:model="uraian" class="form-control @error('uraian') is-invalid @enderror" rows="3"
                        placeholder="{{ $isOpening ? 'Sisa bulan yang lalu' : 'Masukkan uraian' }}"></textarea>
                    @error('uraian')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6 {{ $isOpening ? 'd-none' : '' }}">
                <div class="mb-3">
                    <label class="form-label">Jenis Transaksi</label>
                    <select wire:model="jenis" class="form-select @error('jenis') is-invalid @enderror">
                        <option value="">Pilih Jenis</option>
                        <option value="masuk">Masuk</option>
                        <option value="keluar">Keluar</option>
                    </select>
                    @error('jenis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Nominal</label>
                    <input type="number" step="1" wire:model="saldo" min="0"
                        class="form-control @error('saldo') is-invalid @enderror" placeholder="Masukkan nominal">
                    @error('saldo')
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex">
            <button wire:loading.attr="disabled" wire:click="save" class="btn btn-primary me-2">
                <span wire:loading.remove wire:target="save">Simpan</span>
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Menyimpan...
                </span>
            </button>
            <button wire:click="cancelForm" class="btn btn-outline-secondary">Batal</button>
        </div>
    </div>
@endif
