{{-- Statistics Cards --}}
<div class="row g-3 mb-3">
    {{-- Total Users Card --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold" style="opacity:.85;">
                            Total Pengguna
                        </div>
                        <div class="fw-bold" style="font-size: 1.75rem;">
                            {{ $totalUsers }}
                        </div>
                        <div class="small fw-bold" style="color:#2563eb; opacity:.85;">
                            Akun terdaftar
                        </div>
                    </div>
                    <div
                        style="width:40px;height:40px;border-radius:12px;background:rgba(59,130,246,.12);display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users"
                            style="color:#2563eb;">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Users Card --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold" style="opacity:.85;">
                            Pengguna Disetujui
                        </div>
                        <div class="fw-bold" style="font-size: 1.75rem;">
                            {{ $activeUsers }}
                        </div>
                        <div class="small fw-bold" style="color:#10b981; opacity:.85;">
                            Status aktif
                        </div>
                    </div>
                    <div
                        style="width:40px;height:40px;border-radius:12px;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-check"
                            style="color:#10b981;">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                            <path d="M15 19l2 2l4 -4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inactive Users Card --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold" style="opacity:.85;">
                            Pengguna Belum Disetujui
                        </div>
                        <div class="fw-bold" style="font-size: 1.75rem;">
                            {{ $inactiveUsers }}
                        </div>
                        <div class="small fw-bold" style="color:#ef4444; opacity:.85;">
                            Status pending
                        </div>
                    </div>
                    <div
                        style="width:40px;height:40px;border-radius:12px;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-x"
                            style="color:#ef4444;">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                            <path d="M22 22l-5 -5" />
                            <path d="M17 22l5 -5" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- User Role Card --}}
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-bold" style="opacity:.85;">
                            Total Masjid
                        </div>
                        <div class="fw-bold" style="font-size: 1.75rem;">
                            {{ $userRoleCount }}
                        </div>
                        <div class="small fw-bold" style="color:#8b5cf6; opacity:.85;">
                            Entitas terdaftar
                        </div>
                    </div>
                    <div
                        style="width:40px;height:40px;border-radius:12px;background:rgba(139,92,246,.12);display:flex;align-items:center;justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-building-mosque"
                            style="color:#8b5cf6;">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 21h7v-2a2 2 0 1 1 4 0v2h7" />
                            <path d="M4 21v-10" />
                            <path d="M20 21v-10" />
                            <path d="M4 16h3v-3h10v3h3" />
                            <path d="M17 13a5 5 0 0 0 -10 0" />
                            <path
                                d="M21 10.5c0 -.329 -.077 -.653 -.224 -.947l-.776 -1.553l-.776 1.553a2.118 2.118 0 0 0 -.224 .947a.5 .5 0 0 0 .5 .5h1a.5 .5 0 0 0 .5 -.5z" />
                            <path
                                d="M5 10.5c0 -.329 -.077 -.653 -.224 -.947l-.776 -1.553l-.776 1.553a2.118 2.118 0 0 0 -.224 .947a.5 .5 0 0 0 .5 .5h1a.5 .5 0 0 0 .5 -.5z" />
                            <path d="M12 2a2 2 0 1 0 2 2" />
                            <path d="M12 6v2" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
