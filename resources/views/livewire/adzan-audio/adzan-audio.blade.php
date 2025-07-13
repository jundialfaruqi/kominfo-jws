<div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card rounded-4 shadow-sm">
                        {{-- Header Content --}}
                        @include('livewire.adzan-audio.section-header')

                        {{-- Form Content --}}
                        @include('livewire.adzan-audio.section-form')

                        {{-- Table Content --}}
                        @include('livewire.adzan-audio.section-table')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('livewire.adzan-audio.delete')

    @script
        <script>
            document.addEventListener('livewire:initialized', function() {
                Livewire.on('resetFileInput', (data) => {
                    const inputName = data.inputName;
                    const fileInput = document.querySelector(`input[wire\:model="${inputName}"]`);
                    if (fileInput) {
                        fileInput.value = '';
                        fileInput.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });

                // Pantau perubahan pada input file
                const fileInput = document.querySelector(`input[wire\:model="audioadzan"]`);
                if (fileInput) {
                    fileInput.addEventListener('change', () => {
                        // Beri tahu Livewire untuk memperbarui komponen
                        Livewire.dispatch('fileSelected', {
                            inputName: 'audioadzan'
                        });
                    });
                }

                // Pantau perubahan pada input file adzanshubuh
                const fileInputShubuh = document.querySelector(`input[wire\:model="adzanshubuh"]`);
                if (fileInputShubuh) {
                    fileInputShubuh.addEventListener('change', () => {
                        // Beri tahu Livewire untuk memperbarui komponen
                        Livewire.dispatch('fileSelected', {
                            inputName: 'adzanshubuh'
                        });
                    });
                }
            });

            // Dengarkan event fileSelected dari Livewire
            $wire.on('fileSelected', (data) => {
                const inputName = data.inputName;
                // Tunggu sebentar untuk memastikan DOM diperbarui
                setTimeout(() => {
                    const audioElement = document.querySelector(`audio[wire\:key="${inputName}"]`);
                    if (audioElement) {
                        audioElement.load(); // Memaksa refresh elemen <audio>
                    }
                }, 100); // Delay kecil untuk memastikan DOM diperbarui
            });

            // Dengarkan event refreshAudio dari Livewire
            $wire.on('refreshAudio', (data) => {
                const inputName = data.inputName;
                // Tunggu sebentar untuk memastikan DOM diperbarui
                setTimeout(() => {
                    const audioElement = document.querySelector(`audio[wire\:key="${inputName}"]`);
                    if (audioElement) {
                        audioElement.load(); // Memaksa refresh elemen <audio>
                    }
                }, 100); // Delay kecil untuk memastikan DOM diperbarui
            });

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
