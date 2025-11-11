{{-- Statistics Cards --}}
<div class="card-body">
    <div class="row g-3">
        {{-- Total Users Card --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm bg-primary-lt rounded-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar rounded-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-primary">
                                {{ $totalUsers }}
                            </div>
                            <div class="text-secondary small">
                                Total Pengguna
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Users Card --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm bg-success-lt rounded-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar rounded-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-check">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                    <path d="M15 19l2 2l4 -4" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-success">
                                {{ $activeUsers }}
                            </div>
                            <div class="text-secondary small">
                                Pengguna Aktif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inactive Users Card --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm bg-danger-lt rounded-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-danger text-white avatar rounded-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-x">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                                    <path d="M22 22l-5 -5" />
                                    <path d="M17 22l5 -5" />
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-danger">
                                {{ $inactiveUsers }}
                            </div>
                            <div class="text-secondary small">
                                Pengguna Tidak Aktif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- User Role Card --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm bg-info-lt rounded-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar rounded-circle">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-building-mosque">
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
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-info">
                                {{ $userRoleCount }}
                            </div>
                            <div class="text-secondary small">
                                Total Masjid
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
