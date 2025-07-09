<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                @if ($showForm)
                                    {{ $isEdit ? 'Ubah Pengaturan Durasi' : 'Tambah Durasi Baru' }}
                                @else
                                    {{ Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']) ? 'Daftar Durasi' : 'Ubah Pengaturan Durasi' }}
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
                                            Tambah Durasi
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

                        {{-- Form untuk tambah/edit durasi --}}
                        @if ($showForm)
                            <form wire:submit.prevent="save">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-2">
                                                    <label class="form-label">Admin Masjid</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <select
                                                        class="form-select rounded-3 @error('userId') is-invalid @enderror"
                                                        wire:model="userId">
                                                        <option class="dropdown-header" value="">Pilih Admin
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
                                                            <option disabled>Tidak ada user yang tersedia</option>
                                                        @endif
                                                    </select>
                                                    @error('userId')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    @if ($users->isEmpty() && !$isEdit)
                                                        <div class="form-text">
                                                            <small class="text-muted">Semua user sudah memiliki
                                                                pengaturan durasi</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Durasi Shubuh --}}
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-2">
                                                <label class="form-label required">Shubuh</label>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Adzan (menit)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('adzan_shubuh') is-invalid @enderror"
                                                            wire:model="adzan_shubuh">
                                                            <option value="">Pilih durasi adzan</option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }} menit</option>
                                                            @endfor
                                                        </select>
                                                        @error('adzan_shubuh')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Iqomah (menit)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('iqomah_shubuh') is-invalid @enderror"
                                                            wire:model="iqomah_shubuh">
                                                            <option value="">Pilih durasi iqomah</option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }} menit</option>
                                                            @endfor
                                                        </select>
                                                        @error('iqomah_shubuh')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Final (detik)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('final_shubuh') is-invalid @enderror"
                                                            wire:model="final_shubuh">
                                                            <option value="">Pilih durasi final</option>
                                                            <option value="30">30 detik</option>
                                                            <option value="60">60 detik</option>
                                                        </select>
                                                        @error('final_shubuh')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Durasi Dzuhur --}}
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-2">
                                                <label class="form-label required">Dzuhur</label>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Adzan (menit)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('adzan_dzuhur') is-invalid @enderror"
                                                            wire:model="adzan_dzuhur">
                                                            <option value="">Pilih durasi adzan</option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }} menit</option>
                                                            @endfor
                                                        </select>
                                                        @error('adzan_dzuhur')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Iqomah (menit)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('iqomah_dzuhur') is-invalid @enderror"
                                                            wire:model="iqomah_dzuhur">
                                                            <option value="">Pilih durasi iqomah</option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }} menit</option>
                                                            @endfor
                                                        </select>
                                                        @error('iqomah_dzuhur')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Final (detik)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('final_dzuhur') is-invalid @enderror"
                                                            wire:model="final_dzuhur">
                                                            <option value="">Pilih durasi final</option>
                                                            <option value="30">30 detik</option>
                                                            <option value="60">60 detik</option>
                                                        </select>
                                                        @error('final_dzuhur')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Durasi Jum'at --}}
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-2">
                                                <label class="form-label required">Jum'at</label>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row g-2">
                                                    <div class="col-md-12">
                                                        <label class="form-label">Slide (menit)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('jumat_slide') is-invalid @enderror"
                                                            wire:model="jumat_slide">
                                                            <option value="">Pilih durasi slide</option>
                                                            @for ($i = 1; $i <= 25; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }} menit</option>
                                                            @endfor
                                                        </select>
                                                        @error('jumat_slide')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Durasi Ashar --}}
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-2">
                                                <label class="form-label required">Ashar</label>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Adzan (menit)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('adzan_ashar') is-invalid @enderror"
                                                            wire:model="adzan_ashar">
                                                            <option value="">Pilih durasi adzan</option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }} menit</option>
                                                            @endfor
                                                        </select>
                                                        @error('adzan_ashar')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Iqomah (menit)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('iqomah_ashar') is-invalid @enderror"
                                                            wire:model="iqomah_ashar">
                                                            <option value="">Pilih durasi iqomah</option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }} menit</option>
                                                            @endfor
                                                        </select>
                                                        @error('iqomah_ashar')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Final (detik)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('final_ashar') is-invalid @enderror"
                                                            wire:model="final_ashar">
                                                            <option value="">Pilih durasi final</option>
                                                            <option value="30">30 detik</option>
                                                            <option value="60">60 detik</option>
                                                        </select>
                                                        @error('final_ashar')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Durasi Maghrib --}}
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-2">
                                                <label class="form-label required">Maghrib</label>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Adzan (menit)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('adzan_maghrib') is-invalid @enderror"
                                                            wire:model="adzan_maghrib">
                                                            <option value="">Pilih durasi adzan</option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }} menit</option>
                                                            @endfor
                                                        </select>
                                                        @error('adzan_maghrib')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Iqomah (menit)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('iqomah_maghrib') is-invalid @enderror"
                                                            wire:model="iqomah_maghrib">
                                                            <option value="">Pilih durasi iqomah</option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }} menit</option>
                                                            @endfor
                                                        </select>
                                                        @error('iqomah_maghrib')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Final (detik)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('final_maghrib') is-invalid @enderror"
                                                            wire:model="final_maghrib">
                                                            <option value="">Pilih durasi final</option>
                                                            <option value="30">30 detik</option>
                                                            <option value="60">60 detik</option>
                                                        </select>
                                                        @error('final_maghrib')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Durasi Isya --}}
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-2">
                                                <label class="form-label required">Isya</label>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Adzan (menit)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('adzan_isya') is-invalid @enderror"
                                                            wire:model="adzan_isya">
                                                            <option value="">Pilih durasi adzan</option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }} menit</option>
                                                            @endfor
                                                        </select>
                                                        @error('adzan_isya')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Iqomah (menit)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('iqomah_isya') is-invalid @enderror"
                                                            wire:model="iqomah_isya">
                                                            <option value="">Pilih durasi iqomah</option>
                                                            @for ($i = 1; $i <= 10; $i++)
                                                                <option value="{{ $i }}">
                                                                    {{ $i }} menit</option>
                                                            @endfor
                                                        </select>
                                                        @error('iqomah_isya')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Final (detik)</label>
                                                        <select
                                                            class="form-select rounded-3 @error('final_isya') is-invalid @enderror"
                                                            wire:model="final_isya">
                                                            <option value="">Pilih durasi final</option>
                                                            <option value="30">30 detik</option>
                                                            <option value="60">60 detik</option>
                                                        </select>
                                                        @error('final_isya')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
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
                            {{-- Pagination and search control --}}
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
                            {{-- Table durasi --}}
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter table-hover text-nowrap datatable">
                                    <thead>
                                        <tr>
                                            <th class="w-1">No</th>
                                            <th>Nama Admin Masjid</th>
                                            <th>Shubuh (A/I/F)</th>
                                            <th>Dzuhur (A/I/F)</th>
                                            <th>Jum'at (Slide)</th>
                                            <th>Ashar (A/I/F)</th>
                                            <th>Maghrib (A/I/F)</th>
                                            <th>Isya (A/I/F)</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($durasiList as $durasi)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-wrap">{{ $durasi->user->name }}</td>
                                                <td>{{ $durasi->adzan_shubuh }}/{{ $durasi->iqomah_shubuh }}/{{ $durasi->final_shubuh }}
                                                </td>
                                                <td>{{ $durasi->adzan_dzuhur }}/{{ $durasi->iqomah_dzuhur }}/{{ $durasi->final_dzuhur }}
                                                </td>
                                                <td>{{ $durasi->jumat_slide }}</td>
                                                <td>{{ $durasi->adzan_ashar }}/{{ $durasi->iqomah_ashar }}/{{ $durasi->final_ashar }}
                                                </td>
                                                <td>{{ $durasi->adzan_maghrib }}/{{ $durasi->iqomah_maghrib }}/{{ $durasi->final_maghrib }}
                                                </td>
                                                <td>{{ $durasi->adzan_isya }}/{{ $durasi->iqomah_isya }}/{{ $durasi->final_isya }}
                                                </td>
                                                <td class="text-end">
                                                    <button wire:click="edit({{ $durasi->id }})"
                                                        class="btn py-2 px-2 rounded-3 shadow-sm">
                                                        <span wire:loading.remove
                                                            wire:target="edit({{ $durasi->id }})">
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
                                                            wire:target="edit({{ $durasi->id }})"><span
                                                                class="spinner-border spinner-border-sm"
                                                                role="status" aria-hidden="true"></span>
                                                            <span class="small">loading...</span>
                                                        </span>
                                                    </button>
                                                    <button wire:click="delete('{{ $durasi->id }}')"
                                                        class="btn py-2 px-2 rounded-3 shadow-sm"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                        <span wire:loading.remove
                                                            wire:target="delete('{{ $durasi->id }}')">
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
                                                            wire:target="delete('{{ $durasi->id }}')">
                                                            <span class="spinner-border spinner-border-sm"
                                                                role="status" aria-hidden="true"></span>
                                                            <span class="small">loading...</span>
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
                                {{ $durasiList->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete modal --}}
    @include('livewire.durasi.delete')

    {{-- Close Modal and Notifications --}}
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
