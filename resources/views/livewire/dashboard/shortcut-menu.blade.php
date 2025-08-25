<div class="row row-deck row-cards">
    <div class="col-sm-12 col-lg-3">
        <div class="card card-sm rounded-4">
            <a wire:navigate href="{{ route('slide.index') }}" class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-primary text-white avatar rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
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
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Slide Utama</div>
                        <div class="text-secondary">Atur gambar slider</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-sm-12 col-lg-3">
        <div class="card card-sm rounded-4">
            <a wire:navigate href="{{ route('petugas.index') }}" class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-primary text-white avatar rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
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
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Petugas Jum'at</div>
                        <div class="text-secondary">Atur petugas jum'at</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-sm-12 col-lg-3">
        <div class="card card-sm rounded-4">
            <a wire:navigate href="{{ route('marquee.index') }}" class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-primary text-white avatar rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-abc">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 16v-6a2 2 0 1 1 4 0v6" />
                                <path d="M3 13h4" />
                                <path d="M10 8v6a2 2 0 1 0 4 0v-1a2 2 0 1 0 -4 0v1" />
                                <path d="M20.732 12a2 2 0 0 0 -3.732 1v1a2 2 0 0 0 3.726 1.01" />
                            </svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Teks Berjalan</div>
                        <div class="text-secondary">Atur teks marquee</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="col-sm-12 col-lg-3">
        <div class="card card-sm rounded-4">
            <a wire:navigate href="{{ route('tema.index') }}" class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-primary text-white avatar rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-brush">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 21v-4a4 4 0 1 1 4 4h-4" />
                                <path d="M21 3a16 16 0 0 0 -12.8 10.2" />
                                <path d="M21 3a16 16 0 0 1 -10.2 12.8" />
                                <path d="M10.6 9a9 9 0 0 1 4.4 4.4" />
                            </svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium">Tema</div>
                        <div class="text-secondary">Atur template Masjid</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
