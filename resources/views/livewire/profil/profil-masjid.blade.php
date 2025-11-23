<div>
    <div class="page-body">
        <div class="container-xl">
            @role('Super Admin||Admin')
                <div class="card mb-3 rounded-4 border-0 overflow-hidden">
                    <div class="card-body"
                        style="background: linear-gradient(90deg, #0ea5a3 0%, #1f7ae0 60%, #3b82f6 100%); color: #fff;">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-7 mb-3 mb-md-0">
                                <h1 class="mb-1">Data Masjid</h1>
                                <div class="text-white" style="opacity:.9;">Ringkasan statistik masjid: total terdaftar, JWS
                                    aktif, dan pembaruan minggu ini</div>
                            </div>
                            <div class="col-12 col-md-5">
                                <div class="d-flex flex-wrap justify-content-md-end">
                                    <div class="text-center"
                                        style="border-right: 1px solid rgba(255, 255, 255, 0.498); padding-right: 1rem; margin-right: 1rem;">
                                        <div class="text-white fw-bold lh-1" style="font-size: 2rem;">
                                            {{ $totalMasjid }}
                                        </div>
                                        <div class="text-white small" style="opacity:.85;">
                                            Total Masjid
                                        </div>
                                    </div>
                                    <div class="text-center"
                                        style="border-right: 1px solid rgba(255, 255, 255, 0.498); padding-right: 1rem; margin-right: 1rem;">
                                        <div class="text-white fw-bold lh-1" style="font-size: 2rem;">
                                            {{ $jwsAktif }}
                                        </div>
                                        <div class="text-white small" style="opacity:.85;">
                                            JWS Aktif
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-white fw-bold lh-1" style="font-size: 2rem;">
                                            {{ $baruMingguIni }}
                                        </div>
                                        <div class="text-white small" style="opacity:.85;">
                                            Baru Minggu Ini
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endrole

            {{-- Statistics Cards --}}
            <div class="row g-3 mb-3">

                @role('Super Admin||Admin')
                    {{-- Konten Siap Tayang --}}
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-secondary small fw-bold" style="opacity:.85;">
                                            Konten Siap Tayang
                                        </div>
                                        <div class="fw-bold" style="font-size: 1.75rem;">
                                            {{ $kontenSiapTayangPercent }}%
                                        </div>
                                        <div class="small fw-bold" style="color:#2563eb; opacity:.85;">
                                            Slides/Marquee lengkap
                                        </div>
                                    </div>
                                    <div
                                        style="width:40px;height:40px;border-radius:12px;background:rgba(59,130,246,.12);display:flex;align-items:center;justify-content:center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-list-check"
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

                    {{-- Kelengkapan Profil Masjid --}}
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm rounded-4" style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-secondary small fw-bold" style="opacity:.85;">
                                            Kelengkapan Profil Masjid
                                        </div>
                                        <div class="fw-bold" style="font-size: 1.75rem;">
                                            {{ $kelengkapanProfilRata }}%
                                        </div>
                                        <div class="small fw-bold" style="color:#10b981; opacity:.85;">
                                            Rata-rata kelengkapan
                                        </div>
                                    </div>
                                    <div
                                        style="width:40px;height:40px;border-radius:12px;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-checklist"
                                            style="color:#10b981;">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M9.615 20h-2.615a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8" />
                                            <path d="M14 19l2 2l4 -4" />
                                            <path d="M9 8h4" />
                                            <path d="M9 12h2" />
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
                                            Masjid Perlu Dilengkapi
                                        </div>
                                        <div class="fw-bold" style="font-size: 1.75rem;">
                                            {{ $masjidPerluDilengkapi }}
                                        </div>
                                        <div class="small fw-bold" style="color:#ef4444; opacity:.85;">
                                            < 50% konten terisi </div>
                                        </div>
                                        <div
                                            style="width:40px;height:40px;border-radius:12px;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
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


                        {{-- Pembaruan 24 Jam Terakhir --}}
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm rounded-4"
                                style="border:none; box-shadow: 0 6px 16px rgba(0,0,0,0.08);">
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
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-history"
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

                <div>
                    {{-- <div class="col-md-12"> --}}
                    <div class="card rounded-4 shadow-sm border-0">
                        {{-- Header Profil Masjid --}}
                        @include('livewire.profil.section-header')
                        {{-- Form untuk Tambah/Edit Profil Masjid --}}
                        @include('livewire.profil.section-form')
                        {{-- Data Table Profil Masjid --}}
                        @include('livewire.profil.section-table')
                    </div>
                    {{-- </div> --}}
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        @include('livewire.profil.delete')

        {{-- Close Modal --}}
        @script
            <script>
                $wire.on('closeDeleteModal', () => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    if (modal) {
                        modal.hide();
                    }
                });

                $wire.on('success', message => {
                    iziToast.success({
                        title: 'Berhasil',
                        message,
                        position: 'topRight'
                    });
                });

                $wire.on('error', message => {
                    iziToast.error({
                        title: 'Gagal',
                        message,
                        position: 'topRight'
                    });
                });
            </script>
        @endscript
    </div>
</div>
