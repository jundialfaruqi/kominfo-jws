<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm border-0">
                        <div class="card-header rounded-top-4 bg-dark text-white">
                            <h3 class="card-title d-none d-md-block">
                                @if ($isAdmin && $showForm)
                                    {{ $isEdit ? 'Ubah Pengaturan Tema' : 'Tambah Tema Baru' }}
                                @else
                                    {{ $isAdmin ? 'Daftar Tema' : 'Tema' }}
                                @endif
                            </h3>
                            @if ($isAdmin && !$showForm)
                                <div class="card-actions">
                                    <button wire:click="showAddForm" class="btn btn-primary py-2 rounded-4 shadow-sm"
                                        @if (Auth::user()->role !== 'Super Admin') style="display: none;" @endif>
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
                                            Tambah Tema
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
                        {{-- Form --}}
                        @include('livewire.tema.form')
                        {{-- Table Section --}}
                        @if ($showTable)
                            @include('livewire.tema.table')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($isAdmin)
        {{-- Delete Modal --}}
        @include('livewire.tema.delete')
    @endif
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

            $wire.on('resetFileInput', event => {
                const input = document.querySelector(`input[name="${event.inputName}"]`);
                if (input) {
                    input.value = '';
                }
            });
        </script>
    @endscript
</div>
