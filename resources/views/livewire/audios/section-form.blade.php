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
                                        Masjid</option>
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
                                    @foreach ($users as $user)
                                        @if (!($isEdit && $userId == $user->id))
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                    @if ($users->isEmpty() && !($isEdit && $userId))
                                        <option disabled>Tidak ada user yang tersedia
                                        </option>
                                    @endif
                                </select>
                                @error('userId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($users->isEmpty() && !$isEdit)
                                    <div class="form-text">
                                        <small class="text-muted">Semua user sudah memiliki
                                            audio</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="row g-2 mb-3">
                        <div class="col-md-12 mb-2 px-2">
                            <label class="form-label">Status</label>
                            <label class="form-text small text-muted mb-2 ">
                                👉
                                Audio tidak akan tampil jika status tidak aktif. Audio akan
                                berhenti 1 menit sebelum adzan dan akan play kembali setelah
                                fase adzan, iqomah dan final selesai.
                            </label>
                            <select class="form-select rounded-3 @error('status') is-invalid @enderror"
                                wire:model="status">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        {{-- Audio 1 --}}
                        <div class="col-md-4 mb-2 px-2">
                            <label class="form-label">Audio 1</label>
                            @if ($audio1 && $audio1 instanceof \Livewire\TemporaryUploadedFile)
                                <audio controls class="w-100 mb-2" wire:key="audio1">
                                    <source src="{{ $audio1->temporaryUrl() }}" type="audio/mpeg">
                                    Browser Anda tidak mendukung elemen audio.
                                </audio>
                                <div
                                    class="text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M9 17v-13h10v13" />
                                        <path d="M9 8h10" />
                                    </svg>
                                    File: {{ $audio1->getClientOriginalName() }}
                                </div>
                            @elseif($tmp_audio1)
                                <audio controls class="w-100 mb-2" wire:key="tmp_audio1">
                                    <source src="{{ $this->generateLocalUrl($tmp_audio1) }}" type="audio/mpeg">
                                    Browser Anda tidak mendukung elemen audio.
                                </audio>
                                <div
                                    class="text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M9 17v-13h10v13" />
                                        <path d="M9 8h10" />
                                    </svg>
                                    File: {{ pathinfo($tmp_audio1, PATHINFO_BASENAME) }}
                                </div>
                            @endif
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi untuk
                                    memilih audio</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: MP3, WAV</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Ukuran maksimal:
                                    10MB</small>
                            </div>
                            <!-- Progress Bar untuk Audio 1 -->
                            @if ($audio1)
                                <div wire:loading wire:target="save" class="mt-2 w-100">
                                    <div class="progress w-100"
                                        style="height: 8px; background-color: #e9ecef; border-radius: 4px; overflow: hidden;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            style="width: 100%; background-color: #0d6efd; transition: width 0.6s ease;"
                                            role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Mengupload Audio
                                        1...</small>
                                </div>
                            @endif
                            <div wire:loading wire:target="audio1" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">Loading...</span>
                            </div>
                            @if ($audio1Uploaded)
                                <div class="p-2 my-2 border border-success rounded-4">
                                    <div class="text-center text-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-circle-dashed-check">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                            <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                            <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                            <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                            <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                            <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                            <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                            <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <small class="text-success">
                                            Pemeriksaan file audio berhasil. File aman untuk
                                            disimpan. Klik 'Simpan' untuk melanjutkan.
                                        </small>
                                    </div>
                                </div>
                            @endif
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('audio1') is-invalid @enderror"
                                    wire:model="audio1" accept="audio/*">
                                @if ($tmp_audio1)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAudio1" title="Hapus audio">
                                        <span wire:loading.remove wire:target="clearAudio1">
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
                                        </span>
                                        <span wire:loading wire:target="clearAudio1">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">Menghapus...</span>
                                        </span>
                                    </button>
                                @endif
                            </div>
                            @error('audio1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Audio 2 --}}
                        <div class="col-md-4 mb-2 px-2">
                            <label class="form-label">Audio 2</label>
                            @if ($audio2 && $audio2 instanceof \Livewire\TemporaryUploadedFile)
                                <audio controls class="w-100 mb-2" wire:key="audio2">
                                    <source src="{{ $audio2->temporaryUrl() }}" type="audio/mpeg">
                                    Browser Anda tidak mendukung elemen audio.
                                </audio>
                                <div
                                    class="text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M9 17v-13h10v13" />
                                        <path d="M9 8h10" />
                                    </svg>
                                    File: {{ $audio2->getClientOriginalName() }}
                                </div>
                            @elseif($tmp_audio2)
                                <audio controls class="w-100 mb-2" wire:key="tmp_audio2">
                                    <source src="{{ $this->generateLocalUrl($tmp_audio2) }}" type="audio/mpeg">
                                    Browser Anda tidak mendukung elemen audio.
                                </audio>
                                <div
                                    class="text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M9 17v-13h10v13" />
                                    </svg>
                                    File: {{ pathinfo($tmp_audio2, PATHINFO_BASENAME) }}
                                </div>
                            @endif
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih audio</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: MP3, WAV</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Ukuran maksimal:
                                    10MB</small>
                            </div>
                            <!-- Progress Bar untuk Audio 2 -->
                            @if ($audio2)
                                <div wire:loading wire:target="save" class="mt-2 w-100">
                                    <div class="progress w-100"
                                        style="height: 8px; background-color: #e9ecef; border-radius: 4px; overflow: hidden;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            style="width: 100%; background-color: #0d6efd; transition: width 0.6s ease;"
                                            role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Mengupload Audio
                                        2...</small>
                                </div>
                            @endif
                            <div wire:loading wire:target="audio2" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                <span class="small">Loading...</span>
                            </div>
                            @if ($audio2Uploaded)
                                <div class="p-2 my-2 border border-success rounded-4">
                                    <div class="text-center text-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-circle-dashed-check">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                            <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                            <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                            <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                            <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                            <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                            <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                            <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <small class="text-success">
                                            Pemeriksaan file audio berhasil. File aman untuk
                                            disimpan. Klik 'Simpan' untuk melanjutkan.
                                        </small>
                                    </div>
                                </div>
                            @endif
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('audio2') is-invalid @enderror"
                                    wire:model="audio2" accept="audio/*">
                                @if ($tmp_audio2)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAudio2" title="Hapus audio">
                                        <span wire:loading.remove wire:target="clearAudio2">
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
                                        </span>
                                        <span wire:loading wire:target="clearAudio2">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">Menghapus...</span>
                                        </span>
                                    </button>
                                @endif
                            </div>
                            @error('audio2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Audio 3 --}}
                        <div class="col-md-4 mb-2 px-2">
                            <label class="form-label">Audio 3</label>
                            @if ($audio3 && $audio3 instanceof \Livewire\TemporaryUploadedFile)
                                <audio controls class="w-100 mb-2" wire:key="audio3">
                                    <source src="{{ $audio3->temporaryUrl() }}" type="audio/mpeg">
                                    Browser Anda tidak mendukung elemen audio.
                                </audio>
                                <div
                                    class="text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M9 17v-13h10v13" />
                                        <path d="M9 8h10" />
                                    </svg>
                                    File: {{ $audio3->getClientOriginalName() }}
                                </div>
                            @elseif($tmp_audio3)
                                <audio controls class="w-100 mb-2" wire:key="tmp_audio3">
                                    <source src="{{ $this->generateLocalUrl($tmp_audio3) }}" type="audio/mpeg">
                                    Browser Anda tidak mendukung elemen audio.
                                </audio>
                                <div
                                    class="text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M9 17v-13h10v13" />
                                        <path d="M9 8h10" />
                                    </svg>
                                    File: {{ pathinfo($tmp_audio3, PATHINFO_BASENAME) }}
                                </div>
                            @endif

                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Tekan Browse/Jelajahi
                                    untuk
                                    memilih audio</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Format: MP3, WAV</small>
                            </div>
                            <div class="form-text">
                                <small class="text-muted"><span class="text-danger">*</span>Ukuran maksimal:
                                    10MB</small>
                            </div>
                            <!-- Progress Bar untuk Audio 3 -->
                            @if ($audio3)
                                <div wire:loading wire:target="save" class="mt-2 w-100">
                                    <div class="progress w-100"
                                        style="height: 8px; background-color: #e9ecef; border-radius: 4px; overflow: hidden;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            style="width: 100%; background-color: #0d6efd; transition: width 0.6s ease;"
                                            role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Mengupload Audio
                                        3...</small>
                                </div>
                            @endif
                            <div wire:loading wire:target="audio3" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                <span class="small">Loading...</span>
                            </div>
                            @if ($audio3Uploaded)
                                <div class="p-2 my-2 border border-success rounded-4">
                                    <div class="text-center text-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-circle-dashed-check">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M8.56 3.69a9 9 0 0 0 -2.92 1.95" />
                                            <path d="M3.69 8.56a9 9 0 0 0 -.69 3.44" />
                                            <path d="M3.69 15.44a9 9 0 0 0 1.95 2.92" />
                                            <path d="M8.56 20.31a9 9 0 0 0 3.44 .69" />
                                            <path d="M15.44 20.31a9 9 0 0 0 2.92 -1.95" />
                                            <path d="M20.31 15.44a9 9 0 0 0 .69 -3.44" />
                                            <path d="M20.31 8.56a9 9 0 0 0 -1.95 -2.92" />
                                            <path d="M15.44 3.69a9 9 0 0 0 -3.44 -.69" />
                                            <path d="M9 12l2 2l4 -4" />
                                        </svg>
                                    </div>
                                    <div class="mt-2 text-center">
                                        <small class="text-success">
                                            Pemeriksaan file audio berhasil. File aman untuk
                                            disimpan. Klik 'Simpan' untuk melanjutkan.
                                        </small>
                                    </div>
                                </div>
                            @endif
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('audio3') is-invalid @enderror"
                                    wire:model="audio3" accept="audio/*">
                                @if ($tmp_audio3)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAudio3" title="Hapus audio">
                                        <span wire:loading.remove wire:target="clearAudio3">
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
                                        </span>
                                        <span wire:loading wire:target="clearAudio3">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">Menghapus...</span>
                                        </span>
                                    </button>
                                @endif
                            </div>
                            @error('audio3')
                                <div class="invalid-feedback">{{ $message }}</div>
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
