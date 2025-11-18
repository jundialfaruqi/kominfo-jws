{{-- Moment.js core --}}
<script data-navigate-once src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>

{{-- Moment Hijri --}}
<script data-navigate-once src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.0/moment-hijri.min.js"></script>

{{-- Locale Indonesia --}}
<script data-navigate-once src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {
        // Tambahkan di awal script
        // let userHasInteracted = false;

        // Event listener untuk mendeteksi interaksi user
        // $(document).one('click touchstart keydown', function() {
        //     userHasInteracted = true;
        //     console.log('User interaction detected - audio autoplay enabled');
        // });

        let serverTimestamp = parseInt($('#server-timestamp').val()) || Date.now();
        let pageLoadTimestamp = Date.now();
        let audioPlayer = null;
        let isAudioPlaying = false;
        let audioPlayTimeout = null;
        let lastPlayedAudioIndex = -1;
        let isAudioPausedForAdzan = false; // Variabel untuk melacak apakah audio dijeda karena adzan
        let cachedAudioUrls = []; // Menyimpan URL audio yang tidak berubah selama sesi pemutaran
        let audioRetryCount = 0; // Menghitung percobaan pemutaran ulang jika terjadi error
        const MAX_RETRY_ATTEMPTS = 3; // Maksimum percobaan pemutaran ulang

        // Konstanta untuk localStorage audio cache
        const AUDIO_CACHE_KEY = 'audioCache';
        const AUDIO_CACHE_TIMESTAMP_KEY = 'audioCacheTimestamp';
        const AUDIO_CACHE_SLUG_KEY = 'audioCacheSlug';
        const AUDIO_CACHE_EXPIRY = 24 * 60 * 60 * 1000; // 24 jam dalam milliseconds

        // Inisialisasi cache audio dari localStorage
        initializeAudioCache();

        // Variabel untuk tracking status koneksi dan notifikasi
        let isOffline = false;
        let offlineNotificationShown = false;
        let connectionStatusElement = null;
        const currentMonth = $('#current-month').val() || new Date().getMonth() + 1;
        const currentYear = $('#current-year').val() || new Date().getFullYear();

        // Fungsi untuk menyimpan cache audio ke localStorage
        function saveAudioCacheToLocalStorage(audioUrls, slug) {
            try {
                const cacheData = {
                    urls: audioUrls,
                    timestamp: Date.now(),
                    slug: slug
                };
                localStorage.setItem(AUDIO_CACHE_KEY, JSON.stringify(cacheData));
                localStorage.setItem(AUDIO_CACHE_TIMESTAMP_KEY, Date.now().toString());
                localStorage.setItem(AUDIO_CACHE_SLUG_KEY, slug);
                console.log('Cache audio disimpan ke localStorage:', audioUrls.length, 'audio untuk slug:',
                    slug);
            } catch (error) {
                console.warn('Gagal menyimpan cache audio ke localStorage:', error);
            }
        }

        // Fungsi untuk memuat cache audio dari localStorage
        function loadAudioCacheFromLocalStorage(currentSlug) {
            try {
                const cacheData = localStorage.getItem(AUDIO_CACHE_KEY);
                const cacheTimestamp = localStorage.getItem(AUDIO_CACHE_TIMESTAMP_KEY);
                const cacheSlug = localStorage.getItem(AUDIO_CACHE_SLUG_KEY);

                if (!cacheData || !cacheTimestamp || !cacheSlug) {
                    // console.log('Tidak ada cache audio di localStorage');
                    return null;
                }

                const timestamp = parseInt(cacheTimestamp);
                const now = Date.now();

                // Periksa apakah cache sudah expired
                if (now - timestamp > AUDIO_CACHE_EXPIRY) {
                    console.log('Cache audio di localStorage sudah expired, menghapus...');
                    clearAudioCacheFromLocalStorage();
                    return null;
                }

                // Periksa apakah slug masih sama
                if (cacheSlug !== currentSlug) {
                    console.log('Slug berubah, menghapus cache audio lama dari localStorage');
                    clearAudioCacheFromLocalStorage();
                    return null;
                }

                const parsedData = JSON.parse(cacheData);
                if (parsedData && parsedData.urls && Array.isArray(parsedData.urls)) {
                    console.log('Cache audio dimuat dari localStorage:', parsedData.urls.length,
                        'audio untuk slug:', currentSlug);
                    return parsedData.urls;
                }

                return null;
            } catch (error) {
                console.warn('Gagal memuat cache audio dari localStorage:', error);
                clearAudioCacheFromLocalStorage();
                return null;
            }
        }

        // Fungsi untuk menghapus cache audio dari localStorage
        function clearAudioCacheFromLocalStorage() {
            try {
                localStorage.removeItem(AUDIO_CACHE_KEY);
                localStorage.removeItem(AUDIO_CACHE_TIMESTAMP_KEY);
                localStorage.removeItem(AUDIO_CACHE_SLUG_KEY);
                // console.log('Cache audio dihapus dari localStorage');
            } catch (error) {
                console.warn('Gagal menghapus cache audio dari localStorage:', error);
            }
        }

        // Fungsi untuk memuat cache audio saat halaman dimuat
        function initializeAudioCache() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (slug) {
                const cachedUrls = loadAudioCacheFromLocalStorage(slug);
                if (cachedUrls && cachedUrls.length > 0) {
                    cachedAudioUrls = cachedUrls;
                    console.log('Cache audio diinisialisasi dari localStorage:', cachedAudioUrls.length,
                        'audio');

                    // Update input hidden dengan data dari cache
                    if (cachedUrls[0]) $('#audio1').val(cachedUrls[0]);
                    if (cachedUrls[1]) $('#audio2').val(cachedUrls[1]);
                    if (cachedUrls[2]) $('#audio3').val(cachedUrls[2]);
                    $('#audio_status').val('true');
                }
            }
        }

        // Fungsi untuk menampilkan notifikasi status koneksi
        function showConnectionStatus(message, type = 'info') {
            // Hapus notifikasi sebelumnya jika ada
            if (connectionStatusElement) {
                connectionStatusElement.remove();
            }

            // Buat elemen notifikasi
            connectionStatusElement = document.createElement('div');
            connectionStatusElement.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: ${type === 'warning' ? '#ff9800' : '#4caf50'};
                color: white;
                padding: 12px 20px;
                border-radius: 6px;
                font-size: 14px;
                z-index: 10000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transition: all 0.3s ease;
                max-width: 300px;
             `;
            connectionStatusElement.textContent = message;
            document.body.appendChild(connectionStatusElement);

            // Auto-hide setelah 5 detik untuk notifikasi online
            if (type !== 'warning') {
                setTimeout(() => {
                    if (connectionStatusElement) {
                        connectionStatusElement.style.opacity = '0';
                        setTimeout(() => {
                            if (connectionStatusElement) {
                                connectionStatusElement.remove();
                                connectionStatusElement = null;
                            }
                        }, 300);
                    }
                }, 5000);
            }
        }

        function syncServerTime(callback) {
            const startTime = Date.now();
            $.ajax({
                url: '/api/server-time',
                method: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success && response.data.timestamp) {
                        const endTime = Date.now();
                        const latency = (endTime - startTime) / 2;
                        serverTimestamp = parseInt(response.data.timestamp) + latency;
                        pageLoadTimestamp = endTime;
                    }
                    if (callback) callback();
                },
                error: function(xhr, status, error) {
                    console.warn(
                        'Gagal menyinkronkan waktu server, menggunakan waktu lokal sebagai cadangan'
                    );
                    serverTimestamp = Date.now();
                    pageLoadTimestamp = Date.now();
                    if (callback) callback();
                }
            });
        }

        function getCurrentTimeFromServer() {
            if (!serverTimestamp || !pageLoadTimestamp) {
                console.warn('serverTimestamp atau pageLoadTimestamp tidak tersedia, menggunakan waktu lokal');
                return new Date();
            }
            const elapsed = Date.now() - pageLoadTimestamp;
            return new Date(serverTimestamp + elapsed);
        }

        // Variabel untuk menyimpan timestamp terakhir pembaruan audio
        let lastAudioUpdateTimestamp = 0;
        let audioVersions = {}; // Menyimpan versi terakhir dari setiap audio

        window.newAudioAvailable = false;

        // Fungsi untuk memperbarui dan memutar audio
        function updateAndPlayAudio() {
            // Periksa koneksi jaringan terlebih dahulu
            const networkAvailable = checkNetworkAndRetry();

            // Jika offline dan ada cache, gunakan cache
            if (!networkAvailable && cachedAudioUrls.length > 0) {
                console.log('Mode offline: Menggunakan audio dari cache');
                // Putar audio dari cache jika tidak sedang dijeda untuk adzan
                if (!isAudioPausedForAdzan && !isAudioPlaying) {
                    playAudioFromCache();
                }
                return;
            } else if (!networkAvailable) {
                // Coba muat dari localStorage jika tidak ada cache di memori
                const slug = window.location.pathname.replace(/^\//, '');
                const cachedUrls = loadAudioCacheFromLocalStorage(slug);
                if (cachedUrls && cachedUrls.length > 0) {
                    cachedAudioUrls = cachedUrls;
                    console.log('Mode offline: Menggunakan audio dari localStorage cache');
                    if (!isAudioPausedForAdzan && !isAudioPlaying) {
                        playAudioFromCache();
                    }
                    return;
                }
                console.warn('Offline dan tidak ada cache audio tersedia');
                return; // Keluar jika offline dan tidak ada cache
            }

            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                console.error('Tidak dapat menentukan slug dari URL');
                return;
            }

            // Selalu periksa pembaruan audio, bahkan jika sedang diputar

            // Tambahkan timestamp untuk mencegah caching oleh browser
            const requestTimestamp = Date.now();

            $.ajax({
                url: `/api/audio1/${slug}?_=${requestTimestamp}`,
                method: 'GET',
                dataType: 'json',
                cache: false, // Pastikan browser tidak meng-cache respons
                success: function(response) {
                    if (response.success && response.data.status) {
                        // Update nilai input hidden
                        $('#audio1').val(response.data.audio1);
                        $('#audio2').val(response.data.audio2);
                        $('#audio3').val(response.data.audio3);
                        $('#audio_status').val('true');

                        // Buat hash dari URL audio untuk deteksi perubahan
                        const newAudioHash = [
                            response.data.audio1 || '',
                            response.data.audio2 || '',
                            response.data.audio3 || ''
                        ].join('|');

                        // Periksa apakah audio telah berubah
                        const currentAudioHash = cachedAudioUrls.join('|');
                        const audioChanged = newAudioHash !== currentAudioHash;

                        // Update timestamp terakhir pembaruan
                        lastAudioUpdateTimestamp = Date.now();

                        // Jika audio berubah atau cache kosong, perbarui cache
                        if (audioChanged || cachedAudioUrls.length === 0) {
                            // Jika audio sedang diputar dan berubah, tampilkan pesan
                            if (audioChanged && isAudioPlaying) {
                                console.log(
                                    'Terdeteksi perubahan audio. Audio baru akan diputar setelah audio saat ini selesai.'
                                );
                                // Tandai bahwa ada audio baru tersedia
                                window.newAudioAvailable = true;
                            }

                            // Perbarui cache
                            cachedAudioUrls = [];
                            if (response.data.audio1) cachedAudioUrls.push(response.data.audio1);
                            if (response.data.audio2) cachedAudioUrls.push(response.data.audio2);
                            if (response.data.audio3) cachedAudioUrls.push(response.data.audio3);
                            console.log('Audio URLs di-cache untuk sesi pemutaran:', cachedAudioUrls
                                .length, 'audio');

                            // Simpan cache ke localStorage untuk persistensi
                            saveAudioCacheToLocalStorage(cachedAudioUrls, slug);

                            // Jika audio sedang diputar, jangan reset pemutaran
                            // Audio baru akan diputar setelah audio saat ini selesai
                            if (!isAudioPlaying) {
                                // Putar audio jika tersedia dan tidak sedang dijeda untuk adzan
                                if (!isAudioPausedForAdzan) {
                                    // Cek apakah Friday Info popup sedang ditampilkan
                                    const now = getCurrentTimeFromServer().getTime();
                                    if (fridayInfoStartTime && fridayInfoEndTime && now >=
                                        fridayInfoStartTime && now < fridayInfoEndTime) {
                                        console.log(
                                            'Audio tidak diputar karena Friday Info popup sedang ditampilkan'
                                        );
                                    } else {
                                        playAudio();
                                    }
                                } else {
                                    console.log(
                                        'Audio tidak diputar karena sedang dijeda untuk adzan');
                                }
                            }
                        }

                        // Putar audio jika tersedia dan tidak sedang dijeda untuk adzan
                        if (!isAudioPausedForAdzan && !isAudioPlaying && !audioChanged) {
                            // Cek apakah Friday Info popup sedang ditampilkan
                            const now = getCurrentTimeFromServer().getTime();
                            if (fridayInfoStartTime && fridayInfoEndTime && now >=
                                fridayInfoStartTime && now < fridayInfoEndTime) {
                                console.log(
                                    'Audio tidak diputar karena Friday Info popup sedang ditampilkan'
                                );
                            } else {
                                playAudio();
                            }
                        } else if (isAudioPlaying && audioChanged) {
                            console.log('Audio baru akan diputar setelah audio saat ini selesai');
                        } else if (isAudioPausedForAdzan) {
                            console.log('Audio tidak diputar karena sedang dijeda untuk adzan');
                        }
                    } else {
                        $('#audio_status').val('false');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data audio:', error, xhr.responseText);
                    $('#audio_status').val('false');

                    // Jika error 404 atau 500, hapus cache localStorage yang mungkin tidak valid
                    if (xhr.status === 404 || xhr.status === 500) {
                        const slug = window.location.pathname.replace(/^\//, '');
                        clearAudioCacheFromLocalStorage(slug);
                        // console.log('Cache localStorage dibersihkan');
                    }

                    // Jika masih ada audio di cache, gunakan itu meskipun request gagal
                    if (cachedAudioUrls.length > 0) {
                        console.log('Menggunakan audio dari cache karena request gagal');
                        if (!isAudioPausedForAdzan && !isAudioPlaying) {
                            // Cek apakah Friday Info popup sedang ditampilkan
                            const now = getCurrentTimeFromServer().getTime();
                            if (fridayInfoStartTime && fridayInfoEndTime && now >=
                                fridayInfoStartTime && now < fridayInfoEndTime) {
                                console.log(
                                    'Audio tidak diputar karena Friday Info popup sedang ditampilkan'
                                );
                            } else {
                                playAudio();
                            }
                        }
                    } else {
                        // Coba lagi setelah beberapa waktu jika tidak ada cache
                        // console.log('Mencoba mengambil audio lagi dalam 5 menit...');
                        setTimeout(updateAndPlayAudio, 5 * 60 * 1000);
                    }
                },
                timeout: 15000 // Timeout setelah 15 detik
            });
        }

        // Fungsi untuk memutar audio dari cache saat offline
        function playAudioFromCache() {
            console.log('Memutar audio dari cache:', cachedAudioUrls.length, 'audio tersedia');

            if (cachedAudioUrls.length === 0) {
                console.warn('Cache audio kosong');
                return;
            }

            // Reset retry counter
            audioRetryCount = 0;

            // Bersihkan audio player sebelumnya jika ada
            if (audioPlayer) {
                audioPlayer.onended = null;
                audioPlayer.onerror = null;
                audioPlayer.onplay = null;
                audioPlayer.onpause = null;
                audioPlayer = null;
            }

            // Mulai dari audio pertama di cache
            currentAudioIndex = 0;

            function playNextFromCache() {
                if (currentAudioIndex >= cachedAudioUrls.length) {
                    // Jika sudah mencapai akhir playlist, mulai dari awal
                    currentAudioIndex = 0;
                }

                const audioUrl = cachedAudioUrls[currentAudioIndex];
                console.log(`Memutar audio dari cache [${currentAudioIndex + 1}/${cachedAudioUrls.length}]:`,
                    audioUrl);

                // Buat audio player baru
                audioPlayer = new Audio(audioUrl);

                // Set event listeners
                audioPlayer.onended = function() {
                    console.log('Audio dari cache selesai, melanjutkan ke audio berikutnya');
                    currentAudioIndex++;
                    playNextFromCache();
                };

                audioPlayer.onerror = function(error) {
                    console.error('Error saat memutar audio dari cache:', error);
                    currentAudioIndex++;
                    if (currentAudioIndex < cachedAudioUrls.length) {
                        console.log('Mencoba audio berikutnya dari cache...');
                        playNextFromCache();
                    } else {
                        console.warn('Semua audio di cache gagal diputar');
                        isAudioPlaying = false;
                    }
                };

                audioPlayer.onplay = function() {
                    isAudioPlaying = true;
                    console.log('Audio dari cache mulai diputar');
                };

                audioPlayer.onpause = function() {
                    isAudioPlaying = false;
                    console.log('Audio dari cache dijeda');
                };

                // Putar audio
                audioPlayer.play().catch(function(error) {
                    console.error('Gagal memutar audio dari cache:', error);
                    currentAudioIndex++;
                    if (currentAudioIndex < cachedAudioUrls.length) {
                        playNextFromCache();
                    } else {
                        isAudioPlaying = false;
                    }
                });
            }

            playNextFromCache();
        }

        // Fungsi untuk memutar audio
        function playAudio() {
            // Periksa koneksi jaringan terlebih dahulu
            const networkAvailable = checkNetworkAndRetry();

            // Jika offline, gunakan cache
            if (!networkAvailable) {
                if (cachedAudioUrls.length > 0) {
                    playAudioFromCache();
                } else {
                    console.warn('Offline dan tidak ada cache audio');
                }
                return;
            }

            // Cek status audio dan jika audio sedang dijeda untuk adzan
            const audioStatus = $('#audio_status').val() === 'true';
            if (!audioStatus || isAudioPausedForAdzan) {
                return;
            }

            // Cek apakah Friday Info popup sedang ditampilkan
            const now = getCurrentTimeFromServer().getTime();
            if (fridayInfoStartTime && fridayInfoEndTime && now >= fridayInfoStartTime && now <
                fridayInfoEndTime) {
                console.log('Audio tidak diputar karena Friday Info popup sedang ditampilkan');
                return;
            }

            // Ambil URL audio dari input hidden setiap kali fungsi dipanggil
            const audioUrls = [
                $('#audio1').val(),
                $('#audio2').val(),
                $('#audio3').val()
            ];

            // Filter hanya URL yang valid (tidak kosong) untuk membuat playlist
            let availableAudios = audioUrls.filter(url => url && url.trim() !== '');

            console.log(`Playlist audio dibuat dengan ${availableAudios.length} audio valid:`, availableAudios);

            // Jika tidak ada audio yang valid, hentikan proses dan coba lagi nanti
            if (availableAudios.length === 0) {
                console.warn('Tidak ada audio valid yang tersedia untuk diputar. Mencoba lagi dalam 30 detik.');
                setTimeout(updateAndPlayAudio, 30 * 1000);
                return;
            }

            // Validasi audio URLs
            const validAudios = availableAudios.filter(url => {
                return url && url.trim() !== '' && (url.startsWith('http') || url.startsWith('/'));
            });

            if (validAudios.length === 0) {
                console.warn('Semua URL audio tidak valid, mencoba muat ulang dari server...');
                setTimeout(updateAndPlayAudio, 5 * 1000);
                return;
            }

            // Gunakan hanya audio yang valid
            availableAudios = validAudios;

            // Reset counter percobaan jika kita beralih ke audio baru
            if (lastPlayedAudioIndex === -1 || audioRetryCount >= MAX_RETRY_ATTEMPTS) {
                audioRetryCount = 0;
            }

            // Rotasi ke audio berikutnya jika tidak ada percobaan ulang
            if (audioRetryCount === 0) {
                lastPlayedAudioIndex = (lastPlayedAudioIndex + 1) % availableAudios.length;
            }

            const audioUrl = availableAudios[lastPlayedAudioIndex];
            console.log('Memutar audio index:', lastPlayedAudioIndex, audioRetryCount > 0 ?
                `(percobaan ke-${audioRetryCount})` : '');

            // Jika ada URL audio yang valid
            if (audioUrl && !isAudioPlaying) {
                // Hentikan audio yang sedang diputar (jika ada)
                if (audioPlayer) {
                    audioPlayer.pause();
                    // Bersihkan event listener untuk mencegah memory leak
                    audioPlayer.onended = null;
                    audioPlayer.onerror = null;
                    audioPlayer.onplay = null;
                    audioPlayer.onpause = null;
                    // Hapus event listener yang ditambahkan secara eksplisit
                    try {
                        audioPlayer.removeEventListener('ended', null);
                        audioPlayer.removeEventListener('error', null);
                    } catch (e) {
                        // Abaikan error jika event listener tidak ada
                    }
                    audioPlayer = null;
                }

                // Buat audio player baru
                audioPlayer = new Audio(audioUrl);

                // Definisikan fungsi handler untuk event
                const handleEnded = function() {
                    isAudioPlaying = false;
                    if (audioPlayer) {
                        // Bersihkan event listener
                        audioPlayer.removeEventListener('ended', handleEnded);
                        audioPlayer.removeEventListener('error', handleError);
                        audioPlayer = null;
                    }
                    audioRetryCount = 0; // Reset counter setelah berhasil memutar sampai selesai

                    // Periksa apakah ada audio baru yang tersedia
                    if (window.newAudioAvailable) {
                        console.log('Audio baru tersedia, memuat ulang audio...');
                        window.newAudioAvailable = false;
                        updateAndPlayAudio(); // Reset cache untuk memuat audio baru
                    } else {
                        // Langsung putar audio berikutnya tanpa jeda
                        // Cek apakah Friday Info popup sedang ditampilkan
                        const now = getCurrentTimeFromServer().getTime();
                        if (fridayInfoStartTime && fridayInfoEndTime && now >= fridayInfoStartTime && now <
                            fridayInfoEndTime) {
                            console.log('Audio tidak diputar karena Friday Info popup sedang ditampilkan');
                        } else {
                            playAudio();
                        }
                    }
                };

                // Atur event listener
                audioPlayer.addEventListener('ended', handleEnded);

                // Definisikan fungsi handler untuk error
                const handleError = function(e) {
                    console.error('Error saat memutar audio:', e);
                    isAudioPlaying = false;

                    if (audioPlayer) {
                        // Bersihkan event listener
                        audioPlayer.removeEventListener('ended', handleEnded);
                        audioPlayer.removeEventListener('error', handleError);
                        audioPlayer = null;
                    }

                    // Coba putar audio yang sama lagi jika belum mencapai batas percobaan
                    audioRetryCount++;
                    if (audioRetryCount <= MAX_RETRY_ATTEMPTS) {
                        console.log(
                            `Mencoba memutar ulang audio karena error (percobaan ke-${audioRetryCount})...`
                        );
                        setTimeout(function() {
                            // Cek apakah Friday Info popup sedang ditampilkan
                            const now = getCurrentTimeFromServer().getTime();
                            if (fridayInfoStartTime && fridayInfoEndTime && now >=
                                fridayInfoStartTime && now < fridayInfoEndTime) {
                                console.log(
                                    'Audio tidak diputar karena Friday Info popup sedang ditampilkan'
                                );
                            } else {
                                playAudio();
                            }
                        }, 1000); // Coba lagi setelah 1 detik
                    } else {
                        console.warn(
                            `Gagal memutar audio setelah ${MAX_RETRY_ATTEMPTS} percobaan, beralih ke audio berikutnya`
                        );
                        audioRetryCount = 0; // Reset counter dan coba audio berikutnya
                        setTimeout(function() {
                            // Cek apakah Friday Info popup sedang ditampilkan
                            const now = getCurrentTimeFromServer().getTime();
                            if (fridayInfoStartTime && fridayInfoEndTime && now >=
                                fridayInfoStartTime && now < fridayInfoEndTime) {
                                console.log(
                                    'Audio tidak diputar karena Friday Info popup sedang ditampilkan'
                                );
                            } else {
                                playAudio();
                            }
                        }, 1000);
                    }
                };

                // Tambahkan event listener untuk error
                audioPlayer.addEventListener('error', handleError);

                // Tambahkan timeout untuk menangani kasus audio tidak dapat dimuat
                const audioLoadTimeout = setTimeout(() => {
                    console.error('Timeout: Audio tidak dapat dimuat dalam waktu yang ditentukan');
                    isAudioPlaying = false;

                    if (audioPlayer) {
                        // Bersihkan event listener
                        audioPlayer.removeEventListener('ended', handleEnded);
                        audioPlayer.removeEventListener('error', handleError);
                        audioPlayer = null;
                    }

                    // Coba putar audio yang sama lagi jika belum mencapai batas percobaan
                    audioRetryCount++;
                    if (audioRetryCount <= MAX_RETRY_ATTEMPTS) {
                        console.log(
                            `Mencoba memutar ulang audio karena timeout (percobaan ke-${audioRetryCount})...`
                        );
                        setTimeout(function() {
                            // Cek apakah Friday Info popup sedang ditampilkan
                            const now = getCurrentTimeFromServer().getTime();
                            if (fridayInfoStartTime && fridayInfoEndTime && now >=
                                fridayInfoStartTime && now < fridayInfoEndTime) {
                                console.log(
                                    'Audio tidak diputar karena Friday Info popup sedang ditampilkan'
                                );
                            } else {
                                playAudio();
                            }
                        }, 1000);
                    } else {
                        console.warn(
                            `Gagal memutar audio setelah ${MAX_RETRY_ATTEMPTS} percobaan, beralih ke audio berikutnya`
                        );
                        audioRetryCount = 0; // Reset counter dan coba audio berikutnya
                        setTimeout(function() {
                            // Cek apakah Friday Info popup sedang ditampilkan
                            const now = getCurrentTimeFromServer().getTime();
                            if (fridayInfoStartTime && fridayInfoEndTime && now >=
                                fridayInfoStartTime && now < fridayInfoEndTime) {
                                console.log(
                                    'Audio tidak diputar karena Friday Info popup sedang ditampilkan'
                                );
                            } else {
                                playAudio();
                            }
                        }, 1000);
                    }
                }, 10000); // 10 detik timeout

                // Putar audio
                audioPlayer.play().then(function() {
                    clearTimeout(audioLoadTimeout); // Batalkan timeout jika berhasil
                    isAudioPlaying = true;
                    audioRetryCount = 0; // Reset counter setelah berhasil memutar
                    console.log('Audio berhasil diputar');
                }).catch(function(error) {
                    clearTimeout(audioLoadTimeout); // Batalkan timeout jika gagal dengan error
                    console.error('Gagal memutar audio:', error);
                    isAudioPlaying = false;

                    if (audioPlayer) {
                        // Bersihkan event listener
                        audioPlayer.removeEventListener('ended', handleEnded);
                        audioPlayer.removeEventListener('error', handleError);
                        audioPlayer = null;
                    }

                    // Coba putar audio yang sama lagi jika belum mencapai batas percobaan
                    audioRetryCount++;
                    if (audioRetryCount <= MAX_RETRY_ATTEMPTS) {
                        console.log(`Mencoba memutar ulang audio (percobaan ke-${audioRetryCount})...`);
                        setTimeout(function() {
                            // Cek apakah Friday Info popup sedang ditampilkan
                            const now = getCurrentTimeFromServer().getTime();
                            if (fridayInfoStartTime && fridayInfoEndTime && now >=
                                fridayInfoStartTime && now < fridayInfoEndTime) {
                                console.log(
                                    'Audio tidak diputar karena Friday Info popup sedang ditampilkan'
                                );
                            } else {
                                playAudio();
                            }
                        }, 1000); // Coba lagi setelah 1 detik
                    } else {
                        console.warn(
                            `Gagal memutar audio setelah ${MAX_RETRY_ATTEMPTS} percobaan, beralih ke audio berikutnya`
                        );
                        audioRetryCount = 0; // Reset counter dan coba audio berikutnya
                        setTimeout(function() {
                            // Cek apakah Friday Info popup sedang ditampilkan
                            const now = getCurrentTimeFromServer().getTime();
                            if (fridayInfoStartTime && fridayInfoEndTime && now >=
                                fridayInfoStartTime && now < fridayInfoEndTime) {
                                console.log(
                                    'Audio tidak diputar karena Friday Info popup sedang ditampilkan'
                                );
                            } else {
                                playAudio();
                            }
                        }, 1000);
                    }
                });
            }
        }

        // Fungsi untuk menjeda audio
        function pauseAudio() {
            // Jika audio sudah dijeda untuk adzan, tidak perlu melakukan apa-apa
            if (isAudioPausedForAdzan) {
                return;
            }

            isAudioPausedForAdzan = true;
            if (audioPlayer && isAudioPlaying) {
                audioPlayer.pause();
                isAudioPlaying = false;
                console.log('Audio dijeda karena adzan akan segera dimulai');

                // Tidak perlu membersihkan event listener di sini karena audio akan dilanjutkan nanti
            }
        }

        // Fungsi untuk melanjutkan audio
        function resumeAudio() {
            if (isAudioPausedForAdzan) {
                console.log('Audio akan dilanjutkan setelah fase adzan selesai');
                isAudioPausedForAdzan = false;

                // Tunggu sebentar sebelum memutar audio kembali
                setTimeout(function() {
                    // Cek apakah Friday Info popup sedang ditampilkan
                    const now = getCurrentTimeFromServer().getTime();
                    if (fridayInfoStartTime && fridayInfoEndTime && now >= fridayInfoStartTime && now <
                        fridayInfoEndTime) {
                        console.log('Audio tidak diputar karena Friday Info popup sedang ditampilkan');
                    } else {
                        playAudio(); // Coba putar audio kembali
                    }
                }, 1000); // Tunggu 1 detik
            }
        }

        // Fungsi untuk memeriksa koneksi jaringan dan mencoba ulang jika terjadi masalah
        function checkNetworkAndRetry() {
            // Periksa apakah browser online
            if (!navigator.onLine) {
                if (!isOffline) {
                    isOffline = true;
                    console.warn('Browser offline, beralih ke mode cache-first playback...');

                    // Tampilkan notifikasi offline hanya sekali
                    if (!offlineNotificationShown) {
                        showConnectionStatus('Mode Offline: Internet tidak tersedia. Audio berjalan dari cache',
                            'warning');
                        offlineNotificationShown = true;
                    }
                }

                // Tambahkan event listener untuk online event
                window.addEventListener('online', function onlineHandler() {
                    console.log('Koneksi kembali, memperbarui cache audio...');
                    isOffline = false;
                    offlineNotificationShown = false;

                    // Hapus notifikasi offline
                    if (connectionStatusElement) {
                        connectionStatusElement.remove();
                        connectionStatusElement = null;
                    }

                    // Tampilkan notifikasi online
                    showConnectionStatus('Koneksi kembali - Cache audio diperbarui', 'info');

                    window.removeEventListener('online', onlineHandler);
                    setTimeout(updateAndPlayAudio, 2000); // Tunggu 2 detik setelah online
                }, {
                    once: true
                });

                // Return true jika ada cache, false jika tidak ada cache sama sekali
                return cachedAudioUrls.length > 0;
            }

            // Reset status offline jika online
            if (isOffline) {
                isOffline = false;
                offlineNotificationShown = false;
            }

            return true;
        }

        // Panggil fungsi updateAndPlayAudio saat halaman dimuat
        updateAndPlayAudio();

        // Reset cache audio setiap 30 menit untuk memastikan audio terbaru dimuat
        setInterval(updateAndPlayAudio, 30 * 60 * 1000);

        // Tambahkan event listener untuk mendeteksi perubahan koneksi jaringan - Cache-first strategy
        window.addEventListener('offline', function() {
            console.log('Browser offline - Mode cache-first aktif');
            isOffline = true;

            // Jangan pause audio jika sedang bermain dari cache
            // Audio akan tetap berjalan selama ada cache
            if (cachedAudioUrls.length === 0 && audioPlayer && isAudioPlaying) {
                audioPlayer.pause();
                console.log('Audio dijeda karena offline dan tidak ada cache');
                showConnectionStatus('Tidak ada koneksi dan cache kosong', 'warning');
            } else if (cachedAudioUrls.length > 0) {
                console.log('Audio tetap berjalan dari cache saat offline');
                if (!offlineNotificationShown) {
                    showConnectionStatus(
                        'Mode Offline: Koneksi Internet tidak tersedia. Audio berjalan dari cache',
                        'warning');
                    offlineNotificationShown = true;
                }
            }
        });

        window.addEventListener('online', function() {
            console.log('Browser online kembali - Memperbarui cache');
            isOffline = false;
            offlineNotificationShown = false;

            // Hapus notifikasi offline
            if (connectionStatusElement) {
                connectionStatusElement.remove();
                connectionStatusElement = null;
            }

            // Tampilkan notifikasi online
            showConnectionStatus('Koneksi kembali - Cache audio diperbarui', 'info');

            // Refresh cache dan lanjutkan audio jika diperlukan
            if (audioPlayer && !isAudioPlaying && !isAudioPausedForAdzan) {
                audioPlayer.play().catch(function(error) {
                    console.error('Gagal melanjutkan audio setelah online:', error);

                    // Bersihkan event listener sebelum reset
                    if (audioPlayer) {
                        audioPlayer.onended = null;
                        audioPlayer.onerror = null;
                        audioPlayer.onplay = null;
                        audioPlayer.onpause = null;
                    }

                    // Jika gagal melanjutkan, reset dan coba dari awal
                    updateAndPlayAudio();
                });
            } else if (!audioPlayer || (!isAudioPlaying && !isAudioPausedForAdzan)) {
                // Jika tidak ada audio yang sedang diputar, muat ulang
                updateAndPlayAudio();
            } else if (isAudioPlaying) {
                // Jika audio sedang berjalan, refresh cache di background
                setTimeout(updateAndPlayAudio, 5000);
            }
        });

        // Fungsi untuk memperbarui jam digital
        function updateDigitalClock() {
            const now = getCurrentTimeFromServer();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;

            // Perbarui semua elemen dengan kelas .clock-time
            $('.clock-time').text(timeString);

            // Lanjutkan animasi
            requestAnimationFrame(updateDigitalClock);
        }

        // Mulai animasi jam digital
        requestAnimationFrame(updateDigitalClock);

        setTimeout(() => {
            syncServerTime(() => {
                checkAndRestoreSessions();
                updateSlides();
                updateDate(); // Update tanggal setelah sinkronisasi waktu server selesai
                // console.log('Waktu server diupdate setelah 3 detik halaman di muat');
            });
        }, 3000); // 3000 milidetik = 3 detik

        setInterval(() => {
            syncServerTime();
            // console.log('Waktu server diupdate setiap 30 detik');
        }, 30000);

        let activePrayerStatus = null;
        if ($('#active-prayer-status').val()) {
            try {
                activePrayerStatus = JSON.parse($('#active-prayer-status').val());
                console.log('Active prayer status detected:', activePrayerStatus);
            } catch (e) {
                console.error('Error parsing active prayer status:', e);
            }
        }

        async function fetchPrayerTimes() {
            try {
                const now = getCurrentTimeFromServer();
                const month = now.getMonth() + 1;
                const year = now.getFullYear();
                const monthFormatted = month.toString().padStart(2, '0');
                const url =
                    `https://api.myquran.com/v2/sholat/jadwal/0412/${year}/${monthFormatted}`;

                // Ambil nilai bulan dan tahun saat ini dari input hidden
                const currentMonthValue = $('#current-month').val() || new Date().getMonth() + 1;
                const currentYearValue = $('#current-year').val() || new Date().getFullYear();

                if (month !== parseInt(currentMonthValue) || year !== parseInt(currentYearValue)) {
                    console.log(`Mengambil data jadwal baru: ${url}`);
                    try {
                        const response = await $.ajax({
                            url,
                            method: 'GET'
                        });
                        console.log(
                            `Data bulan baru berhasil diambil untuk bulan ${monthFormatted}/${year}, memperbarui input hidden...`
                        );

                        // Update input hidden dengan bulan dan tahun baru
                        $('#current-month').val(month);
                        $('#current-year').val(year);

                        console.log(`Input hidden diperbarui: bulan=${month}, tahun=${year}`);

                        // Return the jadwal data from the API response structure
                        return response.data && response.data.jadwal ? response.data.jadwal : response;
                    } catch (fetchError) {
                        console.error("Error saat mengambil data jadwal sholat:", fetchError);
                        return null;
                    }
                }
                return null;
            } catch (error) {
                console.error("Error saat mengambil jadwal sholat:", error);
                return null;
            }
        }

        function updateDailyPrayerTimes() {
            try {
                const now = getCurrentTimeFromServer();
                const today = now.getFullYear() + '-' +
                    String(now.getMonth() + 1).padStart(2, '0') + '-' +
                    String(now.getDate()).padStart(2, '0');

                // Ambil data jadwal sholat dari hidden input
                const prayerTimesData = $('#prayer-times').val();
                if (!prayerTimesData) {
                    console.log('Data jadwal sholat tidak tersedia di hidden input');
                    return false;
                }

                let jadwalSholat;
                try {
                    jadwalSholat = JSON.parse(prayerTimesData);
                } catch (e) {
                    console.error('Error parsing prayer times data:', e);
                    return false;
                }

                // Cari jadwal untuk hari ini
                const jadwalHariIni = jadwalSholat.find(item => item.date === today);

                if (!jadwalHariIni) {
                    console.log(`Jadwal sholat tidak ditemukan untuk tanggal: ${today}`);
                    return false;
                }

                // Update tampilan jadwal sholat
                const isFriday = now.getDay() === 5;
                const dzuhurLabel = isFriday ? "Jum'at" : "Dzuhur";

                const prayerTimes = [{
                        name: 'Subuh',
                        time: jadwalHariIni.subuh
                    },
                    {
                        name: 'Syuruq',
                        time: jadwalHariIni.terbit
                    },
                    {
                        name: 'Dhuha',
                        time: jadwalHariIni.dhuha
                    },
                    {
                        name: dzuhurLabel,
                        time: jadwalHariIni.dzuhur
                    },
                    {
                        name: 'Ashar',
                        time: jadwalHariIni.ashar
                    },
                    {
                        name: 'Maghrib',
                        time: jadwalHariIni.maghrib
                    },
                    {
                        name: 'Isya',
                        time: jadwalHariIni.isya
                    }
                ];

                // Update elemen DOM
                $('.prayer-time').each(function(index) {
                    if (index < prayerTimes.length) {
                        const $nameElement = $(this).find('.prayer-name');
                        const $timeElement = $(this).find('.prayer-time-value');

                        if ($nameElement.length && $timeElement.length) {
                            $nameElement.text(prayerTimes[index].name);
                            $timeElement.text(prayerTimes[index].time);
                        }
                    }
                });

                console.log(`Jadwal sholat berhasil diperbarui untuk tanggal: ${today}`);
                return true;

            } catch (error) {
                console.error('Error saat memperbarui jadwal sholat harian:', error);
                return false;
            }
        }

        const $canvas = $('#analogClock');
        const ctx = $canvas[0].getContext('2d');
        let clockRadius = $canvas[0].width / 2 - 10;
        let clockCenter = {
            x: $canvas[0].width / 2,
            y: $canvas[0].height / 2
        };

        // Sumber gaya dari CSS variables dengan fallback default JS
        const hostEl = document.querySelector('.clock-container') || document.documentElement;

        function getCssVar(el, name) {
            const v = getComputedStyle(el).getPropertyValue(name);
            return v ? v.trim() : '';
        }

        function getCssNum(el, name, fallback) {
            const v = getCssVar(el, name);
            const n = parseFloat(v);
            return Number.isFinite(n) ? n : fallback;
        }

        function getCssStr(el, name, fallback) {
            const v = getCssVar(el, name);
            return v !== '' ? v : fallback;
        }

        // Muat gambar logo
        const logo = new Image();
        logo.src = getCssStr(hostEl, '--clock-logo-url', '../theme/static/logo-small.png');

        function drawClock() {
            ctx.clearRect(0, 0, $canvas[0].width, $canvas[0].height);
            const now = getCurrentTimeFromServer();
            const hours = now.getHours() % 12;
            const minutes = now.getMinutes();
            const seconds = now.getSeconds();
            const milliseconds = now.getMilliseconds();

            // Ambil semua variabel gaya (CSS > default JS)
            const faceFill = getCssStr(hostEl, '--clock-face-fill', '#003366');
            const faceStroke = getCssStr(hostEl, '--clock-face-stroke', '#0055a4');
            // Face stroke width dengan dukungan scale
            const faceStrokeWidthScale = getCssNum(hostEl, '--clock-face-stroke-width-scale', null);
            const faceStrokeWidth = (faceStrokeWidthScale && isFinite(faceStrokeWidthScale)) ?
                clockRadius * faceStrokeWidthScale :
                getCssNum(hostEl, '--clock-face-stroke-width', 15);
            const faceShadowColor = getCssStr(hostEl, '--clock-face-shadow-color', 'rgba(0, 0, 0, 0.5)');
            const faceShadowBlur = getCssNum(hostEl, '--clock-face-shadow-blur', 20);
            const faceShadowOffsetX = getCssNum(hostEl, '--clock-face-shadow-offset-x', 0);
            const faceShadowOffsetY = getCssNum(hostEl, '--clock-face-shadow-offset-y', 0);

            const majorTickColor = getCssStr(hostEl, '--clock-major-tick-color', '#ffffff');
            // Major tick width dengan dukungan scale
            const majorTickWidthScale = getCssNum(hostEl, '--clock-major-tick-width-scale', null);
            const majorTickWidth = (majorTickWidthScale && isFinite(majorTickWidthScale)) ?
                clockRadius * majorTickWidthScale :
                getCssNum(hostEl, '--clock-major-tick-width', 4);
            // Major tick start offset dengan dukungan scale
            const majorTickStartOffsetScale = getCssNum(hostEl, '--clock-major-tick-start-offset-scale', null);
            const majorTickStartOffset = (majorTickStartOffsetScale && isFinite(majorTickStartOffsetScale)) ?
                clockRadius * majorTickStartOffsetScale :
                getCssNum(hostEl, '--clock-major-tick-start-offset', 20);
            // Major tick end offset dengan dukungan scale
            const majorTickEndOffsetScale = getCssNum(hostEl, '--clock-major-tick-end-offset-scale', null);
            const majorTickEndOffset = (majorTickEndOffsetScale && isFinite(majorTickEndOffsetScale)) ?
                clockRadius * majorTickEndOffsetScale :
                getCssNum(hostEl, '--clock-major-tick-end-offset', 5);

            const minorTickColor = getCssStr(hostEl, '--clock-minor-tick-color', 'rgba(255, 255, 255, 0.6)');
            // Minor tick width dengan dukungan scale
            const minorTickWidthScale = getCssNum(hostEl, '--clock-minor-tick-width-scale', null);
            const minorTickWidth = (minorTickWidthScale && isFinite(minorTickWidthScale)) ?
                clockRadius * minorTickWidthScale :
                getCssNum(hostEl, '--clock-minor-tick-width', 2);
            // Minor tick start offset dengan dukungan scale
            const minorTickStartOffsetScale = getCssNum(hostEl, '--clock-minor-tick-start-offset-scale', null);
            const minorTickStartOffset = (minorTickStartOffsetScale && isFinite(minorTickStartOffsetScale)) ?
                clockRadius * minorTickStartOffsetScale :
                getCssNum(hostEl, '--clock-minor-tick-start-offset', 10);
            // Minor tick end offset dengan dukungan scale
            const minorTickEndOffsetScale = getCssNum(hostEl, '--clock-minor-tick-end-offset-scale', null);
            const minorTickEndOffset = (minorTickEndOffsetScale && isFinite(minorTickEndOffsetScale)) ?
                clockRadius * minorTickEndOffsetScale :
                getCssNum(hostEl, '--clock-minor-tick-end-offset', 5);

            const numberRadiusOffsetScale = getCssNum(hostEl, '--clock-number-radius-offset-scale', NaN);
            const numberRadiusOffset = Number.isFinite(numberRadiusOffsetScale) ?
                Math.round(clockRadius * numberRadiusOffsetScale) :
                getCssNum(hostEl, '--clock-number-radius-offset', 45);
            const numberColor = getCssStr(hostEl, '--clock-number-color', '#ffffff');
            const number12Color = getCssStr(hostEl, '--clock-number-12-color', '#ff0000');
            const numberShadowColor = getCssStr(hostEl, '--clock-number-shadow-color', 'rgba(0, 0, 0, 0.7)');
            const numberShadowBlur = getCssNum(hostEl, '--clock-number-shadow-blur', 5);
            const numberShadowOffsetX = getCssNum(hostEl, '--clock-number-shadow-offset-x', 3);
            const numberShadowOffsetY = getCssNum(hostEl, '--clock-number-shadow-offset-y', 3);

            const numberFontFamily = getCssStr(hostEl, '--clock-number-font-family', 'Poppins');
            const numberFontWeight = getCssStr(hostEl, '--clock-number-font-weight', 'bold');
            const numberFontSize = getCssNum(hostEl, '--clock-number-font-size', 35);
            const numberFontScale = getCssNum(hostEl, '--clock-number-font-scale', NaN);
            const resolvedNumFontSize = Number.isFinite(numberFontScale) ?
                Math.round(clockRadius * numberFontScale) :
                numberFontSize;
            const numberFont = `${numberFontWeight} ${resolvedNumFontSize}px ${numberFontFamily}`;

            const hourHandColor = getCssStr(hostEl, '--clock-hour-hand-color', '#ffffff');
            const hourHandWidth = getCssNum(hostEl, '--clock-hour-hand-width', 8);
            const hourHandLengthScale = getCssNum(hostEl, '--clock-hour-hand-length-scale', 0.5);

            const minuteHandColor = getCssStr(hostEl, '--clock-minute-hand-color', '#ffffff');
            const minuteHandWidth = getCssNum(hostEl, '--clock-minute-hand-width', 5);
            const minuteHandLengthScale = getCssNum(hostEl, '--clock-minute-hand-length-scale', 0.7);

            const secondHandColor = getCssStr(hostEl, '--clock-second-hand-color', '#ff0000');
            const secondHandWidth = getCssNum(hostEl, '--clock-second-hand-width', 2);
            const secondHandLengthScale = getCssNum(hostEl, '--clock-second-hand-length-scale', 0.85);

            const handShadowColor = getCssStr(hostEl, '--clock-hand-shadow-color', 'rgba(0, 0, 0, 0.6)');
            const handShadowBlur = getCssNum(hostEl, '--clock-hand-shadow-blur', 8);
            const handShadowOffsetX = getCssNum(hostEl, '--clock-hand-shadow-offset-x', 3);
            const handShadowOffsetY = getCssNum(hostEl, '--clock-hand-shadow-offset-y', 3);

            const secondShadowColor = getCssStr(hostEl, '--clock-second-shadow-color', 'rgba(0, 0, 0, 0.4)');
            const secondShadowBlur = getCssNum(hostEl, '--clock-second-shadow-blur', 6);
            const secondShadowOffsetX = getCssNum(hostEl, '--clock-second-shadow-offset-x', 2);
            const secondShadowOffsetY = getCssNum(hostEl, '--clock-second-shadow-offset-y', 2);

            const centerDotColor = getCssStr(hostEl, '--clock-center-dot-color', '#ff0000');
            const centerDotRadius = getCssNum(hostEl, '--clock-center-dot-radius', 8);
            const centerDotShadowColor = getCssStr(hostEl, '--clock-center-dot-shadow-color',
                'rgba(0, 0, 0, 0.5)');
            const centerDotShadowBlur = getCssNum(hostEl, '--clock-center-dot-shadow-blur', 5);
            const centerDotShadowOffsetX = getCssNum(hostEl, '--clock-center-dot-shadow-offset-x', 1);
            const centerDotShadowOffsetY = getCssNum(hostEl, '--clock-center-dot-shadow-offset-y', 1);

            const logoScale = getCssNum(hostEl, '--clock-logo-scale', 0.23);
            const logoOffsetYScale = getCssNum(hostEl, '--clock-logo-offset-y-scale', 0.33);

            ctx.save();
            ctx.beginPath();
            ctx.arc(clockCenter.x, clockCenter.y, clockRadius, 0, Math.PI * 2);
            ctx.shadowColor = faceShadowColor;
            ctx.shadowBlur = faceShadowBlur;
            ctx.shadowOffsetX = faceShadowOffsetX;
            ctx.shadowOffsetY = faceShadowOffsetY;
            ctx.fillStyle = faceFill;
            ctx.fill();
            ctx.shadowColor = 'transparent';
            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;

            ctx.beginPath();
            ctx.arc(clockCenter.x, clockCenter.y, clockRadius, 0, Math.PI * 2);
            ctx.strokeStyle = faceStroke;
            ctx.lineWidth = faceStrokeWidth;
            ctx.stroke();

            // Gambar logo di tengah
            if (logo.complete) {
                const logoHeight = clockRadius * logoScale;
                const logoWidth = (logo.naturalWidth / logo.naturalHeight) * logoHeight;
                const logoX = clockCenter.x - logoWidth / 2;
                const logoY = clockCenter.y - logoHeight / 2 - clockRadius * logoOffsetYScale;
                ctx.drawImage(logo, logoX, logoY, logoWidth, logoHeight);
            }

            // Gambar angka dan tanda jam (major ticks + numbers)
            for (let i = 0; i < 12; i++) {
                const angle = (i * Math.PI / 6) - Math.PI / 2;
                const tickStart = clockRadius - majorTickStartOffset;
                const tickEnd = clockRadius - majorTickEndOffset;

                ctx.beginPath();
                ctx.moveTo(clockCenter.x + Math.cos(angle) * tickStart, clockCenter.y + Math.sin(angle) *
                    tickStart);
                ctx.lineTo(clockCenter.x + Math.cos(angle) * tickEnd, clockCenter.y + Math.sin(angle) *
                    tickEnd);
                ctx.strokeStyle = majorTickColor;
                ctx.lineWidth = majorTickWidth;
                ctx.stroke();

                const numRadius = clockRadius - numberRadiusOffset;
                const numX = clockCenter.x + Math.cos(angle) * numRadius;
                const numY = clockCenter.y + Math.sin(angle) * numRadius;

                ctx.shadowColor = numberShadowColor;
                ctx.shadowBlur = numberShadowBlur;
                ctx.shadowOffsetX = numberShadowOffsetX;
                ctx.shadowOffsetY = numberShadowOffsetY;

                ctx.fillStyle = i === 0 ? number12Color : numberColor;
                ctx.font = numberFont;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText((i === 0 ? 12 : i).toString(), numX, numY);

                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 0;
            }

            // Minor ticks
            for (let i = 0; i < 60; i++) {
                if (i % 5 !== 0) {
                    const angle = (i * Math.PI / 30) - Math.PI / 2;
                    const tickStart = clockRadius - minorTickStartOffset;
                    const tickEnd = clockRadius - minorTickEndOffset;

                    ctx.beginPath();
                    ctx.moveTo(clockCenter.x + Math.cos(angle) * tickStart, clockCenter.y + Math.sin(angle) *
                        tickStart);
                    ctx.lineTo(clockCenter.x + Math.cos(angle) * tickEnd, clockCenter.y + Math.sin(angle) *
                        tickEnd);
                    ctx.strokeStyle = minorTickColor;
                    ctx.lineWidth = minorTickWidth;
                    ctx.stroke();
                }
            }

            // Gambar jarum jam
            const hourAngle = ((hours + minutes / 60) * Math.PI / 6) - Math.PI / 2;
            const minuteAngle = ((minutes + seconds / 60) * Math.PI / 30) - Math.PI / 2;
            const secondAngle = ((seconds + milliseconds / 1000) * Math.PI / 30) - Math.PI / 2;

            ctx.shadowColor = handShadowColor;
            ctx.shadowBlur = handShadowBlur;
            ctx.shadowOffsetX = handShadowOffsetX;
            ctx.shadowOffsetY = handShadowOffsetY;
            drawHand(hourAngle, clockRadius * hourHandLengthScale, hourHandWidth, hourHandColor);
            drawHand(minuteAngle, clockRadius * minuteHandLengthScale, minuteHandWidth, minuteHandColor);

            ctx.shadowColor = secondShadowColor;
            ctx.shadowBlur = secondShadowBlur;
            ctx.shadowOffsetX = secondShadowOffsetX;
            ctx.shadowOffsetY = secondShadowOffsetY;
            drawHand(secondAngle, clockRadius * secondHandLengthScale, secondHandWidth, secondHandColor);

            ctx.shadowColor = centerDotShadowColor;
            ctx.shadowBlur = centerDotShadowBlur;
            ctx.shadowOffsetX = centerDotShadowOffsetX;
            ctx.shadowOffsetY = centerDotShadowOffsetY;
            ctx.beginPath();
            ctx.arc(clockCenter.x, clockCenter.y, centerDotRadius, 0, Math.PI * 2);
            ctx.fillStyle = centerDotColor;
            ctx.fill();

            ctx.restore();

            const $clockText = $('.clock-text');
            if ($clockText.length) {
                const displayHours = now.getHours();
                const colonStyle = (seconds % 2 === 0) ? 'visibility: visible;' : 'visibility: hidden;';
                $clockText.html(
                    `${displayHours.toString().padStart(2, "0")}<span class="colon" style="${colonStyle}">:</span>${minutes.toString().padStart(2, "0")}`
                );
            }
        }

        function drawHand(angle, length, width, color) {
            ctx.beginPath();
            ctx.moveTo(clockCenter.x, clockCenter.y);
            ctx.lineTo(clockCenter.x + Math.cos(angle) * length, clockCenter.y + Math.sin(angle) * length);
            ctx.strokeStyle = color;
            ctx.lineWidth = width;
            ctx.lineCap = 'round';
            ctx.stroke();
        }

        function animateClock() {
            drawClock();
            requestAnimationFrame(animateClock);
        }

        if ($canvas.length && ctx) {
            animateClock();
            $(window).on('resize', function() {
                const $clockContainer = $('.clock-container');
                if ($clockContainer.length) {
                    const containerWidth = $clockContainer.width();
                    const containerHeight = $clockContainer.height();

                    $canvas[0].width = containerWidth;
                    $canvas[0].height = containerHeight;

                    clockRadius = Math.min($canvas[0].width, $canvas[0].height) / 2 - 10;
                    clockCenter = {
                        x: $canvas[0].width / 2,
                        y: $canvas[0].height / 2
                    };
                }
            });
        }

        // Fungsi untuk memeriksa pembaruan tema dengan reload
        function checkThemeUpdate() {
            const slug = window.location.pathname.replace(/^\//, '');
            $.ajax({
                url: `/api/theme-check/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const newThemeId = response.data.theme_id;
                        const newUpdatedAt = response.data.updated_at;
                        const newCssFile = response.data.css_file;

                        const currentThemeId = $('#current-theme-id').val() || null;
                        const currentUpdatedAt = $('#current-theme-updated-at').val() || 0;
                        const currentThemeCss = $('#current-theme-css').val() || '';

                        if (newThemeId && (newThemeId !== currentThemeId || newUpdatedAt >
                                currentUpdatedAt)) {
                            console.log('Tema diperbarui:', {
                                newThemeId,
                                newUpdatedAt,
                                newCssFile
                            });

                            $('#current-theme-id').val(newThemeId);
                            $('#current-theme-updated-at').val(newUpdatedAt);
                            $('#current-theme-css').val(newCssFile);

                            if (newCssFile && newCssFile !== currentThemeCss) {
                                console.log('Memuat ulang halaman untuk CSS baru:', newCssFile);
                                window.location.reload();
                            }
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Gagal memeriksa pembaruan tema:', error);
                }
            });
        }

        function updateMosqueInfo() {
            const slug = window.location.pathname.replace(/^\//, '');

            if (typeof $.ajax === 'undefined') {
                console.error('jQuery AJAX tidak tersedia. Gunakan versi jQuery lengkap, bukan slim.');
                return;
            }

            $.ajax({
                url: `/api/profil/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('.mosque-name-highlight').text(response.data.name);
                        $('.mosque-address').text(response.data.address);

                        // Preload logo masjid
                        const logoMasjidSrc = response.data.logo_masjid ||
                            '/images/other/logo-masjid-default.png';
                        const logoMasjidImg = new Image();
                        logoMasjidImg.src = logoMasjidSrc;

                        logoMasjidImg.onload = function() {
                            let $logoMasjid = $('.logo-container .logo-masjid');
                            if ($logoMasjid.length) {
                                if ($logoMasjid.attr('src') !== logoMasjidSrc) {
                                    $logoMasjid.attr('src', logoMasjidSrc);
                                }
                            } else {
                                $('.logo-container').append(
                                    `<img src="${logoMasjidSrc}" alt="Logo Masjid" class="logo logo-masjid">`
                                );
                            }
                        };

                        // Preload logo pemerintah jika ada
                        if (response.data.logo_pemerintah) {
                            const logoPemerintahImg = new Image();
                            logoPemerintahImg.src = response.data.logo_pemerintah;

                            logoPemerintahImg.onload = function() {
                                let $logoPemerintah = $('.logo-container .logo-pemerintah');
                                if ($logoPemerintah.length) {
                                    if ($logoPemerintah.attr('src') !== response.data
                                        .logo_pemerintah) {
                                        $logoPemerintah.attr('src', response.data
                                            .logo_pemerintah);
                                    }
                                } else {
                                    $('.logo-container').append(
                                        `<img src="${response.data.logo_pemerintah}" alt="Logo Pemerintah" class="logo logo-pemerintah">`
                                    );
                                }
                            };
                        } else {
                            $('.logo-container .logo-pemerintah').remove();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data profil masjid:', error);
                }
            });
        }

        function updateDate() {
            const $dateElement = $('.date-item');
            if (!$dateElement.length) return;

            const serverNow = getCurrentTimeFromServer();

            if (typeof moment === 'undefined') {
                console.warn("moment.js tidak tersedia");
                return;
            }

            const masehiText = formatMasehi(serverNow);
            const hijriText = calculateHijriFromServer(serverNow);

            $dateElement.html(hijriText ? `${masehiText} / ${hijriText}` : masehiText);

            checkMonthChange(serverNow);
        }

        /* --------------------------------------------------------------------------
         * 1. Format Tanggal Masehi
         * -------------------------------------------------------------------------- */
        function formatMasehi(date) {
            moment.locale('id');

            let hari = moment(date).format('dddd');
            if (hari === 'Minggu') hari = 'Ahad';

            const tanggal = moment(date).format('D MMMM YYYY');
            return `<span class="day-name">${hari}</span>, ${tanggal}`;
        }

        /* --------------------------------------------------------------------------
         * 2. Hitung Hijriah berdasarkan serverNow
         * -------------------------------------------------------------------------- */
        function calculateHijriFromServer(serverNow) {
            try {
                const {
                    currentMinutes,
                    isAfterMaghrib
                } = compareWithMaghrib(serverNow);

                // Jika sudah lewat Maghrib → Hijriah +1 hari
                const baseDate = isAfterMaghrib ?
                    new Date(serverNow.getTime() + 86400000) :
                    serverNow;

                const options = {
                    year: "numeric",
                    month: "long",
                    day: "numeric",
                    timeZone: "Asia/Jakarta",
                };

                try {
                    return new Intl.DateTimeFormat("id-ID-u-ca-islamic-umalqura", options)
                        .format(baseDate);
                } catch (_) {
                    // fallback islamic biasa
                    return new Intl.DateTimeFormat("id-ID-u-ca-islamic", options)
                        .format(baseDate);
                }

            } catch (err) {
                console.warn("Gagal menghitung Hijriah:", err);
                return "";
            }
        }

        /* --------------------------------------------------------------------------
         * 3. Hitung apakah sudah lewat Maghrib berdasarkan waktu server
         * -------------------------------------------------------------------------- */
        function compareWithMaghrib(serverNow) {
            const {
                hour: curH,
                minute: curM
            } = getJakartaTimeFromServer(serverNow);
            const currentMinutes = curH * 60 + curM;

            const maghribMinutes = getMaghribFromDOM();
            const fallbackMaghrib = curH >= 18;

            const isAfterMaghrib = (maghribMinutes !== null) ?
                currentMinutes >= maghribMinutes :
                fallbackMaghrib;

            return {
                currentMinutes,
                isAfterMaghrib
            };
        }

        /* --------------------------------------------------------------------------
         * Ambil Jam-Menit Jakarta dari waktu server
         * -------------------------------------------------------------------------- */
        function getJakartaTimeFromServer(serverNow) {
            const hm = new Intl.DateTimeFormat("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
                timeZone: "Asia/Jakarta",
                hour12: false,
            }).format(serverNow);

            const [h, m] = hm.replace('.', ':').split(':').map(n => parseInt(n, 10));
            return {
                hour: h,
                minute: m
            };
        }

        /* --------------------------------------------------------------------------
         * Ambil Waktu Maghrib dari DOM
         * -------------------------------------------------------------------------- */
        function getMaghribFromDOM() {
            let maghribMinutes = null;

            $(".prayer-time").each(function() {
                const name = ($(this).find('.prayer-name').text() || "").trim().toLowerCase();
                if (name !== "maghrib") return;

                const val = ($(this).find('.prayer-time-value').text() || "").trim();
                if (!val) return;

                const [h, m] = val.replace('.', ':').split(':').map(n => parseInt(n, 10));
                if (!isNaN(h) && !isNaN(m)) {
                    maghribMinutes = h * 60 + m;
                }
            });

            return maghribMinutes;
        }

        /* --------------------------------------------------------------------------
         * 4. Cek Pergantian Bulan untuk update jadwal sholat
         * -------------------------------------------------------------------------- */
        function checkMonthChange(serverNow) {
            const hour = serverNow.getHours();
            const minute = serverNow.getMinutes();

            if (hour === 0 && minute <= 5) {
                const currentMonth = serverNow.getMonth() + 1;
                const lastMonth = parseInt(localStorage.getItem("lastCheckedMonth") || "0");

                if (currentMonth !== lastMonth) {
                    console.log("Bulan berubah → update jadwal sholat");
                    localStorage.setItem("lastCheckedMonth", currentMonth);
                    fetchPrayerTimes();
                }
            }
        }



        function updateScrollingText(marqueeData) {
            const $scrollingTextElement = $('.scrolling-text');

            // Ambil teks marquee
            let marqueeTexts;
            if (marqueeData) {
                marqueeTexts = [
                    marqueeData.marquee1 || '',
                    marqueeData.marquee2 || '',
                    marqueeData.marquee3 || '',
                    marqueeData.marquee4 || '',
                    marqueeData.marquee5 || '',
                    marqueeData.marquee6 || ''
                ].filter(text => text.trim() !== '');
            } else {
                marqueeTexts = [
                    $('#marquee1').val() || '',
                    $('#marquee2').val() || '',
                    $('#marquee3').val() || '',
                    $('#marquee4').val() || '',
                    $('#marquee5').val() || '',
                    $('#marquee6').val() || ''
                ].filter(text => text.trim() !== '');
            }

            if (!$scrollingTextElement.length || marqueeTexts.length === 0) return;

            const combinedText = marqueeTexts.join(' <span class="separator">•</span> ');

            // Hitung durasi animasi dengan kecepatan dinamis
            const textLength = combinedText.length;
            const baseDuration = 240;
            const speedRaw = marqueeData && typeof marqueeData.speed !== 'undefined' ? marqueeData.speed : ($('#marquee-speed').val() || '1');
            let speedMultiplier = parseFloat(speedRaw);
            if (!isFinite(speedMultiplier)) speedMultiplier = 1;
            // Clamp 0.1..10 agar aman
            speedMultiplier = Math.max(0.1, Math.min(speedMultiplier, 10));
            const calculatedDuration = Math.max(baseDuration, textLength / 20) / speedMultiplier;

            // Gunakan waktu server untuk sinkronisasi
            const now = getCurrentTimeFromServer().getTime();

            // Gunakan timestamp server untuk menghitung posisi awal yang konsisten
            // Menggunakan modulo dari timestamp server dengan durasi total animasi
            // Ini akan memastikan semua perangkat memulai dari posisi yang sama
            const totalCycleTimeMs = calculatedDuration * 1000;
            const animationProgress = (now % totalCycleTimeMs) / totalCycleTimeMs;

            // Perbarui elemen marquee
            $scrollingTextElement.css('animation', 'none');
            $scrollingTextElement.html(`<p>${combinedText}</p>`);

            if ($scrollingTextElement[0]) {
                $scrollingTextElement[0].offsetHeight; // Trigger reflow
            }

            // Terapkan animasi dengan delay berdasarkan progress
            $scrollingTextElement.css({
                'animation': `scrollText ${calculatedDuration}s linear infinite`,
                'animation-delay': `-${animationProgress * calculatedDuration}s`
            });

            // Simpan teks marquee untuk mendeteksi perubahan
            localStorage.setItem('marqueeText', combinedText);
        }

        function updateMarqueeText() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (typeof $.ajax === 'undefined') {
                console.error('jQuery AJAX tidak tersedia. Gunakan versi jQuery lengkap, bukan slim.');
                return;
            }

            // console.log('Memperbarui teks marquee untuk slug:', slug);

            $.ajax({
                url: `/api/marquee1/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // console.log('Respons API marquee:', response);
                    if (response.success) {
                        $('#marquee1').val(response.data.marquee1);
                        $('#marquee2').val(response.data.marquee2);
                        $('#marquee3').val(response.data.marquee3);
                        $('#marquee4').val(response.data.marquee4);
                        $('#marquee5').val(response.data.marquee5);
                        $('#marquee6').val(response.data.marquee6);
                        if (typeof response.data.speed !== 'undefined') {
                            $('#marquee-speed').val(response.data.speed);
                        }

                        updateScrollingText(response.data);
                        // console.log('Teks marquee diperbarui');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data marquee:', error, xhr.responseText);
                }
            });
        }

        function initMarquee() {
            updateScrollingText();
        }

        const beepSound = new Audio('/sounds/alarm/beep.mp3');
        let adzanTimeout = null;
        let iqomahTimeout = null;
        let adzanImageTimeout = null;
        let currentAdzanIndex = 0;
        let isAdzanPlaying = false;

        let adzanStartTime = localStorage.getItem('adzanStartTime') ? parseInt(localStorage.getItem(
            'adzanStartTime')) : null;
        let iqomahStartTime = localStorage.getItem('iqomahStartTime') ? parseInt(localStorage.getItem(
            'iqomahStartTime')) : null;
        let currentPrayerName = localStorage.getItem('currentPrayerName') || null;
        let currentPrayerTime = localStorage.getItem('currentPrayerTime') || null;

        function checkAndRestoreSessions() {
            const now = getCurrentTimeFromServer();
            const nowTime = now.getTime();
            const currentTimeInMinutes = now.getHours() * 60 + now.getMinutes();

            if (fridayInfoStartTime && fridayInfoEndTime && nowTime < fridayInfoEndTime) {
                clearAdzanState();
                displayFridayInfoPopup(fridayInfoData, true);
                return;
            }

            if (activePrayerStatus) {
                const {
                    phase,
                    prayerName,
                    prayerTime,
                    elapsedSeconds,
                    remainingSeconds,
                    progress,
                    isFriday,
                    isSyuruq,
                    isDhuha
                } = activePrayerStatus;

                if (phase === 'adzan') {
                    adzanStartTime = nowTime - (elapsedSeconds * 1000);
                    localStorage.setItem('adzanStartTime', adzanStartTime);
                    localStorage.setItem('currentPrayerName', prayerName);
                    localStorage.setItem('currentPrayerTime', prayerTime);
                    currentPrayerName = prayerName;
                    currentPrayerTime = prayerTime;

                    if (isSyuruq) {
                        showSyuruqPopup(prayerName, prayerTime, true);
                    } else if (isDhuha) {
                        showDhuhaPopup(prayerName, prayerTime, true);
                    } else {
                        showAdzanPopup(prayerName, prayerTime, true);
                    }
                    return;
                } else if (phase === 'iqomah') {
                    adzanStartTime = nowTime - (elapsedSeconds * 1000);
                    iqomahStartTime = adzanStartTime + (180 * 1000);
                    localStorage.setItem('adzanStartTime', adzanStartTime);
                    localStorage.setItem('iqomahStartTime', iqomahStartTime);
                    localStorage.setItem('currentPrayerName', prayerName);
                    localStorage.setItem('currentPrayerTime', prayerTime);
                    currentPrayerName = prayerName;
                    currentPrayerTime = prayerTime;
                    showIqomahPopup(prayerTime, true);
                    return;
                } else if (phase === 'final') {
                    showFinalAdzanImage();
                    return;
                }

                if (isFriday) {
                    clearAdzanState();
                    const options = {
                        weekday: 'long',
                        day: '2-digit',
                        month: '2-digit',
                        year: '2-digit'
                    };
                    const formattedDate = now.toLocaleDateString('id-ID', options);
                    const khatib = $('#khatib').val();
                    const imam = $('#imam').val();
                    const muadzin = $('#muadzin').val();
                    const fridayData = {
                        date: formattedDate,
                        khatib,
                        imam,
                        muadzin
                    };
                    displayFridayInfoPopup(fridayData);
                    return;
                }
            }

            if (adzanStartTime && !iqomahStartTime) {
                const adzanElapsedSeconds = (nowTime - adzanStartTime) / 1000;
                const adzanDuration = getAdzanDuration(currentPrayerName || 'Dzuhur');

                if (adzanElapsedSeconds < adzanDuration) {
                    showAdzanPopup(currentPrayerName, currentPrayerTime, true);
                } else {
                    if (currentPrayerName === "Jum'at" && now.getDay() === 5) {
                        clearAdzanState();
                        updateFridayImages();
                        startFridayImageSlider();
                        const $fridayPopup = $('#fridayInfoPopup');
                        if ($fridayPopup.length) {
                            $fridayPopup.css('display', 'flex');
                        }
                        const options = {
                            weekday: 'long',
                            day: '2-digit',
                            month: '2-digit',
                            year: '2-digit'
                        };
                        const formattedDate = now.toLocaleDateString('id-ID', options);
                        const khatib = $('#khatib').val();
                        const imam = $('#imam').val();
                        const muadzin = $('#muadzin').val();
                        const fridayData = {
                            date: formattedDate,
                            khatib,
                            imam,
                            muadzin
                        };
                        displayFridayInfoPopup(fridayData);
                    } else {
                        iqomahStartTime = adzanStartTime + (adzanDuration * 1000);
                        localStorage.setItem('iqomahStartTime', iqomahStartTime);
                        showIqomahPopup(currentPrayerTime, true);
                    }
                }
            } else if (iqomahStartTime) {
                const iqomahElapsedSeconds = (nowTime - iqomahStartTime) / 1000;
                const iqomahDuration = getIqomahDuration(currentPrayerName || 'Dzuhur');
                const finalPhaseDuration = getFinalDuration(currentPrayerName || 'Dzuhur');

                if (iqomahElapsedSeconds < iqomahDuration) {
                    showIqomahPopup(currentPrayerTime, true);
                } else if (iqomahElapsedSeconds < iqomahDuration + finalPhaseDuration) {
                    showFinalAdzanImage();
                } else {
                    clearAdzanState();
                }
            } else {
                const $prayerTimesElements = $('.prayer-time');
                let prayerToRestore = null;
                $prayerTimesElements.each(function() {
                    const $nameElement = $(this).find('.prayer-name');
                    const $timeElement = $(this).find('.prayer-time-value');
                    if ($nameElement.length && $timeElement.length) {
                        const prayerName = $nameElement.text().trim();
                        const prayerTime = $timeElement.text().trim();
                        const [hours, minutes] = prayerTime.split(':').map(Number);
                        const prayerTimeInMinutes = hours * 60 + minutes;

                        const timeDiffMinutes = currentTimeInMinutes - prayerTimeInMinutes;
                        if (timeDiffMinutes >= 0 && timeDiffMinutes <= 10) {
                            prayerToRestore = {
                                name: prayerName,
                                time: prayerTime,
                                timeInMinutes: prayerTimeInMinutes
                            };
                        }
                    }
                });

                if (prayerToRestore) {
                    const timeDiffSeconds = (currentTimeInMinutes - prayerToRestore.timeInMinutes) * 60;
                    const adzanDuration = getAdzanDuration(prayerToRestore.name);
                    const iqomahDuration = getIqomahDuration(prayerToRestore.name);
                    const finalPhaseDuration = getFinalDuration(prayerToRestore.name);

                    if (timeDiffSeconds < adzanDuration) {
                        adzanStartTime = nowTime - (timeDiffSeconds * 1000);
                        localStorage.setItem('adzanStartTime', adzanStartTime);
                        localStorage.setItem('currentPrayerName', prayerToRestore.name);
                        localStorage.setItem('currentPrayerTime', prayerToRestore.time);
                        currentPrayerName = prayerToRestore.name;
                        currentPrayerTime = prayerToRestore.time;

                        if (prayerToRestore.name.toLowerCase().includes('syuruq') ||
                            prayerToRestore.name.toLowerCase().includes('shuruq') ||
                            prayerToRestore.name.toLowerCase().includes('terbit')) {
                            showSyuruqPopup(prayerToRestore.name, prayerToRestore.time, true);
                        } else if (prayerToRestore.name.toLowerCase().includes('dhuha')) {
                            showDhuhaPopup(prayerToRestore.name, prayerToRestore.time, true);
                        } else {
                            showAdzanPopup(prayerToRestore.name, prayerToRestore.time, true);
                        }
                    } else if (prayerToRestore.name !== "Jum'at" || now.getDay() !== 5) {
                        // Syuruq/Dhuha hanya memiliki fase adzan, tidak ada iqomah dan final
                        if (prayerToRestore.name.toLowerCase().includes('syuruq') ||
                            prayerToRestore.name.toLowerCase().includes('dhuha')) {
                            // Untuk Syuruq/Dhuha, langsung clear state setelah adzan selesai
                            clearAdzanState();
                        } else {
                            if (timeDiffSeconds < adzanDuration + iqomahDuration) {
                                adzanStartTime = nowTime - (timeDiffSeconds * 1000);
                                iqomahStartTime = adzanStartTime + (adzanDuration * 1000);
                                localStorage.setItem('adzanStartTime', adzanStartTime);
                                localStorage.setItem('iqomahStartTime', iqomahStartTime);
                                localStorage.setItem('currentPrayerName', prayerToRestore.name);
                                localStorage.setItem('currentPrayerTime', prayerToRestore.time);
                                currentPrayerName = prayerToRestore.name;
                                currentPrayerTime = prayerToRestore.time;
                                showIqomahPopup(prayerToRestore.time, true);
                            } else if (timeDiffSeconds < adzanDuration + iqomahDuration + finalPhaseDuration) {
                                adzanStartTime = nowTime - (timeDiffSeconds * 1000);
                                iqomahStartTime = adzanStartTime + (adzanDuration * 1000);
                                localStorage.setItem('adzanStartTime', adzanStartTime);
                                localStorage.setItem('iqomahStartTime', iqomahStartTime);
                                localStorage.setItem('currentPrayerName', prayerToRestore.name);
                                localStorage.setItem('currentPrayerTime', prayerToRestore.time);
                                currentPrayerName = prayerToRestore.name;
                                currentPrayerTime = prayerToRestore.time;
                                showFinalAdzanImage();
                            }
                        }
                    } else {
                        adzanStartTime = nowTime - (timeDiffSeconds * 1000);
                        localStorage.setItem('adzanStartTime', adzanStartTime);
                        localStorage.setItem('currentPrayerName', prayerToRestore.name);
                        localStorage.setItem('currentPrayerTime', prayerToRestore.time);
                        currentPrayerName = prayerToRestore.name;
                        currentPrayerTime = prayerToRestore.time;
                        updateFridayImages();
                        startFridayImageSlider();
                        const options = {
                            weekday: 'long',
                            day: '2-digit',
                            month: '2-digit',
                            year: '2-digit'
                        };
                        const formattedDate = now.toLocaleDateString('id-ID', options);
                        const khatib = $('#khatib').val();
                        const imam = $('#imam').val();
                        const muadzin = $('#muadzin').val();
                        const fridayData = {
                            date: formattedDate,
                            khatib,
                            imam,
                            muadzin
                        };
                        displayFridayInfoPopup(fridayData);
                        clearAdzanState();
                    }
                }
            }
        }

        function clearAdzanState() {
            adzanStartTime = null;
            iqomahStartTime = null;
            isAdzanPlaying = false;
            currentPrayerName = null;
            currentPrayerTime = null;
            localStorage.removeItem('adzanStartTime');
            localStorage.removeItem('iqomahStartTime');
            localStorage.removeItem('currentPrayerName');
            localStorage.removeItem('currentPrayerTime');
            localStorage.removeItem('iqomahSliderStartTime');
            localStorage.removeItem('jumatAdzanShown');

            // Hentikan audio adzan jika sedang diputar
            if (window.adzanAudioPlayer) {
                window.adzanAudioPlayer.pause();
                window.adzanAudioPlayer.currentTime = 0;
            }

            if (isAudioPausedForAdzan) {
                // Hanya panggil resumeAudio jika tidak sedang dalam periode Friday Info popup
                const now = getCurrentTimeFromServer().getTime();
                if (!(fridayInfoStartTime && fridayInfoEndTime && now >= fridayInfoStartTime && now <
                        fridayInfoEndTime)) {
                    resumeAudio();
                }
                isAudioPausedForAdzan = false;
            }

            if (adzanTimeout) {
                clearTimeout(adzanTimeout);
                adzanTimeout = null;
            }
            if (iqomahTimeout) {
                clearTimeout(iqomahTimeout);
                iqomahTimeout = null;
            }
            if (iqomahImageSliderInterval) {
                clearInterval(iqomahImageSliderInterval);
                iqomahImageSliderInterval = null;
            }
            if (adzanImageTimeout) {
                clearTimeout(adzanImageTimeout);
                adzanImageTimeout = null;
            }

            // Lanjutkan pemutaran audio jika tidak ada fase lain yang akan ditampilkan
            // Catatan: Tidak perlu memanggil resumeAudio() di sini karena akan dipanggil
            // di clearFridayInfoState() atau showFinalAdzanImage() tergantung alur program
        }

        function clearAllAdzanStates() {
            const keysToRemove = [
                'adzanStartTime', 'iqomahStartTime', 'currentPrayerName',
                'currentPrayerTime', 'jumatAdzanShown',
                'fridayInfoStartTime', 'adzanImageStartTime'
            ];
            keysToRemove.forEach(key => localStorage.removeItem(key));

            // Reset semua variabel state
            adzanStartTime = null;
            iqomahStartTime = null;
            currentPrayerName = null;
            currentPrayerTime = null;
            isAdzanPlaying = false;
            isAudioPausedForAdzan = false;

            // Bersihkan semua flag fridayAdzanCleared
            Object.keys(localStorage).forEach(key => {
                if (key.startsWith('fridayAdzanCleared_')) {
                    localStorage.removeItem(key);
                }
            });

            // Lanjutkan pemutaran audio jika adzan dibatalkan
            resumeAudio();
        }

        function checkDayChange() {
            const now = getCurrentTimeFromServer();
            const currentDate =
                `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
            const storedDate = localStorage.getItem('lastCheckedDate');
            // console.log('Checking day change...', currentDate, storedDate);
            if (currentDate !== storedDate) {
                clearAllAdzanStates();
                localStorage.setItem('lastCheckedDate', currentDate);

                // Perbarui halaman tanpa reload dengan memanggil fungsi-fungsi yang diperlukan
                console.log('Hari berubah, memperbarui konten...');

                // Update tanggal dan waktu
                updateDate();

                // Update jadwal sholat untuk hari baru dengan AJAX
                $.ajax({
                    url: '/api/refresh-prayer-times',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            console.log('Prayer times refreshed successfully');
                            // Update frontend display setelah diperbarui
                            updateDailyPrayerTimes();
                        } else {
                            console.error('Error refreshing prayer times:', response.message);
                            updateDailyPrayerTimes();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error refreshing prayer times:', error);
                        // Fallback ke update frontend saja
                        updateDailyPrayerTimes();
                    }
                });

                // Juga cek apakah perlu mengambil data bulan baru
                fetchPrayerTimes();

                // Update informasi masjid
                // updateMosqueInfo();

                // Update teks marquee
                updateScrollingText();

                // Update tema jika ada perubahan
                // checkThemeUpdate();

                // Update slides
                updateSlides();

                // Update jumbotron data
                updateJumbotronData();

                // Update audio dan gambar
                updateAndPlayAudio();
                // updateFridayImages();
                updateIqomahImages();
                updateAdzanImages();
                // updateFridayOfficials();

                // Reset prayer times handling untuk hari baru
                handlePrayerTimes();

                console.log('Konten berhasil diperbarui untuk hari baru');
            }
        }

        function scheduleMidnightCheck() {
            const now = getCurrentTimeFromServer();
            const midnight = new Date(now);
            midnight.setHours(24, 0, 0, 0);
            const timeUntilMidnight = midnight - now;
            setTimeout(() => {
                checkDayChange();
                scheduleMidnightCheck();
            }, timeUntilMidnight);
        }

        checkDayChange();
        scheduleMidnightCheck();

        // Update jadwal sholat untuk hari ini saat halaman dimuat dengan AJAX
        $.ajax({
            url: '/api/refresh-prayer-times',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // console.log('Initial prayer times refreshed successfully');
                    updateDailyPrayerTimes();
                } else {
                    console.error('Error refreshing initial prayer times:', response.message);
                    updateDailyPrayerTimes();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error refreshing initial prayer times:', error);
                updateDailyPrayerTimes();
            }
        });

        function playBeepSound(times = 1) {
            let count = 0;
            const play = () => {
                beepSound.play();
                count++;
                if (count < times) {
                    setTimeout(play, 5000);
                }
            };
            play();
        }

        function calculateSyncStartTime(prayerTimeStr) {
            const serverTime = getCurrentTimeFromServer();
            const [prayerHours, prayerMinutes] = prayerTimeStr.split(':').map(Number);
            const prayerTime = new Date(serverTime);
            prayerTime.setHours(prayerHours, prayerMinutes, 0, 0);

            const timeDiff = serverTime - prayerTime;

            if (timeDiff >= 0 && timeDiff <= 10 * 60 * 1000) {
                return prayerTime.getTime();
            } else if (timeDiff < 0) {
                return prayerTime.getTime();
            } else {
                return null;
            }
        }

        function getDurasiData() {
            let durasiData = {};
            try {
                const durasiJson = $('#durasi-data').val();
                if (durasiJson) {
                    durasiData = JSON.parse(durasiJson);
                    // console.log('Data durasi berhasil dimuat:', durasiData);
                } else {
                    console.warn('Data durasi tidak tersedia, menggunakan nilai default');
                }
            } catch (e) {
                console.error('Error parsing durasi data:', e);
            }
            return durasiData;
        }

        // Fungsi untuk mendapatkan durasi adzan berdasarkan nama sholat (dalam detik)
        function getAdzanDuration(prayerName) {
            const durasiData = getDurasiData();
            const prayerLower = prayerName.toLowerCase();

            // Default durasi jika data tidak tersedia (dalam detik)
            const defaultDuration = 3 * 60; // 3 menit

            if (!durasiData) return defaultDuration;

            if (prayerLower === 'subuh' && durasiData.adzan_shubuh) {
                return durasiData.adzan_shubuh * 60;
            } else if ((prayerLower === 'dzuhur' || prayerLower === "jum'at") && durasiData.adzan_dzuhur) {
                return durasiData.adzan_dzuhur * 60;
            } else if (prayerLower === 'ashar' && durasiData.adzan_ashar) {
                return durasiData.adzan_ashar * 60;
            } else if (prayerLower === 'maghrib' && durasiData.adzan_maghrib) {
                return durasiData.adzan_maghrib * 60;
            } else if (prayerLower === 'isya' && durasiData.adzan_isya) {
                return durasiData.adzan_isya * 60;
            }

            return defaultDuration;
        }

        // Fungsi untuk mendapatkan durasi iqomah berdasarkan nama sholat (dalam detik)
        function getIqomahDuration(prayerName) {
            const durasiData = getDurasiData();
            const prayerLower = prayerName.toLowerCase();

            // Default durasi jika data tidak tersedia (dalam detik)
            const defaultDuration = 7 * 60; // 7 menit

            if (!durasiData) return defaultDuration;

            if (prayerLower === 'subuh' && durasiData.iqomah_shubuh) {
                return durasiData.iqomah_shubuh * 60;
            } else if (prayerLower === 'dzuhur' && durasiData.iqomah_dzuhur) {
                return durasiData.iqomah_dzuhur * 60;
            } else if (prayerLower === 'ashar' && durasiData.iqomah_ashar) {
                return durasiData.iqomah_ashar * 60;
            } else if (prayerLower === 'maghrib' && durasiData.iqomah_maghrib) {
                return durasiData.iqomah_maghrib * 60;
            } else if (prayerLower === 'isya' && durasiData.iqomah_isya) {
                return durasiData.iqomah_isya * 60;
            }

            return defaultDuration;
        }

        // Fungsi untuk mendapatkan durasi final berdasarkan nama sholat (dalam detik)
        function getFinalDuration(prayerName) {
            const durasiData = getDurasiData();
            const prayerLower = prayerName.toLowerCase();

            // Default durasi jika data tidak tersedia (dalam detik)
            const defaultDuration = 1 * 60; // 60 detik

            if (!durasiData) return defaultDuration;

            if (prayerLower === 'subuh' && durasiData.final_shubuh) {
                return durasiData.final_shubuh * 60;
            } else if (prayerLower === 'dzuhur' && durasiData.final_dzuhur) {
                return durasiData.final_dzuhur * 60;
            } else if (prayerLower === 'ashar' && durasiData.final_ashar) {
                return durasiData.final_ashar * 60;
            } else if (prayerLower === 'maghrib' && durasiData.final_maghrib) {
                return durasiData.final_maghrib * 60;
            } else if (prayerLower === 'isya' && durasiData.final_isya) {
                return durasiData.final_isya * 60;
            }

            return defaultDuration;
        }

        // Fungsi untuk mendapatkan durasi jumat slide (dalam milidetik)
        function getJumatSlideDuration() {
            // Simpan hasil dalam variabel untuk menghindari pemanggilan berulang
            if (window.cachedJumatSlideDuration !== undefined) {
                return window.cachedJumatSlideDuration;
            }

            const durasiData = getDurasiData();

            // Default durasi jika data tidak tersedia (dalam milidetik)
            const defaultDuration = 10 * 60 * 1000; // 10 menit

            if (!durasiData || !durasiData.jumat_slide) {
                window.cachedJumatSlideDuration = defaultDuration;
                return defaultDuration;
            }

            window.cachedJumatSlideDuration = durasiData.jumat_slide * 60 * 1000; // Konversi menit ke milidetik
            return window.cachedJumatSlideDuration;
        }

        // Fungsi untuk mendapatkan durasi syuruq (dalam detik)
        function getSyuruqDuration() {
            const durasiData = getDurasiData();

            // Default durasi jika data tidak tersedia (dalam detik)
            const defaultDuration = 3 * 60; // 3 menit

            if (!durasiData || !durasiData.adzan_shuruq) {
                return defaultDuration;
            }

            return durasiData.adzan_shuruq * 60; // Konversi menit ke detik
        }

        // Fungsi untuk mendapatkan durasi dhuha (dalam detik)
        function getDhuhaDuration() {
            const durasiData = getDurasiData();

            // Default durasi jika data tidak tersedia (dalam detik)
            const defaultDuration = 1 * 60; // 3 menit

            if (!durasiData || !durasiData.adzan_dhuha) {
                return defaultDuration;
            }

            return durasiData.adzan_dhuha * 60; // Konversi menit ke detik
        }

        function showAdzanPopup(prayerName, prayerTimeStr, isRestored = false) {
            // Pastikan audio dijeda saat adzan dimulai
            pauseAudio();

            const now = getCurrentTimeFromServer();
            const currentDate = now.getDate();
            const serverMonth = now.getMonth() + 1;
            const serverYear = now.getFullYear();
            const scheduleMonth = parseInt($('#current-month').val());
            const scheduleYear = parseInt($('#current-year').val());

            if (scheduleMonth !== serverMonth || scheduleYear !== serverYear) {
                console.log(
                    'Jadwal tidak sesuai dengan tanggal server, memperbarui input hidden dan mengambil data baru...'
                );

                // Update input hidden dengan bulan dan tahun server saat ini
                $('#current-month').val(serverMonth);
                $('#current-year').val(serverYear);

                // Ambil data jadwal sholat baru
                fetchPrayerTimes().then(() => {
                    console.log('Data jadwal sholat berhasil diperbarui');
                }).catch(error => {
                    console.error('Error saat memperbarui jadwal sholat:', error);
                });

                return;
            }

            const $popup = $('#adzanPopup');
            const $title = $('#adzanTitle');
            const $progress = $('#adzanProgress');
            const $countdown = $('#adzanCountdown');
            const $label = $('#adzanLabel');

            // Set label untuk adzan biasa
            $label.text('Waktunya Adzan');
            $title.text(` ${prayerName}`);
            $popup.css('display', 'flex');

            // Inisialisasi audio adzan jika belum ada
            if (!window.adzanAudioPlayer) {
                window.adzanAudioPlayer = new Audio();

                // Tambahkan event handler untuk error
                window.adzanAudioPlayer.onerror = function(e) {
                    console.error('Error saat memutar audio adzan:', e);
                    // Fallback ke beep sound jika terjadi error
                    playBeepSound(1);
                };
            }

            // Putar beep sound terlebih dahulu
            if (!isRestored) {
                // Selalu putar beep sound terlebih dahulu
                playBeepSound(1);

                const prayerLower = prayerName.toLowerCase();
                const adzanStatus = $('#adzan_status').val() === 'true';

                // Setelah beep sound, putar audio adzan jika status aktif
                if (adzanStatus) {
                    // Hentikan audio adzan yang sedang diputar (jika ada)
                    if (window.adzanAudioPlayer) {
                        window.adzanAudioPlayer.pause();
                        window.adzanAudioPlayer.currentTime = 0;
                    }

                    // Tunggu beep sound selesai baru putar audio adzan (5 detik)
                    setTimeout(() => {
                        // Pilih audio adzan berdasarkan waktu sholat
                        if (prayerLower === 'subuh') {
                            // Gunakan adzan_shubuh untuk waktu shubuh
                            const adzanShubuhUrl = $('#adzan_shubuh').val();
                            if (adzanShubuhUrl && adzanShubuhUrl.trim() !== '') {
                                window.adzanAudioPlayer.src = adzanShubuhUrl;
                                window.adzanAudioPlayer.play();
                                console.log('Memutar audio adzan subuh');
                            }
                        } else if (prayerLower !== 'syuruq' && prayerLower !== 'shuruq' &&
                            prayerLower !== 'terbit' && prayerLower !== 'dhuha') {
                            // Gunakan adzan_audio untuk waktu dzuhur, ashar, maghrib, isya dan jumat
                            const adzanAudioUrl = $('#adzan_audio').val();
                            if (adzanAudioUrl && adzanAudioUrl.trim() !== '') {
                                window.adzanAudioPlayer.src = adzanAudioUrl;
                                window.adzanAudioPlayer.play();
                                console.log(`Memutar audio adzan untuk ${prayerName}`);
                            }
                        }
                        // Untuk waktu shuruq, hanya putar beep sound (sudah diputar di awal)
                    }, 3000); // Tunggu 3 detik setelah beep sound
                }

                $progress.css('width', '0%');
                // console.log(`Memutar alarm untuk ${prayerName} pada ${prayerTimeStr}`);
            } else if (activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                $progress.css('width', `${activePrayerStatus.progress}%`);
            }

            if (!adzanStartTime) {
                if (isRestored && activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                    const now = getCurrentTimeFromServer().getTime();
                    adzanStartTime = now - (activePrayerStatus.elapsedSeconds * 1000);
                } else {
                    adzanStartTime = calculateSyncStartTime(prayerTimeStr);
                }
                localStorage.setItem('adzanStartTime', adzanStartTime);
                localStorage.setItem('currentPrayerName', prayerName);
                localStorage.setItem('currentPrayerTime', prayerTimeStr);
                currentPrayerName = prayerName;
                currentPrayerTime = prayerTimeStr;
            }

            // Gunakan durasi dinamis berdasarkan nama sholat
            const duration = getAdzanDuration(prayerName); // dalam detik
            let lastCountdownUpdate = 0;
            isAdzanPlaying = true;
            let animationId;

            // Fungsi animasi dengan requestAnimationFrame
            function updateAdzanAnimation(timestamp) {
                if (!isAdzanPlaying) {
                    if (animationId) {
                        cancelAnimationFrame(animationId);
                    }
                    return;
                }

                const currentTime = getCurrentTimeFromServer().getTime();
                const elapsedSeconds = (currentTime - adzanStartTime) / 1000;
                const timeLeft = duration - elapsedSeconds;

                // Cek apakah adzan sudah selesai
                if (timeLeft <= 0) {
                    $popup.css('display', 'none');
                    isAdzanPlaying = false;
                    if (animationId) {
                        cancelAnimationFrame(animationId);
                    }

                    // Hentikan audio adzan jika sedang diputar
                    if (window.adzanAudioPlayer) {
                        window.adzanAudioPlayer.pause();
                        window.adzanAudioPlayer.currentTime = 0;
                    }

                    // Logika setelah adzan selesai
                    if (prayerName === "Jum'at" && now.getDay() === 5) {
                        // Hapus semua state adzan sebelum menampilkan Friday Info
                        clearAdzanState();
                        updateFridayImages();
                        // Tampilkan popup Friday info dengan data yang sesuai tanpa alarm tambahan
                        const options = {
                            weekday: 'long',
                            day: '2-digit',
                            month: '2-digit',
                            year: '2-digit'
                        };
                        const formattedDate = now.toLocaleDateString('id-ID', options);
                        const khatib = $('#khatib').val();
                        const imam = $('#imam').val();
                        const muadzin = $('#muadzin').val();
                        const fridayData = {
                            date: formattedDate,
                            khatib,
                            imam,
                            muadzin
                        };
                        displayFridayInfoPopup(fridayData); // Tidak memanggil playBeepSound
                    } else {
                        showIqomahPopup(prayerTimeStr);
                    }
                    return;
                }

                // Update progress bar (smooth animation setiap frame)
                $progress.css({
                    'animation': `progressAnimation ${duration}s linear forwards`
                });

                // Update countdown hanya setiap detik untuk efisiensi
                if (currentTime - lastCountdownUpdate >= 1000) {
                    const hours = Math.floor(timeLeft / 3600);
                    const minutes = Math.floor((timeLeft % 3600) / 60);
                    const seconds = Math.floor(timeLeft % 60);

                    $countdown.text(
                        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );

                    lastCountdownUpdate = currentTime;
                }

                // Lanjutkan animasi
                animationId = requestAnimationFrame(updateAdzanAnimation);
            }

            // Mulai animasi
            animationId = requestAnimationFrame(updateAdzanAnimation);

            // Return function untuk cleanup jika diperlukan
            return function stopAdzan() {
                isAdzanPlaying = false;
                if (animationId) {
                    cancelAnimationFrame(animationId);
                }
            };
        }

        function showSyuruqPopup(prayerName, prayerTimeStr, isRestored = false) {
            // Pastikan audio dijeda saat syuruq dimulai
            pauseAudio();

            const now = getCurrentTimeFromServer();
            const currentDate = now.getDate();
            const serverMonth = now.getMonth() + 1;
            const serverYear = now.getFullYear();
            const scheduleMonth = parseInt($('#current-month').val());
            const scheduleYear = parseInt($('#current-year').val());

            if (scheduleMonth !== serverMonth || scheduleYear !== serverYear) {
                console.log(
                    'Jadwal tidak sesuai dengan tanggal server, memperbarui input hidden dan mengambil data baru...'
                );

                // Update input hidden dengan bulan dan tahun server saat ini
                $('#current-month').val(serverMonth);
                $('#current-year').val(serverYear);

                // Ambil data jadwal sholat baru
                fetchPrayerTimes().then(() => {
                    console.log('Data jadwal sholat berhasil diperbarui');
                }).catch(error => {
                    console.error('Error saat memperbarui jadwal sholat:', error);
                });

                return;
            }

            const $popup = $('#adzanPopup');
            const $title = $('#adzanTitle');
            const $progress = $('#adzanProgress');
            const $countdown = $('#adzanCountdown');
            const $label = $('#adzanLabel');

            // Set label khusus untuk syuruq
            $label.text('waktu');
            $title.text(`${prayerName}`);
            $popup.css('display', 'flex');

            // Putar beep sound untuk syuruq (hanya beep, tidak ada audio adzan)
            if (!isRestored) {
                playBeepSound(1);
                $progress.css('width', '0%');
            } else if (activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                $progress.css('width', `${activePrayerStatus.progress}%`);
            }

            if (!adzanStartTime) {
                if (isRestored && activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                    const now = getCurrentTimeFromServer().getTime();
                    adzanStartTime = now - (activePrayerStatus.elapsedSeconds * 1000);
                } else {
                    adzanStartTime = calculateSyncStartTime(prayerTimeStr);
                }
                localStorage.setItem('adzanStartTime', adzanStartTime);
                localStorage.setItem('currentPrayerName', prayerName);
                localStorage.setItem('currentPrayerTime', prayerTimeStr);
                currentPrayerName = prayerName;
                currentPrayerTime = prayerTimeStr;
            }

            // Gunakan durasi dinamis untuk syuruq/dhuha
            const isDhuha = prayerName.toLowerCase().includes('dhuha');
            const duration = isDhuha ? getDhuhaDuration() : getSyuruqDuration(); // dalam detik
            let lastCountdownUpdate = 0;
            let hasPlayedFinalBeep = false;
            isAdzanPlaying = true;
            let animationId;

            // Fungsi animasi dengan requestAnimationFrame
            function updateSyuruqAnimation(timestamp) {
                if (!isAdzanPlaying) {
                    if (animationId) {
                        cancelAnimationFrame(animationId);
                    }
                    return;
                }

                const currentTime = getCurrentTimeFromServer().getTime();
                const elapsedSeconds = (currentTime - adzanStartTime) / 1000;
                const timeLeft = duration - elapsedSeconds;

                // Mainkan beep sound saat 5 detik terakhir
                if (timeLeft <= 5 && !hasPlayedFinalBeep) {
                    playBeepSound(1);
                    hasPlayedFinalBeep = true;
                }

                // Cek apakah syuruq sudah selesai
                if (timeLeft <= 0) {
                    $popup.css('display', 'none');
                    isAdzanPlaying = false;
                    if (animationId) {
                        cancelAnimationFrame(animationId);
                    }

                    // Syuruq langsung clear state setelah selesai (tidak ada iqomah)
                    clearAdzanState();
                    return;
                }

                // Update progress bar
                $progress.css({
                    'animation': `progressAnimation ${duration}s linear forwards`
                });

                // Update countdown hanya setiap detik untuk efisiensi
                if (currentTime - lastCountdownUpdate >= 1000) {
                    const hours = Math.floor(timeLeft / 3600);
                    const minutes = Math.floor((timeLeft % 3600) / 60);
                    const seconds = Math.floor(timeLeft % 60);

                    $countdown.text(
                        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );

                    lastCountdownUpdate = currentTime;
                }

                // Lanjutkan animasi
                animationId = requestAnimationFrame(updateSyuruqAnimation);
            }

            // Mulai animasi
            animationId = requestAnimationFrame(updateSyuruqAnimation);

            // Return function untuk cleanup jika diperlukan
            return function stopSyuruq() {
                isAdzanPlaying = false;
                if (animationId) {
                    cancelAnimationFrame(animationId);
                }
            };
        }

        function showDhuhaPopup(prayerName, prayerTimeStr, isRestored = false) {
            pauseAudio();
            const now = getCurrentTimeFromServer();
            const serverMonth = now.getMonth() + 1;
            const serverYear = now.getFullYear();
            const scheduleMonth = parseInt($('#current-month').val());
            const scheduleYear = parseInt($('#current-year').val());
            if (scheduleMonth !== serverMonth || scheduleYear !== serverYear) {
                $('#current-month').val(serverMonth);
                $('#current-year').val(serverYear);
                fetchPrayerTimes().catch(() => {});
                return;
            }
            const $popup = $('#adzanPopup');
            const $title = $('#adzanTitle');
            const $progress = $('#adzanProgress');
            const $countdown = $('#adzanCountdown');
            const $label = $('#adzanLabel');
            $label.text('waktu');
            $title.text(`${prayerName}`);
            $popup.css('display', 'flex');
            if (!isRestored) {
                playBeepSound(1);
                $progress.css('width', '0%');
            } else if (activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                $progress.css('width', `${activePrayerStatus.progress}%`);
            }
            if (!adzanStartTime) {
                if (isRestored && activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                    const nowMs = getCurrentTimeFromServer().getTime();
                    adzanStartTime = nowMs - (activePrayerStatus.elapsedSeconds * 1000);
                } else {
                    adzanStartTime = calculateSyncStartTime(prayerTimeStr);
                }
                localStorage.setItem('adzanStartTime', adzanStartTime);
                localStorage.setItem('currentPrayerName', prayerName);
                localStorage.setItem('currentPrayerTime', prayerTimeStr);
                currentPrayerName = prayerName;
                currentPrayerTime = prayerTimeStr;
            }
            const duration = getDhuhaDuration();
            let lastCountdownUpdate = 0;
            let hasPlayedFinalBeep = false;
            isAdzanPlaying = true;
            let animationId;
            function updateDhuhaAnimation(timestamp) {
                if (!isAdzanPlaying) {
                    if (animationId) {
                        cancelAnimationFrame(animationId);
                    }
                    return;
                }
                const currentTime = getCurrentTimeFromServer().getTime();
                const elapsedSeconds = (currentTime - adzanStartTime) / 1000;
                const timeLeft = duration - elapsedSeconds;
                if (timeLeft <= 5 && !hasPlayedFinalBeep) {
                    playBeepSound(1);
                    hasPlayedFinalBeep = true;
                }
                if (timeLeft <= 0) {
                    $popup.css('display', 'none');
                    isAdzanPlaying = false;
                    if (animationId) {
                        cancelAnimationFrame(animationId);
                    }
                    clearAdzanState();
                    return;
                }
                $progress.css({
                    'animation': `progressAnimation ${duration}s linear forwards`
                });
                if (currentTime - lastCountdownUpdate >= 1000) {
                    const minutes = Math.floor((timeLeft % 3600) / 60);
                    const seconds = Math.floor(timeLeft % 60);
                    $countdown.text(
                        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );
                    lastCountdownUpdate = currentTime;
                }
                animationId = requestAnimationFrame(updateDhuhaAnimation);
            }
            animationId = requestAnimationFrame(updateDhuhaAnimation);
            return function stopDhuha() {
                isAdzanPlaying = false;
                if (animationId) {
                    cancelAnimationFrame(animationId);
                }
            };
        }

        let iqomahImageSliderInterval = null;
        let iqomahSliderStartTime = localStorage.getItem('iqomahSliderStartTime') ? parseInt(localStorage
            .getItem('iqomahSliderStartTime')) : null;

        function updateIqomahImages() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                console.error('Tidak dapat menentukan slug dari URL');
                return;
            }

            $.ajax({
                url: `/api/adzan1/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: async function(response) {
                    if (response.success) {
                        $('#adzan1').val(response.data.adzan1 || '');
                        $('#adzan2').val(response.data.adzan2 || '');
                        $('#adzan3').val(response.data.adzan3 || '');
                        $('#adzan4').val(response.data.adzan4 || '');
                        $('#adzan5').val(response.data.adzan5 || '');
                        $('#adzan6').val(response.data.adzan6 || '');

                        const newIqomahImages = [];
                        for (let i = 1; i <= 6; i++) {
                            const adzanValue = $(`#adzan${i}`).val();
                            if (adzanValue) {
                                newIqomahImages.push(adzanValue);
                            }
                        }

                        if (JSON.stringify(newIqomahImages) !== JSON.stringify(window
                                .iqomahImages)) {
                            console.log('Gambar Iqomah berubah, memperbarui array:',
                                newIqomahImages);
                            window.iqomahImages = newIqomahImages;
                            // Preload gambar baru jika belum ada di cache
                            const urlsToPreload = newIqomahImages.filter(url => !window.imageCache[
                                url] || !window.imageCache[url].complete);
                            if (urlsToPreload.length > 0) {
                                await preloadImages(urlsToPreload);
                            }
                            // Bersihkan cache yang tidak digunakan
                            clearUnusedCache(window.iqomahImages);
                            // Restart slider jika popup aktif
                            if ($('#iqomahPopup').is(':visible')) {
                                startIqomahImageSlider();
                            }
                        }
                        console.log('Gambar Iqomah diperbarui, jumlah gambar:', window.iqomahImages
                            .length);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data adzan untuk Iqomah:', error, xhr
                        .responseText);
                }
            });
        }

        function startIqomahImageSlider() {
            window.iqomahImages = [];
            for (let i = 1; i <= 6; i++) {
                const adzanElement = $(`#adzan${i}`);
                if (adzanElement.val()) {
                    window.iqomahImages.push(adzanElement.val());
                }
            }

            const $iqomahImageElement = $('#currentIqomahImage');
            if (!$iqomahImageElement.length) {
                console.error('Elemen #currentIqomahImage tidak ditemukan di DOM');
                return;
            }

            if (window.iqomahImages.length === 0) {
                window.iqomahImages = [
                    '/images/other/doa-setelah-adzan-default.webp',
                    '/images/other/doa-masuk-masjid-default.webp',
                    '/images/other/non-silent-hp-default.webp'
                ];
                console.log('Menggunakan gambar default untuk slider Iqomah:', window.iqomahImages);
            }

            if (!iqomahSliderStartTime) {
                iqomahSliderStartTime = getCurrentTimeFromServer().getTime();
                localStorage.setItem('iqomahSliderStartTime', iqomahSliderStartTime);
            }

            async function initIqomahSlider() {
                try {
                    await preloadImages(window.iqomahImages);
                    console.log('Semua gambar Iqomah telah dimuat, memulai slider');

                    let lastIndex = -1;

                    function updateIqomahImage() {
                        if (!window.iqomahImages || window.iqomahImages.length === 0) {
                            window.iqomahImages = [
                                '/images/other/doa-setelah-adzan-default.webp',
                                '/images/other/doa-masuk-masjid-default.webp',
                                '/images/other/non-silent-hp-default.webp'
                            ];
                            console.warn('Array iqomahImages kosong, menggunakan gambar default:', window
                                .iqomahImages);
                        }

                        const now = getCurrentTimeFromServer().getTime();
                        const elapsedMs = now - iqomahSliderStartTime;
                        const elapsedSeconds = Math.floor(elapsedMs / 1000);
                        const currentIndex = Math.floor(elapsedSeconds / 20) % window.iqomahImages.length;

                        if (currentIndex !== lastIndex) {
                            lastIndex = currentIndex;

                            const currentUrl = window.imageCache[window.iqomahImages[currentIndex]]?.src ||
                                '/images/other/doa-masuk-masjid-default.webp';

                            $iqomahImageElement.css({
                                'background-image': `url("${currentUrl}")`,
                                'transition': 'background-image 0.5s ease-in-out'
                            });
                            console.log('Gambar Iqomah diperbarui ke:', currentUrl);

                            clearUnusedCache(window.iqomahImages);
                        }
                    }

                    updateIqomahImage();
                    if (iqomahImageSliderInterval) {
                        clearInterval(iqomahImageSliderInterval);
                        console.log('Interval slider iqomah sebelumnya dihentikan');
                    }
                    iqomahImageSliderInterval = setInterval(updateIqomahImage, 1000);
                } catch (error) {
                    console.error('Error saat preload gambar Iqomah:', error);
                    window.iqomahImages = ['/images/other/doa-masuk-masjid-default.webp'];
                    updateIqomahImage();
                    if (iqomahImageSliderInterval) {
                        clearInterval(iqomahImageSliderInterval);
                    }
                    iqomahImageSliderInterval = setInterval(updateIqomahImage, 1000);
                }
            }

            initIqomahSlider();
        }

        function showIqomahPopup(prayerTimeStr, isRestored = false) {
            // Pastikan audio tetap dijeda saat iqomah dimulai
            pauseAudio();

            const now = getCurrentTimeFromServer();
            if (now.getDay() === 5 && currentPrayerName === "Jum'at") {
                console.log('Tidak menampilkan iqomah untuk sholat Jumat');
                return;
            }

            const $popup = $('#iqomahPopup');
            const $progress = $('#iqomahProgress');
            const $countdown = $('#iqomahCountdown');

            $popup.css('display', 'flex');

            if (!isRestored) {
                $progress.css('width', '0%');
            } else if (activePrayerStatus && activePrayerStatus.phase === 'iqomah') {
                $progress.css('width', `${activePrayerStatus.progress}%`);
            }

            startIqomahImageSlider();

            if (!iqomahStartTime) {
                if (isRestored && activePrayerStatus && activePrayerStatus.phase === 'iqomah') {
                    const now = getCurrentTimeFromServer().getTime();
                    adzanStartTime = now - ((activePrayerStatus.elapsedSeconds + 180) * 1000);
                    iqomahStartTime = adzanStartTime + (180 * 1000);
                    localStorage.setItem('adzanStartTime', adzanStartTime);
                } else {
                    if (!adzanStartTime) {
                        adzanStartTime = calculateSyncStartTime(prayerTimeStr);
                        localStorage.setItem('adzanStartTime', adzanStartTime);
                    }
                    iqomahStartTime = adzanStartTime + (getAdzanDuration(currentPrayerName) * 1000);
                }
                localStorage.setItem('iqomahStartTime', iqomahStartTime);
            }

            // Ekstrak nama sholat dari currentPrayerName
            const prayerName = currentPrayerName || 'Dzuhur';

            // Gunakan durasi dinamis berdasarkan nama sholat
            const duration = getIqomahDuration(prayerName); // dalam detik
            let lastCountdownUpdate = 0;
            let isIqomahPlaying = true;
            let hasPlayedFinalBeep = false;
            let animationId;

            // Fungsi animasi dengan requestAnimationFrame
            function updateIqomahAnimation(timestamp) {
                if (!isIqomahPlaying) {
                    cancelAnimationFrame(animationId);
                    return;
                }

                const currentTime = getCurrentTimeFromServer().getTime();
                const elapsedSeconds = (currentTime - iqomahStartTime) / 1000;
                const timeLeft = duration - elapsedSeconds;

                // Mainkan beep sound saat 5 detik terakhir
                if (timeLeft <= 5 && !hasPlayedFinalBeep) {
                    playBeepSound(1);
                    hasPlayedFinalBeep = true;
                }

                // Cek apakah iqomah sudah selesai
                if (timeLeft <= 0) {
                    $popup.css('display', 'none');
                    isIqomahPlaying = false;
                    cancelAnimationFrame(animationId);
                    clearAdzanState();
                    showFinalAdzanImage();
                    return;
                }

                // Update progress bar iqomah pop up (smooth animation setiap frame)
                $progress.css({
                    'animation': `progressAnimation ${duration}s linear forwards`
                });

                // Update countdown hanya setiap detik untuk efisiensi
                if (currentTime - lastCountdownUpdate >= 1000) {
                    const hours = Math.floor(timeLeft / 3600);
                    const minutes = Math.floor((timeLeft % 3600) / 60);
                    const seconds = Math.floor(timeLeft % 60);

                    $countdown.text(
                        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );

                    lastCountdownUpdate = currentTime;
                }

                // Lanjutkan animasi
                animationId = requestAnimationFrame(updateIqomahAnimation);
            }

            // Mulai animasi
            animationId = requestAnimationFrame(updateIqomahAnimation);

            // Return function untuk membatalkan iqomah
            return function cancelIqomah() {
                isIqomahPlaying = false;
                $popup.css('display', 'none');
                if (animationId) {
                    cancelAnimationFrame(animationId);
                }
            };
        }

        let adzanImageStartTime = localStorage.getItem('adzanImageStartTime') ? parseInt(localStorage.getItem(
            'adzanImageStartTime')) : null;
        let adzanImageEndTime = localStorage.getItem('adzanImageEndTime') ? parseInt(localStorage.getItem(
            'adzanImageEndTime')) : null;
        let adzanImageSrc = localStorage.getItem('adzanImageSrc') || null;

        function checkAndRestoreAdzanImage() {
            const now = getCurrentTimeFromServer().getTime();
            if (adzanImageStartTime && adzanImageEndTime && now >= adzanImageStartTime && now <
                adzanImageEndTime && adzanImageSrc) {
                if ($('#adzanImageDisplay').is(':visible')) {
                    console.log('Gambar adzan sudah ditampilkan, tidak perlu dipulihkan');
                    return;
                }
                console.log('Restoring Adzan final image');
                displayAdzanImage(adzanImageSrc, true);
            } else {
                clearAdzanImageState();
            }
        }

        function clearAdzanImageState() {
            adzanImageStartTime = null;
            adzanImageEndTime = null;
            adzanImageSrc = null;
            localStorage.removeItem('adzanImageStartTime');
            localStorage.removeItem('adzanImageEndTime');
            localStorage.removeItem('adzanImageSrc');

            // Catatan: Tidak perlu memanggil resumeAudio() di sini karena akan dipanggil
            // di showFinalAdzanImage() setelah adzanImageDisplay ditutup
        }

        function updateAdzanImages() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                return;
            }
            $.ajax({
                url: `/api/adzan1/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const oldAdzan15 = $('#adzan15').val();
                        const newAdzan15 = response.data.adzan15 || '';
                        $('#adzan15').val(newAdzan15);

                        if ($('#adzanImageDisplay').is(':visible')) {
                            // Jika gambar default sedang ditampilkan dan respons kosong, jangan ubah gambar
                            if (!newAdzan15 && adzanImageSrc ===
                                '/images/other/lurus-rapat-shaf-default.webp') {
                                console.log(
                                    'Gambar default tetap digunakan karena respons adzan15 kosong'
                                );
                                return;
                            }
                            // Jika ada perubahan atau beralih ke gambar database, perbarui gambar
                            if (oldAdzan15 !== newAdzan15 || (newAdzan15 && adzanImageSrc !==
                                    newAdzan15)) {
                                const $imageElement = $('#currentAdzanImage');
                                $imageElement.css('opacity', '0');
                                setTimeout(() => {
                                    const srcToUse = newAdzan15 ||
                                        '/images/other/lurus-rapat-shaf-default.webp';
                                    $imageElement.attr('src', srcToUse);
                                    $imageElement.css('opacity', '1');
                                    adzanImageSrc = srcToUse;
                                    localStorage.setItem('adzanImageSrc', adzanImageSrc);
                                    console.log('Gambar Adzan15 diperbarui ke:', srcToUse);
                                }, 250);
                            }
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data adzan untuk Adzan Images:', error, xhr
                        .responseText);
                }
            });
        }

        function displayAdzanImage(imageSrc, isRestored = false, duration = 60000) {
            // Pastikan audio tetap dijeda saat adzanImageDisplay ditampilkan
            pauseAudio();

            const $imageDisplay = $('#adzanImageDisplay');
            const $imageElement = $('#currentAdzanImage');

            if (!$imageDisplay.length || !$imageElement.length) {
                console.error('Elemen #adzanImageDisplay atau #currentAdzanImage tidak ditemukan');
                return;
            }

            if (!isRestored) {
                clearAdzanImageState();
            }

            $imageElement.attr('src', imageSrc);
            $imageDisplay.css('display', 'flex');

            if (!isRestored) {
                const now = getCurrentTimeFromServer().getTime();
                adzanImageStartTime = now;
                adzanImageEndTime = now + duration;
                adzanImageSrc = imageSrc;

                localStorage.setItem('adzanImageStartTime', adzanImageStartTime);
                localStorage.setItem('adzanImageEndTime', adzanImageEndTime);
                localStorage.setItem('adzanImageSrc', imageSrc);
            }
        }

        function showFinalAdzanImage() {
            if (currentPrayerName === "Jum'at" && getCurrentTimeFromServer().getDay() === 5) {
                console.log('Tidak menampilkan final adzan image untuk adzan Jum\'at');
                // Untuk sholat Jumat, audio akan dilanjutkan setelah fridayInfoPopup berakhir
                return;
            }

            if (adzanImageStartTime && adzanImageEndTime) {
                const currentTime = getCurrentTimeFromServer().getTime();
                if (currentTime < adzanImageEndTime) {
                    return;
                }
            }

            const $adzan15 = $('#adzan15');
            let imageUrl;

            if ($adzan15.length && $adzan15.val()) {
                imageUrl = $adzan15.val();
            } else {
                console.warn(
                    'Elemen #adzan15 tidak ditemukan atau nilainya kosong, menggunakan gambar default');
                imageUrl = '/images/other/lurus-rapat-shaf-default.webp';
            }

            const durationMs = getFinalDuration(currentPrayerName || 'Dzuhur') * 1000; // convert seconds -> ms

            displayAdzanImage(imageUrl, false, durationMs);

            adzanImageTimeout = setTimeout(() => {
                const $imageDisplay = $('#adzanImageDisplay');
                $imageDisplay.css('display', 'none');
                clearAdzanImageState();
                // console.log('Final adzan image ditutup setelah', durationMs / 1000, 'detik');

                // Lanjutkan pemutaran audio setelah fase adzanImageDisplay berakhir
                resumeAudio();
            }, durationMs);
        }

        // Fungsi helper untuk menutup gambar adzan
        function hideAdzanImage() {
            // Sembunyikan overlay gambar adzan
            const adzanOverlay = document.getElementById('adzan-overlay') || document.querySelector(
                '.adzan-overlay');
            if (adzanOverlay) {
                adzanOverlay.style.display = 'none';
                adzanOverlay.remove();
            }

            // Reset variabel waktu
            adzanImageStartTime = null;
            adzanImageEndTime = null;

            // Jika menggunakan jQuery untuk overlay
            $('#adzan-overlay, .adzan-overlay').fadeOut(500, function() {
                $(this).remove();
            });
        }


        let fridayInfoStartTime = localStorage.getItem('fridayInfoStartTime') ? parseInt(localStorage.getItem(
            'fridayInfoStartTime')) : null;
        let fridayInfoEndTime = localStorage.getItem('fridayInfoEndTime') ? parseInt(localStorage.getItem(
            'fridayInfoEndTime')) : null;
        let fridayInfoData = localStorage.getItem('fridayInfoData') ? JSON.parse(localStorage.getItem(
            'fridayInfoData')) : null;
        let fridayImageSliderInterval = null;

        // ===================== Finance Overlay (summary + auto-scroll) =====================
        let financeScrollRaf = null;
        let financeScrollStartTs = null;
        let financeActiveSection = 'top'; // 'top' or 'latest'

        function initFinanceOverlay() {
            fetchFinanceData();
            // Tidak lagi alternating; kita pakai satu scroll untuk seluruh konten
        }

        function fetchFinanceData() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                console.error('Tidak dapat menentukan slug dari URL');
                showFinanceError('Data tidak tersedia');
                return;
            }
            $.ajax({
                url: `/api/balance-summary-7hari/${encodeURIComponent(slug)}?details=full&hide_empty=true`,
                method: 'GET',
                dataType: 'json',
                success: function(resp) {
                    if (resp && resp.success && resp.data) {
                        renderFinanceOverlay(resp.data);
                    } else {
                        showFinanceError('Data tidak tersedia');
                    }
                },
                error: function() {
                    showFinanceError('Gagal memuat data keuangan');
                }
            });
        }

        function showFinanceError(message) {
            // Ringan: jangan ubah UI saat error/patah koneksi.
            // Tidak mengosongkan nilai, tidak menampilkan pesan status.
            // Intentionally no-op to preserve last successful snapshot.
        }

        function truncateText(text, maxLen) {
            if (!text) return '';
            return text.length > maxLen ? (text.substring(0, maxLen - 1) + '…') : text;
        }

        function renderFinanceOverlay(data) {
            try {
                // Judul menampilkan periode 7 hari (start s/d end)
                if (data.period && data.period.type === 'last_7_days' && data.period.start && data.period.end) {
                    const title = `${data.period.start} s/d ${data.period.end}`;
                    $('#financePeriodTitle').text(title);
                } else {
                    $('#financePeriodTitle').text('Keuangan');
                }

                // Totals
                if (data.grandTotals) {
                    const masukDisplay = data.grandTotals.cumulativeMasukDisplay || data.grandTotals
                        .sumMasukDisplay || '-';
                    const keluarDisplay = data.grandTotals.cumulativeKeluarDisplay || data.grandTotals
                        .sumKeluarDisplay || '-';
                    const endingDisplay = data.grandTotals.totalSaldoDisplay || data.grandTotals
                        .endingDisplay || '-';
                    $('#financeTotalMasukValue').text(masukDisplay);
                    $('#financeTotalKeluarValue').text(keluarDisplay);
                    $('#financeEndingBalanceValue').text(endingDisplay);
                }

                // Top kategori (ambil 3 terbesar berdasarkan ending)
                let categories = Array.isArray(data.categories) ? data.categories.slice() : [];
                categories.sort((a, b) => (b.ending || 0) - (a.ending || 0));

                // Frontend guard: sembunyikan kategori kosong (items kosong & semua total 0)
                // Menggunakan totals numerik dari backend bila tersedia
                if (Array.isArray(data.categoriesWithItems)) {
                    const byName = new Map();
                    data.categoriesWithItems.forEach(b => {
                        byName.set((b.categoryName || ''), b);
                    });
                    categories = categories.filter(cat => {
                        const block = byName.get(cat.categoryName || '');
                        if (!block) return true; // jika tidak ada blok, jangan sembunyikan di sini
                        const totals = block.totals || {};
                        const allZero =
                            (totals.previousBalance || 0) === 0 &&
                            (totals.totalMasuk || 0) === 0 &&
                            (totals.totalKeluar || 0) === 0 &&
                            (totals.endingBalance || 0) === 0 &&
                            (totals.totalSaldo || 0) === 0;
                        const hasItems = Array.isArray(block.items) && block.items.length > 0;
                        return !(allZero && !hasItems);
                    });
                }
                const $topList = $('#financeTopCategoriesList');
                $topList.empty();
                categories.forEach(cat => {
                    // Ambil aktivitas dari backend (dibatasi recent_limit=3)
                    let itemsHtml = '';
                    let prevDisplay = null;
                    let totalSaldoDisplay = null;
                    if (Array.isArray(data.categoriesWithItems)) {
                        const block = data.categoriesWithItems.find(b => (b.categoryName || '') === (cat
                            .categoryName || ''));
                        const itemsForCat = Array.isArray(block && block.items) ? block.items : [];
                        // Tampilkan sesuai urutan dari backend (tanggal desc, id desc)
                        itemsHtml = itemsForCat.map(it => {
                            const nilaiMasuk = it.masukDisplay && it.masukDisplay !== '-';
                            const nilaiKeluar = it.keluarDisplay && it.keluarDisplay !== '-';
                            const nilaiClass = nilaiMasuk ? 'masuk' : (nilaiKeluar ? 'keluar' :
                                'netral');
                            const nilai = nilaiMasuk ? it.masukDisplay : (nilaiKeluar ? it
                                .keluarDisplay : '-');
                            const tanggal = it.tanggal || '-';
                            const uraian = truncateText(it.uraian || '-', 28);
                            return `<div class="activity-pill ${nilaiClass}">
                                <span class="activity-line tanggal">${tanggal}</span>
                                <span class="activity-line uraian">${uraian}</span>
                                <span class="activity-line nominal ${nilaiClass}">${nilai}</span>
                            </div>`;
                        }).join('');

                        // Ambil totals tambahan: saldo sebelumnya & total saldo
                        if (block && block.totals) {
                            prevDisplay = block.totals.previousBalanceDisplay || null;
                            totalSaldoDisplay = block.totals.totalSaldoDisplay || null;
                        }
                    }
                    const row = `<div class="finance-row">
                        <div class="finance-chip kategori">
                            <div class="finance-chip-title">${cat.categoryName || '-'}</div>
                            <div class="finance-pill-container">
                                <div class="finance-pill masuk">
                                    <span class="label">Masuk</span>
                                    <span class="value">${cat.sumMasukDisplay || '-'}</span>
                                </div>
                                <div class="finance-pill keluar">
                                    <span class="label">Keluar</span>
                                    <span class="value">${cat.sumKeluarDisplay || '-'}</span>
                                </div>
                                <div class="finance-pill saldo">
                                    <span class="label">Total</span>
                                    <span class="value">${cat.endingDisplay || '-'}</span>
                                </div>
                                ${prevDisplay ? `
                                <div class="finance-pill saldobefore">
                                    <span class="label">Saldo Sebelumnya</span>
                                    <span class="value">${prevDisplay}</span>
                                </div>` : ''}
                                ${totalSaldoDisplay ? `
                                <div class="finance-pill totalsaldo">
                                    <span class="label">Saldo</span>
                                    <span class="value">${totalSaldoDisplay}</span>
                                </div>` : ''}
                            </div>
                            ${itemsHtml ? `<div class="activity-pill-container">${itemsHtml}</div>` : ''}
                        </div>
                    </div>`;
                    $topList.append(row);
                });

                // Tampilkan overlay
                $('#financeOverlay').show();
                // Mulai scroll pada konten gabungan (totals + kategori + aktivitas)
                const $containerAll = $('#financeScrollContainerAll');
                const $contentAll = $('#financeScrollContentAll');
                startVerticalScroll($containerAll, $contentAll);

            } catch (e) {
                showFinanceError('Kesalahan memproses data keuangan');
            }
        }

        // Auto-scroll util: loop turun tanpa henti, wrap mulus di atas
        function startVerticalScroll($container, $content) {
            stopVerticalScroll();
            if (!$container || !$content || $content.children().length === 0) return;

            const container = $container[0];
            const content = $content[0];

            // Bersihkan clone loop sebelumnya agar tidak menumpuk
            const prevClones = container.querySelectorAll('[data-loop-clone="true"]');
            prevClones.forEach(n => n.remove());
            // Bersihkan spacer sebelumnya bila ada
            const prevSpacers = container.querySelectorAll('[data-loop-spacer="true"]');
            prevSpacers.forEach(n => n.remove());

            // Clone satu kali untuk membuat efek seamless saat wrap
            const baseHeight = content.scrollHeight;
            if (baseHeight <= container.clientHeight) {
                // Tidak perlu scroll jika konten tidak melebihi kontainer
                return;
            }
            // Spacer sebagai margin antar putaran (sedikit lebih kecil)
            const gapPx = Math.max(16, Math.round(container.clientHeight * 0.03));
            const spacer = document.createElement('div');
            spacer.setAttribute('data-loop-spacer', 'true');
            spacer.style.height = gapPx + 'px';
            spacer.style.minHeight = gapPx + 'px';
            spacer.style.width = '100%';
            spacer.style.flex = '0 0 ' + gapPx + 'px';
            spacer.style.pointerEvents = 'none';
            spacer.style.background = 'transparent';

            const clone = content.cloneNode(true);
            if (clone.id) clone.id = '';
            clone.setAttribute('data-loop-clone', 'true');
            // Susun: konten asli -> spacer -> clone
            container.appendChild(spacer);
            container.appendChild(clone);

            let offset = container.scrollTop || 0;
            const vwPerSec = 2; // kecepatan dalam vw per detik (konsisten lintas layar)
            let speedPxPerSec = (vwPerSec / 100) * window.innerWidth;

            // Update kecepatan saat ukuran layar berubah
            const onResize = () => {
                speedPxPerSec = (vwPerSec / 100) * window.innerWidth;
            };
            window.addEventListener('resize', onResize);

            let lastTs = null;
            const loopLength = baseHeight + gapPx; // panjang satu putaran termasuk margin

            function loop(ts) {
                if (lastTs === null) lastTs = ts;
                const dt = Math.min(0.033, (ts - lastTs) / 1000);
                lastTs = ts;

                // Jika karena perubahan layout tinggi dasar jadi tidak valid, hentikan
                if (baseHeight <= container.clientHeight) {
                    stopVerticalScroll();
                    return;
                }

                // Gerak turun terus
                offset += speedPxPerSec * dt;

                // Wrap mulus saat melewati panjang putaran (konten + spacer)
                if (offset >= loopLength) {
                    offset -= loopLength;
                }

                container.scrollTop = offset;
                financeScrollRaf = requestAnimationFrame(loop);
            }
            financeScrollRaf = requestAnimationFrame(loop);
        }

        function stopVerticalScroll() {
            if (financeScrollRaf) {
                cancelAnimationFrame(financeScrollRaf);
                financeScrollRaf = null;
                financeScrollStartTs = null;
            }
        }

        function startFinanceAlternating() {
            // mulai dengan Top Kategori
            activateFinanceSection('top');
            setInterval(() => {
                financeActiveSection = (financeActiveSection === 'top') ? 'latest' : 'top';
                activateFinanceSection(financeActiveSection);
            }, 20000); // perpanjang agar lebih santai
        }

        function activateFinanceSection(section) {
            if (section === 'latest') {
                $('#financeTopCategoriesSection').hide();
                $('#financeLatestItemsSection').show();
                startVerticalScroll($('#financeLatestItemsSection .finance-scroll-container'), $(
                    '#financeLatestItemsList'));
            } else {
                $('#financeLatestItemsSection').hide();
                $('#financeTopCategoriesSection').show();
                startVerticalScroll($('#financeTopCategoriesSection .finance-scroll-container'), $(
                    '#financeTopCategoriesList'));
            }
        }

        // Inisialisasi saat dokumen siap
        $(function() {
            initFinanceOverlay();
        });

        let fridaySliderStartTime = localStorage.getItem('fridaySliderStartTime') ? parseInt(localStorage
            .getItem('fridaySliderStartTime')) : null;

        function checkAndRestoreFridayInfo() {
            const now = getCurrentTimeFromServer().getTime();
            if (fridayInfoStartTime && fridayInfoEndTime && now >= fridayInfoStartTime && now <
                fridayInfoEndTime) {
                // console.log('Restoring Friday info popup');
                displayFridayInfoPopup(fridayInfoData, true);
            } else if (fridayInfoEndTime && now >= fridayInfoEndTime) {
                clearFridayInfoState();
            }
        }

        function clearFridayInfoState() {
            fridayInfoStartTime = null;
            fridayInfoEndTime = null;
            fridayInfoData = null;
            fridaySliderStartTime = null;
            window.cachedJumatSlideDuration = undefined;
            localStorage.removeItem('fridayInfoStartTime');
            localStorage.removeItem('fridayInfoEndTime');
            localStorage.removeItem('fridayInfoData');
            localStorage.removeItem('fridaySliderStartTime');

            // Bersihkan semua flag fridayAdzanCleared
            Object.keys(localStorage).forEach(key => {
                if (key.startsWith('fridayAdzanCleared_')) {
                    localStorage.removeItem(key);
                }
            });

            if (fridayImageSliderInterval) {
                clearInterval(fridayImageSliderInterval);
                fridayImageSliderInterval = null;
            }

            // Lanjutkan pemutaran audio setelah fase fridayInfoPopup berakhir
            resumeAudio();
        }

        function updateFridayImages() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                console.error('Tidak dapat menentukan slug dari URL');
                return;
            }

            $.ajax({
                url: `/api/adzan1/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: async function(response) {
                    if (response.success && response.data) {
                        // Bangun daftar gambar langsung dari respons API
                        const keys = ['adzan7', 'adzan8', 'adzan9', 'adzan10', 'adzan11',
                            'adzan12'
                        ];
                        const imagesFromApi = [];
                        keys.forEach((key) => {
                            const val = response.data[key];
                            if (val) imagesFromApi.push(val);
                        });

                        const prevImages = Array.isArray(window.fridayImages) ? window.fridayImages
                            .slice() : [];
                        window.fridayImages = imagesFromApi.length > 0 ? imagesFromApi : [
                            '/images/other/doa-setelah-adzan-default.webp',
                            '/images/other/doa-masuk-masjid-default.webp',
                            '/images/other/dilarang-bicara-saat-sholat-jumat-default.webp',
                            '/images/other/non-silent-hp-default.webp'
                        ];
                        if (imagesFromApi.length === 0) {
                            console.log('Menggunakan gambar default karena respons API kosong:',
                                window.fridayImages);
                        }

                        // Preload semua gambar Friday yang baru
                        await preloadImages(window.fridayImages);
                        clearUnusedCache(window.fridayImages);

                        // Jika daftar gambar berubah, restart slider agar memakai gambar terbaru
                        const changed = prevImages.length !== window.fridayImages.length ||
                            prevImages.some((v, i) => v !== window.fridayImages[i]);
                        if (changed) {
                            if (fridayImageSliderInterval) {
                                clearInterval(fridayImageSliderInterval);
                                fridayImageSliderInterval = null;
                            }
                            startFridayImageSlider();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data adzan:', error, xhr.responseText);
                }
            });
        }

        function startFridayImageSlider() {
            let images = Array.isArray(window.fridayImages) ? window.fridayImages.slice() : [];

            const $fridayImageElement = $('#currentFridayImage');
            if (!$fridayImageElement.length) {
                console.error('Elemen #currentFridayImage tidak ditemukan di DOM');
                return;
            }

            if (images.length === 0) {
                images = [
                    '/images/other/doa-setelah-adzan-default.webp',
                    '/images/other/doa-masuk-masjid-default.webp',
                    '/images/other/dilarang-bicara-saat-sholat-jumat-default.webp',
                    '/images/other/non-silent-hp-default.webp'
                ];
                console.log('Menggunakan 4 gambar default untuk slider Friday:', images);
            }

            if (!fridaySliderStartTime) {
                fridaySliderStartTime = getCurrentTimeFromServer().getTime();
                localStorage.setItem('fridaySliderStartTime', fridaySliderStartTime);
            }

            async function initFridaySlider() {
                try {
                    await preloadImages(images);
                    console.log('Semua gambar Friday telah dimuat, memulai slider');

                    let lastIndex = -1;

                    function updateFridayImage() {
                        if (!images || images.length === 0) {
                            images = [
                                '/images/other/doa-setelah-adzan-default.webp',
                                '/images/other/doa-masuk-masjid-default.webp',
                                '/images/other/dilarang-bicara-saat-sholat-jumat-default.webp',
                                '/images/other/non-silent-hp-default.webp'
                            ];
                            console.log('Menggunakan 4 gambar default dalam updateFridayImage:', images);
                        }

                        const now = getCurrentTimeFromServer().getTime();
                        const elapsedMs = now - fridaySliderStartTime;
                        const elapsedSeconds = Math.floor(elapsedMs / 1000);
                        const currentIndex = Math.floor(elapsedSeconds / 20) % images.length;

                        if (currentIndex !== lastIndex) {
                            lastIndex = currentIndex;

                            const currentUrl = window.imageCache[images[currentIndex]]?.src ||
                                '/images/other/doa-masuk-masjid-default.webp';

                            $fridayImageElement.css({
                                'background-image': `url("${currentUrl}")`,
                                'transition': 'background-image 0.5s ease-in-out'
                            });
                            console.log('Gambar Friday diperbarui ke:', currentUrl);

                            clearUnusedCache(images);
                        }
                    }

                    updateFridayImage();
                    if (fridayImageSliderInterval) {
                        clearInterval(fridayImageSliderInterval);
                    }
                    fridayImageSliderInterval = setInterval(updateFridayImage, 1000);

                    const displayDuration = getJumatSlideDuration();
                    setTimeout(() => {
                        const $fridayPopup = $('#fridayInfoPopup');
                        if ($fridayPopup.length) {
                            $fridayPopup.css('display', 'none');
                        }
                        clearFridayInfoState();
                        if (fridayImageSliderInterval) {
                            clearInterval(fridayImageSliderInterval);
                            fridayImageSliderInterval = null;
                        }
                    }, displayDuration);
                } catch (error) {
                    console.error('Error saat preload gambar Friday:', error);
                    window.fridayImages = ['/images/other/doa-masuk-masjid-default.webp'];
                    updateFridayImage();
                    if (fridayImageSliderInterval) {
                        clearInterval(fridayImageSliderInterval);
                    }
                    fridayImageSliderInterval = setInterval(updateFridayImage, 1000);
                }
            }

            initFridaySlider();
        }

        function displayFridayInfoPopup(data, isRestored = false) {
            // Pastikan audio tetap dijeda saat fridayInfoPopup ditampilkan
            pauseAudio();

            const $popup = $('#fridayInfoPopup');
            if ($popup.css('display') === 'flex') {
                return; // Jangan tampilkan lagi jika sudah ditampilkan
            }

            updateFridayInfoContent();
            $popup.css('display', 'flex');

            // Pastikan jam digital ada dan diperbarui
            const $clockTime = $popup.find('.clock-time');
            if ($clockTime.length) {
                const now = getCurrentTimeFromServer();
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const seconds = now.getSeconds().toString().padStart(2, '0');
                $clockTime.text(`${hours}:${minutes}:${seconds}`);
            } else {
                console.warn('Elemen .clock-time tidak ditemukan di fridayInfoPopup');
            }

            if (!isRestored) {
                updateFridayImages();
                const now = getCurrentTimeFromServer().getTime();
                fridayInfoStartTime = now;
                const duration = getJumatSlideDuration();
                fridayInfoEndTime = now + duration;

                localStorage.setItem('fridayInfoStartTime', fridayInfoStartTime);
                localStorage.setItem('fridayInfoEndTime', fridayInfoEndTime);
                localStorage.setItem('fridayInfoData', JSON.stringify(data));

                startFridayImageSlider();
            }

            const remainingTime = fridayInfoEndTime - getCurrentTimeFromServer().getTime();
            if (remainingTime > 0) {
                setTimeout(() => {
                    $popup.css('display', 'none');
                    clearFridayInfoState();
                }, remainingTime);
            } else {
                $popup.css('display', 'none');
                clearFridayInfoState();
            }
        }

        let jumatAdzanShown = localStorage.getItem('jumatAdzanShown') === 'true';

        function showFridayInfo() {
            const now = getCurrentTimeFromServer();
            const dayOfWeek = now.getDay();

            if (fridayInfoStartTime && fridayInfoEndTime) {
                const currentTime = now.getTime();
                if (currentTime < fridayInfoEndTime) {
                    return;
                }
            }

            if (dayOfWeek === 5) {
                const $prayerTimes = $('.prayer-time');
                const $jumatTime = $prayerTimes.filter(function() {
                    return $(this).find('.prayer-name').text().includes('Jum\'at');
                });

                if ($jumatTime.length) {
                    const jumatTimeValue = $jumatTime.find('.prayer-time-value').text();
                    const [hours, minutes] = jumatTimeValue.split(':').map(Number);
                    const jumatTimeInMinutes = hours * 60 + minutes;
                    const currentTimeInMinutes = now.getHours() * 60 + now.getMinutes();
                    const currentTimeFormatted =
                        `${now.getHours().toString().padStart(2, "0")}:${now.getMinutes().toString().padStart(2, "0")}`;

                    if (jumatTimeValue === currentTimeFormatted && !isAdzanPlaying && !jumatAdzanShown) {
                        updateFridayImages(); // Pastikan data gambar terbaru
                        showAdzanPopup('Jum\'at', jumatTimeValue);
                        jumatAdzanShown = true;
                        localStorage.setItem('jumatAdzanShown', 'true');
                        return;
                    }

                    if (currentTimeInMinutes >= jumatTimeInMinutes && currentTimeInMinutes <=
                        jumatTimeInMinutes + 10 && !isAdzanPlaying) {
                        const options = {
                            weekday: 'long',
                            day: '2-digit',
                            month: '2-digit',
                            year: '2-digit'
                        };
                        const formattedDate = now.toLocaleDateString('id-ID', options);
                        const khatib = $('#khatib').val();
                        const imam = $('#imam').val();
                        const muadzin = $('#muadzin').val();
                        const fridayData = {
                            date: formattedDate,
                            khatib,
                            imam,
                            muadzin
                        };
                        displayFridayInfoPopup(fridayData);
                    }
                }
            } else {
                jumatAdzanShown = false;
                localStorage.removeItem('jumatAdzanShown');
            }
        }

        function handlePrayerTimes() {
            const now = getCurrentTimeFromServer();
            const currentTimeInMinutes = now.getHours() * 60 + now.getMinutes();
            const currentTimeFormatted =
                `${now.getHours().toString().padStart(2, "0")}:${now.getMinutes().toString().padStart(2, "0")}`;
            const isFriday = now.getDay() === 5;

            const $prayerTimesElements = $('.prayer-time');
            if (!$prayerTimesElements.length) {
                scheduleNextPrayerCheck();
                return;
            }

            const prayerTimes = [];
            $prayerTimesElements.each(function(index) {
                const $nameElement = $(this).find('.prayer-name');
                const $timeElement = $(this).find('.prayer-time-value');

                if ($nameElement.length && $timeElement.length) {
                    if (isFriday && index === 2 && $nameElement.text().trim() === "Dzuhur") {
                        $nameElement.text("Jum'at");
                    }

                    const name = $nameElement.text().trim();
                    const time = $timeElement.text().trim();
                    const [hours, minutes] = time.split(":").map(Number);
                    const timeInMinutes = hours * 60 + minutes;

                    prayerTimes.push({
                        name,
                        time,
                        timeInMinutes
                    });
                }
            });

            if (!prayerTimes.length) {
                scheduleNextPrayerCheck();
                return;
            }

            let activePrayerIndex = -1;
            let nextPrayerIndex = -1;
            let nextPrayerTime = Infinity;

            for (let i = 0; i < prayerTimes.length; i++) {
                if (prayerTimes[i].timeInMinutes > currentTimeInMinutes && prayerTimes[i].timeInMinutes <
                    nextPrayerTime) {
                    nextPrayerIndex = i;
                    nextPrayerTime = prayerTimes[i].timeInMinutes;
                }
            }

            if (nextPrayerIndex === -1) {
                nextPrayerIndex = 0;
            }

            let lastPrayerTime = -1;
            for (let i = 0; i < prayerTimes.length; i++) {
                if (prayerTimes[i].timeInMinutes <= currentTimeInMinutes && prayerTimes[i].timeInMinutes >
                    lastPrayerTime) {
                    activePrayerIndex = i;
                    lastPrayerTime = prayerTimes[i].timeInMinutes;
                }
            }

            if (activePrayerIndex === -1) {
                activePrayerIndex = prayerTimes.length - 1;
            }

            $prayerTimesElements.each(function(index) {
                $(this).removeClass('active next-prayer');
                if (index === activePrayerIndex) {
                    $(this).addClass('active');
                }
                if (index === nextPrayerIndex) {
                    $(this).addClass('next-prayer');
                }
            });

            const nextPrayer = prayerTimes[nextPrayerIndex];
            let timeDiffInMinutes = nextPrayer.timeInMinutes - currentTimeInMinutes;
            if (timeDiffInMinutes < 0) {
                timeDiffInMinutes += 24 * 60;
            }

            // Menjeda audio latar belakang 1 menit sebelum waktu adzan
            const preAdzanPauseSeconds = 60; // 1 menit
            if (timeDiffInMinutes * 60 <= preAdzanPauseSeconds && timeDiffInMinutes > 0) {
                if (!isAudioPausedForAdzan) {
                    pauseAudio();
                    isAudioPausedForAdzan = true;
                }
            } else {
                // Reset flag jika sudah di luar jendela waktu jeda
                // Ini akan direset dengan benar saat adzan selesai
            }

            const totalSecondsRemaining = timeDiffInMinutes * 60 - now.getSeconds();
            const hours = Math.floor(totalSecondsRemaining / 3600);
            const minutes = Math.floor((totalSecondsRemaining % 3600) / 60);
            const seconds = totalSecondsRemaining % 60;
            const countdownFormatted =
                `${hours.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;

            const $nextPrayerLabel = $('#next-prayer-label');
            const $countdownValue = $('#countdown-value');

            if ($nextPrayerLabel.length) {
                $nextPrayerLabel.text(nextPrayer.name);
            }
            if ($countdownValue.length) {
                $countdownValue.text(countdownFormatted);
            }

            prayerTimes.forEach((prayer, index) => {
                const [prayerHours, prayerMinutes] = prayer.time.split(':').map(Number);
                const prayerTimeInMinutes = prayerHours * 60 + prayerMinutes;
                const currentTimeInMinutes = now.getHours() * 60 + now.getMinutes();
                const currentSeconds = now.getSeconds();

                // Cek apakah waktu saat ini kurang dari 10 detik sebelum waktu adzan
                const isAlmostPrayerTime =
                    (prayerTimeInMinutes === currentTimeInMinutes && currentSeconds >= 50) ||
                    // Kurang dari 10 detik sebelum adzan
                    (prayerTimeInMinutes + 1 === currentTimeInMinutes && currentSeconds <
                        10); // Kurang dari 10 detik setelah adzan

                // Jeda audio jika hampir waktu adzan
                if (isAlmostPrayerTime && !isAudioPausedForAdzan && !isAdzanPlaying) {
                    pauseAudio();
                }

                if ((prayerTimeInMinutes === currentTimeInMinutes ||
                        (prayerTimeInMinutes + 1 === currentTimeInMinutes && currentSeconds < 10)) &&
                    !isAdzanPlaying && !adzanStartTime) {
                    if (prayer.name.toLowerCase().includes('syuruq') || prayer.name.toLowerCase()
                        .includes('shuruq') || prayer.name.toLowerCase().includes('terbit')) {
                        showSyuruqPopup(prayer.name, prayer.time);
                    } else if (prayer.name.toLowerCase().includes('dhuha')) {
                        showDhuhaPopup(prayer.name, prayer.time);
                    } else if (prayer.name === "Jum'at" && fridayInfoStartTime && now.getTime() <
                        fridayInfoEndTime) {
                        // Jangan memulai adzan Jumat jika popup Friday Info aktif
                        // Hanya panggil clearAdzanState jika belum dipanggil untuk periode ini
                        if (!localStorage.getItem('fridayAdzanCleared_' + currentTimeFormatted
                            .substring(0, 16))) {
                            clearAdzanState();
                            localStorage.setItem('fridayAdzanCleared_' + currentTimeFormatted.substring(
                                0, 16), 'true');
                        }
                    } else {
                        showAdzanPopup(prayer.name, prayer.time);
                    }
                }
            });

            // showFridayInfo();

            scheduleNextPrayerCheck();
        }

        function scheduleNextPrayerCheck() {
            setTimeout(() => {
                handlePrayerTimes();
            }, 1000);
        }

        function updateSlides() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                console.error('Tidak dapat menentukan slug dari URL');
                return;
            }

            // console.log('Memeriksa update slide untuk slug:', slug);

            $.ajax({
                url: `/api/slides1/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: async function(response) {
                    console.log('Respons API slides:', response);
                    if (response.success) {
                        const previousSlides = [
                            $('#slide1').val() || '',
                            $('#slide2').val() || '',
                            $('#slide3').val() || '',
                            $('#slide4').val() || '',
                            $('#slide5').val() || '',
                            $('#slide6').val() || ''
                        ];

                        const newSlides = [
                            response.data.slide1 || '',
                            response.data.slide2 || '',
                            response.data.slide3 || '',
                            response.data.slide4 || '',
                            response.data.slide5 || '',
                            response.data.slide6 || ''
                        ];

                        const hasChanges = !previousSlides.every((slide, index) => slide ===
                            newSlides[index]);

                        if (hasChanges) {
                            // console.log('Perubahan terdeteksi, memperbarui slide...');

                            $('#slide1').val(newSlides[0]);
                            $('#slide2').val(newSlides[1]);
                            $('#slide3').val(newSlides[2]);
                            $('#slide4').val(newSlides[3]);
                            $('#slide5').val(newSlides[4]);
                            $('#slide6').val(newSlides[5]);

                            const newUrls = newSlides.filter(url => url.trim() !== '');
                            if (newUrls.length === 0) {
                                console.warn('Tidak ada slide baru, menggunakan default');
                                newUrls.push('/images/other/slide-jws-default.jpg');
                            }

                            // Preload gambar baru yang belum ada di cache
                            const urlsToPreload = newUrls.filter(url => !window.imageCache[url] || !
                                window.imageCache[url].complete);
                            if (urlsToPreload.length > 0) {
                                // console.log(`Preload gambar baru dari updateSlides: ${urlsToPreload}`);
                                await preloadImages(urlsToPreload);
                            }

                            window.slideUrls = newUrls;
                            // console.log('Slide diperbarui, jumlah slide:', window.slideUrls.length);
                            $(document).trigger('slidesUpdated', [newSlides]);
                        } else {
                            console.log('Tidak ada perubahan pada slide, update diabaikan');
                        }

                        // Bersihkan cache yang tidak digunakan
                        clearUnusedCache(newSlides);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data slide:', error, xhr.responseText);
                }
            });
        }


        // Objek global untuk menyimpan cache gambar
        window.imageCache = window.imageCache || {};

        function getAllActiveUrls() {
            const slideUrls = window.slideUrls || [];
            const iqomahImages = window.iqomahImages || [];
            const fridayImages = window.fridayImages || [];
            const jumbotronUrls = Array.isArray(window.jumbotronSequence) ? window.jumbotronSequence : [];
            const adzan15 = $('#adzan15').val() || '/images/other/lurus-rapat-shaf-default.webp';
            return [...new Set([...slideUrls, ...iqomahImages, ...fridayImages, ...jumbotronUrls, adzan15])]
                .filter(url => url.trim() !== '');
        }

        function clearUnusedCache(currentUrls) {
            const maxCacheSize = 50;
            const activeUrls = getAllActiveUrls();

            Object.keys(window.imageCache).forEach(url => {
                if (!activeUrls.includes(url)) {
                    delete window.imageCache[url];
                    // console.log(`Gambar dihapus dari cache: ${url}`);
                }
            });

            const cachedUrls = Object.keys(window.imageCache);
            if (cachedUrls.length > maxCacheSize) {
                const urlsToRemove = cachedUrls.slice(0, cachedUrls.length - maxCacheSize);
                urlsToRemove.forEach(url => {
                    delete window.imageCache[url];
                    console.log(`Gambar lama dihapus dari cache: ${url}`);
                });
            }
        }

        // Fungsi preloadImages yang sudah ada
        function preloadImages(urls) {
            return Promise.all(urls.map(url => {
                return new Promise((resolve, reject) => {
                    if (window.imageCache[url] && window.imageCache[url].complete) {
                        console.log(`Gambar sudah ada di cache: ${url}`);
                        resolve(window.imageCache[url]);
                        return;
                    }

                    const img = new Image();
                    img.src = url;

                    img.onload = () => {
                        // console.log(`Gambar berhasil dimuat: ${url}`);
                        window.imageCache[url] = img;
                        resolve(img);
                    };

                    img.onerror = () => {
                        console.warn(`Gagal memuat gambar: ${url}`);
                        resolve(null);
                    };
                });
            }));
        }

        // Contoh penambahan dalam manageSlideDisplay atau fungsi serupa
        function updateJumbotronContent() {
            let animationFrameId = null;

            // Fungsi untuk memperbarui konten jumbotron
            function updateContent() {
                // Mengisi next-prayer-label dan countdown-value
                const nextPrayerLabel = $('#next-prayer-label').text();
                const countdownValue = $('#countdown-value').text();
                $('#jumbotron-next-prayer-label').text(nextPrayerLabel);
                $('#jumbotron-countdown-value').text(countdownValue);

                // Mengisi jam digital berdasarkan waktu server
                const serverTime = getCurrentTimeFromServer();
                const hours = String(serverTime.getHours()).padStart(2, '0');
                const minutes = String(serverTime.getMinutes()).padStart(2, '0');
                const seconds = String(serverTime.getSeconds()).padStart(2, '0');
                $('#jumbotron-clock-time').text(`${hours}:${minutes}:${seconds}`);

                // Lanjutkan animasi
                const isActive = ($('#jumbotron_is_active').val() === 'true') && Array.isArray(window.jumbotronSequence) && window.jumbotronSequence.length > 0;
                if (isActive && $('#jumbotronImage').is(':visible')) {
                    animationFrameId = requestAnimationFrame(updateContent);
                }
            }

            // Mulai pembaruan jika jumbotron aktif
            function startJumbotronUpdates() {
                const isActive = ($('#jumbotron_is_active').val() === 'true') && Array.isArray(window.jumbotronSequence) && window.jumbotronSequence.length > 0;
                if (isActive && $('#jumbotronImage').is(':visible')) {
                    if (!animationFrameId) {
                        updateContent(); // Perbarui segera
                        console.log('Jumbotron countdown updates started with requestAnimationFrame');
                    }
                }
            }

            // Hentikan pembaruan
            function stopJumbotronUpdates() {
                if (animationFrameId) {
                    cancelAnimationFrame(animationFrameId);
                    animationFrameId = null;
                    console.log('Jumbotron countdown updates stopped');
                }
            }

            // Panggil pembaruan awal
            startJumbotronUpdates();

            // Pantau perubahan status jumbotron
            $(document).on('jumbotronUpdated', function() {
                const isActive = ($('#jumbotron_is_active').val() === 'true') && Array.isArray(window.jumbotronSequence) && window.jumbotronSequence.length > 0;
                if (isActive) {
                    startJumbotronUpdates();
                } else {
                    stopJumbotronUpdates();
                }
            });

            // Hentikan pembaruan saat jumbotron disembunyikan
            $(document).on('slideUpdated', function() {
                if (!$('#jumbotronImage').is(':visible')) {
                    stopJumbotronUpdates();
                } else {
                    startJumbotronUpdates();
                }
            });

            // Return fungsi untuk cleanup manual jika diperlukan
            return stopJumbotronUpdates;
        }

        // Panggil fungsi ini saat jumbotron ditampilkan
        $(document).on('jumbotronUpdated', function() {
            const isActive = $('#jumbotron_is_active').val() === 'true' && Array.isArray(window.jumbotronSequence) && window.jumbotronSequence.length > 0;
            if (isActive) updateJumbotronContent();
        });

        function updateJumbotronData() {
            const parts = window.location.pathname.split('/').filter(Boolean);
            const slug = parts[parts.length - 1] || '';
            if (!slug) return;
            $.ajax({
                url: `/api/jumbotron-all/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const d = response.data;
                        $('#jumbotron_is_active').val(d.is_active ? 'true' : 'false');
                        try {
                            const seq = Array.isArray(d.items) ? d.items : [];
                            window.jumbotronSequence = seq.filter(u => typeof u === 'string' && u.trim() !== '');
                            $('#jumbotron-sequence').val(JSON.stringify(window.jumbotronSequence));
                        } catch (e) {
                            window.jumbotronSequence = [];
                            $('#jumbotron-sequence').val('[]');
                        }
                        $(document).trigger('jumbotronUpdated');
                    } else {
                        $('#jumbotron_is_active').val('false');
                        window.jumbotronSequence = [];
                        $('#jumbotron-sequence').val('[]');
                    }
                },
                error: function(xhr) {
                    console.error('Error saat mengambil jumbotron-all:', xhr.responseText);
                    $('#jumbotron_is_active').val('false');
                    window.jumbotronSequence = [];
                    $('#jumbotron-sequence').val('[]');
                }
            });
        }

        function manageSlideDisplay() {
            const $mosqueImageElement = $('.mosque-image');
            const $jumbotronImageElement = $('#jumbotronImage');
            if (!$mosqueImageElement.length || !$jumbotronImageElement.length) {
                console.warn('Elemen .mosque-image atau #jumbotronImage tidak ditemukan');
                return;
            }

            // Inisialisasi slideUrls
            window.slideUrls = [
                $('#slide1').val() || '',
                $('#slide2').val() || '',
                $('#slide3').val() || '',
                $('#slide4').val() || '',
                $('#slide5').val() || '',
                $('#slide6').val() || ''
            ].filter(url => url.trim() !== '');

            if (window.slideUrls.length === 0) {
                // console.warn('Tidak ada slide mosque-image yang valid, menggunakan gambar default');
                window.slideUrls = ['/images/other/slide-jws-default.jpg'];
            }

            // Inisialisasi jumbotron sequence dari hidden input
            try {
                const rawSeq = $('#jumbotron-sequence').val();
                window.jumbotronSequence = JSON.parse(rawSeq || '[]').filter(u => typeof u === 'string' && u.trim() !== '');
            } catch (e) {
                window.jumbotronSequence = [];
            }

            async function initSlider() {
                try {
                    const allUrls = [...window.slideUrls, ...(Array.isArray(window.jumbotronSequence) ? window.jumbotronSequence : [])];
                    await preloadImages(allUrls);
                    // console.log('Semua gambar telah dimuat, memulai slider', allUrls);

                    const slideDuration = 20000; // 20 detik per gambar

                    function updateSlide() {
                        const isSequenceActive = $('#jumbotron_is_active').val() === 'true' && Array.isArray(window.jumbotronSequence) && window.jumbotronSequence.length > 0;
                        if (!isSequenceActive) {
                            window.jumbotronSequence = [];
                            $jumbotronImageElement.css('display', 'none');
                        }

                        if (window.slideUrls.length === 0) {
                            console.warn('slideUrls kosong, menggunakan gambar default');
                            window.slideUrls = ['/images/other/slide-jws-default.jpg'];
                        }

                        const now = getCurrentTimeFromServer();
                        const totalSeconds = (now.getHours() * 3600) + (now.getMinutes() * 60) + now
                            .getSeconds();
                        const slideCycleDuration = slideDuration * window.slideUrls
                            .length; // Durasi siklus penuh mosque-image
                        const totalCycleDuration = isSequenceActive ? slideCycleDuration + slideDuration :
                            slideCycleDuration;

                        const cyclePosition = (totalSeconds * 1000 + now.getMilliseconds()) %
                            totalCycleDuration;
                        const imageIndex = Math.floor(cyclePosition / slideDuration);

                        let currentUrl;
                        if (isSequenceActive && imageIndex === window.slideUrls.length) {
                            if (!window.inJumbotronPhase) {
                                window.inJumbotronPhase = true;
                                window.jsIndex = typeof window.jsIndex === 'number' ? window.jsIndex : 0;
                                const seqLen = window.jumbotronSequence.length;
                                const nextIndex = seqLen > 0 ? (window.jsIndex % seqLen) : 0;
                                window.jsIndex++;
                                window.currentJumbotronUrl = window.imageCache[window.jumbotronSequence[nextIndex]]?.src ||
                                    window.jumbotronSequence[nextIndex] ||
                                    '/images/other/slide-jws-default.jpg';
                            }
                            currentUrl = window.currentJumbotronUrl || '/images/other/slide-jws-default.jpg';
                            $mosqueImageElement.css('display', 'none');
                            $jumbotronImageElement.css({
                                'background-image': `url("${currentUrl}")`,
                                'display': 'block',
                                'transition': 'background-image 0.5s ease-in-out'
                            });
                            // Reset dan jalankan animasi progress bar
                            // const $progressBar = $('.jumbotron-progress-bar');
                            // $progressBar.css('width', '0%');
                            // $progressBar.css('animation',
                            //     `progressAnimation ${slideDuration}ms linear forwards`);
                            // console.log(
                            // `Jumbotron ditampilkan: Index ${currentJumboIndex}, URL ${currentUrl}`);
                        } else {
                            window.inJumbotronPhase = false;
                            window.currentJumbotronUrl = null;
                            const slideIndex = imageIndex % window.slideUrls.length;
                            currentUrl = window.imageCache[window.slideUrls[slideIndex]]?.src ||
                                window.slideUrls[slideIndex] ||
                                '/images/other/slide-jws-default.jpg';
                            $jumbotronImageElement.css('display', 'none');
                            $mosqueImageElement.css({
                                'background-image': `url("${currentUrl}")`,
                                'display': 'block',
                                'transition': 'background-image 0.5s ease-in-out'
                            });
                            // Hentikan animasi progress bar saat jumbotron tidak ditampilkan
                            // $('.jumbotron-progress-bar').css('animation', 'none').css('width', '0%');
                            // console.log(`Mosque-image ditampilkan: Index ${slideIndex}, URL ${currentUrl}`);
                        }

                        clearUnusedCache([...window.slideUrls, ...(Array.isArray(window.jumbotronSequence) ? window.jumbotronSequence : [])]);
                        $(document).trigger('slideUpdated'); // Picu event slideUpdated
                    }

                    updateSlide();
                    setInterval(updateSlide, 1000);

                    $(document).on('slidesUpdated', async function(event, newSlides) {
                        // console.log('Event slidesUpdated diterima, memperbarui slider');
                        const newUrls = newSlides.filter(url => url.trim() !== '');
                        if (newUrls.length === 0) {
                            console.warn(
                                'Tidak ada slide baru yang valid, menggunakan gambar default'
                            );
                            window.slideUrls = ['/images/other/slide-jws-default.jpg'];
                        } else {
                            window.slideUrls = newUrls;
                        }

                        const urlsToPreload = window.slideUrls.filter(url => !window.imageCache[
                            url] || !window.imageCache[url].complete);
                        if (urlsToPreload.length > 0) {
                            console.log(`Preload gambar baru: ${urlsToPreload}`);
                            await preloadImages(urlsToPreload);
                        }

                        // console.log('slideUrls diperbarui:', window.slideUrls);
                        clearUnusedCache([...window.slideUrls, ...(Array.isArray(window.jumbotronSequence) ? window.jumbotronSequence : [])]);
                    });

                    $(document).on('jumbotronUpdated', async function() {
                        try {
                            const seqRaw = $('#jumbotron-sequence').val();
                            window.jumbotronSequence = JSON.parse(seqRaw || '[]').filter(u => typeof u === 'string' && u.trim() !== '');
                        } catch (e) {
                            window.jumbotronSequence = [];
                        }
                        const urlsToPreload = window.jumbotronSequence.filter(url => !window.imageCache[url] || !window.imageCache[url].complete);
                        if (urlsToPreload.length > 0) {
                            await preloadImages(urlsToPreload);
                        }
                        clearUnusedCache([...window.slideUrls, ...(Array.isArray(window.jumbotronSequence) ? window.jumbotronSequence : [])]);
                        try {
                            const newHash = JSON.stringify(window.jumbotronSequence);
                            if (newHash !== window.jumbotronSequenceHash) {
                                window.jumbotronSequenceHash = newHash;
                                window.jsIndex = 0;
                                window.inJumbotronPhase = false;
                                window.currentJumbotronUrl = null;
                            }
                        } catch (e) {}
                    });

                    // jumbotronMasjidUpdated deprecated; use jumbotronUpdated
                } catch (error) {
                    console.error('Error saat menginisialisasi slider:', error);
                    // Fallback: Gunakan gambar default jika preload gagal
                    window.slideUrls = ['/images/other/slide-jws-default.jpg'];
                    const fallbackUrl = window.imageCache[window.slideUrls[0]]?.src || window.slideUrls[0];
                    $mosqueImageElement.css({
                        'background-image': `url("${fallbackUrl}")`,
                        'display': 'block',
                        'transition': 'background-image 0.5s ease-in-out'
                    });
                    $jumbotronImageElement.css('display', 'none');
                    // Hentikan animasi progress bar pada fallback
                    // $('.jumbotron-progress-bar').css('animation', 'none').css('width', '0%');
                    $(document).trigger('slideUpdated');
                }
            }

            // Panggil initSlider di luar definisi fungsi
            initSlider();
        }

        manageSlideDisplay();
        updateScrollingText();

        checkAndRestoreFridayInfo();
        checkAndRestoreAdzanImage();

        // Syuruq sekarang menggunakan sistem yang sama dengan waktu sholat lainnya
        // Tidak perlu lagi logika khusus untuk restore Syuruq karena sudah ditangani di checkAndRestoreSessions

        handlePrayerTimes();
        setInterval(handlePrayerTimes, 1000);

        // Update tanggal awal dengan waktu lokal, akan diperbarui setelah sinkronisasi server
        updateDate();
        setInterval(updateDate, 60000);
        updateMarqueeText();

        setTimeout(function() {
            initMarquee();
        }, 1000);

        setInterval(() => {
            const now = getCurrentTimeFromServer();
            const currentMonthYear =
                `${now.getFullYear()}-${(now.getMonth() + 1).toString().padStart(2, '0')}`;

            // Ambil nilai bulan dan tahun saat ini dari input hidden
            const currentMonthValue = $('#current-month').val() || new Date().getMonth() + 1;
            const currentYearValue = $('#current-year').val() || new Date().getFullYear();
            const storedMonthYear =
                `${currentYearValue}-${currentMonthValue.toString().padStart(2, '0')}`;

            if (currentMonthYear !== storedMonthYear) {
                fetchPrayerTimes();
            }
        }, 60000);

        function updateFridayOfficials() {
            console.log('=== updateFridayOfficials function called ===');
            const slug = window.location.pathname.replace(/^\//, '');
            console.log('Current slug:', slug);

            if (!slug) {
                console.log('No slug found, exiting function');
                return;
            }

            function makeRequest(retryCount = 0) {
                console.log(`Making AJAX request to: /api/petugas/${slug} (attempt ${retryCount + 1})`);
                $.ajax({
                    url: `/api/petugas/${slug}`,
                    method: 'GET',
                    dataType: 'json',
                    timeout: 10000, // 10 detik timeout
                    success: function(response) {
                        console.log('Respons API petugas:', response);
                        if (response.success && response.data) {
                            const now = getCurrentTimeFromServer();
                            const currentDay = now.getDate();
                            const currentMonth = now.getMonth();

                            let selected = null;
                            const data = response.data;

                            if (Array.isArray(data)) {
                                // console.log(
                                // `Menerima array petugas dengan panjang: ${data.length}`);
                                for (let i = 0; i < data.length; i++) {
                                    const item = data[i];
                                    const d = new Date(item.hari);
                                    if (isNaN(d.getTime())) {
                                        continue; // lewati tanggal invalid
                                    }
                                    if (d.getDate() === currentDay && d.getMonth() ===
                                        currentMonth) {
                                        selected = item;
                                        break; // ambil pertama yang cocok (diasumsikan DESC)
                                    }
                                }
                            } else if (typeof data === 'object') {
                                selected = data;
                            }

                            if (!selected) {
                                console.warn(
                                    'Tidak ada entri petugas yang cocok untuk tanggal hari ini.'
                                );
                                clearPetugasData();
                                return;
                            }

                            // Validasi tanggal terpilih
                            const petugasDate = new Date(selected.hari);
                            if (isNaN(petugasDate.getTime())) {
                                console.warn('Format tanggal hari tidak valid pada entri terpilih:',
                                    selected.hari);
                                clearPetugasData();
                                return;
                            }

                            // Bandingkan tanggal dan bulan
                            const petugasDay = petugasDate.getDate();
                            const petugasMonth = petugasDate.getMonth();
                            if (currentDay === petugasDay && currentMonth === petugasMonth) {
                                $('#khatib').val(selected.khatib || '');
                                $('#imam').val(selected.imam || '');
                                $('#muadzin').val(selected.muadzin || '');

                                console.log('Data petugas Jumat diperbarui dari entri terpilih:', {
                                    khatib: selected.khatib || '',
                                    imam: selected.imam || '',
                                    muadzin: selected.muadzin || ''
                                });

                                if ($('#fridayInfoPopup').is(':visible')) {
                                    updateFridayInfoContent();
                                }
                            } else {
                                clearPetugasData();
                                console.log(
                                    'Entri petugas terpilih tidak cocok dengan tanggal hari ini - data dikosongkan'
                                );
                            }
                        } else {
                            // Handle response yang tidak success
                            console.warn('Response API tidak success atau data tidak tersedia:',
                                response.message || 'Unknown error');
                            console.log('Full response:', response);
                            clearPetugasData();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX Error occurred:', {
                            xhr,
                            status,
                            error
                        });
                        let errorMessage = 'Unknown error';
                        let shouldRetry = true;

                        // Parse error response
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            errorMessage = errorResponse.message || errorMessage;

                            // Jangan retry untuk error 404 (data tidak ditemukan)
                            if (xhr.status === 404) {
                                shouldRetry = false;
                                clearPetugasData();
                                console.warn('Data petugas tidak ditemukan untuk slug:', slug);
                                return;
                            }
                        } catch (e) {
                            errorMessage = xhr.responseText || error || status;
                        }

                        console.error(
                            `Error saat mengambil data petugas (Percobaan ${retryCount + 1}):`,
                            errorMessage
                        );

                        // Retry logic untuk error selain 404
                        if (shouldRetry && retryCount < 3) {
                            setTimeout(() => {
                                console.log(
                                    `Mencoba ulang pengambilan data petugas (Percobaan ${retryCount + 2})...`
                                );
                                makeRequest(retryCount + 1);
                            }, 5000); // 5 detik delay
                        } else {
                            console.error(
                                'Maksimum percobaan pengambilan data petugas tercapai atau error tidak dapat di-retry.'
                            );
                            clearPetugasData();
                        }
                    }
                });
            }

            // Helper function untuk membersihkan data petugas
            function clearPetugasData() {
                $('#khatib').val('');
                $('#imam').val('');
                $('#muadzin').val('');

                // Jika popup sedang ditampilkan, perbarui kontennya
                if ($('#fridayInfoPopup').is(':visible')) {
                    updateFridayInfoContent();
                }
            }

            // Mulai request
            console.log('Starting makeRequest call...');
            makeRequest();
        }

        // Tambahkan fungsi updateFridayInfoContent
        function updateFridayInfoContent() {
            const $popup = $('#fridayInfoPopup');
            const $dateElement = $('#fridayDate');
            const $officialsElement = $('#fridayOfficials');
            const now = getCurrentTimeFromServer();

            if (typeof moment !== 'undefined') {
                moment.locale('id');
                const hari = moment(now).format('dddd');
                const tanggalMasehi = moment(now).format('D MMMM YYYY');

                let formattedDate =
                    `<span class="day-name-popup">${hari}</span>, <br />${tanggalMasehi}`;


                if (typeof moment().iDate === 'function') {
                    const hijriBaseMoment = moment(now);
                    const hijriDate = hijriBaseMoment.iDate();
                    const hijriMonth = hijriBaseMoment.iMonth();
                    const hijriYear = hijriBaseMoment.iYear();
                    const bulanHijriyahID = [
                        'Muharam', 'Safar', 'Rabiulawal', 'Rabiulakhir', 'Jumadilawal', 'Jumadilakhir',
                        'Rajab', 'Syaban', 'Ramadhan', 'Syawal', 'Zulkaidah', 'Zulhijah'
                    ];
                    const hijri = `${hijriDate} ${bulanHijriyahID[hijriMonth]} ${hijriYear}H`;
                    formattedDate += ` / ${hijri}`;
                }

                $dateElement.html(formattedDate);
            }

            let officialsHtml = '<table class="responsive-table">';
            const khatib = $('#khatib').val();
            const imam = $('#imam').val();
            const muadzin = $('#muadzin').val();

            if (khatib) {
                officialsHtml +=
                    `<tr><td class="official-name">KHATIB</td></tr><tr><td class="official-name-value" style="font-weight: bold;">${khatib}</td></tr>`;
            }
            if (imam) {
                officialsHtml +=
                    `<tr><td class="official-name">IMAM</td></tr><tr><td class="official-name-value" style="font-weight: bold;">${imam}</td></tr>`;
            }
            if (muadzin) {
                officialsHtml +=
                    `<tr><td class="official-name">MUADZIN</td></tr><tr><td class="official-name-value" style="font-weight: bold;">${muadzin}</td></tr>`;
            }
            officialsHtml += '</table>';

            $officialsElement.html(officialsHtml);
        }

        // Perbarui dan putar audio setiap 30 menit
        setInterval(function() {
            updateAndPlayAudio();
            // updateFridayImages();
            updateIqomahImages();
            updateAdzanImages();
            // updateFridayOfficials();
        }, 1200000); // 1200000 milidetik = 30 menit

        updateJumbotronData();

        setInterval(function() {
            // updateMosqueInfo();
            updateJumbotronData();
            // updateMarqueeText();
            // checkThemeUpdate();
            updateSlides();
            updateDailyPrayerTimes();
        }, 120000); // 120000 milidetik = 2 menit

        // setInterval(function() {
        //     updateFridayOfficials();
        // }, 300000);

        // Fungsi untuk toggle full screen
        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error('Gagal masuk ke mode full screen:', err);
                });
            } else {
                document.exitFullscreen().catch(err => {
                    console.error('Gagal keluar dari mode full screen:', err);
                });
            }
        }

        // Event listener untuk double-click
        $(document).on('dblclick', function() {
            toggleFullScreen();
        });

        // ===================== Realtime Profil Updates via Echo =====================
        (function initProfileRealtimeUpdates() {
            try {
                const slug = window.location.pathname.replace(/^\//, '');
                if (!slug) {
                    console.warn('Slug tidak ditemukan; realtime profil updates dinonaktifkan');
                    return;
                }

                const subscribe = () => {
                    try {
                        window.Echo.channel(`masjid-${slug}`)
                            .listen('ContentUpdatedEvent', (e) => {
                                // Hanya tangani event untuk profil
                                if (e && e.type === 'profil') {
                                    console.log('Menerima event profil; memperbarui info masjid');
                                    // Ambil data terbaru dari API dan render ke UI
                                    updateMosqueInfo();
                                }
                            });
                        console.log(`Subscribed ke channel masjid-${slug} untuk profil`);
                    } catch (err) {
                        console.warn('Gagal subscribe Echo channel:', err);
                    }
                };

                if (window.Echo) {
                    subscribe();
                } else {
                    // Tunggu Echo terinisialisasi oleh Vite (resources/js/app.js)
                    let attempts = 0;
                    const maxAttempts = 20;
                    const interval = setInterval(() => {
                        attempts++;
                        if (window.Echo) {
                            clearInterval(interval);
                            subscribe();
                        } else if (attempts >= maxAttempts) {
                            clearInterval(interval);
                            console.warn(
                                'Echo belum tersedia; realtime profil updates dinonaktifkan');
                        }
                    }, 500);
                }
            } catch (err) {
                console.warn('initProfileRealtimeUpdates error:', err);
            }
        })();

        // ===================== Realtime Petugas Updates via Echo =====================
        (function initPetugasRealtimeUpdates() {
            try {
                const slug = window.location.pathname.replace(/^\//, '');
                if (!slug) {
                    console.warn('Slug tidak ditemukan; realtime petugas updates dinonaktifkan');
                    return;
                }

                const subscribe = () => {
                    try {
                        window.Echo.channel(`masjid-${slug}`)
                            .listen('ContentUpdatedEvent', (e) => {
                                // Tangani event untuk petugas Jumat
                                if (e && e.type === 'petugas') {
                                    console.log(
                                        'Menerima event petugas; memperbarui data petugas Jumat'
                                    );
                                    // Ambil data terbaru dari API dan render ke UI
                                    updateFridayOfficials();
                                }
                            });
                        // console.log(`Subscribed ke channel masjid-${slug} untuk petugas`);
                    } catch (err) {
                        console.warn('Gagal subscribe Echo channel:', err);
                    }
                };

                if (window.Echo) {
                    subscribe();
                } else {
                    // Tunggu Echo terinisialisasi oleh Vite (resources/js/app.js)
                    let attempts = 0;
                    const maxAttempts = 20;
                    const interval = setInterval(() => {
                        attempts++;
                        if (window.Echo) {
                            clearInterval(interval);
                            subscribe();
                        } else if (attempts >= maxAttempts) {
                            clearInterval(interval);
                            console.warn(
                                'Echo belum tersedia; realtime petugas updates dinonaktifkan');
                        }
                    }, 500);
                }
            } catch (err) {
                console.warn('initPetugasRealtimeUpdates error:', err);
            }
        })();

        // ===================== Realtime Marquee Updates via Echo =====================
        (function initMarqueeRealtimeUpdates() {
            try {
                const slug = window.location.pathname.replace(/^\//, '');
                if (!slug) {
                    console.warn('Slug tidak ditemukan; realtime marquee updates dinonaktifkan');
                    return;
                }

                const subscribe = () => {
                    try {
                        window.Echo.channel(`masjid-${slug}`)
                            .listen('ContentUpdatedEvent', (e) => {
                                // Tangani event untuk marquee
                                if (e && e.type === 'marquee') {
                                    console.log(
                                        'Menerima event marquee; memperbarui teks berjalan');
                                    // Ambil data marquee terbaru dari API dan render ke UI
                                    updateMarqueeText();
                                }
                            });
                        console.log(`Subscribed ke channel masjid-${slug} untuk marquee`);
                    } catch (err) {
                        console.warn('Gagal subscribe Echo channel:', err);
                    }
                };

                if (window.Echo) {
                    subscribe();
                } else {
                    // Tunggu Echo terinisialisasi oleh Vite (resources/js/app.js)
                    let attempts = 0;
                    const maxAttempts = 20;
                    const interval = setInterval(() => {
                        attempts++;
                        if (window.Echo) {
                            clearInterval(interval);
                            subscribe();
                        } else if (attempts >= maxAttempts) {
                            clearInterval(interval);
                            console.warn(
                                'Echo belum tersedia; realtime marquee updates dinonaktifkan');
                        }
                    }, 500);
                }
            } catch (err) {
                console.warn('initMarqueeRealtimeUpdates error:', err);
            }
        })();

        // ===================== Realtime Slides (Friday Images) Updates via Echo =====================
        (function initFridayImagesRealtimeUpdates() {
            try {
                const slug = window.location.pathname.replace(/^\//, '');
                if (!slug) {
                    console.warn('Slug tidak ditemukan; realtime slides updates dinonaktifkan');
                    return;
                }

                const subscribe = () => {
                    try {
                        window.Echo.channel(`masjid-${slug}`)
                            .listen('ContentUpdatedEvent', (e) => {
                                // Tangani event untuk update gambar Friday
                                // Komponen Adzan mem-broadcast dengan type 'adzan'
                                if (e && e.type === 'adzan') {
                                    console.log('Menerima event slide; memperbarui gambar Friday');
                                    // Ambil data terbaru dari API dan render ke UI
                                    updateFridayImages();
                                }
                            });
                        console.log(`Subscribed ke channel masjid-${slug} untuk slides friday`);
                    } catch (err) {
                        console.warn('Gagal subscribe Echo channel:', err);
                    }
                };

                if (window.Echo) {
                    subscribe();
                } else {
                    // Tunggu Echo terinisialisasi oleh Vite (resources/js/app.js)
                    let attempts = 0;
                    const maxAttempts = 20;
                    const interval = setInterval(() => {
                        attempts++;
                        if (window.Echo) {
                            clearInterval(interval);
                            subscribe();
                        } else if (attempts >= maxAttempts) {
                            clearInterval(interval);
                            console.warn(
                                'Echo belum tersedia; realtime slides updates dinonaktifkan');
                        }
                    }, 500);
                }
            } catch (err) {
                console.warn('initFridayImagesRealtimeUpdates error:', err);
            }
        })();

        // ===================== Realtime Theme Updates via Echo =====================
        (function initThemeRealtimeUpdates() {
            try {
                const slug = window.location.pathname.replace(/^\//, '');
                if (!slug) {
                    console.warn('Slug tidak ditemukan; realtime tema updates dinonaktifkan');
                    return;
                }

                const subscribe = () => {
                    try {
                        window.Echo.channel(`masjid-${slug}`)
                            .listen('ContentUpdatedEvent', (e) => {
                                // Tangani event untuk perubahan tema
                                if (e && e.type === 'theme') {
                                    console.log('Menerima event tema; memeriksa pembaruan tema');
                                    // Cek API tema dan reload bila perlu
                                    checkThemeUpdate();
                                }
                            });
                        console.log(`Subscribed ke channel masjid-${slug} untuk tema`);
                    } catch (err) {
                        console.warn('Gagal subscribe Echo channel:', err);
                    }
                };

                if (window.Echo) {
                    subscribe();
                } else {
                    // Tunggu Echo terinisialisasi oleh Vite (resources/js/app.js)
                    let attempts = 0;
                    const maxAttempts = 20;
                    const interval = setInterval(() => {
                        attempts++;
                        if (window.Echo) {
                            clearInterval(interval);
                            subscribe();
                        } else if (attempts >= maxAttempts) {
                            clearInterval(interval);
                            console.warn(
                                'Echo belum tersedia; realtime tema updates dinonaktifkan');
                        }
                    }, 500);
                }
            } catch (err) {
                console.warn('initThemeRealtimeUpdates error:', err);
            }
        })();

        // ===================== Realtime Finance Overlay Updates via Echo =====================
        (function initFinanceRealtimeUpdates() {
            try {
                const slug = window.location.pathname.replace(/^\//, '');
                if (!slug) {
                    console.warn('Slug tidak ditemukan; realtime finance updates dinonaktifkan');
                    return;
                }

                const subscribe = () => {
                    try {
                        window.Echo.channel(`masjid-${slug}`)
                            .listen('ContentUpdatedEvent', (e) => {
                                // Tangani event untuk pembaruan laporan keuangan
                                if (e && e.type === 'laporan') {
                                    console.log(
                                        'Menerima event laporan; memuat ulang data finance overlay'
                                    );
                                    // Ambil data finance terbaru dari API dan render ke UI
                                    fetchFinanceData();
                                }
                            });
                        console.log(`Subscribed ke channel masjid-${slug} untuk finance overlay`);
                    } catch (err) {
                        console.warn('Gagal subscribe Echo channel (finance):', err);
                    }
                };

                if (window.Echo) {
                    subscribe();
                } else {
                    // Tunggu Echo terinisialisasi oleh Vite (resources/js/app.js)
                    let attempts = 0;
                    const maxAttempts = 20;
                    const interval = setInterval(() => {
                        attempts++;
                        if (window.Echo) {
                            clearInterval(interval);
                            subscribe();
                        } else if (attempts >= maxAttempts) {
                            clearInterval(interval);
                            console.warn(
                                'Echo belum tersedia; realtime finance updates dinonaktifkan');
                        }
                    }, 500);
                }
            } catch (err) {
                console.warn('initFinanceRealtimeUpdates error:', err);
            }
        })();

        // Event handler untuk menghentikan audio adzan saat halaman di-refresh atau ditutup
        // $(window).on('beforeunload', function() {
        //     // Hentikan audio adzan jika sedang diputar
        //     if (window.adzanAudioPlayer) {
        //         window.adzanAudioPlayer.pause();
        //         window.adzanAudioPlayer.currentTime = 0;
        //     }
        // });

        // Tambahkan popup izin audio di bagian atas agar pemutaran sesuai kebijakan browser
        (function setupAudioPermissionPopup() {
            try {

                // Jika tombol lama ada, hapus agar tidak duplikat
                const oldBtn = document.getElementById('start-audio-btn');
                if (oldBtn) oldBtn.remove();

                if (document.getElementById('audio-permission-overlay')) return;


                const overlay = document.createElement('div');
                overlay.id = 'audio-permission-overlay';
                overlay.style.position = 'fixed';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.right = '0';
                overlay.style.zIndex = '10000';
                overlay.style.background = '#fff';
                overlay.style.color = '#003366';
                overlay.style.padding = '12px 16px';
                overlay.style.boxShadow = '0 2px 12px rgba(0,0,0,0.15)';
                overlay.style.borderBottom = '1px solid #e5e7eb';
                overlay.style.display = 'flex';
                overlay.style.alignItems = 'center';
                overlay.style.justifyContent = 'space-between';
                overlay.style.gap = '12px';

                const text = document.createElement('div');
                text.textContent = 'Izinkan pemutaran audio Alarm Waktu Sholat dan Tampilkan Fullscreen.';
                text.style.fontWeight = '600';
                text.style.fontSize = '16px';

                const actions = document.createElement('div');
                actions.style.display = 'flex';
                actions.style.flexDirection = 'column';
                actions.style.alignItems = 'flex-start';
                actions.style.gap = '8px';

                const buttonsRow = document.createElement('div');
                buttonsRow.style.display = 'flex';
                buttonsRow.style.gap = '8px';

                const allowBtn = document.createElement('button');
                allowBtn.type = 'button';
                allowBtn.textContent = 'Izinkan Audio & Fullscreen';
                allowBtn.style.padding = '8px 12px';
                allowBtn.style.borderRadius = '6px';
                allowBtn.style.border = '1px solid #0a3a70';
                allowBtn.style.fontWeight = '600';
                allowBtn.style.background = '#003366';
                allowBtn.style.color = '#fff';
                allowBtn.style.cursor = 'pointer';

                const laterBtn = document.createElement('button');
                laterBtn.type = 'button';
                laterBtn.textContent = 'Nanti';
                laterBtn.style.padding = '8px 12px';
                laterBtn.style.borderRadius = '6px';
                laterBtn.style.border = '1px solid #bbb';

                buttonsRow.appendChild(allowBtn);
                buttonsRow.appendChild(laterBtn);

                overlay.appendChild(text);
                overlay.appendChild(actions);

                // Auto hide dan hitung mundur 15 detik
                let autoHideTimerId = null;
                let countdownTimerId = null;
                let remaining = 15;

                const countdown = document.createElement('div');
                countdown.id = 'audio-permission-countdown';
                countdown.style.fontSize = '14px';
                countdown.style.color = '#555';
                countdown.style.fontFamily = '"JetBrains Mono", monospace';
                countdown.textContent = 'Menutup otomatis dalam 15';
                actions.appendChild(countdown);
                actions.appendChild(buttonsRow);

                function updateCountdown() {
                    countdown.textContent = `Menutup otomatis dalam ${remaining}`;
                }

                const hideOverlay = () => {
                    try {
                        overlay.style.display = 'none';
                    } catch (e) {}
                    if (countdownTimerId) {
                        clearInterval(countdownTimerId);
                        countdownTimerId = null;
                    }
                    if (autoHideTimerId) {
                        clearTimeout(autoHideTimerId);
                        autoHideTimerId = null;
                    }
                    // Hapus overlay dari DOM untuk pembersihan total
                    if (overlay && overlay.parentNode) {
                        overlay.parentNode.removeChild(overlay);
                    }
                };

                countdownTimerId = setInterval(() => {
                    remaining--;
                    if (remaining <= 0) {
                        remaining = 0;
                        updateCountdown();
                        clearInterval(countdownTimerId);
                        countdownTimerId = null;
                    } else {
                        updateCountdown();
                    }
                }, 1000);

                autoHideTimerId = setTimeout(() => {
                    hideOverlay();
                }, 15000);

                // fungsi untuk masuk ke layar fullscreen
                const enterFullscreen = async () => {
                    try {
                        const el = document.documentElement;
                        if (!document.fullscreenElement) {
                            if (el.requestFullscreen) {
                                await el.requestFullscreen();
                            } else if (el.webkitRequestFullscreen) {
                                await el.webkitRequestFullscreen();
                            } else if (el.msRequestFullscreen) {
                                await el.msRequestFullscreen();
                            }
                        }
                    } catch (e) {}
                };

                const unlockAudio = async () => {
                    // Tombol hanya sebagai trigger interaksi: tutup overlay dan masuk fullscreen
                    try {
                        hideOverlay();
                    } catch (e) {}

                    await enterFullscreen();
                };

                const REPROMPT_MS = 60000; // 60 detik
                allowBtn.addEventListener('click', unlockAudio);
                laterBtn.addEventListener('click', hideOverlay);

                document.body.appendChild(overlay);

            } catch (e) {
                console.error('Gagal membuat popup izin audio:', e);
            }
        })();
    });
</script>
