<div>
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css" rel="stylesheet">
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

                // Summernote Init
                let isInitializing = false;

                function initSummernote(content = '') {
                    if (isInitializing) return;
                    isInitializing = true;

                    setTimeout(() => {
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
                                    @this.set('content', contents);
                                }
                            }
                        });

                        if (content) {
                            $('#summernote').summernote('code', content);
                        }

                        isInitializing = false;
                    }, 100);
                }

                $wire.on('initSummernote', (data) => {
                    if ($('#summernote').data('summernote')) {
                        $('#summernote').summernote('destroy');
                    }
                    initSummernote(data.content || '');
                });

                // Cleanup on component removal
                document.addEventListener('livewire:navigating', () => {
                    if ($('#summernote').data('summernote')) {
                        $('#summernote').summernote('destroy');
                    }
                });
            </script>
        @endscript
    </div>

    @push('scripts')
        <script data-navigate-once src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script data-navigate-once src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.js"></script>
    @endpush
</div>
