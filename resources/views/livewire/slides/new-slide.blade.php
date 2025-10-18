<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card rounded-4 shadow-sm">
                    <div class="card-header">
                        <h3
                            class="card-title d-none d-md-block flex w-full flex-col gap-2 sm:flex-row sm:items-center">
                            Slider Image
                        </h3>
                        <div class="position-fixed bottom-0 end-0 mb-3 me-3"
                            style="z-index:1050;">
                            <button class="btn btn-primary"
                                wire:click="addSliderForm">Add new row</button>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Form for creating a new slide --}}
                        <form wire:submit.prevent="saveSlide"
                            enctype="multipart/form-data">
                            <div class="row g-3">
                                @for ($i = 1; $i <= $sliderFormCount; $i++)
                                    @php
                                        $temporaryFile = $slideImages[$i] ?? null;
                                        $existingData = $existingSlides[$i] ?? null;
                                        $existingPath = is_array($existingData) ? ($existingData['path'] ?? null) : $existingData;
                                        $hasImage = $temporaryFile || $existingPath;
                                    @endphp
                                    <div class="col-12 col-md-6 col-lg-3" wire:key="slider-field-{{ $i }}">
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <label class="form-label mb-0" for="slideImage{{ $i }}">
                                                    Slide Image {{ $i }}
                                                </label>
                                                @if ($hasImage)
                                                    <button type="button"
                                                        class="btn btn-link text-danger p-0 d-inline-flex align-items-center gap-1"
                                                        wire:click="removeSlideImage({{ $i }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="removeSlideImage({{ $i }})">
                                                        <span wire:loading.remove wire:target="removeSlideImage({{ $i }})">Hapus</span>
                                                        <span class="spinner-border spinner-border-sm"
                                                            wire:loading
                                                            wire:target="removeSlideImage({{ $i }})"
                                                            role="status" aria-hidden="true"></span>
                                                    </button>
                                                @endif
                                            </div>

                                            @if ($temporaryFile)
                                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                    <div class="img-responsive rounded-3"
                                                        style="background-image: url('{{ $temporaryFile->temporaryUrl() }}'); background-size: cover; background-position: center; min-height: 180px;">
                                                    </div>
                                                </div>
                                            @elseif ($existingPath)
                                                <div class="card p-2 rounded-4 shadow-sm border mb-2">
                                                    <div class="img-responsive rounded-3"
                                                        style="background-image: url('{{ asset($existingPath) }}'); background-size: cover; background-position: center; min-height: 180px;">
                                                    </div>
                                                </div>
                                            @endif

                                            <input class="form-control rounded-4"
                                                id="slideImage{{ $i }}"
                                                type="file"
                                                wire:model="slideImages.{{ $i }}"
                                                accept="image/*">
                                            <div class="small text-muted mt-2"
                                                wire:loading
                                                wire:target="slideImages.{{ $i }}">
                                                Mengunggah...
                                            </div>
                                            @error('slideImages.' . $i)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            @error('slideImages')
                                <span
                                    class="text-danger">{{ $message }}</span>
                            @enderror
                            <button class="btn btn-success" type="submit">Save
                                Slide</button>
                        </form>
                        {{-- end of form --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('slider-form-added', () => {
                window.scrollTo({
                    top: document.body.scrollHeight,
                    behavior: 'smooth'
                });
            });

            Livewire.on('success', (message) => {
                iziToast.success({
                    title: 'Berhasil',
                    message,
                    position: 'topRight'
                });
            });

            Livewire.on('error', (message) => {
                iziToast.error({
                    title: 'Gagal',
                    message,
                    position: 'topRight'
                });
            });
        });
    </script>
@endscript
