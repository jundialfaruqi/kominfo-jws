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

                {{-- Durasi Shuruq --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-2">
                        <label class="form-label required">Syuruq</label>
                    </div>
                    <div class="col-md-10">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Menit</label>
                                <select class="form-select rounded-3 @error('adzan_shuruq') is-invalid @enderror"
                                    wire:model="adzan_shuruq">
                                    <option value="">Pilih durasi shuruq</option>
                                    @for ($i = 2; $i <= 20; $i++)
                                        <option value="{{ $i }}">
                                            {{ $i }} menit</option>
                                    @endfor
                                </select>
                                @error('adzan_shuruq')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Durasi Shubuh --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-2">
                        <label class="form-label required">Shubuh</label>
                    </div>
                    <div class="col-md-10">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Adzan (menit)</label>
                                <select class="form-select rounded-3 @error('adzan_shubuh') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('iqomah_shubuh') is-invalid @enderror"
                                    wire:model="iqomah_shubuh">
                                    <option value="">Pilih durasi iqomah</option>
                                    @for ($i = 1; $i <= 25; $i++)
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
                                <select class="form-select rounded-3 @error('final_shubuh') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('adzan_dzuhur') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('iqomah_dzuhur') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('final_dzuhur') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('jumat_slide') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('adzan_ashar') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('iqomah_ashar') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('final_ashar') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('adzan_maghrib') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('iqomah_maghrib') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('final_maghrib') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('adzan_isya') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('iqomah_isya') is-invalid @enderror"
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
                                <select class="form-select rounded-3 @error('final_isya') is-invalid @enderror"
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
