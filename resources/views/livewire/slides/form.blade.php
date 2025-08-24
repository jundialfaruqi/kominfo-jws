@if ($showForm)
    <form wire:submit.prevent="save">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
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
                                        Masjid</option> {{-- Jika sedang edit dan user sudah dipilih, tampilkan user tersebut --}}
                                    @if ($isEdit && $userId)
                                        @php
                                            $selectedUser = \App\Models\User::find($userId);
                                        @endphp
                                        @if ($selectedUser && (Auth::user()->role === 'Super Admin' || !in_array($selectedUser->role, ['Super Admin', 'Admin'])))
                                            <option value="{{ $selectedUser->id }}" selected>
                                                {{ $selectedUser->name }} (Dipilih)
                                            </option>
                                        @endif
                                    @endif {{-- Tampilkan user yang belum memiliki slide --}}
                                    @foreach ($users as $user)
                                        {{-- Jangan tampilkan user yang sudah dipilih saat edit --}}
                                        @if (!($isEdit && $userId == $user->id))
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }}
                                            </option>
                                        @endif
                                    @endforeach {{-- Jika tidak ada user yang tersedia --}}
                                    @if ($users->isEmpty() && !($isEdit && $userId))
                                        <option disabled>Tidak ada user yang tersedia
                                        </option>
                                    @endif
                                </select>
                                @error('userId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror {{-- Informasi tambahan --}}
                                @if ($users->isEmpty() && !$isEdit)
                                    <div class="form-text">
                                        <small class="text-muted">Semua user sudah memiliki
                                            slide</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="row g-2 mb-3">
                        {{-- Gambar Slide 1 --}}
                        <div class="col-md-4 mb-2 px-2">
                            <label class="form-label">Gambar Slide 1</label>
                            {{-- Gambar Slide 1 --}}
                            @if ($slide1)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ $slide1->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @elseif($tmp_slide1)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ asset($tmp_slide1) }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @endif
                            <div wire:loading wire:target="slide1" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format:
                                    JPG, PNG, JPEG, WEBP
                                </small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar
                                    16:9 (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>

                            {{-- Container untuk input file dan tombol trash --}}
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('slide1') is-invalid @enderror"
                                    wire:model="slide1" accept="image/*">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($slide1 || $tmp_slide1)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearSlide1" title="Hapus gambar">
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

                            @error('slide1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Gambar Slide 2 --}}
                        <div class="col-md-4 mb-2 px-2">
                            <label class="form-label">Gambar Slide 2</label>
                            {{-- Gambar Slide 2 --}}
                            @if ($slide2)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ $slide2->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @elseif($tmp_slide2)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ asset($tmp_slide2) }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @endif
                            <div wire:loading wire:target="slide2" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format:
                                    JPG, PNG, JPEG, WEBP
                                </small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar
                                    16:9 (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>

                            {{-- Container untuk input file dan tombol trash --}}
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('slide2') is-invalid @enderror"
                                    wire:model="slide2" accept="image/*">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($slide2 || $tmp_slide2)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearSlide2" title="Hapus gambar">
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

                            @error('slide2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Gambar Slide 3 --}}
                        <div class="col-md-4 mb-2 px-2">
                            <label class="form-label">Gambar Slide 3</label>
                            {{-- Gambar Slide 3 --}}
                            @if ($slide3)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ $slide3->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @elseif($tmp_slide3)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ asset($tmp_slide3) }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @endif
                            <div wire:loading wire:target="slide3" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format:
                                    JPG, PNG, JPEG, WEBP
                                </small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar
                                    16:9 (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>

                            {{-- Container untuk input file dan tombol trash --}}
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('slide3') is-invalid @enderror"
                                    wire:model="slide3" accept="image/*">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($slide3 || $tmp_slide3)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearSlide3" title="Hapus gambar">
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

                            @error('slide3')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Gambar Slide 4 --}}
                        <div class="col-md-4 mb-2 px-2">
                            <label class="form-label">Gambar Slide 4</label>
                            {{-- Gambar Slide 4 --}}
                            @if ($slide4)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ $slide4->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @elseif($tmp_slide4)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ asset($tmp_slide4) }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @endif
                            <div wire:loading wire:target="slide4" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format:
                                    JPG, PNG, JPEG, WEBP
                                </small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar
                                    16:9 (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>

                            {{-- Container untuk input file dan tombol trash --}}
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('slide4') is-invalid @enderror"
                                    wire:model="slide4" accept="image/*">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($slide4 || $tmp_slide4)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearSlide4" title="Hapus gambar">
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

                            @error('slide4')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Gambar Slide 5 --}}
                        <div class="col-md-4 mb-2 px-2">
                            <label class="form-label">Gambar Slide 5</label>
                            {{-- Gambar Slide 5 --}}
                            @if ($slide5)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ $slide5->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @elseif($tmp_slide5)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ asset($tmp_slide5) }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @endif
                            <div wire:loading wire:target="slide5" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format:
                                    JPG, PNG, JPEG, WEBP
                                </small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar
                                    16:9 (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>

                            {{-- Container untuk input file dan tombol trash --}}
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('slide5') is-invalid @enderror"
                                    wire:model="slide5" accept="image/*">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($slide5 || $tmp_slide5)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearSlide5" title="Hapus gambar">
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

                            @error('slide5')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>

                        {{-- Gambar Slide 6 --}}
                        <div class="col-md-4 mb-2 px-2">
                            <label class="form-label">Gambar Slide 6</label>
                            {{-- Gambar Slide 6 --}}
                            @if ($slide6)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ $slide6->temporaryUrl() }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @elseif($tmp_slide6)
                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                    <div class="img-responsive rounded-3"
                                        style="background-image: url('{{ asset($tmp_slide6) }}'); background-size: cover; background-position: center;">
                                    </div>
                                </div>
                            @endif
                            <div wire:loading wire:target="slide6" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                <span class="small">Mengupload...</span>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format:
                                    JPG, PNG, JPEG, WEBP
                                </small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar
                                    16:9 (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('slide6') is-invalid @enderror"
                                    wire:model="slide6" accept="image/*">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($slide6 || $tmp_slide6)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearSlide6" title="Hapus gambar">
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
                            @error('slide6')
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
