{{-- Moment.js core --}}
<script data-navigate-once src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>
{{-- Moment Hijri --}}
<script data-navigate-once src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.0/moment-hijri.min.js"></script>
{{-- Locale Indonesia --}}
<script data-navigate-once src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

{{-- Laravel Echo and Pusher for WebSocket --}}
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

<script>
    $(document).ready(function() {
        // Tambahkan di awal script
        let userHasInteracted = false;

        // Event listener untuk mendeteksi interaksi user
        $(document).one('click touchstart keydown', function() {
            userHasInteracted = true;
            console.log('User interaction detected - audio autoplay enabled');
        });

        let serverTimestamp = parseInt($('#server-timestamp').val()) || Date.now();
        let pageLoadTimestamp = Date.now();
        let audioPlayer = null;
        let isAudioPlaying = false;
        let audioPlayTimeout = null;
        let lastPlayedAudioIndex = -1;
        let isAudioPausedForAdzan = false;
        let cachedAudioUrls = [];
        let audioRetryCount = 0;
        const MAX_RETRY_ATTEMPTS = 3;

        // Variabel untuk tracking status koneksi dan notifikasi
        let isOffline = false;
        let offlineNotificationShown = false;
        let connectionStatusElement = null;
        const currentMonth = $('#current-month').val() || new Date().getMonth() + 1;
        const currentYear = $('#current-year').val() || new Date().getFullYear();

        // WebSocket connection status
        let wsConnected = false;
        let wsReconnectAttempts = 0;
        const MAX_RECONNECT_ATTEMPTS = 5;

        // Initialize WebSocket connection
        function initializeWebSocket() {
            try {
                // Configure Laravel Echo with Reverb
                window.Echo = new Echo({
                    broadcaster: 'reverb',
                    key: '{{ env("REVERB_APP_KEY") }}',
                    wsHost: '{{ env("REVERB_HOST", "localhost") }}',
                    wsPort: {{ env('REVERB_PORT', 8080) }},
                    wssPort: {{ env('REVERB_PORT', 8080) }},
                    forceTLS: {{ env('REVERB_SCHEME', 'http') === 'https' ? 'true' : 'false' }},
                    enabledTransports: ['ws', 'wss'],
                    disableStats: true,
                });

                // Connection event handlers
                window.Echo.connector.pusher.connection.bind('connected', function() {
                    wsConnected = true;
                    wsReconnectAttempts = 0;
                    console.log('WebSocket connected successfully');
                    showConnectionStatus('Real-time connection established', 'success');
                });

                window.Echo.connector.pusher.connection.bind('disconnected', function() {
                    wsConnected = false;
                    console.log('WebSocket disconnected');
                    showConnectionStatus('Connection lost, attempting to reconnect...', 'warning');
                    attemptReconnect();
                });

                window.Echo.connector.pusher.connection.bind('error', function(error) {
                    console.error('WebSocket error:', error);
                    wsConnected = false;
                    attemptReconnect();
                });

                // Setup channels and listeners
                setupWebSocketChannels();

            } catch (error) {
                console.error('Failed to initialize WebSocket:', error);
                // Fallback to polling if WebSocket fails
                initializePollingFallback();
            }
        }

        function attemptReconnect() {
            if (wsReconnectAttempts < MAX_RECONNECT_ATTEMPTS) {
                wsReconnectAttempts++;
                const delay = Math.min(1000 * Math.pow(2, wsReconnectAttempts), 30000);
                console.log(`Attempting WebSocket reconnection ${wsReconnectAttempts}/${MAX_RECONNECT_ATTEMPTS} in ${delay}ms`);
                
                setTimeout(() => {
                    try {
                        window.Echo.disconnect();
                        initializeWebSocket();
                    } catch (error) {
                        console.error('Reconnection failed:', error);
                        if (wsReconnectAttempts >= MAX_RECONNECT_ATTEMPTS) {
                            console.log('Max reconnection attempts reached, falling back to polling');
                            initializePollingFallback();
                        }
                    }
                }, delay);
            }
        }

        function setupWebSocketChannels() {
            const slug = window.location.pathname.replace(/^\//, '') || 'default';

            // Server time updates
            window.Echo.channel('server-updates')
                .listen('.server.time.updated', (data) => {
                    console.log('Server time updated via WebSocket:', data);
                    if (data.server_time) {
                        const latency = 50; // Estimate WebSocket latency
                        serverTimestamp = parseInt(data.server_time) + latency;
                        pageLoadTimestamp = Date.now();
                    }
                });

            // Audio updates
            window.Echo.channel('audio-updates')
                .listen('.audio.updated', (data) => {
                    console.log('Audio updated via WebSocket:', data);
                    if (data.audio) {
                        handleAudioUpdate(data.audio);
                    }
                });

            // Content updates (marquee, slides, jumbotron, petugas)
            window.Echo.channel('content-updates')
                .listen('.content.updated', (data) => {
                    console.log('Content updated via WebSocket:', data);
                    handleContentUpdate(data.type, data.data);
                });

            // Adzan updates
            window.Echo.channel('adzan-updates')
                .listen('.adzan.updated', (data) => {
                    console.log('Adzan updated via WebSocket:', data);
                    if (data.adzan) {
                        handleAdzanUpdate(data.adzan);
                    }
                });

            // Profile updates
            window.Echo.channel('profile-updates')
                .listen('.profile.updated', (data) => {
                    console.log('Profile updated via WebSocket:', data);
                    if (data.profile) {
                        handleProfileUpdate(data.profile);
                    }
                });
        }

        // WebSocket event handlers
        function handleAudioUpdate(audioData) {
            // Update audio URLs and trigger audio update
            if (audioData.urls && Array.isArray(audioData.urls)) {
                cachedAudioUrls = audioData.urls;
                updateAndPlayAudio();
            }
        }

        function handleContentUpdate(type, data) {
            switch (type) {
                case 'marquee':
                    if (data.text) {
                        updateMarqueeTextFromWebSocket(data.text);
                    }
                    break;
                case 'slides':
                    if (data.slides) {
                        updateSlidesFromWebSocket(data.slides);
                    }
                    break;
                case 'jumbotron':
                    if (data.jumbotron) {
                        updateJumbotronFromWebSocket(data.jumbotron);
                    }
                    break;
                case 'petugas':
                    if (data.petugas) {
                        updatePetugasFromWebSocket(data.petugas);
                    }
                    break;
            }
        }

        function handleAdzanUpdate(adzanData) {
            // Update adzan images and related content
            if (adzanData.images) {
                updateAdzanImagesFromWebSocket(adzanData.images);
            }
            if (adzanData.iqomah_images) {
                updateIqomahImagesFromWebSocket(adzanData.iqomah_images);
            }
        }

        function handleProfileUpdate(profileData) {
            // Update mosque information
            if (profileData.mosque_info) {
                updateMosqueInfoFromWebSocket(profileData.mosque_info);
            }
        }

        // Fallback polling functions (simplified versions of original AJAX calls)
        function initializePollingFallback() {
            console.log('Initializing polling fallback...');
            showConnectionStatus('Using fallback mode (polling)', 'warning');
            
            // Reduced polling intervals for fallback
            setInterval(syncServerTime, 30000); // Every 30 seconds
            setInterval(updateAndPlayAudio, 60000); // Every minute
            setInterval(updateMarqueeText, 45000); // Every 45 seconds
            setInterval(updateSlides, 60000); // Every minute
            setInterval(updateJumbotronData, 45000); // Every 45 seconds
            setInterval(updateFridayOfficials, 60000); // Every minute
            setInterval(updateMosqueInfo, 120000); // Every 2 minutes
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
                background: ${type === 'warning' ? '#ff9800' : type === 'success' ? '#4caf50' : '#2196f3'};
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

            // Auto-hide setelah 5 detik untuk notifikasi success
            if (type === 'success') {
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
                    console.warn('Server time sync failed, using local time');
                    if (callback) callback();
                }
            });
        }