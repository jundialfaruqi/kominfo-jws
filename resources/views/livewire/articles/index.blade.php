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
                let isDirty = false;

                // Draft Key Generator (Bisa bedakan draft Tambah vs Edit)
                const getDraftKey = () => 'article_draft_' + ($wire.articleId || 'create');

                function initSummernote(content = '') {
                    if (isInitializing) return;
                    isInitializing = true;

                    // Cek ketersediaan Draft di LocalStorage
                    let savedDraft = localStorage.getItem(getDraftKey());
                    if (savedDraft) {
                        try {
                            let draft = JSON.parse(savedDraft);
                            if (draft && (draft.title || draft.content)) {
                                if (confirm('Ada draf ketikan yang belum tersimpan. Apakah Anda ingin mengembalikannya?')) {
                                    $wire.title = draft.title || '';
                                    let titleEl = document.querySelector('[wire\\:model="title"]');
                                    if (titleEl) titleEl.value = $wire.title;

                                    $wire.article_category_id = draft.article_category_id || '';
                                    let catEl = document.querySelector('[wire\\:model="article_category_id"]');
                                    if (catEl) catEl.value = $wire.article_category_id;

                                    $wire.description = draft.description || '';
                                    let descEl = document.querySelector('[wire\\:model="description"]');
                                    if (descEl) descEl.value = $wire.description;

                                    if (draft.published_at) {
                                        $wire.published_at = draft.published_at;
                                        let pubEl = document.querySelector('[wire\\:model="published_at"]');
                                        if (pubEl) pubEl.value = $wire.published_at;
                                    }
                                    
                                    if (draft.status) {
                                        $wire.status = draft.status;
                                        let statusEl = document.querySelector('[wire\\:model="status"]');
                                        if (statusEl) statusEl.value = $wire.status;
                                    }
                                    
                                    content = draft.content || content;
                                    $wire.content = content;
                                } else {
                                    localStorage.removeItem(getDraftKey()); // Hapus jika menolak
                                }
                            }
                        } catch(e) {}
                    }

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
                                        isDirty = true;
                                    }
                                }
                            });

                            if (content) {
                                $('#summernote').summernote('code', content);
                            }

                            isInitializing = false;
                            setTimeout(() => { isDirty = false; }, 100); // Reset isDirty yang mungkin terpicu saat load awal
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

                // Tangkap event ketikan pada form input biasa
                document.addEventListener('input', () => { isDirty = true; });

                // Simpan Draft setiap 3 detik jika form sedang dibuka & isDirty
                setInterval(() => {
                    if ($wire.showForm && isDirty) {
                        localStorage.setItem(getDraftKey(), JSON.stringify({
                            title: document.querySelector('[wire\\:model="title"]')?.value || $wire.title,
                            article_category_id: document.querySelector('[wire\\:model="article_category_id"]')?.value || $wire.article_category_id,
                            description: document.querySelector('[wire\\:model="description"]')?.value || $wire.description,
                            content: $wire.content,
                            published_at: document.querySelector('[wire\\:model="published_at"]')?.value || $wire.published_at,
                            status: document.querySelector('[wire\\:model="status"]')?.value || $wire.status
                        }));
                    }
                }, 3000);

                // Bersihkan draf setelah berhasil disimpan ke DB
                $wire.on('success', () => {
                    localStorage.removeItem('article_draft_create');
                    if ($wire.articleId) {
                        localStorage.removeItem('article_draft_' + $wire.articleId);
                    }
                });
            </script>
        @endscript
    </div>


</div>
