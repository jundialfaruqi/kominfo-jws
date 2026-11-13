@if (Auth::check())
    <div wire:ignore.self class="modal modal-blur fade" id="pdfProgressModal" tabindex="-1" role="dialog"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title d-flex align-items-center gap-2 mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf text-danger">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                            <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                            <path d="M17 18h2" />
                            <path d="M20 15h-3v6" />
                            <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" />
                        </svg>
                        <span>Generate PDF Informasi Akun</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="bg-light rounded-3 p-3 mb-3 border">
                        <div class="fw-bold text-dark" id="pdfModalUserName">-</div>
                        <div class="text-muted small" id="pdfModalUserEmail">-</div>
                        <div class="text-muted small mt-1" id="pdfModalUserMosque">-</div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-medium text-secondary" id="pdfProgressStatus">
                            Menyiapkan proses generate PDF...
                        </span>
                        <span class="badge bg-primary-lt fw-bold" id="pdfProgressPercent">0%</span>
                    </div>

                    <div class="progress rounded-pill mb-3" style="height: 12px;">
                        <div id="pdfProgressBar"
                            class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100"></div>
                    </div>

                    <div id="pdfStepsList" class="small text-muted d-flex flex-column gap-1 mb-2">
                        <div class="d-flex align-items-center gap-2" data-pdf-step="1">
                            <span class="badge bg-secondary-lt rounded-pill">1</span>
                            <span>Memvalidasi data akun &amp; profil masjid</span>
                        </div>
                        <div class="d-flex align-items-center gap-2" data-pdf-step="2">
                            <span class="badge bg-secondary-lt rounded-pill">2</span>
                            <span>Menyiapkan kop surat resmi &amp; logo WebP</span>
                        </div>
                        <div class="d-flex align-items-center gap-2" data-pdf-step="3">
                            <span class="badge bg-secondary-lt rounded-pill">3</span>
                            <span>Menyusun informasi akun &amp; aturan aplikasi JWS</span>
                        </div>
                        <div class="d-flex align-items-center gap-2" data-pdf-step="4">
                            <span class="badge bg-secondary-lt rounded-pill">4</span>
                            <span>Merender dokumen PDF</span>
                        </div>
                    </div>

                    <div id="pdfSuccessBox" class="alert alert-success rounded-3 mb-0 mt-3 d-none" role="alert">
                        <div class="fw-bold mb-1">File PDF siap diunduh!</div>
                        <div class="small text-secondary" id="pdfFileMeta">-</div>
                    </div>

                    <div id="pdfErrorBox" class="alert alert-danger rounded-3 mb-0 mt-3 d-none" role="alert">
                        <div class="fw-bold mb-1">Gagal membuat PDF</div>
                        <div class="small" id="pdfErrorMessage">Terjadi kesalahan saat memproses file PDF.</div>
                    </div>
                </div>

                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <button type="button" id="pdfRetryBtn" class="btn btn-outline-primary rounded-3 d-none">
                        Coba Lagi
                    </button>
                    <a id="pdfDownloadLink" href="#"
                        class="btn btn-danger rounded-3 d-none d-inline-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 11l5 5l5 -5" />
                            <path d="M12 4l0 12" />
                        </svg>
                        <span>Download PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

