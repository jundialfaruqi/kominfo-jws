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
                                Audio Adzan hanya akan diputar jika statusnya aktif. Jika
                                status aktif, audio akan mulai diputar di halaman adzan setelah
                                bunyi alarm beep berakhir, dan akan berhenti secara otomatis
                                saat durasi adzan selesai. Pastikan panjang file audio adzan
                                sesuai dengan durasi adzan yang ditentukan di <a wire:navigate
                                    href="{{ route('durasi.index') }}">Pengaturan Durasi</a>.
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
                        {{-- Audio Adzan --}}
                        <div class="col-md-6 mb-2 px-2">
                            <label class="form-label">Audio Adzan</label>
                            @if ($audioadzan && $audioadzan instanceof \Livewire\TemporaryUploadedFile)
                                <audio controls class="w-100 mb-2" wire:key="audioadzan">
                                    <source src="{{ $audioadzan->temporaryUrl() }}" type="audio/mpeg">
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
                                    File: {{ $audioadzan->getClientOriginalName() }}
                                </div>
                            @elseif($tmp_audioadzan)
                                <audio controls class="w-100 mb-2" wire:key="tmp_audioadzan">
                                    <source src="{{ $this->generateCloudinaryUrl($tmp_audioadzan) }}" type="audio/mpeg">
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
                                    File: {{ basename($tmp_audioadzan) }}
                                </div>
                            @endif
                            <!-- Progress Bar untuk Audio Adzan -->
                            @if ($audioadzan)
                                <div wire:loading wire:target="save" class="mt-2 w-100">
                                    <div class="progress w-100"
                                        style="height: 8px; background-color: #e9ecef; border-radius: 4px; overflow: hidden;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            style="width: 100%; background-color: #0d6efd; transition: width 0.6s ease;"
                                            role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Mengupload Audio Adzan...</small>
                                </div>
                            @endif
                            <div wire:loading wire:target="audioadzan" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="small">Loading...</span>
                            </div>
                            @if ($audioadzanUploaded)
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
                                            Pemeriksaan file audio berhasil. File aman untuk disimpan. Klik 'Simpan'
                                            untuk
                                            melanjutkan.
                                        </small>
                                    </div>
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
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('audioadzan') is-invalid @enderror"
                                    wire:model="audioadzan" accept="audio/*">
                                @if ($audioadzan || $tmp_audioadzan)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAudioAdzan" title="Hapus audio">
                                        <span wire:loading.remove wire:target="clearAudioAdzan">
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
                                        <span wire:loading wire:target="clearAudioAdzan">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">Menghapus...</span>
                                        </span>
                                    </button>
                                @endif
                            </div>
                            @error('audioadzan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Audio Adzan Shubuh --}}
                        <div class="col-md-6 mb-2 px-2">
                            <label class="form-label">Audio Adzan Shubuh</label>
                            @if ($adzanshubuh && $adzanshubuh instanceof \Livewire\TemporaryUploadedFile)
                                <audio controls class="w-100 mb-2" wire:key="adzanshubuh">
                                    <source src="{{ $adzanshubuh->temporaryUrl() }}" type="audio/mpeg">
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
                                    File: {{ $adzanshubuh->getClientOriginalName() }}
                                </div>
                            @elseif($tmp_adzanshubuh)
                                <audio controls class="w-100 mb-2" wire:key="tmp_adzanshubuh">
                                    <source src="{{ $this->generateCloudinaryUrl($tmp_adzanshubuh) }}"
                                        type="audio/mpeg">
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
                                    File: {{ basename($tmp_adzanshubuh) }}
                                </div>
                            @endif
                            <!-- Progress Bar untuk Audio Adzan Shubuh -->
                            @if ($adzanshubuh)
                                <div wire:loading wire:target="save" class="mt-2 w-100">
                                    <div class="progress w-100"
                                        style="height: 8px; background-color: #e9ecef; border-radius: 4px; overflow: hidden;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            style="width: 100%; background-color: #0d6efd; transition: width 0.6s ease;"
                                            role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Mengupload Audio Adzan Shubuh...</small>
                                </div>
                            @endif
                            <div wire:loading wire:target="adzanshubuh" class="mt-2 text-center">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                <span class="small">Loading...</span>
                            </div>
                            @if ($adzanshubuhUploaded)
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
                                        <small class="text-success">Pemeriksaan file audio berhasil. File aman untuk
                                            disimpan. Klik 'Simpan' untuk melanjutkan.</small>
                                    </div>
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
                            <div class="d-flex align-items-center gap-2">
                                <input type="file"
                                    class="form-control my-2 rounded-4 @error('adzanshubuh') is-invalid @enderror"
                                    wire:model="adzanshubuh" accept="audio/*">
                                @if ($adzanshubuh || $tmp_adzanshubuh)
                                    <button type="button"
                                        class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                        wire:click="clearAdzanShubuh" title="Hapus audio">
                                        <span wire:loading.remove wire:target="clearAdzanShubuh">
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
                                        <span wire:loading wire:target="clearAdzanShubuh">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">Menghapus...</span>
                                        </span>
                                    </button>
                                @endif
                            </div>
                            @error('adzanshubuh')
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
                    <button type="button" wire:click="closeForm" class="btn py-2 px-2 rounded-3 shadow-sm">
                        <span wire:loading.remove wire:target="closeForm">
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
                        <span wire:loading wire:target="closeForm">
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
