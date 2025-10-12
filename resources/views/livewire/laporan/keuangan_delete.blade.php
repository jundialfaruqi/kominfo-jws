<div wire:ignore.self class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="modal-title text-danger">Apakah anda yakin ingin menghapus Data Laporan Keuangan untuk
                    Profil:</div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-building-mosque">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 21h18" />
                        <path d="M5 21v-9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v9" />
                        <path d="M13 21v-9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v9" />
                        <path d="M9 21v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                        <path d="M9.5 8.5l5 -5" />
                        <path d="M9.5 3.5l5 5" />
                        <path d="M12 3v5" />
                    </svg>
                    <span class="fw-bold">
                        {{ $deleteLaporanName }}
                    </span>?
                </div>
                <div class="pt-3">Pilih <b>Ya, Hapus</b> untuk melanjutkan</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto"
                    data-bs-dismiss="modal">Batal</button>
                <button wire:loading.attr="disabled" wire:click="destroyLaporan" type="button" class="btn btn-danger">
                    <span wire:loading.remove wire:target="destroyLaporan">
                        Ya, Hapus
                    </span>
                    <span wire:loading wire:target="destroyLaporan">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>