<div>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
        <style>
            .note-editor.note-frame {
                border-radius: 12px;
                border: 1px solid #dee2e6;
                overflow: hidden;
            }

            .note-toolbar {
                background-color: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
            }
        </style>
    @endpush

    <div class="page-body">
        <div class="container-xl">
            <div class="card rounded-4 shadow-sm border-0">
                {{-- Header --}}
                @include('livewire.articles.section-header')
                {{-- Form --}}
                @include('livewire.articles.section-form')
                {{-- Table --}}
                @include('livewire.articles.section-table')
            </div>
        </div>

        {{-- Delete Modal --}}
        @include('livewire.articles.delete')

        {{-- Scripts --}}
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

                // Script Loader untuk mencegah race condition di Livewire SPA
                function loadScript(src) {
                    return new Promise((resolve, reject) => {
                        // Cek jika script sudah pernah ditambahkan
                        if (document.querySelector(`script[src="${src}"]`)) {
                            resolve();
                            return;
                        }
                        const script = document.createElement('script');
                        script.src = src;
                        script.onload = resolve;
                        script.onerror = reject;
                        document.head.appendChild(script);
                    });
                }

                // Summernote Init
                let isInitializing = false;

                function initSummernote(content = '') {
                    if (isInitializing) return;
                    isInitializing = true;

                    loadScript('https://code.jquery.com/jquery-3.6.0.min.js')
                        .then(() => loadScript('https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js'))
                        .then(() => {
                            // Pastikan elemen masih ada di DOM
                            if ($('#summernote').length === 0) {
                                isInitializing = false;
                                return;
                            }

                            $('#summernote').summernote({
                                placeholder: 'Tulis konten berita disini...',
                                tabsize: 2,
                                height: 1000,
                                toolbar: [
                                    ['style', ['style']],
                                    ['font', ['bold', 'underline', 'clear']],
                                    ['color', ['color']],
                                    ['para', ['ul', 'ol', 'paragraph']],
                                    ['table', ['table']],
                                    ['insert', ['link', 'picture', 'video']],
                                    ['view', ['fullscreen', 'codeview', 'help']]
                                ],
                                callbacks: {
                                    onChange: function(contents, $editable) {
                                        $wire.content = contents;
                                    }
                                }
                            });

                            if (content) {
                                $('#summernote').summernote('code', content);
                            }

                            isInitializing = false;
                        })
                        .catch(err => {
                            console.error("Gagal memuat Summernote:", err);
                            isInitializing = false;
                        });
                }

                $wire.on('initSummernote', (data) => {
                    if (typeof $ !== 'undefined' && $('#summernote').length && $('#summernote').data('summernote')) {
                        $('#summernote').summernote('destroy');
                    }
                    initSummernote(data.content || '');
                });

                // Cleanup on component removal
                document.addEventListener('livewire:navigating', () => {
                    if (typeof $ !== 'undefined' && $('#summernote').length && $('#summernote').data('summernote')) {
                        $('#summernote').summernote('destroy');
                    }
                });

                // Jika halaman direload dalam state form terbuka, init otomatis
                if ($wire.showForm) {
                    initSummernote($wire.content || '');
                }
            </script>
        @endscript
    </div>


</div>
