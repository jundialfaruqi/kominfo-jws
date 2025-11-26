{{-- Statistics Cards --}}
<div class="row g-3 mb-3">
    {{-- Agenda 7 Hari Ke Depan --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold" style="opacity:.85;">
                            Agenda 7 Hari Ke Depan
                        </div>
                        <div class="fw-bold" style="font-size: 1.75rem;">
                            {{ $agenda7HariKedepan }}
                        </div>
                        <div class="small fw-bold" style="color:#2563eb; opacity:.85;">
                            Terjadwal 7 hari ke depan
                        </div>
                    </div>
                    <div
                        style="width:40px;height:40px;border-radius:12px;background:rgba(59,130,246,.12);display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event"
                            style="color:#2563eb;">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                            <path d="M8 15h2v2h-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Agenda Tidak Aktif --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold" style="opacity:.85;">
                            Agenda Tidak Aktif
                        </div>
                        <div class="fw-bold" style="font-size: 1.75rem;">
                            {{ $agendaTidakAktif }}
                        </div>
                        <div class="small fw-bold" style="color:#ef4444; opacity:.85;">
                            Perlu ditinjau/diaktifkan
                        </div>
                    </div>
                    <div
                        style="width:40px;height:40px;border-radius:12px;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-x"
                            style="color:#ef4444;">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M10 10l4 4m0 -4l-4 4" />
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
