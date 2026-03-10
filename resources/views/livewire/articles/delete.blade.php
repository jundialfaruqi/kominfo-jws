<div wire:ignore.self class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="modal-title text-danger">Apakah anda yakin ingin menghapus berita?</div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-news">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M16 6h3a1 1 0 0 1 1 1v11a2 2 0 0 1 -4 0v-13a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a3 3 0 0 0 3 3h11" />
                        <path d="M8 8l4 0" />
                        <path d="M8 12l4 0" />
                        <path d="M8 16l4 0" />
                    </svg>
                    <span class="fw-bold">
                        {{ $deleteArticleTitle }}
                    </span>
                </div>
                <div class="pt-3">Pilih <b>Ya, Hapus</b> untuk melanjutkan</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto"
                    data-bs-dismiss="modal">Batal</button>
                <button wire:loading.attr="disabled" wire:click="destroyArticle" type="button" class="btn btn-danger">
                    <span wire:loading.remove wire:target="destroyArticle">
                        Ya, Hapus
                    </span>
                    <span wire:loading wire:target="destroyArticle">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
