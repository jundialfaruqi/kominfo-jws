<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                @if ($showForm)
                                    {{ $isEdit ? 'Ubah Pengaturan Profil Masjid' : 'Tambah Profil Masjid Baru' }}
                                @else
                                    {{ Auth::user()->role === 'Admin' ? 'Daftar Profil Masjid' : 'Ubah Pengaturan Profil Masjid' }}
                                @endif
                            </h3>
                            @if (Auth::user()->role === 'Admin' && !$showForm)
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
                                            Tambah Profil Masjid
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

                        <!-- Form untuk Tambah/Edit Profil Masjid -->
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
                                                    <input type="text"
                                                        class="form-control rounded-3 @error('name') is-invalid @enderror"
                                                        wire:model="name" placeholder="Masukkan nama masjid">
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            @if (Auth::user()->role === 'Admin')
                                                <div class="row g-2 mb-3">
                                                    <div class="col-md-2">
                                                        <label class="form-label">Admin Masjid</label>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <select
                                                            class="form-select rounded-3 @error('userId') is-invalid @enderror"
                                                            wire:model="userId">
                                                            <option class="dropdown-header" selected>Pilih Admin Masjid
                                                            </option>
                                                            @foreach ($users as $user)
                                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('userId')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-2">
                                                    <label class="form-label required">No HP</label>
                                                </div>
                                                <div class="col-md-10">
                                                    <input type="text"
                                                        class="form-control rounded-3 @error('phone') is-invalid @enderror"
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
                                                    <!-- Photo logo masjid-->
                                                    @if ($logo_masjid)
                                                        <div class="card p-5 rounded-3">
                                                            <div class="img-responsive img-responsive-21x9"
                                                                style="background-image: url('{{ $logo_masjid->temporaryUrl() }}'); background-size: contain; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($temp_logo)
                                                        <div class="card p-5 rounded-3">
                                                            <div class="img-responsive img-responsive-21x9"
                                                                style="background-image: url('{{ asset($temp_logo) }}'); background-size: contain; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="logo_masjid"
                                                        class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi untuk
                                                            memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, GIF.
                                                            Maks:
                                                            1MB</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-3 @error('logo_masjid') is-invalid @enderror"
                                                        wire:model="logo_masjid" accept="image/*">
                                                    @error('logo_masjid')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Logo Pemerintah</label>
                                                    <!-- Photo logo pemerintah -->
                                                    @if ($logo_pemerintah)
                                                        <div class="card p-5 rounded-3">
                                                            <div class="img-responsive img-responsive-21x9 card-img-top"
                                                                style="background-image: url('{{ $logo_pemerintah->temporaryUrl() }}'); background-size: contain; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @elseif($temp_logo_pemerintah)
                                                        <div class="card p-5 rounded-3">
                                                            <div class="img-responsive img-responsive-21x9 card-img-top"
                                                                style="background-image: url('{{ asset($temp_logo_pemerintah) }}'); background-size: contain; background-position: center;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div wire:loading wire:target="logo_pemerintah"
                                                        class="mt-2 text-center">
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="small">Mengupload...</span>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Tekan Browse/Jelajahi untuk
                                                            memilih
                                                            gambar</small>
                                                    </div>
                                                    <div class="form-text">
                                                        <small class="text-muted">*Format: JPG, PNG, GIF.
                                                            Maks:
                                                            1MB</small>
                                                    </div>
                                                    <input type="file"
                                                        class="form-control my-2 rounded-3 @error('logo_pemerintah') is-invalid @enderror"
                                                        wire:model="logo_pemerintah" accept="image/*">
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
                                        @if (Auth::user()->role === 'Admin')
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

                        @if (Auth::user()->role === 'Admin')
                            <!-- Pagination & Search Controls -->
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

                            <!-- Table of mosque profiles -->
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter table-hover text-nowrap datatable">
                                    <thead>
                                        <tr>
                                            <th class="w-1">No.</th>
                                            <th>Nama Masjid</th>
                                            <th>Admin Masjid</th>
                                            <th>Alamat Masjid</th>
                                            <th>No Hp</th>
                                            <th>Logo Masjid</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($profilList as $profil)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-wrap">{{ $profil->name }}</td>
                                                <td class="text-wrap">{{ $profil->user->name ?? '-' }}</td>
                                                <td class="text-wrap">{{ $profil->address }}</td>
                                                <td class="text-wrap">{{ $profil->phone }}</td>
                                                <td>
                                                    @if ($profil->logo_masjid)
                                                        <img src="{{ asset($profil->logo_masjid) }}" width="60"
                                                            class="img-thumbnail">
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <button wire:click="edit('{{ $profil->id }}')"
                                                        class="btn py-2 px-2 rounded-3 shadow-sm">
                                                        <span wire:loading.remove
                                                            wire:target="edit('{{ $profil->id }}')">
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
                                                        <span wire:loading wire:target="edit('{{ $profil->id }}')">
                                                            <span class="spinner-border spinner-border-sm"
                                                                role="status" aria-hidden="true"></span>
                                                            <span class="small">Loading...</span>
                                                        </span>
                                                    </button>
                                                    <button wire:click="delete('{{ $profil->id }}')"
                                                        class="btn py-2 px-2 rounded-3 shadow-sm"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                                        <span wire:loading.remove
                                                            wire:target="delete('{{ $profil->id }}')">
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
                                                            wire:target="delete('{{ $profil->id }}')">
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
                                {{ $profilList->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    @include('livewire.profil.delete')

    {{-- Close Modal --}}
    @script
        <script>
            $wire.on('closeDeleteModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                if (modal) {
                    modal.hide();
                }
            });

            // Untuk menampilkan notifikasi
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
