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

                // Bersihkan draf lama di localStorage jika masih ada tersisa
                try {
                    Object.keys(localStorage)
                        .filter(k => k.startsWith('article_draft_'))
                        .forEach(k => localStorage.removeItem(k));
                } catch(e) {}

                // Cek dukungan format WebP pada browser
                function isWebpSupported() {
                    try {
                        const canvas = document.createElement('canvas');
                        canvas.width = 1;
                        canvas.height = 1;
                        return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
                    } catch (e) {
                        return false;
                    }
                }

                // Baca file gambar ke HTML Image element
                function loadImage(file) {
                    return new Promise((resolve, reject) => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const img = new Image();
                            img.onload = () => resolve(img);
                            img.onerror = () => reject(new Error('Gagal membaca gambar.'));
                            img.src = e.target.result;
                        };
                        reader.onerror = () => reject(new Error('Gagal membaca file gambar.'));
                        reader.readAsDataURL(file);
                    });
                }

                // Kompres gambar di sisi klien (browser) ke WebP (target ~70KB), fallback ke JPEG
                async function compressImage(file) {
                    if (!file.type.match(/^image\//)) {
                        throw new Error('File yang dipilih bukan gambar.');
                    }

                    const canWebp = isWebpSupported();
                    const targetMime = canWebp ? 'image/webp' : 'image/jpeg';
                    const targetExt = canWebp ? 'webp' : 'jpeg';
                    const targetBytes = 70 * 1024; // Target ukuran ~70KB

                    const img = await loadImage(file);

                    // Batasi resolusi maksimum awal agar tidak memakan memori berlebih
                    let maxDim = 1600;
                    let w = img.width;
                    let h = img.height;
                    if (w > maxDim || h > maxDim) {
                        if (w > h) {
                            h = Math.round((h * maxDim) / w);
                            w = maxDim;
                        } else {
                            w = Math.round((w * maxDim) / h);
                            h = maxDim;
                        }
                    }

                    let canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    let ctx = canvas.getContext('2d');

                    // Jika fallback ke JPEG, beri latar belakang putih agar transparansi PNG tidak hitam
                    if (targetMime === 'image/jpeg') {
                        ctx.fillStyle = '#FFFFFF';
                        ctx.fillRect(0, 0, w, h);
                    }
                    ctx.drawImage(img, 0, 0, w, h);

                    // Mulai dari kualitas 0.82
                    let quality = 0.82;
                    let blob = await new Promise(resolve => canvas.toBlob(resolve, targetMime, quality));

                    // Iterasi menurunkan kualitas bertahap hingga mendekati ~70KB
                    let attempts = 0;
                    while (blob && blob.size > targetBytes && quality > 0.35 && attempts < 5) {
                        quality -= 0.12;
                        blob = await new Promise(resolve => canvas.toBlob(resolve, targetMime, quality));
                        attempts++;
                    }

                    // Jika kualitas sudah 0.35 tapi ukuran masih di atas ~70KB, turunkan resolusi dimensi secara bertahap
                    attempts = 0;
                    while (blob && blob.size > targetBytes && attempts < 4) {
                        w = Math.round(w * 0.8);
                        h = Math.round(h * 0.8);
                        const scaledCanvas = document.createElement('canvas');
                        scaledCanvas.width = w;
                        scaledCanvas.height = h;
                        const sCtx = scaledCanvas.getContext('2d');
                        if (targetMime === 'image/jpeg') {
                            sCtx.fillStyle = '#FFFFFF';
                            sCtx.fillRect(0, 0, w, h);
                        }
                        sCtx.drawImage(canvas, 0, 0, w, h);
                        canvas = scaledCanvas;
                        blob = await new Promise(resolve => canvas.toBlob(resolve, targetMime, quality));
                        attempts++;
                    }

                    const cleanName = (file.name || 'image').replace(/\.[^/.]+$/, '');
                    const newFileName = `${cleanName}.${targetExt}`;
                    return new File([blob], newFileName, { type: targetMime });
                }

                // Kirim file ke endpoint upload dengan tracking progress XMLHttpRequest
                function uploadFileWithProgress(file, onProgress) {
                    return new Promise((resolve, reject) => {
                        const xhr = new XMLHttpRequest();
                        const formData = new FormData();
                        formData.append('image', file);

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        if (csrfToken) {
                            formData.append('_token', csrfToken);
                        }

                        xhr.open('POST', '{{ route('articles.upload-image') }}', true);
                        if (csrfToken) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        }
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                        xhr.upload.onprogress = (e) => {
                            if (e.lengthComputable && onProgress) {
                                const percent = (e.loaded / e.total) * 100;
                                onProgress(percent);
                            }
                        };

                        xhr.onload = () => {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    if (response.status === 'success' && response.url) {
                                        resolve(response.url);
                                    } else {
                                        reject(new Error(response.message || 'Respon server tidak valid.'));
                                    }
                                } catch (e) {
                                    reject(new Error('Format respon server gagal diproses.'));
                                }
                            } else {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    reject(new Error(response.message || 'Gagal mengunggah ke server (status ' + xhr.status + ').'));
                                } catch (e) {
                                    reject(new Error('Gagal mengunggah gambar (status ' + xhr.status + ').'));
                                }
                            }
                        };

                        xhr.onerror = () => {
                            reject(new Error('Koneksi terputus saat mengunggah gambar.'));
                        };

                        xhr.send(formData);
                    });
                }

                // Update tampilan UI Progress Bar
                function updateUploadProgress(percent, message) {
                    const pContainer = document.getElementById('imageUploadProgressContainer');
                    const pBar = document.getElementById('imageUploadProgressBar');
                    const pStatus = document.getElementById('imageUploadStatus');
                    const pPercent = document.getElementById('imageUploadPercent');

                    if (pContainer) pContainer.classList.remove('d-none');
                    const rounded = Math.min(100, Math.max(0, Math.round(percent)));
                    if (pBar) {
                        pBar.style.width = rounded + '%';
                        pBar.setAttribute('aria-valuenow', rounded);
                    }
                    if (pPercent) {
                        pPercent.innerText = rounded + '%';
                    }
                    if (pStatus && message) {
                        pStatus.innerHTML = `
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                            <span>${message}</span>
                        `;
                    }
                }

                // Tangani file gambar yang di-upload dari Summernote (file picker, drag & drop, paste)
                async function handleImageUpload(files) {
                    if (!files || files.length === 0) return;

                    const totalFiles = files.length;
                    try {
                        for (let i = 0; i < totalFiles; i++) {
                            const file = files[i];
                            const fileNum = i + 1;
                            const baseProgress = (i / totalFiles) * 100;
                            const slice = 100 / totalFiles;

                            // Fase 1: Kompresi gambar di client browser
                            updateUploadProgress(baseProgress + (slice * 0.1), `Mengompres gambar (${fileNum}/${totalFiles})...`);
                            const compressed = await compressImage(file);

                            // Fase 2: Upload ke server dengan progress bar
                            updateUploadProgress(baseProgress + (slice * 0.3), `Mengunggah gambar (${fileNum}/${totalFiles})...`);
                            const uploadedUrl = await uploadFileWithProgress(compressed, (percent) => {
                                const currentProgress = baseProgress + (slice * 0.3) + (slice * 0.7 * (percent / 100));
                                updateUploadProgress(currentProgress, `Mengunggah gambar (${fileNum}/${totalFiles}): ${Math.round(percent)}%`);
                            });

                            // Fase 3: Masukkan URL gambar ke Summernote & sinkronkan state Livewire
                            $('#summernote').summernote('insertImage', uploadedUrl);
                            const updatedHtml = $('#summernote').summernote('code');
                            $wire.content = updatedHtml;
                            $wire.set('content', updatedHtml);
                        }

                        // Selesai
                        updateUploadProgress(100, 'Semua gambar berhasil diproses!');
                        const pStatus = document.getElementById('imageUploadStatus');
                        if (pStatus) {
                            pStatus.innerHTML = `
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-success"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M9 12l2 2l4 -4"/></svg>
                                <span class="text-success fw-medium">Gambar berhasil diunggah!</span>
                            `;
                        }

                        setTimeout(() => {
                            const pContainer = document.getElementById('imageUploadProgressContainer');
                            const pBar = document.getElementById('imageUploadProgressBar');
                            const pPercent = document.getElementById('imageUploadPercent');
                            if (pContainer) pContainer.classList.add('d-none');
                            if (pBar) pBar.style.width = '0%';
                            if (pPercent) pPercent.innerText = '0%';
                        }, 1500);

                    } catch (err) {
                        console.error('Gagal memproses gambar:', err);
                        iziToast.error({
                            title: 'Gagal',
                            message: err.message || 'Gagal memproses atau mengunggah gambar.',
                            position: 'topRight'
                        });
                        const pContainer = document.getElementById('imageUploadProgressContainer');
                        if (pContainer) pContainer.classList.add('d-none');
                    }
                }

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
                                    },
                                    onImageUpload: function(files) {
                                        handleImageUpload(files);
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
