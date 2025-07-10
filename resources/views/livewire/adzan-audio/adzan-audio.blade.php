<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                @if ($showForm)
                                    {{ $isEdit ? 'Ubah Pengaturan Audio Adzan' : 'Tambah Audio Adzan Baru' }}
                                @else
                                    {{ Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']) ? 'Daftar Audio Adzan' : 'Ubah Pengaturan Audio Adzan' }}
                                @endif
                            </h3>
                            @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']) && !$showForm)
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
                                            Tambah Audio Adzan
                                        </span>
                                        <span wire:loading wire:target="showAddForm">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">Loading...</span>
                                        </span>
                                    </button>
                                </div>
                            @endif
                        </div>

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
                                                        <select
                                                            class="form-select rounded-3 @error('userId') is-invalid @enderror"
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
                                                        Audio Adzan tidak akan tampil jika status tidak aktif. Jika
                                                        status Audio Adzan aktif, maka audio akan di tampilkan pada
                                                        halaman adzan setelah bunyi alarm beep adzan berakhir. Dan akan
                                                        berhenti saat durasi adzan selesai.
                                                    </label>
                                                    <select
                                                        class="form-select rounded-3 @error('status') is-invalid @enderror"
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
                                                <div class="col-md-12 mb-2 px-2">
                                                    <label class="form-label">Audio Adzan</label>
                                                    @if ($audioadzan && $audioadzan instanceof \Livewire\TemporaryUploadedFile)
                                                        <audio controls class="w-100 mb-2" wire:key="audioadzan">
                                                            <source src="{{ $audioadzan->temporaryUrl() }}"
                                                                type="audio/mpeg">
                                                            Browser Anda tidak mendukung elemen audio.
                                                        </audio>
                                                        <div
                                                            class="text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
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
                                                            <source
                                                                src="{{ $this->generateCloudinaryUrl($tmp_audioadzan) }}"
                                                                type="audio/mpeg">
                                                            Browser Anda tidak mendukung elemen audio.
                                                        </audio>
                                                        <div
                                                            class="text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
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
                                                    <div wire:loading wire:target="audioadzan" class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Loading...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Tekan Browse/Jelajahi untuk
                                                            memilih audio</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Format: MP3, WAV</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Ukuran maksimal:
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
                                                                <span wire:loading.remove
                                                                    wire:target="clearAudioAdzan">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="24" height="24"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        class="icon icon-1">
                                                                        <path d="M4 7l16 0"></path>
                                                                        <path d="M10 11l0 6"></path>
                                                                        <path d="M14 11l0 6"></path>
                                                                        <path
                                                                            d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                        </path>
                                                                        <path
                                                                            d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                        </path>
                                                                    </svg>
                                                                    reset
                                                                </span>
                                                                <span wire:loading wire:target="clearAudioAdzan">
                                                                    <span class="spinner-border spinner-border-sm"
                                                                        role="status" aria-hidden="true"></span>
                                                                    <span class="small">Menghapus...</span>
                                                                </span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @error('audioadzan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                {{-- Audio Adzan Shubuh --}}
                                                <div class="col-md-12 mb-2 px-2">
                                                    <label class="form-label">Audio Adzan Shubuh</label>
                                                    @if ($adzanshubuh && $adzanshubuh instanceof \Livewire\TemporaryUploadedFile)
                                                        <audio controls class="w-100 mb-2" wire:key="adzanshubuh">
                                                            <source src="{{ $adzanshubuh->temporaryUrl() }}"
                                                                type="audio/mpeg">
                                                            Browser Anda tidak mendukung elemen audio.
                                                        </audio>
                                                        <div
                                                            class="text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                                <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                                <path d="M9 17v-13h10v13" />
                                                                <path d="M9 8h10" />
                                                            </svg>
                                                            File: {{ $adzanshubuh->getClientOriginalName() }}
                                                        </div>
                                                    @elseif($tmp_adzanshubuh)
                                                        <audio controls class="w-100 mb-2" wire:key="tmp_adzanshubuh">
                                                            <source
                                                                src="{{ $this->generateCloudinaryUrl($tmp_adzanshubuh) }}"
                                                                type="audio/mpeg">
                                                            Browser Anda tidak mendukung elemen audio.
                                                        </audio>
                                                        <div
                                                            class="text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                                <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                                <path d="M9 17v-13h10v13" />
                                                                <path d="M9 8h10" />
                                                            </svg>
                                                            File: {{ basename($tmp_adzanshubuh) }}
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="adzanshubuh"
                                                        class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Loading...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Tekan Browse/Jelajahi untuk
                                                            memilih audio</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Format: MP3, WAV</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted"><span
                                                                class="text-danger">*</span>Ukuran maksimal:
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
                                                                <span wire:loading.remove
                                                                    wire:target="clearAdzanShubuh">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="24" height="24"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        class="icon icon-1">
                                                                        <path d="M4 7l16 0"></path>
                                                                        <path d="M10 11l0 6"></path>
                                                                        <path d="M14 11l0 6"></path>
                                                                        <path
                                                                            d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                        </path>
                                                                        <path
                                                                            d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                        </path>
                                                                    </svg>
                                                                    reset
                                                                </span>
                                                                <span wire:loading wire:target="clearAdzanShubuh">
                                                                    <span class="spinner-border spinner-border-sm"
                                                                        role="status" aria-hidden="true"></span>
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

                        @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
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

                            <div class="table-responsive">
                                <table class="table card-table table-vcenter table-hover text-nowrap datatable">
                                    <thead>
                                        <tr>
                                            <th class="w-1">No.</th>
                                            <th>Admin Masjid</th>
                                            <th class="text-center">Audio Adzan</th>
                                            <th class="text-center">Adzan Shubuh</th>
                                            <th class="text-center">Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($adzanAudioList as $adzanAudio)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-wrap">{{ $adzanAudio->user->name ?? '-' }}</td>
                                                <td class="text-center">
                                                    @if ($adzanAudio->audioadzan)
                                                        <div
                                                            class="text-wrap text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                                <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                                <path d="M9 17v-13h10v13" />
                                                                <path d="M9 8h10" />
                                                            </svg>
                                                            {{ pathinfo($adzanAudio->audioadzan, PATHINFO_BASENAME) }}
                                                        </div>
                                                        <audio controls>
                                                            <source
                                                                src="{{ $this->generateCloudinaryUrl($adzanAudio->audioadzan) }}"
                                                                type="audio/mpeg">
                                                            Browser Anda tidak mendukung elemen audio.
                                                        </audio>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($adzanAudio->adzanshubuh)
                                                        <div
                                                            class="text-wrap text-center mb-2 small align-items-center d-flex justify-content-center gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                                                <path stroke="none" d="M0 0h24v24H0z"
                                                                    fill="none" />
                                                                <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                                <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                                                <path d="M9 17v-13h10v13" />
                                                                <path d="M9 8h10" />
                                                            </svg>
                                                            {{ pathinfo($adzanAudio->adzanshubuh, PATHINFO_BASENAME) }}
                                                        </div>
                                                        <audio controls>
                                                            <source
                                                                src="{{ $this->generateCloudinaryUrl($adzanAudio->adzanshubuh) }}"
                                                                type="audio/mpeg">

                                                            Browser Anda tidak mendukung elemen audio.
                                                        </audio>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge {{ $adzanAudio->status ? 'bg-primary-lt' : 'bg-danger-lt' }}">
                                                        {{ $adzanAudio->status ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <button wire:click="edit('{{ $adzanAudio->id }}')"
                                                        class="btn py-2 px-2 rounded-3 shadow-sm">
                                                        <span wire:loading.remove
                                                            wire:target="edit('{{ $adzanAudio->id }}')">
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
                                                        <span wire:loading
                                                            wire:target="edit('{{ $adzanAudio->id }}')">
                                                            <span class="spinner-border spinner-border-sm"
                                                                role="status" aria-hidden="true"></span>
                                                            <span class="small">Loading...</span>
                                                        </span>
                                                    </button>
                                                    <button wire:click="delete('{{ $adzanAudio->id }}')"
                                                        class="btn py-2 px-2 rounded-3 shadow-sm"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                        <span wire:loading.remove
                                                            wire:target="delete('{{ $adzanAudio->id }}')">
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
                                                            wire:target="delete('{{ $adzanAudio->id }}')">
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
                                {{ $adzanAudioList->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('livewire.adzan-audio.delete')

    @script
        <script>
            document.addEventListener('livewire:initialized', function() {
                Livewire.on('resetFileInput', (data) => {
                    const inputName = data.inputName;
                    const fileInput = document.querySelector(`input[wire\:model="${inputName}"]`);
                    if (fileInput) {
                        fileInput.value = '';
                        fileInput.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });

                // Pantau perubahan pada input file
                const fileInput = document.querySelector(`input[wire\:model="audioadzan"]`);
                if (fileInput) {
                    fileInput.addEventListener('change', () => {
                        // Beri tahu Livewire untuk memperbarui komponen
                        Livewire.dispatch('fileSelected', {
                            inputName: 'audioadzan'
                        });
                    });
                }
                
                // Pantau perubahan pada input file adzanshubuh
                const fileInputShubuh = document.querySelector(`input[wire\:model="adzanshubuh"]`);
                if (fileInputShubuh) {
                    fileInputShubuh.addEventListener('change', () => {
                        // Beri tahu Livewire untuk memperbarui komponen
                        Livewire.dispatch('fileSelected', {
                            inputName: 'adzanshubuh'
                        });
                    });
                }
            });

            // Dengarkan event fileSelected dari Livewire
            $wire.on('fileSelected', (data) => {
                const inputName = data.inputName;
                // Tunggu sebentar untuk memastikan DOM diperbarui
                setTimeout(() => {
                    const audioElement = document.querySelector(`audio[wire\:key="${inputName}"]`);
                    if (audioElement) {
                        audioElement.load(); // Memaksa refresh elemen <audio>
                    }
                }, 100); // Delay kecil untuk memastikan DOM diperbarui
            });

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
