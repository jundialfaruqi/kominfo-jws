<div wire:ignore.self class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="modal-title text-danger">Apakah anda yakin ingin menghapus?</div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-category">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 4h6v6h-6z" />
                        <path d="M14 4h6v6h-6z" />
                        <path d="M4 14h6v6h-6z" />
                        <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                    </svg>
                    <span class="fw-bold">
                        {{ $deleteCategoryName }}
                    </span>
                </div>
                <div class="pt-3">Pilih <b>Ya, Hapus</b> untuk melanjutkan</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto"
                    data-bs-dismiss="modal">Batal</button>
                <button wire:loading.attr="disabled" wire:click="destroyCategory" type="button" class="btn btn-danger">
                    <span wire:loading.remove wire:target="destroyCategory">
                        Ya, Hapus
                    </span>
                    <span wire:loading wire:target="destroyCategory">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
