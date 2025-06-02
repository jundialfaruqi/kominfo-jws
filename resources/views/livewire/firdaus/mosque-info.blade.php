<div>
    <div class="mosque-info">
        <div class="logo-container">
            @if ($profil->logo_masjid)
                <img src="{{ asset($profil->logo_masjid) }}" alt="Logo Masjid" class="logo logo-masjid">
            @endif
            @if ($profil->logo_pemerintah)
                <img src="{{ asset($profil->logo_pemerintah) }}" alt="Logo Pemerintah" class="logo logo-pemerintah">
            @endif
        </div>
        {{-- @if ($profil->logo_pemerintah)
            <img src="{{ asset($profil->logo_pemerintah) }}" alt="Logo Pemerintah" class="logopemerintah logo-pemerintah">
        @endif --}}
        <div class="mosque-text">
            <h1><span class="mosque-name-highlight">{{ $profil->name }}</span></h1>
            <p class="mosque-address">{{ $profil->address }}</p>
        </div>
        {{-- @if ($profil->logo_masjid)
            <img src="{{ asset($profil->logo_masjid) }}" alt="Logo Masjid" class="logomasjid logo-masjid">
        @endif --}}
    </div>
</div>
