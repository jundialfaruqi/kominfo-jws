<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="alert alert-danger rounded-4 shadow-sm" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon alert-icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                            <path d="M12 8v4" />
                            <path d="M12 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="alert-title">Akun Tidak Aktif!</h4>
                        <div class="text-secondary">
                            Akun kamu tidak aktif atau ditangguhkan. Silahkan hubungi Admin untuk
                            mengaktifkan akun.
                        </div>
                    </div>
                </div>
            </div>
            <div class="ms-auto d-flex align-items-center">
                <button type="button" class="btn btn-danger rounded-4 shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#inactiveAccountModal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-alert-triangle">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9v4" />
                        <path
                            d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                        <path d="M12 16h.01" />
                    </svg>
                    Hubungi Admin
                </button>
            </div>
        </div>
    </div>
    @include('livewire.inactive.inactive-modal')

    @script
        <script>
            $wire.on('closeInactiveModal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('inactiveAccountModal'));
                if (modal) {
                    modal.hide();
                }
            });
        </script>
    @endscript
</div>
