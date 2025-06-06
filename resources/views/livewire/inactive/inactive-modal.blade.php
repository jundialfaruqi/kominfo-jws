<!-- Modal untuk akun tidak aktif -->
<div wire:ignore.self class="modal modal-blur fade" id="inactiveAccountModal" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered rounded-4" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24"
                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 9v4" />
                    <path
                        d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                    <path d="M12 16h.01" />
                </svg>
                <h3>Akun Tidak Aktif</h3>
                <div class="text-secondary">
                    Akun kamu tidak aktif atau ditangguhkan. Silahkan hubungi Admin untuk mengaktifkan akun.
                </div>
            </div>
            <div class="modal-footer rounded-4">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn rounded-4 w-100" data-bs-dismiss="modal">
                                Tutup
                            </button>
                        </div>
                        <div class="col">
                            <a href="mailto:jundialfaruqi@gmail.com" class="btn btn-danger rounded-4 w-100">
                                Hubungi Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
