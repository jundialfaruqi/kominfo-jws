<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card rounded-4 shadow-sm border-0">
                {{-- Header --}}
                @include('livewire.article-categories.section-header')
                {{-- Form --}}
                @include('livewire.article-categories.section-form')
                {{-- Table --}}
                @include('livewire.article-categories.section-table')
            </div>
        </div>

        {{-- Delete Modal --}}
        @include('livewire.article-categories.delete')

        {{-- Scripts --}}
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
</div>
