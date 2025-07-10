<div wire:ignore.self class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="modal-title text-danger">Apakah anda yakin ingin menghapus Data Audio Adzan:</div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-volume">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M15 8a5 5 0 0 1 0 8" />
                        <path d="M17.7 5a9 9 0 0 1 0 14" />
                        <path d="M6 15h-2a1 1 0 0 1 -1 -1v-4a1 1 0 0 1 1 -1h2l3.5 -4.5a.8 .8 0 0 1 1.5 .5v14a.8 .8 0 0 1 -1.5 .5l-3.5 -4.5" />
                      </svg>
                    <span class="fw-bold">
                        {{ $deleteAdzanAudioName }}
                    </span>?
                </div>
                <div class="pt-3">Pilih <b>Ya, Hapus</b> untuk melanjutkan</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto"
                    data-bs-dismiss="modal">Batal</button>
                <button wire:loading.attr="disabled" wire:click="destroyAdzanAudio" type="button" class="btn btn-danger">
                    <span wire:loading.remove wire:target="destroyAdzanAudio">
                        Ya, Hapus
                    </span>
                    <span wire:loading wire:target="destroyAdzanAudio">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>