<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title d-none d-md-block">
                                Daftar Jumbotron
                            </h3>
                        </div>
                        <form wire:submit.prevent="save">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="col-md-12 mb-4">
                                            <label class="form-label">Status Jumbotron Masjid</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model.live="is_active" wire:change="$refresh" id="is_active"
                                                    {{ $is_active ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    {{ $is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 1</label>
                                                @if ($jumbotron_masjid_1)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_1->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_1)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_1) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_1"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Format:
                                                        JPG, PNG, JPEG, WEBP Maksimal 800Kb</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_1-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_1') is-invalid @enderror"
                                                        wire:model="jumbotron_masjid_1" accept="image/*"
                                                        onchange="(function(el){const f=el.files[0];const err=document.getElementById('jumbotron_masjid_1-upload-error');if(f&& !f.type.startsWith('image/')){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Hanya gambar yang diizinkan (JPG, PNG, JPEG, WEBP, GIF).';err.style.display='block';}else if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP, GIF.';err.style.display='block';}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">
                                                    @if ($jumbotron_masjid_1 || $tmp_jumbotron_masjid_1)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid1"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                @error('jumbotron_masjid_1')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div id="jumbotron_masjid_1-upload-error" class="invalid-feedback"
                                                    style="display:none"></div>
                                            </div>

                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 2</label>
                                                @if ($jumbotron_masjid_2)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_2->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_2)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_2) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_2"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Format:
                                                        JPG, PNG, JPEG, WEBP Maksimal 800Kb</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_2-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_2') is-invalid @enderror"
                                                        wire:model="jumbotron_masjid_2" accept="image/*"
                                                        onchange="(function(el){const f=el.files[0];const err=document.getElementById('jumbotron_masjid_2-upload-error');if(f&& !f.type.startsWith('image/')){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Hanya gambar yang diizinkan (JPG, PNG, JPEG, WEBP, GIF).';err.style.display='block';}else if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP, GIF.';err.style.display='block';}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">
                                                    @if ($jumbotron_masjid_2 || $tmp_jumbotron_masjid_2)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid2"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                @error('jumbotron_masjid_2')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div id="jumbotron_masjid_2-upload-error" class="invalid-feedback"
                                                    style="display:none"></div>
                                            </div>

                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 3</label>
                                                @if ($jumbotron_masjid_3)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_3->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_3)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_3) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_3"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span
                                                            class="text-danger">*</span>Format: JPG, PNG, JPEG, WEBP
                                                        Maksimal 800Kb</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_3-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_3') is-invalid @enderror"
                                                        wire:model="jumbotron_masjid_3" accept="image/*"
                                                        onchange="(function(el){const f=el.files[0];const err=document.getElementById('jumbotron_masjid_3-upload-error');if(f&& !f.type.startsWith('image/')){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Hanya gambar yang diizinkan (JPG, PNG, JPEG, WEBP, GIF).';err.style.display='block';}else if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP, GIF.';err.style.display='block';}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">
                                                    @if ($jumbotron_masjid_3 || $tmp_jumbotron_masjid_3)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid3"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                @error('jumbotron_masjid_3')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div id="jumbotron_masjid_3-upload-error" class="invalid-feedback"
                                                    style="display:none"></div>
                                            </div>

                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 4</label>
                                                @if ($jumbotron_masjid_4)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_4->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_4)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_4) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_4"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span
                                                            class="text-danger">*</span>Format: JPG, PNG, JPEG, WEBP
                                                        Maksimal 800Kb</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_4-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_4') is-invalid @enderror"
                                                        wire:model="jumbotron_masjid_4" accept="image/*"
                                                        onchange="(function(el){const f=el.files[0];const err=document.getElementById('jumbotron_masjid_4-upload-error');if(f&& !f.type.startsWith('image/')){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Hanya gambar yang diizinkan (JPG, PNG, JPEG, WEBP, GIF).';err.style.display='block';}else if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP, GIF.';err.style.display='block';}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">
                                                    @if ($jumbotron_masjid_4 || $tmp_jumbotron_masjid_4)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid4"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                @error('jumbotron_masjid_4')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div id="jumbotron_masjid_4-upload-error" class="invalid-feedback"
                                                    style="display:none"></div>
                                            </div>

                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 5</label>
                                                @if ($jumbotron_masjid_5)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_5->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_5)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_5) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_5"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span
                                                            class="text-danger">*</span>Format: JPG, PNG, JPEG, WEBP
                                                        Maksimal 800Kb</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_5-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_5') is-invalid @enderror"
                                                        wire:model="jumbotron_masjid_5" accept="image/*"
                                                        onchange="(function(el){const f=el.files[0];const err=document.getElementById('jumbotron_masjid_5-upload-error');if(f&& !f.type.startsWith('image/')){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Hanya gambar yang diizinkan (JPG, PNG, JPEG, WEBP, GIF).';err.style.display='block';}else if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP, GIF.';err.style.display='block';}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">
                                                    @if ($jumbotron_masjid_5 || $tmp_jumbotron_masjid_5)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid5"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                @error('jumbotron_masjid_5')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div id="jumbotron_masjid_5-upload-error" class="invalid-feedback"
                                                    style="display:none"></div>
                                            </div>

                                            <div class="col-md-4 mb-2 px-2">
                                                <label class="form-label">Gambar Jumbotron Masjid 6</label>
                                                @if ($jumbotron_masjid_6)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ $jumbotron_masjid_6->temporaryUrl() }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @elseif($tmp_jumbotron_masjid_6)
                                                    <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                        <div class="img-responsive rounded-3"
                                                            style="background-image: url('{{ asset($tmp_jumbotron_masjid_6) }}'); background-size: cover; background-position: center; height: 150px;">
                                                        </div>
                                                    </div>
                                                @endif
                                                <div wire:loading wire:target="jumbotron_masjid_6"
                                                    class="mt-2 text-center">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                    <span class="small">Mengupload...</span>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Tekan
                                                        Browse/Jelajahi untuk memilih gambar</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span
                                                            class="text-danger">*</span>Format: JPG, PNG, JPEG, WEBP
                                                        Maksimal 800Kb</small>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted"><span class="text-danger">*</span>Rasio
                                                        gambar 16:9 (Rekomendasi: 1920x1080 Piksel)</small>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="file" id="jumbotron_masjid_6-input"
                                                        class="form-control my-2 rounded-4 @error('jumbotron_masjid_6') is-invalid @enderror"
                                                        wire:model="jumbotron_masjid_6" accept="image/*"
                                                        onchange="(function(el){const f=el.files[0];const err=document.getElementById('jumbotron_masjid_6-upload-error');if(f&& !f.type.startsWith('image/')){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Hanya gambar yang diizinkan (JPG, PNG, JPEG, WEBP, GIF).';err.style.display='block';}else if(f&&f.size>819200){event.stopImmediatePropagation();el.classList.add('is-invalid');err.textContent='Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP, GIF.';err.style.display='block';}else{el.classList.remove('is-invalid');if(err){err.style.display='none';}}})(this)">
                                                    @if ($jumbotron_masjid_6 || $tmp_jumbotron_masjid_6)
                                                        <button type="button"
                                                            class="btn btn-danger rounded-4 my-2 d-flex align-items-center justify-content-center"
                                                            wire:click="clearJumbotronMasjid6"
                                                            title="Hapus gambar">reset</button>
                                                    @endif
                                                </div>
                                                @error('jumbotron_masjid_6')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div id="jumbotron_masjid_6-upload-error" class="invalid-feedback"
                                                    style="display:none"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer rounded-bottom-4 border-0">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('jumbotron-masjid.index') }}" wire:navigate
                                        class="btn py-2 px-2 rounded-3 shadow-sm">
                                        <span class="d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left-dashed">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 12h6m3 0h1.5m3 0h.5" />
                                                <path d="M5 12l6 6" />
                                                <path d="M5 12l6 -6" />
                                            </svg>
                                            Batal
                                        </span>
                                    </a>
                                    <button type="submit" class="btn btn-primary py-2 px-2 rounded-3 shadow-sm"
                                        wire:loading.attr="disabled">
                                        <span class="d-flex align-items-center" wire:target="save"
                                            wire:loading.class="d-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                                <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                                <path d="M14 4l0 4l-6 0l0 -4" />
                                            </svg>
                                            Simpan
                                        </span>
                                        <span class="d-none d-flex align-items-center" wire:target="save"
                                            wire:loading.class.remove="d-none">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            <span class="small">Menyimpan...</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:load', () => {
        const setupUploadError = (name) => {
            const input = document.getElementById(`${name}-input`);
            const errorEl = document.getElementById(`${name}-upload-error`);
            if (!input || !errorEl) return;

            const showError = (message) => {
                input.classList.add('is-invalid');
                errorEl.textContent = message ||
                    'Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP, GIF.';
                errorEl.style.display = 'block';
            };
            const clearError = () => {
                input.classList.remove('is-invalid');
                errorEl.style.display = 'none';
            };

            input.addEventListener('livewire-upload-error', () => {
                showError(
                    'Ukuran file maksimal 800KB. Format diizinkan: JPG, PNG, JPEG, WEBP, GIF.');
            });
            input.addEventListener('livewire-upload-start', clearError);
            input.addEventListener('livewire-upload-finish', clearError);
        };

        ['jumbotron_masjid_1', 'jumbotron_masjid_2', 'jumbotron_masjid_3', 'jumbotron_masjid_4',
            'jumbotron_masjid_5', 'jumbotron_masjid_6'
        ]
        .forEach(setupUploadError);

        Livewire.on('success', (message) => {
            if (window.iziToast) {
                iziToast.success({
                    title: 'Berhasil',
                    message,
                    position: 'topRight'
                });
            }
            setTimeout(() => {
                Livewire.navigate("{{ route('jumbotron-masjid.index') }}");
            }, 800);
        });
        Livewire.on('error', (message) => {
            if (window.iziToast) {
                iziToast.error({
                    title: 'Gagal',
                    message,
                    position: 'topRight'
                });
            }
        });
        Livewire.on('reset_success', (message) => {
            if (window.iziToast) {
                iziToast.success({
                    title: 'Berhasil',
                    message,
                    position: 'topRight'
                });
            }
        });
        Livewire.on('reset_error', (message) => {
            if (window.iziToast) {
                iziToast.error({
                    title: 'Gagal',
                    message,
                    position: 'topRight'
                });
            }
        });
    });
</script>
@script
<script>
    $wire.on('reset_success', message => {
        if (window.iziToast) {
            iziToast.success({
                title: 'Berhasil',
                message,
                position: 'topRight'
            });
        }
    });
    $wire.on('reset_error', message => {
        if (window.iziToast) {
            iziToast.error({
                title: 'Gagal',
                message,
                position: 'topRight'
            });
        }
    });
</script>
@endscript
