<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        @include('livewire.petugas.header')

                        {{-- form untuk tambah/edit petugas --}}
                        @include('livewire.petugas.form')

                        {{-- table petugas --}}
                        @include('livewire.petugas.table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- delete modal --}}
    @include('livewire.petugas.delete')

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
    @endscript
</div>
