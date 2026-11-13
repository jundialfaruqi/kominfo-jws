<div class="table-responsive">
    <style>
        .user-skeleton-box {
            display: inline-block;
            border-radius: 0.375rem;
            background: linear-gradient(
                90deg,
                rgba(148, 163, 184, 0.18) 25%,
                rgba(148, 163, 184, 0.36) 50%,
                rgba(148, 163, 184, 0.18) 75%
            );
            background-size: 200% 100%;
            animation: userSkeletonShimmer 1.35s ease-in-out infinite;
        }

        @keyframes userSkeletonShimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }
    </style>
    <table class="table card-table table-vcenter table-striped table-hover text-nowrap datatable">
        <thead>
            <tr>
                <th class="w-1">No.</th>
                <th>Nama, Email & No. Telp</th>
                <th>Nama & Url Masjid</th>
                {{-- <th>No. HP</th> --}}
                {{-- <th>Role (Legacy)</th> --}}
                <th>Tingkat Aktivitas</th>
                <th>Peran</th>
                <th>Akun</th>
                <th></th>
            </tr>
        </thead>

        {{-- Skeleton Shimmer saat memproses pencarian / ubah jumlah baris / pindah halaman --}}
        <tbody class="d-none" wire:loading.class.remove="d-none"
            wire:target="search,paginate,gotoPage,nextPage,previousPage" aria-hidden="true">
            @php
                $skeletonRows = [
                    ['name' => '165px', 'email' => '195px', 'phone' => '115px', 'mosque' => '180px', 'url' => '240px', 'activity' => '145px', 'role' => '105px'],
                    ['name' => '140px', 'email' => '175px', 'phone' => '125px', 'mosque' => '160px', 'url' => '215px', 'activity' => '125px', 'role' => '95px'],
                    ['name' => '185px', 'email' => '205px', 'phone' => '110px', 'mosque' => '195px', 'url' => '250px', 'activity' => '155px', 'role' => '110px'],
                    ['name' => '150px', 'email' => '185px', 'phone' => '120px', 'mosque' => '170px', 'url' => '230px', 'activity' => '135px', 'role' => '100px'],
                    ['name' => '170px', 'email' => '190px', 'phone' => '115px', 'mosque' => '185px', 'url' => '235px', 'activity' => '140px', 'role' => '105px'],
                    ['name' => '145px', 'email' => '180px', 'phone' => '120px', 'mosque' => '165px', 'url' => '220px', 'activity' => '130px', 'role' => '95px'],
                ];
            @endphp
            @foreach ($skeletonRows as $sk)
                <tr>
                    <td class="text-center">
                        <span class="user-skeleton-box" style="width: 24px; height: 14px;"></span>
                    </td>
                    <td>
                        <div class="mb-1">
                            <span class="user-skeleton-box" style="width: {{ $sk['name'] }}; height: 15px;"></span>
                        </div>
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <span class="user-skeleton-box" style="width: 15px; height: 14px;"></span>
                            <span class="user-skeleton-box" style="width: {{ $sk['email'] }}; height: 13px;"></span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="user-skeleton-box" style="width: 15px; height: 14px;"></span>
                            <span class="user-skeleton-box" style="width: {{ $sk['phone'] }}; height: 13px;"></span>
                        </div>
                    </td>
                    <td>
                        <div class="mb-1">
                            <span class="user-skeleton-box" style="width: {{ $sk['mosque'] }}; height: 15px;"></span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="user-skeleton-box" style="width: 15px; height: 14px;"></span>
                            <span class="user-skeleton-box" style="width: {{ $sk['url'] }}; height: 13px;"></span>
                        </div>
                    </td>
                    <td>
                        <span class="user-skeleton-box" style="width: {{ $sk['activity'] }}; height: 22px;"></span>
                    </td>
                    <td>
                        <span class="user-skeleton-box" style="width: {{ $sk['role'] }}; height: 22px;"></span>
                    </td>
                    <td>
                        <span class="user-skeleton-box" style="width: 74px; height: 22px;"></span>
                    </td>
                    <td class="text-end">
                        <div class="d-inline-flex align-items-center gap-1">
                            <span class="user-skeleton-box rounded-4" style="width: 112px; height: 32px;"></span>
                            <span class="user-skeleton-box rounded-4" style="width: 68px; height: 32px;"></span>
                            <span class="user-skeleton-box rounded-4" style="width: 74px; height: 32px;"></span>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>

        <tbody wire:loading.class="d-none" wire:target="search,paginate,gotoPage,nextPage,previousPage">
            @forelse ($user as $users)
                <tr>
                    <td class="text-center text-muted">
                        {{ $loop->iteration + ($user->currentPage() - 1) * $user->perPage() }}</td>
                    <td>
                        <div class="fw-bold">
                            {{ $users->name }}
                        </div>
                        <div class="text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                <path d="M3 7l9 6l9 -6" />
                            </svg>
                            {{ $users->email }}
                        </div>
                        <div class="text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                            </svg>
                            {{ $users->phone }}
                        </div>
                    </td>
                    <td>
                        {{ $users->profil->name ?? '-' }}
                        @php $slug = $users->profil?->slug; @endphp

                        @if ($slug)
                            @php $url = route('firdaus', $slug); @endphp
                            <a href="{{ $url }}" target="_blank"
                                class="d-flex align-items-center gap-2 text-blue-600 hover:underline">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-link">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 15l6 -6" />
                                    <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464" />
                                    <path
                                        d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463" />
                                </svg>
                                {{ $url }}
                            </a>
                        @else
                            <span class="text-gray-400"></span>
                        @endif
                    </td>
                    <td>
                        @php
                            $last = $users->last_activity_at ?? null;
                            $diffDays = $last ? \Carbon\Carbon::parse($last)->diffInDays(now()) : null;
                        @endphp

                        @if (is_null($last))
                            <span class="badge bg-gray-lt">Tidak ada aktivitas</span>
                        @elseif ($diffDays <= 30)
                            <span class="badge bg-green-lt d-inline-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-activity">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12h4l3 8l4 -16l3 8h4" />
                                </svg>
                                Aktif {{ \Carbon\Carbon::parse($last)->diffForHumans() }}
                            </span>
                        @elseif ($diffDays <= 90)
                            <span class="badge bg-yellow-lt">Kurang</span>
                        @else
                            <span class="badge bg-red-lt">Tidak aktif lebih dari 3 bulan</span>
                        @endif
                    </td>
                    <td>
                        @if ($users->roles->count() > 0)
                            @foreach ($users->roles as $role)
                                <span class="badge bg-info-lt d-inline-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
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
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        @else
                            <span class="badge bg-gray-lt">No roles</span>
                        @endif
                    </td>
                    @if ($users->status == 'Active')
                        <td>
                            <span class="badge bg-green-lt d-inline-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-rosette-discount-check">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h1a2.2 2.2 0 0 0 1.55 -.64l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7c.412 .41 .97 .64 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1c0 .58 .23 1.138 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.64 1.55v1a2.2 2.2 0 0 1 -2.2 2.2h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55v-1" />
                                    <path d="M9 12l2 2l4 -4" />
                                </svg>
                                {{ $users->status }}
                            </span>
                        </td>
                    @else
                        <td>
                            <span class="badge bg-red-lt d-inline-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-rosette-discount-check-off">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M9 12l2 2l1.5 -1.5m2 -2l.5 -.5" />
                                    <path
                                        d="M8.887 4.89a2.2 2.2 0 0 0 .863 -.53l.7 -.7a2.2 2.2 0 0 1 3.12 0l.7 .7c.412 .41 .97 .64 1.55 .64h1a2.2 2.2 0 0 1 2.2 2.2v1c0 .58 .23 1.138 .64 1.55l.7 .7a2.2 2.2 0 0 1 0 3.12l-.7 .7a2.2 2.2 0 0 0 -.528 .858m-.757 3.248a2.193 2.193 0 0 1 -1.555 .644h-1a2.2 2.2 0 0 0 -1.55 .64l-.7 .7a2.2 2.2 0 0 1 -3.12 0l-.7 -.7a2.2 2.2 0 0 0 -1.55 -.64h-1a2.2 2.2 0 0 1 -2.2 -2.2v-1a2.2 2.2 0 0 0 -.64 -1.55l-.7 -.7a2.2 2.2 0 0 1 0 -3.12l.7 -.7a2.2 2.2 0 0 0 .64 -1.55v-1c0 -.604 .244 -1.152 .638 -1.55" />
                                    <path d="M3 3l18 18" />
                                </svg>
                                {{ $users->status }}
                            </span>
                        </td>
                    @endif
                    <td class="text-end">
                        @php
                            $canEdit = false;
                            $canDelete = false;
                            $canDownloadPdf = false;
                            $currentUserRole = auth()->user()->role;

                            if ($currentUserRole === 'Super Admin') {
                                $canEdit = true;
                                $canDelete = $users->id !== auth()->id();
                                $canDownloadPdf = true;
                            } elseif ($currentUserRole === 'Admin') {
                                $canEdit = $users->role === 'User';
                                $canDelete = $users->role === 'User';
                                $canDownloadPdf = $users->role === 'User';
                            }
                        @endphp

                        @if ($canDownloadPdf)
                            <button type="button"
                                class="btn py-2 px-2 rounded-4 shadow-sm"
                                data-pdf-generate-btn
                                data-generate-url="{{ route('admin.user.account-pdf.generate', $users->id) }}"
                                data-user-name="{{ $users->name }}"
                                data-user-email="{{ $users->email }}"
                                data-user-mosque="{{ $users->profil->name ?? '-' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf text-danger me-0">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                    <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                                    <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                                    <path d="M17 18h2" />
                                    <path d="M20 15h-3v6" />
                                    <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" />
                                </svg>
                                Download PDF
                            </button>
                        @endif

                        @if ($canEdit)
                            <button wire:click="edit('{{ $users->id }}')"
                                class="btn py-2 px-2 rounded-4 shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#editModal">
                                <span wire:loading.remove wire:target="edit('{{ $users->id }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-edit me-0">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                        <path
                                            d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        <path d="M16 5l3 3" />
                                    </svg>
                                    Ubah
                                </span>
                                <span wire:loading wire:target="edit('{{ $users->id }}')">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    <span class="small">Loading...</span>
                                </span>
                            </button>
                        @endif

                        @if ($canDelete)
                            <button wire:click="delete('{{ $users->id }}')"
                                class="btn py-2 px-2 rounded-4 shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteModal">
                                <span wire:loading.remove wire:target="delete('{{ $users->id }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-0">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                    </svg>
                                    Hapus
                                </span>
                                <span wire:loading wire:target="delete('{{ $users->id }}')">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    <span class="small">Loading...</span>
                                </span>
                            </button>
                        @endif

                        @if (!$canEdit && !$canDelete && !$canDownloadPdf)
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Data user tidak ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
