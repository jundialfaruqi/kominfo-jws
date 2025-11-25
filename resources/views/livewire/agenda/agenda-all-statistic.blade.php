@role('Super Admin||Admin')
    {{-- Statistics Cards --}}
    <div class="row g-3 mb-3">
        {{-- Konten Siap Tayang --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-secondary small fw-bold" style="opacity:.85;">
                                Masjid Memiliki Agenda
                            </div>
                            <div class="fw-bold" style="font-size: 1.75rem;">
                                {{ $masjidDenganAgendaPercent }}%
                            </div>
                            <div class="small fw-bold" style="color:#2563eb; opacity:.85;">
                                Sudah memiliki agenda
                            </div>
                        </div>
                        <div
                            style="width:40px;height:40px;border-radius:12px;background:rgba(59,130,246,.12);display:flex;align-items:center;justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list-check"
                                style="color:#2563eb;">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3.5 5.5l1.5 1.5l2.5 -2.5" />
                                <path d="M3.5 11.5l1.5 1.5l2.5 -2.5" />
                                <path d="M3.5 17.5l1.5 1.5l2.5 -2.5" />
                                <path d="M11 6l9 0" />
                                <path d="M11 12l9 0" />
                                <path d="M11 18l9 0" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Masjid Perlu Dilengkapi --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-secondary small fw-bold" style="opacity:.85;">
                                Masjid Tanpa Agenda
                            </div>
                            <div class="fw-bold" style="font-size: 1.75rem;">
                                {{ $masjidPerluDilengkapi }}%
                            </div>
                            <div class="small fw-bold" style="color:#ef4444; opacity:.85;">
                                Belum ada agenda
                            </div>
                        </div>
                        <div
                            style="width:40px;height:40px;border-radius:12px;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-alert-triangle"
                                style="color:#ef4444;">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 9v4" />
                                <path
                                    d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                                <path d="M12 16h.01" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Agenda Aktif Minggu Ini --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-secondary small fw-bold" style="opacity:.85;">
                                Agenda Aktif Minggu Ini
                            </div>
                            <div class="fw-bold" style="font-size: 1.75rem;">
                                {{ $agendaAktifMingguIni }}
                            </div>
                            <div class="small fw-bold" style="color:#10b981; opacity:.85;">
                                Dalam minggu berjalan
                            </div>
                        </div>
                        <div
                            style="width:40px;height:40px;border-radius:12px;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar"
                                style="color:#10b981;">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h16" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pembaruan 24 Jam Terakhir --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-secondary small fw-bold" style="opacity:.85;">
                                Pembaruan 24 Jam Terakhir
                            </div>
                            <div class="fw-bold" style="font-size: 1.75rem;">
                                {{ $perubahan24JamTerakhir }}
                            </div>
                            <div class="small fw-bold" style="color:#8b5cf6; opacity:.85;">
                                Perubahan konten
                            </div>
                        </div>
                        <div
                            style="width:40px;height:40px;border-radius:12px;background:rgba(139,92,246,.12);display:flex;align-items:center;justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-history"
                                style="color:#8b5cf6;">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 8l0 4l2 2" />
                                <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endrole
