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
                            <select class="form-select rounded-3 @error('userId') is-invalid @enderror"
                                wire:model="userId">
                                <option class="dropdown-header" value="">Pilih Admin
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
                                @endif {{-- Tampilkan user yang belum memiliki Petugas Jumat --}}
                                @foreach ($users as $user)
                                    {{-- Jangan tampilkan user yang sudah dipilih saat edit --}}
                                    @if (!($isEdit && $userId == $user->id))
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endif
                                @endforeach {{-- Jika tidak ada user yang tersedia --}}
                                @if ($users->isEmpty() && !($isEdit && $userId))
                                    <option disabled>Tidak ada user yang tersedia</option>
                                @endif
                            </select>
                            @error('userId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror {{-- Informasi tambahan --}}
                            @if ($users->isEmpty() && !$isEdit)
                                <div class="form-text">
                                    <small class="text-muted">Semua user sudah memiliki
                                        Petugas Jumat</small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="row g-2 mb-3">
                    <div class="col-md-2">
                        <label class="form-label required">Tanggal
                        </label>
                    </div>
                    <div class="col-md-10">
                        <input type="date"
                            class="form-control rounded-3 @error('hari') is-invalid @enderror {{ $hariError ? 'is-invalid' : '' }}"
                            wire:model="hari">
                        @error('hari')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($hariError)
                            <div class="invalid-feedback d-block">{{ $hariError }}</div>
                        @endif
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-2">
                        <label class="form-label required">Khatib
                        </label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" class="form-control rounded-3 @error('khatib') is-invalid @enderror"
                            wire:model="khatib" placeholder="Masukkan Nama Khatib">
                        @error('khatib')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-2">
                        <label class="form-label required">Imam
                        </label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" class="form-control rounded-3 @error('imam') is-invalid @enderror"
                            wire:model="imam" placeholder="Masukkan Nama imam">
                        @error('imam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-2">
                        <label class="form-label required">Muadzin
                        </label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" class="form-control rounded-3 @error('muadzin') is-invalid @enderror"
                            wire:model="muadzin" placeholder="Masukkan Nama muadzin">
                        @error('muadzin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer rounded-bottom-4 border-0">
            <div class="d-flex justify-content-end gap-2">
                @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
                    <button type="button" wire:click="cancelForm" class="btn py-2 px-2 rounded-3 shadow-sm">
                        <span wire:loading.remove wire:target="cancelForm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
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
