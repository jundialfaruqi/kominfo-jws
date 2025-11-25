<aside class="navbar navbar-vertical navbar-expand-lg sticky-top" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand me-lg-3 me-3">
            <a wire:navigate href="{{ route('dashboard.index') }}">
                <span class="navbar-brand-image">
                    <a wire:navigate href="{{ route('dashboard.index') }}">
                        <img src="{{ asset('nav-brand.png') }}" width="30" alt="JWS Diskominfo"
                            class="navbar-brand-image">
                    </a>
                </span>
                <span class="navbar-brand-text">JWS Diskominfo</span>
            </a>
        </h1>
        <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    @if (Auth::user()->photo)
                        <img class="avatar avatar-sm rounded-circle" src="{{ asset(Auth::user()->photo) }}"
                            alt="{{ Auth::user()->name }}">
                    @else
                        <span class="avatar avatar-sm rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                        </span>
                    @endif
                    <div class="d-none d-xl-block ps-2">
                        <div>Admin</div>
                        <div class="mt-1 small text-secondary">Administrator</div>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end rounded-3" aria-labelledby="adminDropdown">
                    <li>
                        @livewire('auth.logout')
                    </li>
                    <li>
                        <a wire:navigate href="{{ route('updateprofile.index') }}"
                            class="dropdown-item d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065z" />
                                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                            </svg>
                            Pengaturan
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav">
                @if (Auth::user()->status === 'Active')
                    <div class="mt-3 mb-1 px-3">
                        <div class="d-flex align-items-center">
                            <div class="text-muted small fw-bold ms-2">MENU</div>
                        </div>
                    </div>
                    <li class="nav-item mx-2 {{ request()->routeIs('dashboard.index') ? 'bg-azure rounded-4' : '' }}">
                        <a wire:navigate class="nav-link" href="{{ route('dashboard.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-layout-dashboard">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                                    <path
                                        d="M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
                                    <path
                                        d="M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                                    <path
                                        d="M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
                                </svg>
                            </span>
                            <span class="nav-link-title text-white">
                                Dashboard
                            </span>
                        </a>
                    </li>
                    <li
                        class="nav-item mx-2 {{ request()->routeIs('profilmasjid.index') ? 'bg-azure rounded-4' : '' }}">
                        <a wire:navigate class="nav-link" href="{{ route('profilmasjid.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
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
                            <span class="nav-link-title text-white">
                                Profil Masjid
                            </span>
                        </a>
                    </li>
                    <li class="nav-item mx-2 {{ request()->routeIs('tema.index') ? 'bg-azure rounded-4' : '' }}">
                        <a wire:navigate class="nav-link" href="{{ route('tema.index') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-template">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M4 4m0 1a1 1 0 0 1 1 -1h14a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-14a1 1 0 0 1 -1 -1z" />
                                    <path
                                        d="M4 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    <path d="M14 12l6 0" />
                                    <path d="M14 16l6 0" />
                                    <path d="M14 20l6 0" />
                                </svg>
                            </span>
                            <span class="nav-link-title text-white">
                                Tema JWS
                            </span>
                        </a>
                    </li>
                    @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
                        <li
                            class="nav-item mx-2 {{ request()->routeIs('tema.set-tema') ? 'bg-azure rounded-4' : '' }}">
                            <a wire:navigate class="nav-link" href="{{ route('tema.set-tema') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-brush">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 21v-4a4 4 0 1 1 4 4h-4" />
                                        <path d="M21 3a16 16 0 0 0 -12.8 10.2" />
                                        <path d="M21 3a16 16 0 0 1 -10.2 12.8" />
                                        <path d="M10.6 9a9 9 0 0 1 4.4 4.4" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Set Tema
                                </span>
                            </a>
                        </li>
                    @endif

                    {{-- Manajemen User --}}
                    @if (Auth::check() && in_array(Auth::user()->role, ['Super Admin', 'Admin']))
                        @can('view-users')
                            <div>
                                <div class="mt-3 mb-1 px-3">
                                    <div class="d-flex align-items-center">
                                        <div class="text-muted small fw-bold ms-2">MANAJEMEN USER</div>
                                    </div>
                                </div>
                                <li
                                    class="nav-item mx-2 {{ request()->routeIs('admin.user.index') ? 'bg-azure rounded-4 shadow-sm' : '' }}">
                                    <a wire:navigate class="nav-link" href="{{ route('admin.user.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                                            </svg>
                                        </span>
                                        <span class="nav-link-title text-white">
                                            User
                                        </span>
                                    </a>
                                </li>
                            @endcan

                            @can('view-roles')
                                <li
                                    class="nav-item mx-2 {{ request()->routeIs('admin.role.index') ? 'bg-azure rounded-4' : '' }}">
                                    <a wire:navigate class="nav-link" href="{{ route('admin.role.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-shield-cog">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M12 21a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3a12 12 0 0 0 8.5 3c.568 1.933 .635 3.957 .223 5.89" />
                                                <path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                <path d="M19.001 15.5v1.5" />
                                                <path d="M19.001 21v1.5" />
                                                <path d="M22.032 17.25l-1.299 .75" />
                                                <path d="M17.27 20l-1.3 .75" />
                                                <path d="M15.97 17.25l1.3 .75" />
                                                <path d="M20.733 20l1.3 .75" />
                                            </svg>
                                        </span>
                                        <span class="nav-link-title text-white">
                                            Role
                                        </span>
                                    </a>
                                </li>
                            @endcan

                            @can('view-permissions')
                                <li
                                    class="nav-item mx-2 {{ request()->routeIs('admin.permission.index') ? 'bg-azure rounded-4' : '' }}">
                                    <a wire:navigate class="nav-link" href="{{ route('admin.permission.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-lock-open-2">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M3 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                                <path d="M9 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
                                                <path d="M13 11v-4a4 4 0 1 1 8 0v4" />
                                            </svg>
                                        </span>
                                        <span class="nav-link-title text-white">
                                            Permission
                                        </span>
                                    </a>
                                </li>
                            @endcan

                            @can('view-user-role-assignment')
                                <li
                                    class="nav-item mx-2 {{ request()->routeIs('admin.user-role-assignment.index') ? 'bg-azure rounded-4' : '' }}">
                                    <a wire:navigate class="nav-link"
                                        href="{{ route('admin.user-role-assignment.index') }}">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-user-cog">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h2.5" />
                                                <path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                <path d="M19.001 15.5v1.5" />
                                                <path d="M19.001 21v1.5" />
                                                <path d="M22.032 17.25l-1.299 .75" />
                                                <path d="M17.27 20l-1.3 .75" />
                                                <path d="M15.97 17.25l1.3 .75" />
                                                <path d="M20.733 20l1.3 .75" />
                                            </svg>
                                        </span>
                                        <span class="nav-link-title text-white">
                                            Assign User Roles
                                        </span>
                                    </a>
                                </li>
                            @endcan
                        </div>
                    @endif

                    {{-- Menu Keuangan --}}
                    <div>
                        <div class="mt-3 mb-1 px-3">
                            <div class="d-flex align-items-center">
                                <div class="text-muted small fw-bold ms-2">KEUANGAN</div>
                            </div>
                        </div>
                        <li
                            class="nav-item mx-2 {{ request()->routeIs('group-category.index') ? 'bg-azure rounded-4' : '' }}">
                            <a wire:navigate class="nav-link" href="{{ route('group-category.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-category">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 4h6v6h-6z" />
                                        <path d="M14 4h6v6h-6z" />
                                        <path d="M4 14h6v6h-6z" />
                                        <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Group Category
                                </span>
                            </a>
                        </li>
                        <li
                            class="nav-item mx-2 {{ request()->routeIs('laporan-keuangan.index') ? 'bg-azure rounded-4' : '' }}">
                            <a wire:navigate class="nav-link" href="{{ route('laporan-keuangan.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-cash">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" />
                                        <path
                                            d="M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" />
                                        <path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Laporan Keuangan
                                </span>
                            </a>
                        </li>
                    </div>

                    {{-- Menu audio --}}
                    <div>
                        <div class="mt-3 mb-1 px-3">
                            <div class="d-flex align-items-center">
                                <div class="text-muted small fw-bold ms-2">AUDIO</div>
                            </div>
                        </div>
                        <li class="nav-item mx-2 {{ request()->routeIs('audios') ? 'bg-azure rounded-4' : '' }}">
                            <a wire:navigate class="nav-link" href="{{ route('audios') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-music">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M13 17a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M9 17v-13h10v13" />
                                        <path d="M9 8h10" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Audio Murottal
                                </span>
                            </a>
                        </li>
                        <li
                            class="nav-item mx-2 {{ request()->routeIs('adzan-audio.index') ? 'bg-azure rounded-4' : '' }}">
                            <a wire:navigate class="nav-link" href="{{ route('adzan-audio.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-volume">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M15 8a5 5 0 0 1 0 8" />
                                        <path d="M17.7 5a9 9 0 0 1 0 14" />
                                        <path
                                            d="M6 15h-2a1 1 0 0 1 -1 -1v-4a1 1 0 0 1 1 -1h2l3.5 -4.5a.8 .8 0 0 1 1.5 .5v14a.8 .8 0 0 1 -1.5 .5l-3.5 -4.5" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Audio Adzan
                                </span>
                            </a>
                        </li>
                    </div>

                    {{-- Menu Slide --}}
                    <div>
                        <div class="mt-3 mb-1 px-3">
                            <div class="d-flex align-items-center">
                                <div class="text-muted small fw-bold ms-2">SLIDER</div>
                            </div>
                        </div>
                        @if (Auth::check() && (in_array(Auth::user()->role, ['Super Admin', 'Admin']) || Auth::user()->can('view-jumbotron')))
                            <li
                                class="nav-item mx-2 {{ request()->routeIs('jumbotron.index') ? 'bg-azure rounded-4' : '' }}">
                                <a wire:navigate class="nav-link" href="{{ route('jumbotron.index') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-library-photo">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M7 3m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" />
                                            <path
                                                d="M4.012 7.26a2.005 2.005 0 0 0 -1.012 1.737v10c0 1.1 .9 2 2 2h10c.75 0 1.158 -.385 1.5 -1" />
                                            <path d="M17 7h.01" />
                                            <path d="M7 13l3.644 -3.644a1.21 1.21 0 0 1 1.712 0l3.644 3.644" />
                                            <path d="M15 12l1.644 -1.644a1.21 1.21 0 0 1 1.712 0l2.644 2.644" />
                                        </svg>
                                    </span>
                                    <span class="nav-link-title text-white">
                                        Jumbotron
                                    </span>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item mx-2 {{ request()->routeIs('slide.index') ? 'bg-azure rounded-4' : '' }}">
                            <a wire:navigate class="nav-link" href="{{ route('slide.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-slideshow">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M15 6l.01 0" />
                                        <path
                                            d="M3 3m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
                                        <path d="M3 13l4 -4a3 5 0 0 1 3 0l4 4" />
                                        <path d="M13 12l2 -2a3 5 0 0 1 3 0l3 3" />
                                        <path d="M8 21l.01 0" />
                                        <path d="M12 21l.01 0" />
                                        <path d="M16 21l.01 0" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Slide Utama
                                </span>
                            </a>
                        </li>
                        @can('view-data-jumbotron-semua-masjid')
                            <li
                                class="nav-item mx-2 {{ request()->routeIs('data-jumbotron-semua-masjid.*') ? 'bg-azure rounded-4' : '' }}">
                                <a wire:navigate class="nav-link"
                                    href="{{ route('data-jumbotron-semua-masjid.index') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-slideshow">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M15 6l.01 0" />
                                            <path
                                                d="M3 3m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
                                            <path d="M3 13l4 -4a3 5 0 0 1 3 0l4 4" />
                                            <path d="M13 12l2 -2a3 5 0 0 1 3 0l3 3" />
                                            <path d="M8 21l.01 0" />
                                            <path d="M12 21l.01 0" />
                                            <path d="M16 21l.01 0" />
                                        </svg>
                                    </span>
                                    <span class="nav-link-title text-white">
                                        Data Jumbotron Masjid
                                    </span>
                                </a>
                            </li>
                        @endcan
                        @can('view-jumbotron-masjid')
                            <li
                                class="nav-item mx-2 {{ request()->routeIs('jumbotron-masjid.*') ? 'bg-azure rounded-4' : '' }}">
                                <a wire:navigate class="nav-link" href="{{ route('jumbotron-masjid.index') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-slideshow">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M15 6l.01 0" />
                                            <path
                                                d="M3 3m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
                                            <path d="M3 13l4 -4a3 5 0 0 1 3 0l4 4" />
                                            <path d="M13 12l2 -2a3 5 0 0 1 3 0l3 3" />
                                            <path d="M8 21l.01 0" />
                                            <path d="M12 21l.01 0" />
                                            <path d="M16 21l.01 0" />
                                        </svg>
                                    </span>
                                    <span class="nav-link-title text-white">
                                        Jumbotron Masjid
                                    </span>
                                </a>
                            </li>
                        @endcan
                        <li
                            class="nav-item mx-2 {{ request()->routeIs('adzan.index') ? 'bg-azure rounded-4' : '' }} ">
                            <a wire:navigate class="nav-link" href="{{ route('adzan.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-photo-video">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M9 15h-3a3 3 0 0 1 -3 -3v-6a3 3 0 0 1 3 -3h6a3 3 0 0 1 3 3v3" />
                                        <path
                                            d="M9 9m0 3a3 3 0 0 1 3 -3h6a3 3 0 0 1 3 3v6a3 3 0 0 1 -3 3h-6a3 3 0 0 1 -3 -3z" />
                                        <path d="M3 12l2.296 -2.296a2.41 2.41 0 0 1 3.408 0l.296 .296" />
                                        <path d="M14 13.5v3l2.5 -1.5z" />
                                        <path d="M7 6v.01" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Slide Iqomah & Jumat
                                </span>
                            </a>
                        </li>
                    </div>

                    {{-- Menu lainnya --}}
                    <div>
                        <div class="mt-3 mb-1 px-3">
                            <div class="d-flex align-items-center">
                                <div class="text-muted small fw-bold ms-2">MENU LAINNYA</div>
                            </div>
                        </div>
                        <li
                            class="nav-item mx-2 {{ request()->routeIs('petugas.index') ? 'bg-azure rounded-4' : '' }}">
                            <a wire:navigate class="nav-link" href="{{ route('petugas.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-users-group">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                                        <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                                        <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Petugas Jum'at
                                </span>
                            </a>
                        </li>
                        <li
                            class="nav-item mx-2 {{ request()->routeIs('agenda-all.*') ? 'bg-azure rounded-4' : '' }}">
                            <a wire:navigate class="nav-link" href="{{ route('agenda-all.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-clock">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h10" />
                                        <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                        <path d="M18 16.5v1.5l.5 .5" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Semua Agenda
                                </span>
                            </a>
                        </li>
                        <li
                            class="nav-item mx-2 {{ request()->routeIs('marquee.index') ? 'bg-azure rounded-4' : '' }}">
                            <a wire:navigate class="nav-link" href="{{ route('marquee.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-text-grammar">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 9a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M4 12v-5a3 3 0 1 1 6 0v5" />
                                        <path d="M4 9h6" />
                                        <path d="M20 6v6" />
                                        <path d="M4 16h12" />
                                        <path d="M4 20h6" />
                                        <path d="M14 20l2 2l5 -5" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Teks Berjalan
                                </span>
                            </a>
                        </li>
                        <li
                            class="nav-item mx-2 {{ request()->routeIs('durasi.index') ? 'bg-azure rounded-4' : '' }} ">
                            <a wire:navigate class="nav-link" href="{{ route('durasi.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-time-duration-30">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 10.5v3a1.5 1.5 0 0 0 3 0v-3a1.5 1.5 0 0 0 -3 0z" />
                                        <path d="M8 9h1.5a1.5 1.5 0 0 1 0 3h-.5h.5a1.5 1.5 0 0 1 0 3h-1.5" />
                                        <path d="M3 12v.01" />
                                        <path d="M7.5 4.2v.01" />
                                        <path d="M7.5 19.8v.01" />
                                        <path d="M4.2 16.5v.01" />
                                        <path d="M4.2 7.5v.01" />
                                        <path d="M12 21a9 9 0 0 0 0 -18" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Pengaturan Durasi
                                </span>
                            </a>
                        </li>
                    </div>

                    {{-- Lihat halaman utama --}}
                    @if (Auth::user()->role === 'User')
                        <li class="nav-item">
                            <a class="nav-link mx-2" href="{{ route('my.mosque') }}" target="_blank">
                                <span class="nav-link-icon d-md-none d-lg-inline-block text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-external-link">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" />
                                        <path d="M11 13l9 -9" />
                                        <path d="M15 4h5v5" />
                                    </svg>
                                </span>
                                <span class="nav-link-title text-white">
                                    Lihat JWS Saya
                                </span>
                            </a>
                        </li>
                    @endif
                @else
                    <!-- Jika user status bukan 'Active', tampilkan pesan peringatan -->
                    <li class="nav-item d-none d-md-block">
                        <a wire:navigate class="nav-link text-danger disabled" onclick="return false;"
                            href="{{ route('inactive.index') }}">
                            <span class="nav-link-icon d-md-none text-danger d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                    <path d="M12 8v4" />
                                    <path d="M12 16h.01" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Status Akun
                            </span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</aside>
