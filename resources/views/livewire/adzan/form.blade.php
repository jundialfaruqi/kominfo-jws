@if ($showForm)
    <form wire:submit.prevent="save">
        <div class="card-body">
            <p class="text-muted small py-2">Gambar yang diupload disini akan ditampilkan di
                Iqomah, shalat jum'at, dan setelah iqomah.</p>
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
                                    @endif {{-- Tampilkan user yang belum memiliki Gambar Adzan --}}
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
                                            Gambar Iqomah, Final, dan Shalat Jum'at</small>
                                    </div>
                                @endif
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
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas
                                    Gambar Terbaik</small>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan1') is-invalid @enderror"
                                    wire:model="adzan1" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan1-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan1 || $tmp_adzan1)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan1" title="Hapus gambar">
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
                            @error('adzan1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="adzan1-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas Gambar Terbaik</small>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan2') is-invalid @enderror"
                                    wire:model="adzan2" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan2-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan2 || $tmp_adzan2)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan2" title="Hapus gambar">
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

                            @error('adzan2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="adzan2-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas Gambar Terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan3') is-invalid @enderror"
                                    wire:model="adzan3" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan3-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan3 || $tmp_adzan3)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan3" title="Hapus gambar">
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
                            @error('adzan3')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="adzan3-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas Gambar Terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan4') is-invalid @enderror"
                                    wire:model="adzan4" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan4-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan4 || $tmp_adzan4)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan4" title="Hapus gambar">
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
                            @error('adzan4')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="adzan4-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi untuk
                                    memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas Gambar Terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan5') is-invalid @enderror"
                                    wire:model="adzan5" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan5-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan5 || $tmp_adzan5)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan5" title="Hapus gambar">
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
                            @error('adzan5')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="adzan5-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi
                                    untuk memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas Gambar Terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan6') is-invalid @enderror"
                                    wire:model="adzan6" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan6-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan6 || $tmp_adzan6)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan6" title="Hapus gambar">
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
                            @error('adzan6')
                                <div class="invalid-feedback">{{ $message }}
                                </div>
                            @enderror
                            <div id="adzan6-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi
                                    untuk memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Rasio gambar 16:9 (Rekomendasi :
                                    1920x1080 Piksel)</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan15') is-invalid @enderror"
                                    wire:model="adzan15" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan15-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan15 || $tmp_adzan15)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan15" title="Hapus gambar">
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
                            @error('adzan15')
                                <div class="invalid-feedback">{{ $message }}
                                </div>
                            @enderror
                            <div id="adzan15-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi
                                    untuk memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas Gambar Terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan7') is-invalid @enderror"
                                    wire:model="adzan7" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan7-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan7 || $tmp_adzan7)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan7" title="Hapus gambar">
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
                            @error('adzan7')
                                <div class="invalid-feedback">{{ $message }}
                                </div>
                            @enderror
                            <div id="adzan7-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi
                                    untuk memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas Gambar Terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan8') is-invalid @enderror"
                                    wire:model="adzan8" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan8-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan8 || $tmp_adzan8)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan8" title="Hapus gambar">
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
                            @error('adzan8')
                                <div class="invalid-feedback">{{ $message }}
                                </div>
                            @enderror
                            <div id="adzan8-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi
                                    untuk memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas Gambar Terbaik
                                </small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan9') is-invalid @enderror"
                                    wire:model="adzan9" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan9-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan9 || $tmp_adzan9)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan9" title="Hapus gambar">
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
                            @error('adzan9')
                                <div class="invalid-feedback">{{ $message }}
                                </div>
                            @enderror
                            <div id="adzan9-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi
                                    untuk memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas Gambar Terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan10') is-invalid @enderror"
                                    wire:model="adzan10" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan10-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan10 || $tmp_adzan10)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan10" title="Hapus gambar">
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
                            @error('adzan10')
                                <div class="invalid-feedback">{{ $message }}
                                </div>
                            @enderror
                            <div id="adzan10-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi
                                    untuk memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas Gambar Terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan11') is-invalid @enderror"
                                    wire:model="adzan11" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan11-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan11 || $tmp_adzan11)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan11" title="Hapus gambar">
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
                            @error('adzan11')
                                <div class="invalid-feedback">{{ $message }}
                                </div>
                            @enderror
                            <div id="adzan11-upload-error" class="invalid-feedback" style="display:none"></div>
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
                                <small class="text-danger">*</small>
                                <small class="text-muted">Tekan Browse/Jelajahi
                                    untuk memilih
                                    gambar</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Format: jpg, jpeg, png,
                                    webp</small>
                            </div>
                            <div class="form-text">
                                <small class="text-danger">*</small>
                                <small class="text-muted">Gunakan ukuran 960x1080 Untuk
                                    Kualitas Gambar Terbaik</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzan12') is-invalid @enderror"
                                    wire:model="adzan12" accept="image/*"
                                    onchange="(function(el){const f=el.files[0];const err=document.getElementById('adzan12-upload-error');if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');if(err){err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP.';err.style.display='block';}}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">

                                {{-- Tombol Trash - hanya muncul jika ada gambar --}}
                                @if ($adzan12 || $tmp_adzan12)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzan12" title="Hapus gambar">
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
                            @error('adzan12')
                                <div class="invalid-feedback">{{ $message }}
                                </div>
                            @enderror
                            <div id="adzan12-upload-error" class="invalid-feedback" style="display:none"></div>
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

    <script>
        (function initAdzanUploadGuards() {
            const SIZE_LIMIT = 800 * 1024; // 800KB
            const MESSAGE = 'Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP, GIF.';

            function ensureErrorEl(input) {
                // Gunakan div statis jika tersedia (id: <model>-upload-error), jika tidak buat fallback dinamis
                const model = input.getAttribute('wire:model');
                let el = model ? document.getElementById(`${model}-upload-error`) : null;
                if (!el) {
                    el = input.parentElement.querySelector('.client-upload-error');
                }
                if (!el) {
                    el = document.createElement('div');
                    el.className = 'invalid-feedback client-upload-error';
                    el.style.display = 'none';
                    input.parentElement.appendChild(el);
                }
                return el;
            }

            function showError(input, msg) {
                const el = ensureErrorEl(input);
                el.textContent = msg;
                el.style.display = 'block';
                input.classList.add('is-invalid');
            }

            function clearError(input) {
                const model = input.getAttribute('wire:model');
                const elById = model ? document.getElementById(`${model}-upload-error`) : null;
                const elDynamic = input.parentElement.querySelector('.client-upload-error');
                if (elById) elById.style.display = 'none';
                if (elDynamic) elDynamic.style.display = 'none';
                input.classList.remove('is-invalid');
            }

            function attachGuards() {
                // Tangkap semua input file yang terkait dengan properti adzan*
                document.querySelectorAll('input[type="file"][wire\\:model]').forEach((input) => {
                    const model = input.getAttribute('wire:model');
                    if (!model || !model.startsWith('adzan')) return;

                    input.addEventListener('change', function(e) {
                        const file = this.files && this.files[0] ? this.files[0] : null;
                        if (!file) {
                            clearError(this);
                            return;
                        }
                        if (file.size > SIZE_LIMIT) {
                            // Tampilkan error di sisi klien, namun tetap biarkan Livewire memproses
                            // agar validasi backend (validateOnly/save) ikut memunculkan pesan server.
                            showError(this, MESSAGE);
                            return;
                        }
                        clearError(this);
                    });
                });
            }

            function attachLivewireEvents() {
                // Pasang listener Livewire upload events langsung pada setiap input adzan*
                document.querySelectorAll('input[type="file"][wire\\:model]').forEach((input) => {
                    const model = input.getAttribute('wire:model');
                    if (!model || !model.startsWith('adzan')) return;

                    input.addEventListener('livewire-upload-error', () => {
                        showError(input, MESSAGE);
                    });
                    input.addEventListener('livewire-upload-start', () => {
                        clearError(input);
                    });
                    input.addEventListener('livewire-upload-finish', () => {
                        clearError(input);
                    });
                });
            }

            function attachResetListener() {
                // Bersihkan error client ketika komponen mengirim event resetFileInput
                window.addEventListener('resetFileInput', function(e) {
                    const name = e.detail?.inputName;
                    const input = document.querySelector(`input[type="file"][wire\\:model="${name}"]`);
                    if (input) {
                        input.value = '';
                        clearError(input);
                    }
                });
            }

            // Jalankan segera jika DOM sudah siap; jika belum, tunggu DOMContentLoaded
            function bootstrap() {
                attachGuards();
                attachLivewireEvents();
                attachResetListener();
            }

            if (document.readyState !== 'loading') {
                bootstrap();
            } else {
                document.addEventListener('DOMContentLoaded', bootstrap, {
                    once: true
                });
            }
        })();
    </script>
@endif
