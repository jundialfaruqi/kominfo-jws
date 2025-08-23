<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                @if ($showForm)
                                    {{ $isEdit ? 'Ubah Pengaturan Gambar Slide Masjid' : 'Tambah Slide Masjid Baru' }}
                                @else
                                    {{ Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']) ? 'Daftar Gambar Slide Masjid' : 'Ubah Pengaturan Gambar Slide Masjid' }}
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
                                            Tambah Slide
                                        </span>
                                        <span wire:loading wire:target="showAddForm">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">
                                                Loading...
                                            </span>
                                        </span>
                                        </span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Form untuk tambah/edit slide --}}
                        @include('livewire.slides.form')

                        @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
                            @if ($showTable)
                                {{-- Pagination & search control --}}
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

                                {{-- table --}}
                                @include('livewire.slides.table')

                                <div
                                    class="card-footer d-flex align-items-center justify-content-end pb-0 rounded-4 shadow-sm">
                                    {{ $slideList->links() }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    @include('livewire.slides.delete')

    {{-- Close Modal --}}
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

        <script>
            document.addEventListener('livewire:initialized', function() {
                // Listen untuk event resetFileInput dari Livewire
                Livewire.on('resetFileInput', (data) => {
                    const inputName = data.inputName;
                    const fileInput = document.querySelector(`input[wire\\:model="${inputName}"]`);
                    if (fileInput) {
                        fileInput.value = '';
                        // Trigger change event untuk memastikan Livewire mendeteksi perubahan
                        fileInput.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });
            });
        </script>
    @endscript

</div>
