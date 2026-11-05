<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card rounded-4 shadow-sm border-0">
                {{-- Header Profil Masjid --}}
                @include('livewire.profil.section-header')
                {{-- Form untuk Tambah/Edit Profil Masjid --}}
                @include('livewire.profil.section-form')
                {{-- Data Table Profil Masjid --}}
                @include('livewire.profil.section-table')
            </div>
        </div>

        {{-- Delete Modal --}}
        @include('livewire.profil.delete')

        {{-- Close Modal --}}
        @script
            <script>
                $wire.on('resetFileInput', (data) => {
                    const name = data?.inputName || (Array.isArray(data) && data[0]?.inputName) || data;
                    if (typeof resetLogoInput === 'function') {
                        resetLogoInput(name);
                    }
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
</div>
