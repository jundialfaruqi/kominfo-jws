@if ($showForm)
    <form wire:submit.prevent="save">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="row g-2 mb-3">
                        <div class="col-md-2">
                            <label class="form-label required">Nama Masjid</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror"
                                wire:model="name"
                                @if (!$isEdit) wire:keyup="generateSlugFromName" @endif
                                placeholder="Masukkan nama masjid">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-2">
                            <label class="form-label required">Slug</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control rounded-3 @error('slug') is-invalid @enderror"
                                wire:model="slug" placeholder="Masukkan slug (URL-friendly)">
                            <div class="form-text">
                                <small class="text-muted">Slug akan digunakan untuk URL. Hanya boleh mengandung huruf
                                    kecil, angka, dan tanda hubung (-)</small>
                                @if ($isEdit)
                                    <br><small class="text-info">Saat edit, slug tidak akan otomatis berubah meskipun
                                        nama diubah</small>
                                @else
                                    <br><small class="text-success">Slug akan otomatis dibuat dari nama masjid</small>
                                @endif
                            </div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
                        <div class="row g-2 mb-3">
                            <div class="col-md-2">
                                <label class="form-label">Admin Masjid</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-select rounded-3 @error('userId') is-invalid @enderror"
                                    wire:model="userId">
                                    <option class="dropdown-header" value="">Pilih
                                        Admin
                                        Masjid</option>

                                    {{-- Jika sedang edit dan user sudah dipilih, tampilkan user tersebut --}}
                                    @if ($isEdit && $userId)
                                        @php
                                            $selectedUser = \App\Models\User::find($userId);
                                        @endphp
                                        @if ($selectedUser && (Auth::user()->role === 'Super Admin' || !in_array($selectedUser->role, ['Super Admin', 'Admin'])))
                                            <option value="{{ $selectedUser->id }}" selected>
                                                {{ $selectedUser->name }} (Dipilih)
                                            </option>
                                        @endif
                                    @endif

                                    {{-- Tampilkan user yang belum memiliki profil --}}
                                    @foreach ($users as $user)
                                        {{-- Jangan tampilkan user yang sudah dipilih saat edit --}}
                                        @if (!($isEdit && $userId == $user->id))
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }}
                                            </option>
                                        @endif
                                    @endforeach

                                    {{-- Jika tidak ada user yang tersedia --}}
                                    @if ($users->isEmpty() && !($isEdit && $userId))
                                        <option disabled>Tidak ada user yang tersedia
                                        </option>
                                    @endif
                                </select>
                                @error('userId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- Informasi tambahan --}}
                                @if ($users->isEmpty() && !$isEdit)
                                    <div class="form-text">
                                        <small class="text-muted">Semua user sudah memiliki
                                            profil masjid</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="row g-2 mb-3">
                        <div class="col-md-2">
                            <label class="form-label required">No HP</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control rounded-3 @error('phone') is-invalid @enderror"
                                wire:model="phone" placeholder="Masukkan nomor HP">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="row g-2 mb-3">
                        <div class="col-md-2">
                            <label class="form-label required">Alamat Masjid</label>
                        </div>
                        <div class="col-md-10">
                            <textarea class="form-control rounded-3 @error('address') is-invalid @enderror" wire:model="address" rows="3"
                                placeholder="Masukkan alamat masjid"></textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-10 offset-md-2">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Logo Masjid</label>
                            {{-- Photo logo masjid --}}
                            @if ($logo_masjid)
                                <div class="card p-5 rounded-3 shadow-sm border mb-2">
                                    <div class="img-responsive img-responsive-21x9"
                                        style="background-image: url('{{ $logo_masjid->temporaryUrl() }}'); background-size: contain; background-position: center;">
                                    </div>
                                </div>
                            @elseif($temp_logo)
                                <div class="card p-5 rounded-3 shadow-sm border mb-2">
                                    <div class="img-responsive img-responsive-21x9"
                                        style="background-image: url('{{ asset($temp_logo) }}'); background-size: contain; background-position: center;">
                                    </div>
                                </div>
                            @endif
                            <div wire:loading wire:target="logo_masjid" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png, webp, gif.
                                    Maks: 1MB</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran persegi untuk
                                    kualitas
                                    gambar terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('logo_masjid') is-invalid @enderror"
                                    wire:model="logo_masjid" accept="image/*">
                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($logo_masjid || $temp_logo)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearLogoMasjid" title="Hapus gambar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                            <path d="M4 7l16 0"></path>
                                            <path d="M10 11l0 6"></path>
                                            <path d="M14 11l0 6"></path>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                            </path>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                            </path>
                                        </svg>
                                        reset
                                    </button>
                                @endif
                            </div>
                            @error('logo_masjid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Logo Instansi</label>
                            {{-- Photo logo pemerintah --}}
                            @if ($logo_pemerintah)
                                <div class="card p-5 rounded-3 shadow-sm border mb-2">
                                    <div class="img-responsive img-responsive-21x9"
                                        style="background-image: url('{{ $logo_pemerintah->temporaryUrl() }}'); background-size: contain; background-position: center;">
                                    </div>
                                </div>
                            @elseif($temp_logo_pemerintah)
                                <div class="card p-5 rounded-3 shadow-sm border mb-2">
                                    <div class="img-responsive img-responsive-21x9"
                                        style="background-image: url('{{ asset($temp_logo_pemerintah) }}'); background-size: contain; background-position: center;">
                                    </div>
                                </div>
                            @endif
                            <div wire:loading wire:target="logo_pemerintah" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png, webp,
                                    gif.
                                    Maks: 1MB</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran persegi untuk
                                    kualitas
                                    gambar terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('logo_pemerintah') is-invalid @enderror"
                                    wire:model="logo_pemerintah" accept="image/*">
                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($logo_pemerintah || $temp_logo_pemerintah)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearLogoPemerintah" title="Hapus gambar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-1">
                                            <path d="M4 7l16 0"></path>
                                            <path d="M10 11l0 6"></path>
                                            <path d="M14 11l0 6"></path>
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                            </path>
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                            </path>
                                        </svg>
                                        reset
                                    </button>
                                @endif
                            </div>
                            @error('logo_pemerintah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer rounded-bottom-4 border-0 sticky-bottom"
            style="background-color: rgba(255, 255, 255, 0.9);">
            <div class="d-flex justify-content-end gap-2">
                @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
                    <button type="button" wire:click="cancelForm" class="btn py-2 px-2 rounded-3 shadow-sm">
                        <span wire:loading.remove wire:target="cancelForm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
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
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="small">Loading...</span>
                        </span>
                    </button>
                @endif
                <button type="submit" class="btn py-2 px-2 rounded-3 shadow-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send-2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M4.698 4.034l16.302 7.966l-16.302 7.966a.503 .503 0 0 1 -.546 -.124a.555 .555 0 0 1 -.12 -.568l2.468 -7.274l-2.468 -7.274a.555 .555 0 0 1 .12 -.568a.503 .503 0 0 1 .546 -.124z" />
                            <path d="M6.5 12h14.5" />
                        </svg>
                        {{ $isEdit ? 'Perbarui' : 'Simpan' }}
                    </span>
                    <span wire:loading wire:target="save">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="small">Menyimpan...</span>
                    </span>
                </button>
            </div>
        </div>
    </form>
@endif
