<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header bg-dark text-white rounded-top-4">
                            <h3 class="card-title d-none d-md-block">
                                Gambar Jumbotron Masjid
                            </h3>
                            <div class="card-actions">
                                @can('create-jumbotron-masjid')
                                    <a href="{{ route('jumbotron-masjid.edit') }}"
                                        class="btn btn-primary py-2 rounded-4 shadow-sm">
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                <path d="M13.5 6.5l4 4" />
                                                <path d="M16 19h6" />
                                                <path d="M19 16v6" />
                                            </svg>
                                            Ubah Jumbotron Masjid
                                        </span>
                                    </a>
                                @endcan
                            </div>
                        </div>

                        {{-- Tabel Jumbotron Masjid --}}
                        @include('livewire.jumbotron-masjid.jumbotron-masjid-table')

                        {{-- Pagination --}}
                        {{-- <div class="card-footer align-items-center pb-0 rounded-bottom-4 shadow-sm">
                            {{ $jumbotronMasjidsData->links() }}
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var success = {!! json_encode(session('success')) !!};
        var error = {!! json_encode(session('error')) !!};
        if (success) {
            if (window.iziToast) {
                iziToast.success({
                    title: 'Berhasil',
                    message: success,
                    position: 'topRight'
                });
            } else {
                alert(success);
            }
        }
        if (error) {
            if (window.iziToast) {
                iziToast.error({
                    title: 'Gagal',
                    message: error,
                    position: 'topRight'
                });
            } else {
                alert(error);
            }
        }
    });
</script>

{{-- @include('livewire.jumbotron.delete') --}}

{{-- @script
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
                Livewire.on('resetFileInput', (data) => {
                    const inputName = data.inputName;
                    const fileInput = document.querySelector(`input[wire\\:model="${inputName}"]`);
                    if (fileInput) {
                        fileInput.value = '';
                        fileInput.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });

                // Sinkronkan status toggle setelah pembaruan Livewire
                Livewire.on('updated', (data) => {
                    const isActiveInput = document.getElementById('is_active');
                    if (isActiveInput) {
                        isActiveInput.checked = @json($is_active);
                    }
                });
            });
        </script>
    @endscript --}}
</div>
