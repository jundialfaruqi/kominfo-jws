<div>
    <div class="mosque-info">
        <div class="logo-container">
            <img src="{{ $profil->logo_masjid ? asset($profil->logo_masjid) : asset('images/other/logo-masjid-default.png') }}"
                alt="Logo Masjid" class="logo logo-masjid">
            @if ($profil->logo_pemerintah)
                <img src="{{ asset($profil->logo_pemerintah) }}" alt="Logo Pemerintah" class="logo logo-pemerintah">
            @endif
        </div>
        <div class="mosque-text">
            <h1><span class="mosque-name-highlight">{{ ucwords(strtolower($profil->name)) }}</span></h1>
            <p class="mosque-address">{{ ucwords(strtolower($profil->address)) }}</p>
        </div>
    </div>
</div>
