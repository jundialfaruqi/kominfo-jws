<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                @if ($showForm)
                                    {{ $isEdit ? 'Ubah Pengaturan Slide Iqomah, azan, tata tertib masjid' : 'Tambah Slide Iqomah, azan, tata tertib masjid Baru' }}
                                @else
                                    {{ Auth::user()->role === 'Admin' ? 'Daftar Slide Iqomah, azan, tata tertib masjid' : 'Ubah Pengaturan Slide Iqomah, azan, tata tertib masjid' }}
                                @endif
                            </h3>
                            @if (Auth::user()->role === 'Admin' && !$showForm)
                                <div class="card-actions">
                                    <button wire:click="showAddForm" class="btn py-2 px-2 rounded-3 shadow-sm">
                                        <span wire:loading.remove wire:target="showAddForm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                <path d="M13.5 6.5l4 4" />
                                                <path d="M16 19h6" />
                                                <path d="M19 16v6" />
                                            </svg>
                                            Tambah Slide Iqomah, azan, tata tertib masjid
                                        </span>
                                        <span wire:loading wire:target="showAddForm">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">
                                                Loading...
                                            </span>
                                        </span>
                                        </span>
                                    </button>
                                </div>
                            @endif
                        </div>
                        {{-- form untuk tambah/edit adzan --}}
                        @if ($showForm)
                            <form wire:submit.prevent="save">
                                <div class="card-body">
                                    <p class="text-muted small px-2">*Gambar yang diupload disini akan ditampilkan di
                                        slide
                                        Iqomah,
                                        slide shalat jum'at, dan gambar yg muncul setelah iqomah.</p>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            @if (Auth::user()->role === 'Admin')
                                                <div class="row g-2 mb-3">
                                                    <div class="col-md-2">
                                                        <label class="form-label"> Admin Masjid</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <select
                                                            class="form-select @error('userId') is-invalid @enderror"
                                                            wire:model="userId">
                                                            <option class="dropdown-header" selected>Pilih Admin Masjid
                                                            </option>
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('userId')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="row g-2 mb-3">
                                                {{-- Gambar Slide Iqomah 1 --}}
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Iqomah 1</label>
                                                    {{-- Gambar Slide Iqomah 1 --}}
                                                    @if ($adzan1)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan1->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan1)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan1) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan1" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi untuk memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan1') is-invalid @enderror"
                                                        wire:model="adzan1" accept="image/*">
                                                    @error('adzan1')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                {{-- Gambar Slide Iqomah 2 --}}
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Iqomah 2</label>
                                                    {{-- Gambar Slide Iqomah 2 --}}
                                                    @if ($adzan2)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan2->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan2)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan2) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan2" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi untuk memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan2') is-invalid @enderror"
                                                        wire:model="adzan2" accept="image/*">
                                                    @error('adzan2')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                {{-- Gambar Slide Iqomah 3 --}}
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Iqomah 3</label>
                                                    {{-- Gambar Slide Iqomah 3 --}}
                                                    @if ($adzan3)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan3->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan3)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan3) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan3" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi untuk memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan3') is-invalid @enderror"
                                                        wire:model="adzan3" accept="image/*">
                                                    @error('adzan3')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                {{-- Gambar Slide Iqomah 4 --}}
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Iqomah 4</label>
                                                    {{-- Gambar Slide Iqomah 4 --}}
                                                    @if ($adzan4)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan4->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan4)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan4) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan4" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi untuk
                                                            memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan4') is-invalid @enderror"
                                                        wire:model="adzan4" accept="image/*">
                                                    @error('adzan4')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                {{-- Gambar Slide Iqomah 5 --}}
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Iqomah 5</label>
                                                    {{-- Gambar Slide Iqomah 5 --}}
                                                    @if ($adzan5)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan5->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan5)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan5) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan5" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi untuk
                                                            memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan5') is-invalid @enderror"
                                                        wire:model="adzan5" accept="image/*">
                                                    @error('adzan5')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                {{-- Gambar Slide Iqomah 6 --}}
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Iqomah 6</label>
                                                    {{-- Gambar Slide Iqomah 6 --}}
                                                    @if ($adzan6)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan6->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan6)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan6) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan6" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi
                                                            untuk memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan6') is-invalid @enderror"
                                                        wire:model="adzan6" accept="image/*">
                                                    @error('adzan6')
                                                        <div class="invalid-feedback">{{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Gambar Setelah Iqomah --}}
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Gambar Setelah Iqomah</label>
                                                    {{-- Gambar Setelah Iqomah --}}
                                                    @if ($adzan15)
                                                        <div class="img-responsive img-responsive-21x9 rounded-3 shadow-sm my-2"
                                                            style="background-image: url('{{ $adzan15->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                        </div>
                                                    @elseif($tmp_adzan15)
                                                        <div class="img-responsive img-responsive-21x9 rounded-3 shadow-sm my-2"
                                                            style="background-image: url('{{ asset($tmp_adzan15) }}'); background-size: cover; background-position: center;">
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan15" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan
                                                            Browse/Jelajahi untuk memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Rasio gambar 16:9 (Rekomendasi :
                                                            1417x800 Piksel)</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 @error('adzan15') is-invalid @enderror"
                                                        wire:model="adzan15" accept="image/*">
                                                    @error('adzan15')
                                                        <div class="invalid-feedback">{{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Slide Jum'at --}}
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Jum'at 1</label>
                                                    <!-- Gambar Slide Jum'at 1 -->
                                                    @if ($adzan7)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan7->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan7)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan7) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan7" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi
                                                            untuk memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan7') is-invalid @enderror"
                                                        wire:model="adzan7" accept="image/*">
                                                    @error('adzan7')
                                                        <div class="invalid-feedback">{{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Jum'at 2</label>
                                                    {{-- Gambar Slide Jum'at 2 --}}
                                                    @if ($adzan8)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan8->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan8)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan8) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan8" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi
                                                            untuk memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan8') is-invalid @enderror"
                                                        wire:model="adzan8" accept="image/*">
                                                    @error('adzan8')
                                                        <div class="invalid-feedback">{{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Jum'at 3</label>
                                                    {{-- Gambar Slide Jum'at 3 --}}
                                                    @if ($adzan9)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan9->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan9)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan9) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan9" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi
                                                            untuk memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan9') is-invalid @enderror"
                                                        wire:model="adzan9" accept="image/*">
                                                    @error('adzan9')
                                                        <div class="invalid-feedback">{{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Jum'at 4</label>
                                                    {{-- Gambar Slide Jum'at 4 --}}
                                                    @if ($adzan10)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan10->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan10)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan10) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan10" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi
                                                            untuk memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan10') is-invalid @enderror"
                                                        wire:model="adzan10" accept="image/*">
                                                    @error('adzan10')
                                                        <div class="invalid-feedback">{{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Jum'at 5</label>
                                                    {{-- Gambar Slide Jum'at 5 --}}
                                                    @if ($adzan11)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan11->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan11)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan11) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan11" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi
                                                            untuk memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan11') is-invalid @enderror"
                                                        wire:model="adzan11" accept="image/*">
                                                    @error('adzan11')
                                                        <div class="invalid-feedback">{{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4 mb-2 px-2">
                                                    <label class="form-label">Slide Jum'at 6</label>
                                                    {{-- Gambar Slide Jum'at 6 --}}
                                                    @if ($adzan12)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ $adzan12->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($tmp_adzan12)
                                                        <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                            <div class="img-responsive rounded-3"
                                                                style="background-image: url('{{ asset($tmp_adzan12) }}'); background-size: cover; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzan12" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi
                                                            untuk memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, JPEG, GIF, WEBP,
                                                            SVG. Maks:
                                                            5MB</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Gunakan ukuran 939x1162 Piksel untuk
                                                            kualitas gambar terbaik</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-4 @error('adzan12') is-invalid @enderror"
                                                        wire:model="adzan12" accept="image/*">
                                                    @error('adzan12')
                                                        <div class="invalid-feedback">{{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer rounded-bottom-4 border-0 sticky-bottom"
                                    style="background-color: rgba(255, 255, 255, 0.9);">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if (Auth::user()->role === 'Admin')
                                            <button type="button" wire:click="cancelForm"
                                                class="btn py-2 px-2 rounded-3 shadow-sm">
                                                <span wire:loading.remove wire:target="cancelForm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-copy-x">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path stroke="none" d="M0 0h24v24H0z" />
                                                        <path
                                                            d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                                                        <path
                                                            d="M4.012 16.737a2 2 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" />
                                                        <path d="M11.5 11.5l4.9 5" />
                                                        <path d="M16.5 11.5l-5.1 5" />
                                                    </svg>
                                                    Tutup
                                                </span>
                                                <span wire:loading wire:target="cancelForm">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Loading...</span>
                                                </span>
                                            </button>
                                        @endif
                                        <button type="submit" class="btn py-2 px-2 rounded-3 shadow-sm"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="save">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-send-2">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M4.698 4.034l16.302 7.966l-16.302 7.966a.503 .503 0 0 1 -.546 -.124a.555 .555 0 0 1 -.12 -.568l2.468 -7.274l-2.468 -7.274a.555 .555 0 0 1 .12 -.568a.503 .503 0 0 1 .546 -.124z" />
                                                    <path d="M6.5 12h14.5" />
                                                </svg>
                                                {{ $isEdit ? 'Perbarui' : 'Simpan' }}
                                            </span>
                                            <span wire:loading wire:target="save">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                                <span class="small">Menyimpan...</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                        @if (Auth::user()->role === 'Admin')
                            {{-- Pagination & search control --}}
                            <div class="card-body border-bottom py-3">
                                <div class="d-flex">
                                    <div class="text-secondary">
                                        Lihat
                                        <div class="mx-2 d-inline-block">
                                            <select wire:model.live="paginate"
                                                class="form-select form-select py-1 rounded-3">
                                                <option>5</option>
                                                <option>10</option>
                                                <option>25</option>
                                                <option>50</option>
                                                <option>100</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ms-auto text-secondary">
                                        <span>Cari</span>
                                        <div class="ms-2 d-inline-block">
                                            <input wire:model.live="search" type="text"
                                                class="form-control form-control py-1 rounded-3"
                                                placeholder="Ketik disini">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- table --}}
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter table-hover text-nowrap datatable">
                                    <thead>
                                        <tr>
                                            <th class="w-1">No.</th>
                                            <th>Admin Masjid</th>
                                            <th>Adzan 1</th>
                                            <th>Adzan 2</th>
                                            <th>Adzan 3</th>
                                            <th>Adzan 4</th>
                                            <th>Adzan 5</th>
                                            <th>Adzan 6</th>
                                            <th>Adzan 7</th>
                                            <th>Adzan 8</th>
                                            <th>Adzan 9</th>
                                            <th>Adzan 10</th>
                                            <th>Adzan 11</th>
                                            <th>Adzan 12</th>
                                            <th>Adzan 13</th>
                                            <th>Adzan 14</th>
                                            <th>Adzan 15</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($adzanList as $adzan)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-wrap">{{ $adzan->user->name ?? '-' }}</td>
                                                <td>
                                                    @if ($adzan->adzan1)
                                                        <img src="{{ asset($adzan->adzan1) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan2)
                                                        <img src="{{ asset($adzan->adzan2) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan3)
                                                        <img src="{{ asset($adzan->adzan3) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan4)
                                                        <img src="{{ asset($adzan->adzan4) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan5)
                                                        <img src="{{ asset($adzan->adzan5) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan6)
                                                        <img src="{{ asset($adzan->adzan6) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan7)
                                                        <img src="{{ asset($adzan->adzan7) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan8)
                                                        <img src="{{ asset($adzan->adzan8) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan9)
                                                        <img src="{{ asset($adzan->adzan9) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan10)
                                                        <img src="{{ asset($adzan->adzan10) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan11)
                                                        <img src="{{ asset($adzan->adzan11) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan12)
                                                        <img src="{{ asset($adzan->adzan12) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan13)
                                                        <img src="{{ asset($adzan->adzan13) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan14)
                                                        <img src="{{ asset($adzan->adzan14) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($adzan->adzan15)
                                                        <img src="{{ asset($adzan->adzan15) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <button wire:click="edit('{{ $adzan->id }}')"
                                                        class="btn py-2 px-2 rounded-3 shadow-sm">
                                                        <span wire:loading.remove
                                                            wire:target="edit('{{ $adzan->id }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <path
                                                                    d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                <path
                                                                    d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                <path d="M16 5l3 3" />
                                                            </svg>
                                                            Ubah
                                                        </span>
                                                        <span wire:loading wire:target="edit('{{ $adzan->id }}')">
                                                            <span class="spinner-border spinner-border-sm"
                                                                role="status" aria-hidden="true"></span>
                                                            <span class="small">Loading...</span>
                                                        </span>
                                                    </button>
                                                    <button wire:click="delete('{{ $adzan->id }}')"
                                                        class="btn py-2 px-2 rounded-3 shadow-sm"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                        <span wire:loading.remove
                                                            wire:target="delete('{{ $adzan->id }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <path d="M4 7l16 0" />
                                                                <path d="M10 11l0 6" />
                                                                <path d="M14 11l0 6" />
                                                                <path
                                                                    d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                            </svg>
                                                            Hapus
                                                        </span>
                                                        <span wire:loading
                                                            wire:target="delete('{{ $adzan->id }}')">
                                                            <span class="spinner-border spinner-border-sm"
                                                                role="status" aria-hidden="true"></span>
                                                            <span class="small">Loading...</span>
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div
                                class="card-footer d-flex align-items-center justify-content-end pb-0 rounded-4 shadow-sm">
                                {{ $adzanList->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Delete Modal --}}
    @include('livewire.adzan.delete')
    {{-- Close Modal --}}
    @script
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
    @endscript
</div>
