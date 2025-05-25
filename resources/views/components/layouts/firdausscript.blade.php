<!-- Moment.js core -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>

<!-- Moment Hijri -->
<script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.0/moment-hijri.min.js"></script>

<!-- Locale Indonesia -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
    // Fungsi untuk memperbarui informasi masjid secara realtime
    function updateMosqueInfo() {
        // Ambil slug dari URL
        const slug = window.location.pathname.replace(/^\//, '');

        // Pastikan jQuery tersedia dan bukan versi slim
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
                    // Perbarui nama dan alamat masjid
                    $('.mosque-name-highlight').text(response.data.name);
                    $('.mosque-address').text(response.data.address);

                    // Perbarui logo masjid dan pemerintah
                    $('.logo-container').empty(); // Hapus logo yang ada

                    // Tambahkan logo masjid jika ada
                    if (response.data.logo_masjid) {
                        $('.logo-container').append(
                            `<img src="${response.data.logo_masjid}" alt="Logo Masjid" class="logo logo-masjid">`
                        );
                    }

                    // Tambahkan logo pemerintah jika ada
                    if (response.data.logo_pemerintah) {
                        $('.logo-container').append(
                            `<img src="${response.data.logo_pemerintah}" alt="Logo Pemerintah" class="logo logo-pemerintah">`
                        );
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error saat mengambil data profil masjid:', error);
            }
        });
    }

    $(document).ready(function() {
        // Ambil waktu server dari input hidden
        let serverTimestamp = parseInt($('#server-timestamp').val());
        let pageLoadTimestamp = Date.now();
        const currentMonth = $('#current-month').val() || new Date().getMonth() + 1;
        const currentYear = $('#current-year').val() || new Date().getFullYear();

        // Inisialisasi pembaruan informasi masjid
        updateMosqueInfo();

        // Get active prayer time status if available
        let activePrayerStatus = null;
        if ($('#active-prayer-status').val()) {
            try {
                activePrayerStatus = JSON.parse($('#active-prayer-status').val());
                console.log('Active prayer status detected:', activePrayerStatus);
            } catch (e) {
                console.error('Error parsing active prayer status:', e);
            }
        }

        // Fungsi untuk ambil waktu sekarang berdasarkan waktu server
        function getCurrentTimeFromServer() {
            const elapsed = Date.now() - pageLoadTimestamp;
            return new Date(serverTimestamp + elapsed);
        }

        // Fungsi untuk memperbarui waktu server secara berkala
        function fetchServerTime() {
            $.ajax({
                url: '/server-time',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Perbarui timestamp server dan waktu halaman dimuat
                        serverTimestamp = response.data.timestamp * 1000;
                        pageLoadTimestamp = Date.now();
                        console.log('Server time updated:', new Date(serverTimestamp));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching server time:', error);
                }
            });
        }

        // Mendapatkan jadwal sholat secara dinamis jika diperlukan
        async function fetchPrayerTimes() {
            try {
                const now = getCurrentTimeFromServer();
                const month = now.getMonth() + 1;
                const year = now.getFullYear();
                const monthFormatted = month.toString().padStart(2, '0');
                const url =
                    `https://raw.githubusercontent.com/lakuapik/jadwalsholatorg/master/adzan/pekanbaru/${year}/${monthFormatted}.json`;

                if (month !== parseInt(currentMonth) || year !== parseInt(currentYear)) {
                    console.log(`Mengambil data jadwal baru: ${url}`);
                    const response = await $.ajax({
                        url,
                        method: 'GET'
                    });
                    
                    // Update data tanpa refresh halaman
                    console.log("Data bulan baru tersedia, memperbarui data di background...");
                    
                    // Update current month dan year
                    currentMonth = month;
                    currentYear = year;
                    
                    // Update prayer times dengan data baru
                    if (response && response[now.getDate()]) {
                        const todayPrayer = response[now.getDate()];
                        // Update prayer times display
                        updatePrayerTimesDisplay(todayPrayer);
                    }
                    
                    // Update background data
                    updateDataInBackground();
                    
                    return response;
                }
                return null;
            } catch (error) {
                console.error("Error saat mengambil jadwal sholat:", error);
                return null;
            }
        }

        // Fungsi baru untuk update prayer times display
        function updatePrayerTimesDisplay(prayerData) {
            // Update hidden inputs dengan data baru
            $('#current-month').val(currentMonth);
            $('#current-year').val(currentYear);
            
            // Update prayer times di UI
            const prayerNames = ['shubuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
            prayerNames.forEach(prayer => {
                if (prayerData[prayer]) {
                    $(`.prayer-time[data-prayer="${prayer}"]`).text(prayerData[prayer]);
                }
            });
            
            console.log('Prayer times display updated');
        }

        // Canvas-based Analog Clock
        const $canvas = $('#analogClock');
        const ctx = $canvas[0].getContext('2d');
        let clockRadius = $canvas[0].width / 2 - 10;
        let clockCenter = {
            x: $canvas[0].width / 2,
            y: $canvas[0].height / 2
        };

        function drawClock() {
            ctx.clearRect(0, 0, $canvas[0].width, $canvas[0].height);
            const now = getCurrentTimeFromServer();
            const hours = now.getHours() % 12;
            const minutes = now.getMinutes();
            const seconds = now.getSeconds();
            const milliseconds = now.getMilliseconds();

            ctx.save();
            ctx.beginPath();
            ctx.arc(clockCenter.x, clockCenter.y, clockRadius, 0, Math.PI * 2);
            ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
            ctx.shadowBlur = 15;
            ctx.shadowOffsetX = 5;
            ctx.shadowOffsetY = 5;
            ctx.fillStyle = '#003366';
            ctx.fill();
            ctx.shadowColor = 'transparent';
            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;

            ctx.beginPath();
            ctx.arc(clockCenter.x, clockCenter.y, clockRadius, 0, Math.PI * 2);
            ctx.strokeStyle = '#0055a4';
            ctx.lineWidth = 15;
            ctx.stroke();

            for (let i = 0; i < 12; i++) {
                const angle = (i * Math.PI / 6) - Math.PI / 2;
                const tickStart = clockRadius - 20;
                const tickEnd = clockRadius - 5;

                ctx.beginPath();
                ctx.moveTo(clockCenter.x + Math.cos(angle) * tickStart, clockCenter.y + Math.sin(angle) *
                    tickStart);
                ctx.lineTo(clockCenter.x + Math.cos(angle) * tickEnd, clockCenter.y + Math.sin(angle) *
                    tickEnd);
                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = 4;
                ctx.stroke();

                const numRadius = clockRadius - 40;
                const numX = clockCenter.x + Math.cos(angle) * numRadius;
                const numY = clockCenter.y + Math.sin(angle) * numRadius;

                ctx.shadowColor = 'rgba(0, 0, 0, 0.7)';
                ctx.shadowBlur = 5;
                ctx.shadowOffsetX = 2;
                ctx.shadowOffsetY = 2;

                ctx.fillStyle = i === 0 ? '#ff0000' : '#ffffff';
                ctx.font = 'bold 30px Poppins';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText((i === 0 ? 12 : i).toString(), numX, numY);

                ctx.shadowColor = 'transparent';
                ctx.shadowBlur = 0;
                ctx.shadowOffsetX = 0;
                ctx.shadowOffsetY = 0;
            }

            for (let i = 0; i < 60; i++) {
                if (i % 5 !== 0) {
                    const angle = (i * Math.PI / 30) - Math.PI / 2;
                    const tickStart = clockRadius - 10;
                    const tickEnd = clockRadius - 5;

                    ctx.beginPath();
                    ctx.moveTo(clockCenter.x + Math.cos(angle) * tickStart, clockCenter.y + Math.sin(angle) *
                        tickStart);
                    ctx.lineTo(clockCenter.x + Math.cos(angle) * tickEnd, clockCenter.y + Math.sin(angle) *
                        tickEnd);
                    ctx.strokeStyle = 'rgba(255, 255, 255, 0.6)';
                    ctx.lineWidth = 2;
                    ctx.stroke();
                }
            }

            const hourAngle = ((hours + minutes / 60) * Math.PI / 6) - Math.PI / 2;
            const minuteAngle = ((minutes + seconds / 60) * Math.PI / 30) - Math.PI / 2;
            const secondAngle = ((seconds + milliseconds / 1000) * Math.PI / 30) - Math.PI / 2;

            ctx.shadowColor = 'rgba(0, 0, 0, 0.6)';
            ctx.shadowBlur = 8;
            ctx.shadowOffsetX = 3;
            ctx.shadowOffsetY = 3;
            drawHand(hourAngle, clockRadius * 0.5, 8, '#ffffff');
            drawHand(minuteAngle, clockRadius * 0.7, 5, '#ffffff');

            ctx.shadowColor = 'rgba(0, 0, 0, 0.4)';
            ctx.shadowBlur = 6;
            ctx.shadowOffsetX = 2;
            ctx.shadowOffsetY = 2;
            drawHand(secondAngle, clockRadius * 0.85, 2, '#ff0000');

            ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
            ctx.shadowBlur = 5;
            ctx.shadowOffsetX = 1;
            ctx.shadowOffsetY = 1;
            ctx.beginPath();
            ctx.arc(clockCenter.x, clockCenter.y, 8, 0, Math.PI * 2);
            ctx.fillStyle = '#ff0000';
            ctx.fill();

            ctx.restore();

            const $clockText = $('.clock-text');
            if ($clockText.length) {
                const displayHours = now.getHours();
                $clockText.html(
                    `${displayHours.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}<br>KOMINFO`
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

        function updateDate() {
            const $dateElement = $('.date-item');
            const now = getCurrentTimeFromServer();

            if (typeof moment !== 'undefined') {
                moment.locale('id');
                const hari = moment(now).format('dddd');
                const tanggalMasehi = moment(now).format('D MMMM YYYY');
                const masehi = `<span class="day-name">${hari}</span>, ${tanggalMasehi}`;

                if (typeof moment().iDate === 'function') {
                    const hijriDate = moment(now).iDate();
                    const hijriMonth = moment(now).iMonth();
                    const hijriYear = moment(now).iYear();
                    const bulanHijriyahID = [
                        'Muharam', 'Safar', 'Rabiulawal', 'Rabiulakhir', 'Jumadilawal', 'Jumadilakhir',
                        'Rajab', 'Syaban', 'Ramadhan', 'Syawal', 'Zulkaidah', 'Zulhijah'
                    ];
                    const hijri = `${hijriDate} ${bulanHijriyahID[hijriMonth]} ${hijriYear}H`;
                    if ($dateElement.length) {
                        $dateElement.html(`${masehi} / ${hijri}`);
                    }
                } else {
                    if ($dateElement.length) {
                        $dateElement.html(masehi);
                        console.warn("moment-hijri tidak tersedia");
                    }
                }
            } else {
                console.warn("moment.js tidak tersedia");
            }

            if (now.getHours() === 0 && now.getMinutes() <= 5) {
                const currentMonthNow = now.getMonth() + 1;
                const storedMonth = parseInt(localStorage.getItem('lastCheckedMonth') || '0');
                if (currentMonthNow !== storedMonth) {
                    console.log("Bulan berubah, memperbarui jadwal sholat");
                    localStorage.setItem('lastCheckedMonth', currentMonthNow);
                    fetchPrayerTimes();
                }
            }
        }

        function updateScrollingText(marqueeData) {
            const $scrollingTextElement = $('.scrolling-text');

            // Simpan posisi animasi saat ini
            let currentPosition = 0;
            if ($scrollingTextElement.length) {
                const computedStyle = window.getComputedStyle($scrollingTextElement[0]);
                const transform = computedStyle.getPropertyValue('transform');

                if (transform && transform !== 'none') {
                    // Ekstrak nilai translateX dari matrix transform
                    const matrix = transform.match(/matrix\((.*?)\)/);
                    if (matrix) {
                        const values = matrix[1].split(', ');
                        currentPosition = parseFloat(values[4]); // Nilai translateX dalam matrix
                    }
                }
            }

            // Gunakan data dari parameter jika tersedia, jika tidak gunakan nilai input hidden
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

            // Hentikan animasi sementara
            $scrollingTextElement.css('animation', 'none');

            // Update konten
            $scrollingTextElement.html(`<p>${combinedText}</p>`);

            // Force reflow untuk menerapkan perubahan
            if ($scrollingTextElement[0]) {
                $scrollingTextElement[0].offsetHeight;
            }

            // Hitung durasi animasi berdasarkan panjang teks
            // Semakin panjang teks, semakin lama durasinya
            const textLength = combinedText.length;
            const baseDuration = 60; // Durasi dasar dalam detik
            const calculatedDuration = Math.max(baseDuration, textLength / 10); // Minimal 60 detik

            // Hitung persentase progres animasi saat ini
            const containerWidth = $scrollingTextElement.parent().width();
            const textWidth = $scrollingTextElement.find('p').width();
            const totalDistance = textWidth + containerWidth;

            // Jika posisi saat ini adalah 0, gunakan offset waktu server
            let animationProgress;
            if (currentPosition === 0) {
                const now = getCurrentTimeFromServer();
                const currentSeconds = now.getSeconds();
                const currentMilliseconds = now.getMilliseconds();
                const totalOffset = (currentSeconds * 1000 + currentMilliseconds) % 60000;
                animationProgress = totalOffset / 60000; // Persentase dari 1 menit
            } else {
                // Jika tidak, hitung berdasarkan posisi translateX saat ini
                animationProgress = Math.abs(currentPosition) / totalDistance;
            }

            // Terapkan animasi dengan posisi yang dipertahankan
            $scrollingTextElement.css({
                'animation': `scrollText ${calculatedDuration}s linear infinite`,
                'animation-delay': `-${animationProgress * calculatedDuration}s`
            });
        }

        // Pastikan jQuery tersedia dan bukan versi slim
        function updateMarqueeText() {
            // Ambil slug dari URL
            const slug = window.location.pathname.replace(/^\//, '');

            // Pastikan jQuery tersedia dan bukan versi slim
            if (typeof $.ajax === 'undefined') {
                console.error('jQuery AJAX tidak tersedia. Gunakan versi jQuery lengkap, bukan slim.');
                return;
            }

            console.log('Memperbarui teks marquee untuk slug:', slug); // Tambahkan log

            $.ajax({
                url: `/api/marquee/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Respons API marquee:', response); // Tambahkan log
                    if (response.success) {
                        // Perbarui nilai input hidden untuk marquee
                        $('#marquee1').val(response.data.marquee1);
                        $('#marquee2').val(response.data.marquee2);
                        $('#marquee3').val(response.data.marquee3);
                        $('#marquee4').val(response.data.marquee4);
                        $('#marquee5').val(response.data.marquee5);
                        $('#marquee6').val(response.data.marquee6);

                        // Perbarui tampilan teks marquee dengan data dari API
                        updateScrollingText(response.data);
                        console.log('Teks marquee diperbarui'); // Tambahkan log
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
        let isShuruqAlarmPlaying = false;
        let shuruqAlarmTimeout = null;

        let currentPrayerStatus = null;
        let isAdzanActive = false;
        let isIqomahActive = false;
        let isFinalActive = false;
        let isFridayActive = false;

        let adzanStartTime = localStorage.getItem('adzanStartTime') ? parseInt(localStorage.getItem(
            'adzanStartTime')) : null;
        let iqomahStartTime = localStorage.getItem('iqomahStartTime') ? parseInt(localStorage.getItem(
            'iqomahStartTime')) : null;
        let currentPrayerName = localStorage.getItem('currentPrayerName') || null;
        let currentPrayerTime = localStorage.getItem('currentPrayerTime') || null;

        function fetchPrayerStatus() {
            const slug = window.location.pathname.replace(/^\//, ''); // Fixed: remove leading slash

            $.ajax({
                url: `/api/prayer-status/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        handlePrayerStatusUpdate(response.data);
                    } else {
                        // No active prayer status
                        clearAllPrayerStates();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching prayer status:', error);
                }
            });
        }

        // Handle prayer status updates
        function handlePrayerStatusUpdate(status) {
            const {
                phase,
                prayerName,
                prayerTime,
                isFriday
            } = status;

            // Clear previous states
            clearAllPrayerStates();

            currentPrayerStatus = status;

            switch (phase) {
                case 'adzan':
                    if (!isAdzanActive) {
                        isAdzanActive = true;
                        showAdzanPopup(prayerName, prayerTime);
                    }
                    break;

                case 'iqomah':
                    if (!isIqomahActive) {
                        isIqomahActive = true;
                        showIqomahPopup(prayerTime);
                    }
                    break;

                case 'final':
                    if (!isFinalActive) {
                        isFinalActive = true;
                        showFinalAdzanImage();
                    }
                    break;
            }

            // Handle Friday prayer info
            if (isFriday && !isFridayActive) {
                isFridayActive = true;
                showFridayInfo();
            }
        }

        // Clear all prayer states
        function clearAllPrayerStates() {
            isAdzanActive = false;
            isIqomahActive = false;
            isFinalActive = false;
            isFridayActive = false;

            // Hide all popups
            $('#adzanPopup').css('display', 'none');
            $('#iqomahPopup').css('display', 'none');
            $('#adzanImageDisplay').css('display', 'none');
            $('#fridayInfoPopup').css('display', 'none');
        }

        function checkAndRestoreSessions() {
            const now = getCurrentTimeFromServer().getTime();

            if (activePrayerStatus) {
                console.log('Processing active prayer status from server');
                const {
                    phase,
                    prayerName,
                    prayerTime,
                    elapsedSeconds,
                    remainingSeconds,
                    progress,
                    isFriday
                } = activePrayerStatus;

                if (phase === 'adzan') {
                    console.log(
                        `Active adzan phase detected for ${prayerName}, elapsed: ${elapsedSeconds}s, remaining: ${remainingSeconds}s`
                    );
                    adzanStartTime = now - (elapsedSeconds * 1000);
                    localStorage.setItem('adzanStartTime', adzanStartTime);
                    localStorage.setItem('currentPrayerName', prayerName);
                    localStorage.setItem('currentPrayerTime', prayerTime);
                    currentPrayerName = prayerName;
                    currentPrayerTime = prayerTime;
                    showAdzanPopup(prayerName, prayerTime, true);
                    return;
                } else if (phase === 'iqomah') {
                    console.log(
                        `Active iqomah phase detected for ${prayerName}, elapsed: ${elapsedSeconds}s, remaining: ${remainingSeconds}s`
                    );
                    adzanStartTime = now - (elapsedSeconds * 1000);
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
                    console.log(`Final adzan image phase detected for ${prayerName}`);
                    showFinalAdzanImage();
                    return;
                }

                if (isFriday) {
                    console.log('Friday prayer info detected');
                    const now = getCurrentTimeFromServer();
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
                const adzanElapsedSeconds = (now - adzanStartTime) / 1000;
                const adzanDuration = 3 * 60;

                if (adzanElapsedSeconds < adzanDuration) {
                    console.log('Restoring Adzan session from localStorage');
                    showAdzanPopup(currentPrayerName, currentPrayerTime, true);
                } else {
                    console.log('Adzan finished, starting Iqomah from localStorage');
                    iqomahStartTime = adzanStartTime + (adzanDuration * 1000);
                    localStorage.setItem('iqomahStartTime', iqomahStartTime);
                    showIqomahPopup(currentPrayerTime, true);
                }
            } else if (iqomahStartTime) {
                const iqomahElapsedSeconds = (now - iqomahStartTime) / 1000;
                const iqomahDuration = 420;

                if (iqomahElapsedSeconds < iqomahDuration) {
                    console.log('Restoring Iqomah session from localStorage');
                    showIqomahPopup(currentPrayerTime, true);
                } else {
                    console.log('Iqomah finished, clearing state');
                    clearAdzanState();
                }
            }
        }

        function clearAdzanState() {
            adzanStartTime = null;
            iqomahStartTime = null;
            isAdzanPlaying = false;
            localStorage.removeItem('adzanStartTime');
            localStorage.removeItem('iqomahStartTime');
            localStorage.removeItem('currentPrayerName');
            localStorage.removeItem('currentPrayerTime');
            localStorage.removeItem('iqomahSliderStartTime');

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
        }

        function clearAllAdzanStates() {
            const keysToRemove = [
                'adzanStartTime', 'iqomahStartTime', 'currentPrayerName',
                'currentPrayerTime', 'jumatAdzanShown', 'shuruqAlarmTime',
                'fridayInfoStartTime', 'adzanImageStartTime'
            ];
            keysToRemove.forEach(key => localStorage.removeItem(key));
        }

        // Fungsi untuk memperbarui data tanpa refresh halaman
        function updateDataInBackground() {
            // Update prayer times
            fetchPrayerStatus();
            
            // Update mosque info
            updateMosqueInfo();
            
            // Update marquee text
            updateMarqueeText();
            
            // Update slides
            updateSlides();
            
            // Update Friday officials
            updateFridayOfficials();
            
            // Update images
            updateFridayImages();
            updateIqomahImages();
            
            console.log('Data updated in background without page refresh');
        }

        function checkDayChange() {
            const now = getCurrentTimeFromServer();
            const currentDate = `${now.getFullYear()}-${now.getMonth() + 1}-${now.getDate()}`;
            const storedDate = localStorage.getItem('lastCheckedDate');
            console.log('Checking day change...', currentDate, storedDate);
            if (currentDate !== storedDate) {
                clearAllAdzanStates();
                localStorage.setItem('lastCheckedDate', currentDate);
                
                // Ganti location.reload() dengan background update
                updateDataInBackground();
                
                // Update prayer times untuk hari baru
                fetchPrayerTimes();
            }
        }

        function clearShuruqAlarmState() {
            isShuruqAlarmPlaying = false;
            localStorage.removeItem('shuruqAlarmTime');
            if (shuruqAlarmTimeout) {
                clearTimeout(shuruqAlarmTimeout);
                shuruqAlarmTimeout = null;
            }
        }

        function playBeepSound(times = 1) {
            let count = 0;
            const play = () => {
                beepSound.play();
                count++;
                if (count < times) {
                    setTimeout(play, 1000);
                }
            };
            play();
        }

        function calculateSyncStartTime(prayerTimeStr) {
            const serverTime = getCurrentTimeFromServer(); // waktu server
            const [prayerHours, prayerMinutes] = prayerTimeStr.split(':').map(Number);
            const prayerTime = new Date(serverTime);
            prayerTime.setHours(prayerHours, prayerMinutes, 0, 0);

            const timeDiff = serverTime - prayerTime;

            if (timeDiff >= 0 && timeDiff <= 10 * 60 * 1000) {
                return prayerTime.getTime(); // semua mulai dari waktu salat
            } else if (timeDiff < 0) {
                return prayerTime.getTime(); // masih sebelum waktunya
            } else {
                return null; // sudah lewat 10 menit, tidak tampilkan countdown
            }
        }

        function showAdzanPopup(prayerName, prayerTimeStr, isRestored = false) {
            const now = getCurrentTimeFromServer();
            const currentDate = now.getDate();
            const serverMonth = now.getMonth() + 1;
            const serverYear = now.getFullYear();
            const scheduleMonth = parseInt($('#current-month').val());
            const scheduleYear = parseInt($('#current-year').val());

            if (scheduleMonth !== serverMonth || scheduleYear !== serverYear) {
                console.log('Jadwal tidak sesuai dengan tanggal server, memperbarui data di background...');
                
                // Ganti location.reload() dengan background update
                updateDataInBackground();
                fetchPrayerTimes();
                return;
            }

            const $popup = $('#adzanPopup');
            const $title = $('#adzanTitle');
            const $progress = $('#adzanProgress');
            const $countdown = $('#adzanCountdown');

            $title.text(` ${prayerName}`);
            $popup.css('display', 'flex');

            if (!isRestored) {
                playBeepSound(3);
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
            }

            const duration = 3 * 60;
            let lastUpdateTime = getCurrentTimeFromServer().getTime();
            isAdzanPlaying = true;

            function updateAdzan(timestamp) {
                const currentTime = getCurrentTimeFromServer().getTime();
                const elapsedSeconds = (currentTime - adzanStartTime) / 1000;
                const timeLeft = duration - elapsedSeconds;

                if (timeLeft <= 0) {
                    $popup.css('display', 'none');
                    isAdzanPlaying = false;
                    if (prayerName === "Jum'at") {
                        clearAdzanState();
                    } else {
                        showIqomahPopup(prayerTimeStr);
                    }
                    return;
                }

                $progress.css({
                    width: `${(elapsedSeconds / duration) * 100}%`,
                    transition: 'width 0.3s linear'
                });

                if (currentTime - lastUpdateTime >= 1000) {
                    const hours = Math.floor(timeLeft / 3600);
                    const minutes = Math.floor((timeLeft % 3600) / 60);
                    const seconds = Math.floor(timeLeft % 60);
                    $countdown.text(
                        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );
                    lastUpdateTime = currentTime;
                }

                requestAnimationFrame(updateAdzan);
            }

            requestAnimationFrame(updateAdzan);
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

            console.log('Memperbarui gambar Iqomah untuk slug:', slug);

            $.ajax({
                url: `/api/adzan/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Respons API adzan untuk Iqomah:', response);
                    if (response.success) {
                        // Simpan nilai sebelumnya untuk perbandingan
                        const previousAdzan = [];
                        for (let i = 1; i <= 6; i++) {
                            previousAdzan.push($(`#adzan${i}`).val() || '');
                        }

                        // Perbarui nilai input hidden untuk adzan
                        $('#adzan1').val(response.data.adzan1);
                        $('#adzan2').val(response.data.adzan2);
                        $('#adzan3').val(response.data.adzan3);
                        $('#adzan4').val(response.data.adzan4);
                        $('#adzan5').val(response.data.adzan5);
                        $('#adzan6').val(response.data.adzan6);

                        // Perbarui array iqomahImages global
                        window.iqomahImages = ['/images/other/doa-setelah-azan.png'];
                        for (let i = 1; i <= 6; i++) {
                            const adzanValue = $(`#adzan${i}`).val();
                            if (adzanValue) {
                                window.iqomahImages.push(adzanValue);
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
            // Gunakan variabel global untuk iqomahImages agar bisa diakses dari updateIqomahImages()
            window.iqomahImages = ['/images/other/doa-setelah-azan.png'];
            for (let i = 1; i <= 6; i++) {
                const adzanElement = $(`#adzan${i}`);
                if (adzanElement.val()) {
                    window.iqomahImages.push(adzanElement.val());
                }
            }

            const $iqomahImageElement = $('#currentIqomahImage');

            if (!iqomahSliderStartTime) {
                iqomahSliderStartTime = getCurrentTimeFromServer().getTime();
                localStorage.setItem('iqomahSliderStartTime', iqomahSliderStartTime);
            }

            // Variabel untuk melacak indeks gambar saat ini
            let lastIndex = -1;

            // Fungsi untuk memperbarui gambar berdasarkan waktu server
            function updateIqomahImage() {
                if (!window.iqomahImages || window.iqomahImages.length === 0) return;

                const now = getCurrentTimeFromServer().getTime();
                const elapsedMs = now - iqomahSliderStartTime;
                const elapsedSeconds = Math.floor(elapsedMs / 1000);
                const currentIndex = Math.floor(elapsedSeconds / 10) % window.iqomahImages.length;

                // Hanya perbarui gambar jika indeks berubah
                if (currentIndex !== lastIndex) {
                    lastIndex = currentIndex;

                    // Transisi mulus dengan opacity
                    $iqomahImageElement.css('opacity', '0');
                    setTimeout(() => {
                        $iqomahImageElement.attr('src', window.iqomahImages[currentIndex]);
                        $iqomahImageElement.css('opacity', '1');
                    }, 250);
                }
            }

            // Update gambar pertama kali
            updateIqomahImage();

            // Set interval untuk update gambar setiap 1 detik untuk sinkronisasi yang lebih baik
            if (iqomahImageSliderInterval) {
                clearInterval(iqomahImageSliderInterval);
            }
            iqomahImageSliderInterval = setInterval(updateIqomahImage, 1000);
        }

        function showIqomahPopup(prayerTimeStr, isRestored = false) {
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
                    iqomahStartTime = adzanStartTime + (3 * 60 * 1000);
                }
                localStorage.setItem('iqomahStartTime', iqomahStartTime);
            }

            const duration = 420;
            let lastUpdateTime = getCurrentTimeFromServer().getTime();
            let isIqomahPlaying = true;
            let hasPlayedFinalBeep = false;

            function updateIqomah() {
                if (!isIqomahPlaying) return;

                const currentTime = getCurrentTimeFromServer().getTime();
                const elapsedSeconds = (currentTime - iqomahStartTime) / 1000;
                const timeLeft = duration - elapsedSeconds;

                if (timeLeft <= 4 && !hasPlayedFinalBeep) {
                    playBeepSound(3);
                    hasPlayedFinalBeep = true;
                }

                if (timeLeft <= 0) {
                    $popup.css('display', 'none');
                    isIqomahPlaying = false;
                    clearAdzanState();
                    showFinalAdzanImage();
                    return;
                }

                $progress.css({
                    width: `${(elapsedSeconds / duration) * 100}%`,
                    transition: 'width 0.3s linear'
                });

                if (currentTime - lastUpdateTime >= 1000) {
                    const hours = Math.floor(timeLeft / 3600);
                    const minutes = Math.floor((timeLeft % 3600) / 60);
                    const seconds = Math.floor(timeLeft % 60);
                    $countdown.text(
                        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );
                    lastUpdateTime = currentTime;
                }

                requestAnimationFrame(updateIqomah);
            }

            requestAnimationFrame(updateIqomah);

            return function cancelIqomah() {
                isIqomahPlaying = false;
                $popup.css('display', 'none');
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
                console.log('Restoring Adzan final image');
                displayAdzanImage(adzanImageSrc, true);
            } else if (adzanImageEndTime && now >= adzanImageEndTime) {
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
        }

        function displayAdzanImage(imageSrc, isRestored = false) {
            const $imageDisplay = $('#adzanImageDisplay');
            const $imageElement = $('#currentAdzanImage');

            $imageElement.attr('src', imageSrc);
            $imageDisplay.css('display', 'flex');

            if (!isRestored) {
                const now = getCurrentTimeFromServer().getTime();
                adzanImageStartTime = now;
                adzanImageEndTime = now + 60000;
                adzanImageSrc = imageSrc;

                localStorage.setItem('adzanImageStartTime', adzanImageStartTime);
                localStorage.setItem('adzanImageEndTime', adzanImageEndTime);
                localStorage.setItem('adzanImageSrc', imageSrc);
            }

            const remainingTime = adzanImageEndTime - getCurrentTimeFromServer().getTime();
            if (remainingTime > 0) {
                setTimeout(() => {
                    $imageDisplay.css('display', 'none');
                    clearAdzanImageState();
                }, remainingTime);
            } else {
                $imageDisplay.css('display', 'none');
                clearAdzanImageState();
            }
        }

        function showFinalAdzanImage() {
            if (adzanImageStartTime && adzanImageEndTime) {
                const currentTime = getCurrentTimeFromServer().getTime();
                if (currentTime < adzanImageEndTime) {
                    return;
                }
            }

            const $adzan15 = $('#adzan15');
            if ($adzan15.val()) {
                displayAdzanImage($adzan15.val());
            }
        }

        let fridayInfoStartTime = localStorage.getItem('fridayInfoStartTime') ? parseInt(localStorage.getItem(
            'fridayInfoStartTime')) : null;
        let fridayInfoEndTime = localStorage.getItem('fridayInfoEndTime') ? parseInt(localStorage.getItem(
            'fridayInfoEndTime')) : null;
        let fridayInfoData = localStorage.getItem('fridayInfoData') ? JSON.parse(localStorage.getItem(
            'fridayInfoData')) : null;
        let fridayImageSliderInterval = null;
        let fridaySliderStartTime = localStorage.getItem('fridaySliderStartTime') ? parseInt(localStorage
            .getItem('fridaySliderStartTime')) : null;

        function checkAndRestoreFridayInfo() {
            const now = getCurrentTimeFromServer().getTime();
            if (fridayInfoStartTime && fridayInfoEndTime && now >= fridayInfoStartTime && now <
                fridayInfoEndTime) {
                console.log('Restoring Friday info popup');
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
            localStorage.removeItem('fridayInfoStartTime');
            localStorage.removeItem('fridayInfoEndTime');
            localStorage.removeItem('fridayInfoData');
            localStorage.removeItem('fridaySliderStartTime');

            if (fridayImageSliderInterval) {
                clearInterval(fridayImageSliderInterval);
                fridayImageSliderInterval = null;
            }
        }

        function updateFridayImages() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                console.error('Tidak dapat menentukan slug dari URL');
                return;
            }

            console.log('Memperbarui gambar Friday untuk slug:', slug);

            $.ajax({
                url: `/api/adzan/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Respons API adzan:', response);
                    if (response.success) {
                        // Simpan nilai sebelumnya untuk perbandingan
                        const previousAdzan = [];
                        for (let i = 1; i <= 6; i++) {
                            previousAdzan.push($(`#adzan${i}`).val() || '');
                        }

                        // Perbarui nilai input hidden untuk adzan
                        $('#adzan1').val(response.data.adzan1);
                        $('#adzan2').val(response.data.adzan2);
                        $('#adzan3').val(response.data.adzan3);
                        $('#adzan4').val(response.data.adzan4);
                        $('#adzan5').val(response.data.adzan5);
                        $('#adzan6').val(response.data.adzan6);

                        // Perbarui array fridayImages global
                        window.fridayImages = ['/images/other/doa-setelah-azan.png'];
                        for (let i = 1; i <= 6; i++) {
                            const adzanValue = $(`#adzan${i}`).val();
                            if (adzanValue) {
                                window.fridayImages.push(adzanValue);
                            }
                        }

                        console.log('Gambar Friday diperbarui, jumlah gambar:', window.fridayImages
                            .length);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data adzan:', error, xhr.responseText);
                }
            });
        }

        function startFridayImageSlider() {
            // Gunakan variabel global untuk fridayImages agar bisa diakses dari updateFridayImages()
            window.fridayImages = ['/images/other/doa-setelah-azan.png'];
            for (let i = 1; i <= 6; i++) {
                const adzanElement = $(`#adzan${i}`);
                if (adzanElement.val()) {
                    window.fridayImages.push(adzanElement.val());
                }
            }

            const $fridayImageElement = $('#currentFridayImage');

            if (!fridaySliderStartTime) {
                fridaySliderStartTime = getCurrentTimeFromServer().getTime();
                localStorage.setItem('fridaySliderStartTime', fridaySliderStartTime);
            }

            // Variabel untuk melacak indeks gambar saat ini
            let lastIndex = -1;

            // Fungsi untuk memperbarui gambar berdasarkan waktu server
            function updateFridayImage() {
                if (!window.fridayImages || window.fridayImages.length === 0) return;

                const now = getCurrentTimeFromServer().getTime();
                const elapsedMs = now - fridaySliderStartTime;
                const elapsedSeconds = Math.floor(elapsedMs / 1000);
                const currentIndex = Math.floor(elapsedSeconds / 10) % window.fridayImages.length;

                // Hanya perbarui gambar jika indeks berubah
                if (currentIndex !== lastIndex) {
                    lastIndex = currentIndex;

                    // Transisi mulus dengan opacity
                    $fridayImageElement.css('opacity', '0');
                    setTimeout(() => {
                        $fridayImageElement.attr('src', window.fridayImages[currentIndex]);
                        $fridayImageElement.css('opacity', '1');
                    }, 250);
                }
            }

            // Update gambar pertama kali
            updateFridayImage();

            // Set interval untuk update gambar setiap 1 detik untuk sinkronisasi yang lebih baik
            if (fridayImageSliderInterval) {
                clearInterval(fridayImageSliderInterval);
            }
            fridayImageSliderInterval = setInterval(updateFridayImage, 1000);
        }

        function displayFridayInfoPopup(data, isRestored = false) {
            const $popup = $('#fridayInfoPopup');
            const $dateElement = $('#fridayDate');
            const $officialsElement = $('#fridayOfficials');
            const now = getCurrentTimeFromServer();

            // Update tanggal dan officials dengan transisi mulus
            updateFridayInfoContent();

            // Tampilkan popup jika belum ditampilkan
            if ($popup.css('display') !== 'flex') {
                $popup.css('display', 'flex');
            }

            // Mulai slider gambar jika belum dimulai
            if (!fridayImageSliderInterval) {
                startFridayImageSlider();
            }

            if (!isRestored) {
                const now = getCurrentTimeFromServer().getTime();
                fridayInfoStartTime = now;
                fridayInfoEndTime = now + 600000;
                localStorage.setItem('fridayInfoStartTime', fridayInfoStartTime);
                localStorage.setItem('fridayInfoEndTime', fridayInfoEndTime);
                localStorage.setItem('fridayInfoData', JSON.stringify(data));
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
            if (!$prayerTimesElements.length) return;

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

            if (!prayerTimes.length) return;

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

                if ((prayerTimeInMinutes === currentTimeInMinutes ||
                        (prayerTimeInMinutes + 1 === currentTimeInMinutes && now.getSeconds() < 10)) &&
                    !isAdzanPlaying) {
                    if (prayer.name.toLowerCase().includes('shuruq') || prayer.name.toLowerCase()
                        .includes('syuruq') || prayer.name.toLowerCase().includes('terbit')) {
                        if (!isShuruqAlarmPlaying) {
                            console.log('Shuruq time - playing beep sound only');
                            isShuruqAlarmPlaying = true;
                            localStorage.setItem('shuruqAlarmTime', currentTimeFormatted);
                            playBeepSound(2);
                            shuruqAlarmTimeout = setTimeout(() => {
                                clearShuruqAlarmState();
                            }, 60000);
                        }
                    } else {
                        showAdzanPopup(prayer.name, prayer.time);
                    }
                }
            });

            showFridayInfo();
        }

        function updateSlides() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                console.error('Tidak dapat menentukan slug dari URL');
                return;
            }

            console.log('Memperbarui slide untuk slug:', slug);

            $.ajax({
                url: `/api/slides/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Respons API slides:', response);
                    if (response.success) {
                        // Simpan nilai slide sebelumnya untuk perbandingan
                        const previousSlides = [
                            $('#slide1').val() || '',
                            $('#slide2').val() || '',
                            $('#slide3').val() || '',
                            $('#slide4').val() || '',
                            $('#slide5').val() || '',
                            $('#slide6').val() || ''
                        ];

                        // Perbarui nilai input hidden untuk slide
                        $('#slide1').val(response.data.slide1);
                        $('#slide2').val(response.data.slide2);
                        $('#slide3').val(response.data.slide3);
                        $('#slide4').val(response.data.slide4);
                        $('#slide5').val(response.data.slide5);
                        $('#slide6').val(response.data.slide6);

                        // Perbarui slideUrls global jika ada
                        if (window.slideUrls) {
                            window.slideUrls = [
                                response.data.slide1 || '',
                                response.data.slide2 || '',
                                response.data.slide3 || '',
                                response.data.slide4 || '',
                                response.data.slide5 || '',
                                response.data.slide6 || ''
                            ].filter(url => url.trim() !== '');

                            console.log('Slide diperbarui, jumlah slide:', window.slideUrls.length);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data slide:', error, xhr.responseText);
                }
            });
        }

        function manageSlideDisplay() {
            const $mosqueImageElement = $('.mosque-image');
            if (!$mosqueImageElement.length) return;

            // Gunakan variabel global untuk slideUrls agar bisa diakses dari updateSlides()
            window.slideUrls = [
                $('#slide1').val() || '',
                $('#slide2').val() || '',
                $('#slide3').val() || '',
                $('#slide4').val() || '',
                $('#slide5').val() || '',
                $('#slide6').val() || ''
            ].filter(url => url.trim() !== '');

            if (window.slideUrls.length === 0) return;

            function updateSlide() {
                if (window.slideUrls.length === 0) return;

                // Gunakan waktu server untuk sinkronisasi
                const now = getCurrentTimeFromServer();

                // Hitung durasi per slide (10 detik per slide)
                const slideDuration = 10000; // 10 detik

                // Hitung indeks slide berdasarkan waktu server
                // Gunakan menit dan detik untuk menentukan indeks slide
                // Ini memastikan semua device menampilkan slide yang sama pada waktu yang sama
                const totalSeconds = (now.getMinutes() * 60) + now.getSeconds();
                const totalSlideTime = slideDuration * window.slideUrls.length;
                const cyclePosition = (totalSeconds * 1000 + now.getMilliseconds()) % totalSlideTime;
                const slideIndex = Math.floor(cyclePosition / slideDuration);

                // Perbarui gambar dengan transisi mulus
                $mosqueImageElement.css({
                    'background-image': `url("${window.slideUrls[slideIndex]}")`,
                    'background-size': 'cover',
                    'background-position': 'center',
                    'transition': 'background-image 0.5s ease-in-out'
                });
            }

            // Update slide pertama kali
            updateSlide();

            // Set interval untuk update slide setiap 1 detik
            setInterval(updateSlide, 1000);
        }

        manageSlideDisplay();
        updateScrollingText();
        fetchPrayerStatus(); // Initial check
        setInterval(fetchPrayerStatus, 30000);
        checkAndRestoreSessions();
        checkAndRestoreFridayInfo();
        checkAndRestoreAdzanImage();

        const savedShuruqTime = localStorage.getItem('shuruqAlarmTime');
        if (savedShuruqTime) {
            const now = getCurrentTimeFromServer();
            const currentTimeFormatted =
                `${now.getHours().toString().padStart(2, "0")}:${now.getMinutes().toString().padStart(2, "0")}`;
            if (savedShuruqTime === currentTimeFormatted) {
                isShuruqAlarmPlaying = true;
                shuruqAlarmTimeout = setTimeout(() => {
                    clearShuruqAlarmState();
                }, 60000);
            } else {
                clearShuruqAlarmState();
            }
        }

        handlePrayerTimes();
        setInterval(handlePrayerTimes, 1000);
        updateDate();
        setInterval(updateDate, 60000);
        checkDayChange();
        setInterval(checkDayChange, 60000);
        updateMarqueeText();

        setTimeout(function() {
            initMarquee();
        }, 1000);

        setInterval(() => {
            const now = getCurrentTimeFromServer();
            const currentMonthYear =
                `${now.getFullYear()}-${(now.getMonth() + 1).toString().padStart(2, '0')}`;
            const storedMonthYear = `${currentYear}-${currentMonth.toString().padStart(2, '0')}`;
            if (currentMonthYear !== storedMonthYear) {
                console.log("Bulan/tahun berubah, memperbarui jadwal sholat di background");
                fetchPrayerTimes();
            }
        }, 60000);

        function updateFridayOfficials() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                console.error('Tidak dapat menentukan slug dari URL');
                return;
            }

            console.log('Memperbarui data petugas Jumat untuk slug:', slug);

            $.ajax({
                url: `/api/petugas/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Respons API petugas:', response);
                    if (response.success) {
                        // Simpan nilai sebelumnya untuk perbandingan
                        const previousKhatib = $('#khatib').val();
                        const previousImam = $('#imam').val();
                        const previousMuadzin = $('#muadzin').val();

                        // Perbarui nilai input hidden untuk petugas
                        $('#khatib').val(response.data.khatib);
                        $('#imam').val(response.data.imam);
                        $('#muadzin').val(response.data.muadzin);

                        // Jika popup sedang ditampilkan, perbarui kontennya tanpa mengganggu animasi
                        if ($('#fridayInfoPopup').is(':visible')) {
                            updateFridayInfoContent();
                        }
                        console.log('Data petugas Jumat diperbarui');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data petugas:', error, xhr.responseText);
                }
            });
        }

        // Tambahkan fungsi updateFridayInfoContent
        function updateFridayInfoContent() {
            const $popup = $('#fridayInfoPopup');
            const $dateElement = $('#fridayDate');
            const $officialsElement = $('#fridayOfficials');
            const now = getCurrentTimeFromServer();

            // Update tanggal
            if (typeof moment !== 'undefined') {
                moment.locale('id');
                const hari = moment(now).format('dddd');
                const tanggalMasehi = moment(now).format('D MMMM YYYY');
                // let formattedDate = `<span class="day-name">${hari}</span>, ${tanggalMasehi}`;
                let formattedDate = `<span class="day-name">${hari}</span>, <br />${tanggalMasehi}`;


                if (typeof moment().iDate === 'function') {
                    const hijriDate = moment(now).iDate();
                    const hijriMonth = moment(now).iMonth();
                    const hijriYear = moment(now).iYear();
                    const bulanHijriyahID = [
                        'Muharam', 'Safar', 'Rabiulawal', 'Rabiulakhir', 'Jumadilawal', 'Jumadilakhir',
                        'Rajab', 'Syaban', 'Ramadhan', 'Syawal', 'Zulkaidah', 'Zulhijah'
                    ];
                    const hijri = `${hijriDate} ${bulanHijriyahID[hijriMonth]} ${hijriYear}H`;
                    formattedDate += ` / ${hijri}`;
                }

                $dateElement.html(formattedDate);
            }

            // Update data petugas
            let officialsHtml = '<table class="responsive-table">';
            const khatib = $('#khatib').val();
            const imam = $('#imam').val();
            const muadzin = $('#muadzin').val();

            if (khatib) {
                officialsHtml +=
                    `<tr><td style="font-weight: bold;">KHATIB</td><td>:</td><td>${khatib}</td></tr>`;
            }
            if (imam) {
                officialsHtml += `<tr><td style="font-weight: bold;">IMAM</td><td>:</td><td>${imam}</td></tr>`;
            }
            if (muadzin) {
                officialsHtml +=
                    `<tr><td style="font-weight: bold;">MUADZIN</td><td>:</td><td>${muadzin}</td></tr>`;
            }
            officialsHtml += '</table>';

            $officialsElement.html(officialsHtml);
        }

        // Jadwalkan pembaruan informasi masjid, marquee, slide, dan Friday images setiap 30 detik
        setInterval(function() {
            updateMosqueInfo(); // Tambahkan pembaruan informasi masjid
            updateMarqueeText(); // Tambahkan pembaruan marquee
            updateSlides(); // Tambahkan pembaruan slide
            updateFridayOfficials(); // Tambahkan pembaruan informasi petugas Jumat
            updateFridayImages(); // Tambahkan pembaruan gambar Friday
            updateIqomahImages(); // Tambahkan pembaruan gambar Iqomah
        }, 30000);

        // Perbarui waktu server setiap 60 detik
        fetchServerTime(); // Panggil sekali saat halaman dimuat untuk sinkronisasi awal
        setInterval(fetchServerTime, 60000); // Perbarui setiap 1 menit
    });
</script>
