{{-- Card Pengaturan Tampilan Laporan Keuangan pada Display JWS (Tema 3 & Tema 4) --}}
<div class="card mb-3 rounded-3 shadow-sm border"
     x-data="{
         currentVal: '{{ $isFinanceActive ? '1' : '0' }}',
         originalVal: '{{ $isFinanceActive ? '1' : '0' }}'
     }"
     x-init="$watch('$wire.isFinanceActive', val => {
         const sVal = val ? '1' : '0';
         currentVal = sVal;
         originalVal = sVal;
     })"
     @finance-status-saved.window="
         const sVal = $event.detail.status ? '1' : '0';
         currentVal = sVal;
         originalVal = sVal;
     ">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="fw-bold text-dark fs-3">Aktifkan laporan keuangan?</span>
                <div class="d-flex align-items-center gap-3">
                    <label class="form-check form-check-inline m-0" style="cursor: pointer;">
                        <input class="form-check-input" type="radio" value="1" x-model="currentVal" name="financeActiveRadio_{{ $idMasjid ?? 'default' }}">
                        <span class="form-check-label fw-semibold text-success">Ya, tampilkan</span>
                    </label>
                    <label class="form-check form-check-inline m-0" style="cursor: pointer;">
                        <input class="form-check-input" type="radio" value="0" x-model="currentVal" name="financeActiveRadio_{{ $idMasjid ?? 'default' }}">
                        <span class="form-check-label fw-semibold text-danger">Tidak, jangan tampilkan</span>
                    </label>
                </div>
            </div>
            <div>
                <button type="button"
                        class="btn btn-primary px-4 rounded-3 shadow-sm"
                        :disabled="currentVal === originalVal"
                        @click="$wire.saveFinanceStatus(currentVal === '1')"
                        wire:loading.attr="disabled"
                        wire:target="saveFinanceStatus">
                    <span wire:loading.remove wire:target="saveFinanceStatus">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                            <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M14 4l0 4l-6 0l0 -4" />
                        </svg>
                        Simpan
                    </span>
                    <span wire:loading wire:target="saveFinanceStatus">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
