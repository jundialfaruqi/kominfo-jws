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
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi untuk
                                    memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                    WEBP.</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
                                    (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>

                            {{-- Container untuk input file dan tombol trash --}}
                            <div class="d-flex align-items-center gap-2">
                                <input type="file" id="slide1-input"
                                    class="form-control my-2 rounded-4 @error('slide1') is-invalid @enderror"
                                    accept="image/*" onchange="handleSlideUpload(this, 'slide1')">

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

                            <div id="slide1-compress-status" class="small text-primary mt-1" style="display: none;">
                            </div>
                            <div id="slide1-upload-error" class="invalid-feedback" style="display: none;"></div>
                            @error('slide1')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi untuk
                                    memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                    WEBP.</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
                                    (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>

                            {{-- Container untuk input file dan tombol trash --}}
                            <div class="d-flex align-items-center gap-2">
                                <input type="file" id="slide2-input"
                                    class="form-control my-2 rounded-4 @error('slide2') is-invalid @enderror"
                                    accept="image/*" onchange="handleSlideUpload(this, 'slide2')">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($slide2 || $tmp_slide2)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearSlide2" title="Hapus gambar">
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

                            <div id="slide2-compress-status" class="small text-primary mt-1" style="display: none;">
                            </div>
                            <div id="slide2-upload-error" class="invalid-feedback" style="display: none;"></div>
                            @error('slide2')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                    untuk memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                    WEBP.</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
                                    (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>

                            {{-- Container untuk input file dan tombol trash --}}
                            <div class="d-flex align-items-center gap-2">
                                <input type="file" id="slide3-input"
                                    class="form-control my-2 rounded-4 @error('slide3') is-invalid @enderror"
                                    accept="image/*" onchange="handleSlideUpload(this, 'slide3')">

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

                            <div id="slide3-compress-status" class="small text-primary mt-1" style="display: none;">
                            </div>
                            <div id="slide3-upload-error" class="invalid-feedback" style="display: none;"></div>
                            @error('slide3')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                    untuk memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                </small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
                                    (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>

                            {{-- Container untuk input file dan tombol trash --}}
                            <div class="d-flex align-items-center gap-2">
                                <input type="file" id="slide4-input"
                                    class="form-control my-2 rounded-4 @error('slide4') is-invalid @enderror"
                                    accept="image/*" onchange="handleSlideUpload(this, 'slide4')">

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

                            <div id="slide4-compress-status" class="small text-primary mt-1" style="display: none;">
                            </div>
                            <div id="slide4-upload-error" class="invalid-feedback" style="display: none;"></div>
                            @error('slide4')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                    untuk memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                    WEBP.</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
                                    (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>

                            {{-- Container untuk input file dan tombol trash --}}
                            <div class="d-flex align-items-center gap-2">
                                <input type="file" id="slide5-input"
                                    class="form-control my-2 rounded-4 @error('slide5') is-invalid @enderror"
                                    accept="image/*" onchange="handleSlideUpload(this, 'slide5')">

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

                            <div id="slide5-compress-status" class="small text-primary mt-1" style="display: none;">
                            </div>
                            <div id="slide5-upload-error" class="invalid-feedback" style="display: none;"></div>
                            @error('slide5')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                    untuk memilih gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: JPG, PNG, JPEG,
                                    WEBP.</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Rasio gambar 16:9
                                    (Rekomendasi : 1920x1080 Piksel)</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file" id="slide6-input"
                                    class="form-control my-2 rounded-4 @error('slide6') is-invalid @enderror"
                                    accept="image/*" onchange="handleSlideUpload(this, 'slide6')">

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
                            <div id="slide6-compress-status" class="small text-primary mt-1" style="display: none;">
                            </div>
                            <div id="slide6-upload-error" class="invalid-feedback" style="display: none;"></div>
                            @error('slide6')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer rounded-bottom-4 border-0">
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

{{-- Script Kompresi Client-Side ke WebP (Maks 80KB) & Upload Livewire --}}
<script>
    /**
     * Kompres gambar di browser ke WebP dengan batas maksimal 80KB (81.920 bytes).
     * Menyesuaikan kualitas dan/atau dimensi secara adaptif agar strictly <= 80KB.
     */
    function compressImageToWebP(file, maxSizeBytes = 80 * 1024) {
        return new Promise((resolve, reject) => {
            if (!file || !file.type.match(/^image\//i)) {
                return reject(new Error('File yang dipilih bukan gambar yang valid.'));
            }

            const reader = new FileReader();
            reader.onerror = () => reject(new Error('Gagal membaca file gambar.'));
            reader.onload = (e) => {
                const img = new Image();
                img.onerror = () => reject(new Error('Gagal memproses file gambar.'));
                img.onload = () => {
                    const maxInitialWidth = 1920;
                    const maxInitialHeight = 1080;
                    let curWidth = img.naturalWidth || img.width;
                    let curHeight = img.naturalHeight || img.height;

                    // Batasi resolusi awal ke 1920x1080 jika lebih besar, mempertahankan rasio aspek
                    if (curWidth > maxInitialWidth || curHeight > maxInitialHeight) {
                        const ratio = Math.min(maxInitialWidth / curWidth, maxInitialHeight /
                            curHeight);
                        curWidth = Math.round(curWidth * ratio);
                        curHeight = Math.round(curHeight * ratio);
                    }

                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    if (!ctx) {
                        return reject(new Error('Canvas 2D tidak didukung di browser ini.'));
                    }

                    const getBlob = (w, h, q) => {
                        return new Promise((res) => {
                            canvas.width = w;
                            canvas.height = h;
                            ctx.imageSmoothingEnabled = true;
                            ctx.imageSmoothingQuality = 'high';
                            ctx.clearRect(0, 0, w, h);
                            ctx.drawImage(img, 0, 0, w, h);
                            canvas.toBlob((blob) => {
                                res(blob);
                            }, 'image/webp', q);
                        });
                    };

                    (async () => {
                        try {
                            let quality = 0.85;
                            let blob = await getBlob(curWidth, curHeight, quality);

                            // Tahap 1: Turunkan kualitas bertahap jika masih > 80KB
                            while (blob && blob.size > maxSizeBytes && quality > 0.30) {
                                quality -= 0.10;
                                blob = await getBlob(curWidth, curHeight, Math.max(quality,
                                    0.20));
                            }

                            // Tahap 2: Jika masih > 80KB (misal gambar sangat kompleks), kecilkan resolusi bertahap
                            let attempts = 0;
                            while (blob && blob.size > maxSizeBytes && attempts < 10) {
                                curWidth = Math.round(curWidth * 0.85);
                                curHeight = Math.round(curHeight * 0.85);
                                quality = 0.75;
                                blob = await getBlob(curWidth, curHeight, quality);

                                while (blob && blob.size > maxSizeBytes && quality > 0.25) {
                                    quality -= 0.10;
                                    blob = await getBlob(curWidth, curHeight, Math.max(quality,
                                        0.20));
                                }
                                attempts++;
                            }

                            if (!blob) {
                                return reject(new Error(
                                    'Gagal mengompres gambar ke format WebP.'));
                            }

                            // Buat nama file aman dengan ekstensi .webp
                            const originalName = file.name.replace(/\.[^/.]+$/, '');
                            const safeName = originalName.replace(/[^a-zA-Z0-9_-]/g, '_');
                            const webpFile = new File([blob], `${safeName || 'slide'}.webp`, {
                                type: 'image/webp',
                                lastModified: Date.now()
                            });

                            resolve({
                                file: webpFile,
                                originalSize: file.size,
                                compressedSize: blob.size,
                                width: curWidth,
                                height: curHeight
                            });
                        } catch (err) {
                            reject(err);
                        }
                    })();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    /**
     * Handler onchange saat file dipilih pada input slide.
     * Mengompres di browser terlebih dahulu menjadi WebP <= 80KB sebelum diunggah ke Livewire.
     */
    function handleSlideUpload(inputEl, propertyName) {
        const file = inputEl.files && inputEl.files[0];
        if (!file) return;

        const statusEl = document.getElementById(`${propertyName}-compress-status`);
        const errorEl = document.getElementById(`${propertyName}-upload-error`);

        inputEl.classList.remove('is-invalid');
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }
        if (statusEl) {
            statusEl.className = 'small text-primary mt-1';
            statusEl.style.display = 'block';
            statusEl.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengompres gambar ke WebP (maks 80KB)...';
        }

        const container = inputEl.closest('[wire\\:id]');
        const wireId = container ? container.getAttribute('wire:id') : null;
        const component = (window.Livewire && wireId) ? window.Livewire.find(wireId) : null;

        if (!component) {
            if (statusEl) statusEl.style.display = 'none';
            if (errorEl) {
                errorEl.textContent = 'Komponen Livewire tidak ditemukan.';
                errorEl.style.display = 'block';
            }
            return;
        }

        compressImageToWebP(file, 80 * 1024)
            .then((result) => {
                const {
                    file: compressedFile,
                    originalSize,
                    compressedSize
                } = result;
                const origKb = (originalSize / 1024).toFixed(1);
                const compKb = (compressedSize / 1024).toFixed(1);

                if (statusEl) {
                    statusEl.innerHTML =
                        `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengunggah WebP (${compKb} KB)...`;
                }

                component.upload(
                    propertyName,
                    compressedFile,
                    () => {
                        // Selesai berhasil
                        if (statusEl) {
                            statusEl.className = 'small text-success mt-1';
                            statusEl.style.display = 'block';
                            statusEl.innerHTML =
                                `✓ Berhasil dikompres ke WebP: <strong>${compKb} KB</strong> (dari ${origKb} KB)`;
                        }
                        inputEl.classList.remove('is-invalid');
                        if (errorEl) errorEl.style.display = 'none';
                    },
                    (error) => {
                        // Selesai gagal
                        if (statusEl) statusEl.style.display = 'none';
                        inputEl.classList.add('is-invalid');
                        if (errorEl) {
                            errorEl.textContent = error || 'Gagal mengunggah file ke server.';
                            errorEl.style.display = 'block';
                        }
                    },
                    (event) => {
                        // Progress
                        const percent = event?.detail?.progress || 0;
                        if (statusEl) {
                            statusEl.innerHTML =
                                `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengunggah WebP (${compKb} KB) ${percent}%...`;
                        }
                    }
                );
            })
            .catch((err) => {
                if (statusEl) statusEl.style.display = 'none';
                inputEl.classList.add('is-invalid');
                if (errorEl) {
                    errorEl.textContent = err?.message || 'Terjadi kesalahan saat mengompres gambar.';
                    errorEl.style.display = 'block';
                }
            });
    }

    /**
     * Handler reset input file ketika gambar dihapus via tombol trash / reset
     */
    function resetSlideInput(name) {
        if (!name) return;
        const input = document.getElementById(`${name}-input`);
        const statusEl = document.getElementById(`${name}-compress-status`);
        const errorEl = document.getElementById(`${name}-upload-error`);

        if (input) {
            input.value = '';
            input.classList.remove('is-invalid');
        }
        if (statusEl) {
            statusEl.style.display = 'none';
            statusEl.textContent = '';
        }
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }
    }

    // Listener Livewire 3 Event
    document.addEventListener('DOMContentLoaded', () => {
        if (window.Livewire) {
            window.Livewire.on('resetFileInput', (event) => {
                const name = event?.inputName || (Array.isArray(event) && event[0]?.inputName) || event;
                resetSlideInput(name);
            });
        }
    });

    window.addEventListener('resetFileInput', (event) => {
        const detail = event?.detail;
        const name = detail?.inputName || (Array.isArray(detail) && detail[0]?.inputName) || detail;
        resetSlideInput(name);
    });
</script>
