<div class="row row-deck pt-3 pe-3 ps-3">
    @if ($jumbotronMasjidsData->count() > 0)
        @foreach ($jumbotronMasjidsData as $jm)
            <div>
                <p class="text-sm">Status:
                    <span class="badge badge-sm bg-{{ $jm->aktif ? 'success' : 'danger' }} text-white">
                        {{ $jm->aktif ? 'Aktif' : 'Tidak Aktif' }}</span>
                </p>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="img-responsive img-responsive-21x9 card-img-top"
                        style="background-image: url('{{ $jm->jumbotron_masjid_1 ? asset($jm->jumbotron_masjid_1) : asset('theme/static/belum-ada-gambar.webp') }}')">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">Gambar Jumbotron Masjid Slide 1</h3>
                        <div class="mb-2">
                            @if ($jm->jumbotron_masjid_1)
                                <a href="{{ asset($jm->jumbotron_masjid_1) }}" target="_blank"
                                    class="small">{{ basename($jm->jumbotron_masjid_1) }}</a>
                            @else
                                <span class="text-muted text-xs">-</span>
                            @endif
                        </div>
                        <p class="text-secondary">{{ optional($jm->profilMasjid)->name }} • {{ $jm->user->name }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="img-responsive img-responsive-21x9 card-img-top"
                        style="background-image: url('{{ $jm->jumbotron_masjid_2 ? asset($jm->jumbotron_masjid_2) : asset('theme/static/belum-ada-gambar.webp') }}')">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">Gambar Jumbotron Masjid Slide 2</h3>
                        <div class="mb-2">
                            @if ($jm->jumbotron_masjid_2)
                                <a href="{{ asset($jm->jumbotron_masjid_2) }}" target="_blank"
                                    class="small">{{ basename($jm->jumbotron_masjid_2) }}</a>
                            @else
                                <span class="text-muted text-xs">-</span>
                            @endif
                        </div>
                        <p class="text-secondary">{{ optional($jm->profilMasjid)->name }} • {{ $jm->user->name }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="img-responsive img-responsive-21x9 card-img-top"
                        style="background-image: url('{{ $jm->jumbotron_masjid_3 ? asset($jm->jumbotron_masjid_3) : asset('theme/static/belum-ada-gambar.webp') }}')">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">Gambar Jumbotron Masjid Slide 3</h3>
                        <div class="mb-2">
                            @if ($jm->jumbotron_masjid_3)
                                <a href="{{ asset($jm->jumbotron_masjid_3) }}" target="_blank"
                                    class="small">{{ basename($jm->jumbotron_masjid_3) }}</a>
                            @else
                                <span class="text-muted text-xs">-</span>
                            @endif
                        </div>
                        <p class="text-secondary">{{ optional($jm->profilMasjid)->name }} • {{ $jm->user->name }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="img-responsive img-responsive-21x9 card-img-top"
                        style="background-image: url('{{ $jm->jumbotron_masjid_4 ? asset($jm->jumbotron_masjid_4) : asset('theme/static/belum-ada-gambar.webp') }}')">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">Gambar Jumbotron Masjid Slide 4</h3>
                        <div class="mb-2">
                            @if ($jm->jumbotron_masjid_4)
                                <a href="{{ asset($jm->jumbotron_masjid_4) }}" target="_blank"
                                    class="small">{{ basename($jm->jumbotron_masjid_4) }}</a>
                            @else
                                <span class="text-muted text-xs">-</span>
                            @endif
                        </div>
                        <p class="text-secondary">{{ optional($jm->profilMasjid)->name }} • {{ $jm->user->name }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="img-responsive img-responsive-21x9 card-img-top"
                        style="background-image: url('{{ $jm->jumbotron_masjid_5 ? asset($jm->jumbotron_masjid_5) : asset('theme/static/belum-ada-gambar.webp') }}')">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">Gambar Jumbotron Masjid Slide 5</h3>
                        <div class="mb-2">
                            @if ($jm->jumbotron_masjid_5)
                                <a href="{{ asset($jm->jumbotron_masjid_5) }}" target="_blank"
                                    class="small">{{ basename($jm->jumbotron_masjid_5) }}</a>
                            @else
                                <span class="text-muted text-xs">-</span>
                            @endif
                        </div>
                        <p class="text-secondary">{{ optional($jm->profilMasjid)->name }} • {{ $jm->user->name }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="img-responsive img-responsive-21x9 card-img-top"
                        style="background-image: url('{{ $jm->jumbotron_masjid_6 ? asset($jm->jumbotron_masjid_6) : asset('theme/static/belum-ada-gambar.webp') }}')">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">Gambar Jumbotron Masjid Slide 6</h3>
                        <div class="mb-2">
                            @if ($jm->jumbotron_masjid_6)
                                <a href="{{ asset($jm->jumbotron_masjid_6) }}" target="_blank"
                                    class="small">{{ basename($jm->jumbotron_masjid_6) }}</a>
                            @else
                                <span class="text-muted text-xs">-</span>
                            @endif
                        </div>
                        <p class="text-secondary">{{ optional($jm->profilMasjid)->name }} • {{ $jm->user->name }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center pb-3">
            Data Jumbotron Masjid belum ada
        </div>
    @endif
</div>
