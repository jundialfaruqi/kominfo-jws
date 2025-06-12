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

                    const capitalizedName = response.data.name
                        .toLowerCase()
                        .split(' ')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ');
                    $('.mosque-name-highlight').text(capitalizedName);

                    const capitalizedAddress = response.data.address
                        .toLowerCase()
                        .split(' ')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ');
                    $('.mosque-address').text(capitalizedAddress);

                    $('.logo-container').empty();
                    // Menambahkan logo masjid dengan gambar default jika tidak ada logo
                    $('.logo-container').append(
                        `<img src="${response.data.logo_masjid || '/images/other/logo-masjid-default.png'}" alt="Logo Masjid" class="logo logo-masjid">`
                    );

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
        // Inisialisasi variabel
        let serverTimestamp = parseInt($('#server-timestamp').val()) || Date.now();
        let pageLoadTimestamp = Date.now();
        const currentMonth = $('#current-month').val() || new Date().getMonth() + 1;
        const currentYear = $('#current-year').val() || new Date().getFullYear();
        let activePrayerStatus = null;
        let durasi = {};

        // Parsing data durasi dari backend
        if ($('#durasi-data').val()) {
            try {
                durasi = JSON.parse($('#durasi-data').val());
                console.log('Durasi loaded:', durasi);
            } catch (e) {
                console.error('Error parsing durasi data:', e);
                // Fallback durasi default
                durasi = {
                    adzan_shubuh: 4,
                    iqomah_shubuh: 6,
                    final_shubuh: 30,
                    adzan_dzuhur: 4,
                    iqomah_dzuhur: 6,
                    final_dzuhur: 30,
                    jumat_slide: 20,
                    adzan_ashar: 4,
                    iqomah_ashar: 6,
                    final_ashar: 30,
                    adzan_maghrib: 4,
                    iqomah_maghrib: 6,
                    final_maghrib: 30,
                    adzan_isya: 4,
                    iqomah_isya: 6,
                    final_isya: 30
                };
            }
        }

        function getPrayerDurations(prayerName) {
            const prayerLower = prayerName.toLowerCase().replace("'", "").replace(" ", "");
            const isFriday = prayerLower === "jumat" && getCurrentTimeFromServer().getDay() === 5;

            // Default durations jika data durasi tidak tersedia
            const defaultDurasi = {
                adzan: 60, // 1 menit dalam detik
                iqomah: 420, // 7 menit dalam detik
                final: 60, // 1 menit dalam detik
                jumat_slide: 600 // 10 menit dalam detik
            };

            if (!durasi || !durasi[`adzan_${prayerLower}`]) {
                return defaultDurasi;
            }

            if (isFriday) {
                return {
                    adzan: durasi.adzan_dzuhur * 60, // Konversi menit ke detik
                    jumat_slide: durasi.jumat_slide * 60 // Konversi menit ke detik
                };
            } else {
                return {
                    adzan: durasi[`adzan_${prayerLower}`] * 60, // Konversi menit ke detik
                    iqomah: durasi[`iqomah_${prayerLower}`] * 60, // Konversi menit ke detik
                    final: durasi[`final_${prayerLower}`] // TIDAK dikalikan 60, sudah dalam detik
                };
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
                        console.log('Waktu server diperbarui dari:', response.data.source, new Date(
                            serverTimestamp).toISOString());
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

        setTimeout(() => {
            syncServerTime(() => {
                checkAndRestoreSessions();
            });
        }, 3000); // 3000 milidetik = 3 detik

        setInterval(() => {
            syncServerTime();
        }, 300000); // 300000 milidetik = 5 menit

        if ($('#active-prayer-status').val()) {
            try {
                activePrayerStatus = JSON.parse($('#active-prayer-status').val());
                console.log('Active prayer status detected:', activePrayerStatus);
            } catch (e) {
                console.error('Error parsing active prayer status:', e);
            }
        }

        function updateActivePrayerStatus() {
            const slug = window.location.pathname.replace(/^\//, '');
            $.ajax({
                url: `/api/prayer-status/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        activePrayerStatus = response.data;
                        $('#active-prayer-status').val(JSON.stringify(activePrayerStatus));
                        checkAndRestoreSessions();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil status sholat:', error);
                }
            });
        }

        setInterval(updateActivePrayerStatus, 30000); // Perbarui setiap 30 detik

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
                    console.log("Data bulan baru tersedia, memuat ulang halaman...");
                    location.reload();
                    return response;
                }
                return null;
            } catch (error) {
                console.error("Error saat mengambil jadwal sholat:", error);
                return null;
            }
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
                location.reload();
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
                    `${displayHours.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}<br>DISKOMINFO`
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
            let currentPosition = 0;

            // Ambil posisi transformasi saat ini (jika ada)
            if ($scrollingTextElement.length) {
                const computedStyle = window.getComputedStyle($scrollingTextElement[0]);
                const transform = computedStyle.getPropertyValue('transform');
                if (transform && transform !== 'none') {
                    const matrix = transform.match(/matrix\((.*?)\)/);
                    if (matrix) {
                        const values = matrix[1].split(', ');
                        currentPosition = parseFloat(values[4]);
                    }
                }
            }

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

            // Hitung durasi animasi
            const textLength = combinedText.length;
            const baseDuration = 60;
            const calculatedDuration = Math.max(baseDuration, textLength / 10);

            // Ambil state animasi dari localStorage
            const savedMarqueeState = localStorage.getItem('marqueeState');
            let animationProgress = 0;
            let marqueeStartTime = localStorage.getItem('marqueeStartTime') ?
                parseInt(localStorage.getItem('marqueeStartTime')) :
                null;

            if (marqueeStartTime) {
                // Hitung progress berdasarkan waktu yang telah berlalu
                const now = getCurrentTimeFromServer().getTime();
                const elapsedMs = now - marqueeStartTime;
                const totalCycleTime = calculatedDuration * 1000;
                animationProgress = (elapsedMs % totalCycleTime) / totalCycleTime;
            } else {
                // Jika tidak ada state sebelumnya, mulai dari waktu saat ini
                marqueeStartTime = getCurrentTimeFromServer().getTime();
                localStorage.setItem('marqueeStartTime', marqueeStartTime);
            }

            // Perbarui elemen marquee
            $scrollingTextElement.css('animation', 'none');
            $scrollingTextElement.html(`<p>${combinedText}</p>`);

            if ($scrollingTextElement[0]) {
                $scrollingTextElement[0].offsetHeight; // Trigger reflow
            }

            const containerWidth = $scrollingTextElement.parent().width();
            const textWidth = $scrollingTextElement.find('p').width();
            const totalDistance = textWidth + containerWidth;

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
                url: `/api/marquee/${slug}`,
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
        let isShuruqAlarmPlaying = false;
        let shuruqAlarmTimeout = null;

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

            if (activePrayerStatus) {
                const {
                    phase,
                    prayerName,
                    prayerTime,
                    elapsedSeconds,
                    remainingSeconds,
                    progressPercentage,
                    isFriday
                } = activePrayerStatus;

                if (phase === 'adzan') {
                    adzanStartTime = nowTime - (elapsedSeconds * 1000);
                    localStorage.setItem('adzanStartTime', adzanStartTime);
                    localStorage.setItem('currentPrayerName', prayerName);
                    localStorage.setItem('currentPrayerTime', prayerTime);
                    currentPrayerName = prayerName;
                    currentPrayerTime = prayerTime;
                    showAdzanPopup(prayerName, prayerTime, true);
                    return;
                } else if (phase === 'iqomah') {
                    adzanStartTime = nowTime - (elapsedSeconds * 1000);
                    iqomahStartTime = adzanStartTime + ((elapsedSeconds - remainingSeconds) * 1000);
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
                } else if (phase === 'friday' && isFriday) {
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
                    displayFridayInfoPopup(fridayData, true);
                    return;
                }
            }

            // Jika activePrayerStatus tidak tersedia, coba ambil dari API
            const slug = window.location.pathname.replace(/^\//, '');
            $.ajax({
                url: `/api/prayer-status/${slug}`,
                method: 'GET',
                dataType: 'json',
                async: false, // Gunakan synchronous untuk memastikan data tersedia
                success: function(response) {
                    if (response.success && response.data) {
                        activePrayerStatus = response.data;
                        $('#active-prayer-status').val(JSON.stringify(activePrayerStatus));
                        // Ulangi pemulihan dengan data baru
                        checkAndRestoreSessions();
                        return;
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Gagal mengambil status sholat:', error);
                }
            });

            // Fallback jika API gagal: gunakan durasi default dari model Durasi
            const defaultDurasi = {
                adzan: 4 * 60, // 4 menit
                iqomah: 10 * 60, // 10 menit
                final: 30 * 60, // 30 menit
                jumat_slide: 20 * 60 // 20 menit
            };

            if (adzanStartTime && !iqomahStartTime) {
                const adzanElapsedSeconds = (nowTime - adzanStartTime) / 1000;
                let adzanDuration = defaultDurasi.adzan; // Gunakan default
                if (activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                    adzanDuration = activePrayerStatus.elapsedSeconds + activePrayerStatus.remainingSeconds;
                }

                if (adzanElapsedSeconds < adzanDuration) {
                    showAdzanPopup(currentPrayerName, currentPrayerTime, true);
                } else {
                    iqomahStartTime = adzanStartTime + (adzanDuration * 1000);
                    localStorage.setItem('iqomahStartTime', iqomahStartTime);
                    if (currentPrayerName !== "Jum'at" || now.getDay() !== 5) {
                        showIqomahPopup(currentPrayerTime, true);
                    } else {
                        updateFridayImages();
                        startFridayImageSlider();
                        const $fridayPopup = $('#fridayInfoPopup');
                        if ($fridayPopup.length) {
                            $fridayPopup.css('display', 'flex');
                        }
                        clearAdzanState();
                    }
                }
            } else if (iqomahStartTime) {
                const iqomahElapsedSeconds = (nowTime - iqomahStartTime) / 1000;
                let iqomahDuration = defaultDurasi.iqomah;
                let finalPhaseDuration = defaultDurasi.final;
                if (activePrayerStatus && activePrayerStatus.phase === 'iqomah') {
                    iqomahDuration = activePrayerStatus.elapsedSeconds + activePrayerStatus.remainingSeconds;
                } else if (activePrayerStatus && activePrayerStatus.phase === 'final') {
                    finalPhaseDuration = activePrayerStatus.elapsedSeconds + activePrayerStatus
                        .remainingSeconds;
                }

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
                        if (timeDiffMinutes >= 0 && timeDiffMinutes <= 10 && !prayerName.toLowerCase()
                            .includes('shuruq')) {
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
                    let adzanDuration = defaultDurasi.adzan;
                    let iqomahDuration = defaultDurasi.iqomah;
                    let finalPhaseDuration = defaultDurasi.final;
                    let jumatDuration = defaultDurasi.jumat_slide;

                    // Coba ambil durasi dari activePrayerStatus jika tersedia
                    if (activePrayerStatus) {
                        const totalDuration = activePrayerStatus.elapsedSeconds + activePrayerStatus
                            .remainingSeconds;
                        if (activePrayerStatus.phase === 'adzan') {
                            adzanDuration = totalDuration;
                        } else if (activePrayerStatus.phase === 'iqomah') {
                            iqomahDuration = totalDuration;
                        } else if (activePrayerStatus.phase === 'final') {
                            finalPhaseDuration = totalDuration;
                        } else if (activePrayerStatus.phase === 'friday') {
                            jumatDuration = totalDuration;
                        }
                    }

                    if (timeDiffSeconds < adzanDuration) {
                        adzanStartTime = nowTime - (timeDiffSeconds * 1000);
                        localStorage.setItem('adzanStartTime', adzanStartTime);
                        localStorage.setItem('currentPrayerName', prayerToRestore.name);
                        localStorage.setItem('currentPrayerTime', prayerToRestore.time);
                        currentPrayerName = prayerToRestore.name;
                        currentPrayerTime = prayerToRestore.time;
                        showAdzanPopup(prayerToRestore.name, prayerToRestore.time, true);
                    } else if (prayerToRestore.name !== "Jum'at" || now.getDay() !== 5) {
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
                        } else {
                            clearAdzanState();
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

        function showAdzanPopup(prayerName, prayerTimeStr, isRestored = false) {
            const now = getCurrentTimeFromServer();
            const currentDate = now.getDate();
            const serverMonth = now.getMonth() + 1;
            const serverYear = now.getFullYear();
            const scheduleMonth = parseInt($('#current-month').val());
            const scheduleYear = parseInt($('#current-year').val());

            if (scheduleMonth !== serverMonth || scheduleYear !== serverYear) {
                location.reload();
                return;
            }

            const $popup = $('#adzanPopup');
            const $title = $('#adzanTitle');
            const $progress = $('#adzanProgress');
            const $countdown = $('#adzanCountdown');

            $title.text(` ${prayerName}`);
            $popup.css('display', 'flex');

            // Fungsi untuk mendapatkan durasi dari backend atau fallback
            function getAdzanDuration() {
                if (activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                    // Gunakan data dari backend yang sudah dihitung secara dinamis
                    return activePrayerStatus.remainingSeconds + activePrayerStatus.elapsedSeconds;
                }

                // Gunakan fungsi getPrayerDurations yang sudah ada
                const prayerDurations = getPrayerDurations(prayerName);
                return prayerDurations.adzan; // Sudah dalam detik
            }

            const duration = getAdzanDuration();

            // Inisialisasi progress bar
            if (!isRestored) {
                playBeepSound(3);
                $progress.css('width', '0%');
            } else if (activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                // Gunakan progress dari backend untuk akurasi
                $progress.css('width', `${activePrayerStatus.progressPercentage}%`);
            }

            // Set waktu mulai adzan
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

            let lastCountdownUpdate = 0;
            isAdzanPlaying = true;
            let animationId;

            function updateAdzanAnimation(timestamp) {
                if (!isAdzanPlaying) {
                    cancelAnimationFrame(animationId);
                    return;
                }

                const currentTime = getCurrentTimeFromServer().getTime();
                const elapsedSeconds = (currentTime - adzanStartTime) / 1000;

                // Hitung waktu tersisa
                let timeLeft;
                let progressPercentage;

                if (activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                    // Gunakan data real-time dari backend jika tersedia
                    timeLeft = activePrayerStatus.remainingSeconds;
                    progressPercentage = activePrayerStatus.progressPercentage;
                } else {
                    // Fallback ke perhitungan manual
                    timeLeft = duration - elapsedSeconds;
                    progressPercentage = (elapsedSeconds / duration) * 100;
                }

                // Cek apakah adzan sudah selesai
                if (timeLeft <= 0) {
                    $popup.css('display', 'none');
                    isAdzanPlaying = false;
                    cancelAnimationFrame(animationId);

                    // Transition ke fase berikutnya
                    if (prayerName === "Jum'at" && now.getDay() === 5) {
                        // Untuk Jumat, lanjut ke Friday popup
                        updateFridayImages();
                        startFridayImageSlider();
                        const $fridayPopup = $('#fridayInfoPopup');
                        if ($fridayPopup.length) {
                            $fridayPopup.css('display', 'flex');
                        }
                        clearAdzanState();
                    } else {
                        // Untuk sholat biasa, lanjut ke Iqomah
                        showIqomahPopup(prayerTimeStr);
                    }
                    return;
                }

                // Update progress bar
                $progress.css({
                    width: `${Math.min(Math.max(progressPercentage, 0), 100)}%`
                });

                // Update countdown setiap detik
                if (currentTime - lastCountdownUpdate >= 1000) {
                    const hours = Math.floor(timeLeft / 3600);
                    const minutes = Math.floor((timeLeft % 3600) / 60);
                    const seconds = Math.floor(timeLeft % 60);

                    $countdown.text(
                        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );

                    lastCountdownUpdate = currentTime;
                }

                animationId = requestAnimationFrame(updateAdzanAnimation);
            }

            // Mulai animasi
            animationId = requestAnimationFrame(updateAdzanAnimation);

            // Return function untuk stop adzan
            return function stopAdzan() {
                isAdzanPlaying = false;
                if (animationId) {
                    cancelAnimationFrame(animationId);
                }
            };
        }

        let iqomahImageSliderInterval = null;
        let iqomahSliderStartTime = localStorage.getItem('iqomahSliderStartTime') ? parseInt(localStorage
            .getItem('iqomahSliderStartTime')) : null;
        let isIqomahPlaying = false;

        function updateIqomahImages() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                console.error('Tidak dapat menentukan slug dari URL');
                return;
            }

            // console.log('Memperbarui gambar Iqomah untuk slug:', slug);

            $.ajax({
                url: `/api/adzan/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // console.log('Respons API adzan untuk Iqomah:', response);
                    if (response.success) {
                        const previousAdzan = [];
                        for (let i = 1; i <= 6; i++) {
                            previousAdzan.push($(`#adzan${i}`).val() || '');
                        }
                        $('#adzan1').val(response.data.adzan1);
                        $('#adzan2').val(response.data.adzan2);
                        $('#adzan3').val(response.data.adzan3);
                        $('#adzan4').val(response.data.adzan4);
                        $('#adzan5').val(response.data.adzan5);
                        $('#adzan6').val(response.data.adzan6);

                        window.iqomahImages = [];
                        for (let i = 1; i <= 6; i++) {
                            const adzanValue = $(`#adzan${i}`).val();
                            if (adzanValue) {
                                window.iqomahImages.push(adzanValue);
                            }
                        }

                        // console.log('Gambar Iqomah diperbarui, jumlah gambar:', window.iqomahImages
                        //     .length);

                        if (window.iqomahImages.length === 0 && iqomahImageSliderInterval) {
                            clearInterval(iqomahImageSliderInterval);
                            iqomahImageSliderInterval = null;
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data adzan untuk Iqomah:', error, xhr
                        .responseText);
                }
            });
        }

        function startIqomahImageSlider() {
            console.log('Starting iqomah image slider...');
            console.log('window.iqomahImages:', window.iqomahImages);
            
            if (!window.iqomahImages || window.iqomahImages.length === 0) {
                console.log('No iqomah images available');
                return;
            }

            // Gunakan elemen gambar yang benar sesuai HTML
            const $imageElement = $('#currentIqomahImage');
            if (!$imageElement.length) {
                console.log('currentIqomahImage element not found');
                return;
            }

            console.log('Found currentIqomahImage element');

            function updateImage() {
                // Pastikan isIqomahPlaying terdefinisi
                if (typeof isIqomahPlaying !== 'undefined' && !isIqomahPlaying) {
                    console.log('Iqomah not playing, stopping image updates');
                    return;
                }

                const currentTime = getCurrentTimeFromServer().getTime();
                let elapsedSeconds;
                
                if (activePrayerStatus && activePrayerStatus.phase === 'iqomah') {
                    // Gunakan elapsed time dari backend untuk sinkronisasi yang tepat
                    const totalDuration = activePrayerStatus.elapsedSeconds + activePrayerStatus.remainingSeconds;
                    elapsedSeconds = totalDuration - activePrayerStatus.remainingSeconds;
                } else {
                    // Fallback ke perhitungan manual
                    if (iqomahStartTime) {
                        const elapsedMs = currentTime - iqomahStartTime;
                        elapsedSeconds = Math.floor(elapsedMs / 1000);
                    } else {
                        elapsedSeconds = 0;
                    }
                }
                
                const currentIndex = Math.floor(elapsedSeconds / 10) % window.iqomahImages.length;
                const imageUrl = window.iqomahImages[currentIndex];
                
                console.log('Updating iqomah image:', imageUrl, 'Index:', currentIndex, 'Elapsed:', elapsedSeconds);
                
                // Update src gambar langsung
                $imageElement.attr('src', imageUrl);
                
                // Pastikan gambar visible
                $imageElement.show();
            }

            // Update gambar pertama kali
            updateImage();
            
            // Set interval untuk update gambar
            const imageInterval = setInterval(() => {
                if (typeof isIqomahPlaying !== 'undefined' && !isIqomahPlaying) {
                    console.log('Clearing iqomah image interval');
                    clearInterval(imageInterval);
                    return;
                }
                updateImage();
            }, 1000);
            
            console.log('Iqomah image slider started with interval');
        }

        function showIqomahPopup(prayerTimeStr, isRestored = false) {
            const $popup = $('#iqomahPopup');
            const $progress = $('#iqomahProgress');
            const $countdown = $('#iqomahCountdown');

            $popup.css('display', 'flex');

            // Ambil nama sholat dari context atau dari activePrayerStatus
            let prayerName = 'Dzuhur'; // default
            if (activePrayerStatus && activePrayerStatus.prayerName) {
                prayerName = activePrayerStatus.prayerName;
            } else if (currentPrayerName) {
                prayerName = currentPrayerName;
            }

            // Fungsi untuk mendapatkan durasi adzan yang benar
            function getAdzanDuration() {
                if (activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                    return activePrayerStatus.remainingSeconds + activePrayerStatus.elapsedSeconds;
                }
                const prayerDurations = getPrayerDurations(prayerName);
                return prayerDurations.adzan; // Sudah dalam detik
            }

            let duration = 420; // Default 7 menit (dalam detik)
            if (activePrayerStatus && activePrayerStatus.phase === 'iqomah') {
                duration = activePrayerStatus.remainingSeconds + activePrayerStatus.elapsedSeconds;
            } else {
                // Gunakan durasi dinamis dari getPrayerDurations
                const prayerDurations = getPrayerDurations(prayerName);
                duration = prayerDurations.iqomah; // Sudah dalam detik
            }

            if (!isRestored) {
                $progress.css('width', '0%');
            } else if (activePrayerStatus && activePrayerStatus.phase === 'iqomah') {
                $progress.css('width', `${activePrayerStatus.progressPercentage}%`);
            }

            // Initialize iqomah images
            window.iqomahImages = [];
            for (let i = 1; i <= 6; i++) {
                const adzanElement = $(`#adzan${i}`);
                if (adzanElement.val()) {
                    window.iqomahImages.push(adzanElement.val());
                }
            }

            // Use six default images if no Iqomah images are available
            if (window.iqomahImages.length === 0) {
                window.iqomahImages = [
                    '/images/other/doa-setelah-adzan-default.webp',
                    '/images/other/doa-setelah-adzan-default.webp',
                    '/images/other/doa-masuk-masjid-default.webp',
                    '/images/other/doa-masuk-masjid-default.webp',
                    '/images/other/non-silent-hp-default.webp',
                    '/images/other/non-silent-hp-default.webp'
                ];
                console.log('Menggunakan enam gambar default untuk slider Iqomah:', window.iqomahImages);
            }

            console.log('Iqomah images initialized:', window.iqomahImages);
            startIqomahImageSlider();

            if (!iqomahStartTime) {
                if (isRestored && activePrayerStatus && activePrayerStatus.phase === 'iqomah') {
                    const now = getCurrentTimeFromServer().getTime();
                    // Hitung waktu mulai iqomah berdasarkan sisa waktu dari backend
                    const elapsedIqomahSeconds = (activePrayerStatus.elapsedSeconds + activePrayerStatus.remainingSeconds) - activePrayerStatus.remainingSeconds;
                    iqomahStartTime = now - (elapsedIqomahSeconds * 1000);

                    // Hitung adzanStartTime berdasarkan iqomahStartTime
                    const adzanDuration = getAdzanDuration();
                    adzanStartTime = iqomahStartTime - (adzanDuration * 1000);
                    localStorage.setItem('adzanStartTime', adzanStartTime);
                } else {
                    if (!adzanStartTime) {
                        adzanStartTime = calculateSyncStartTime(prayerTimeStr);
                        localStorage.setItem('adzanStartTime', adzanStartTime);
                    }
                    const adzanDuration = getAdzanDuration(); // Dapatkan durasi adzan yang benar
                    iqomahStartTime = adzanStartTime + (adzanDuration * 1000);
                }
                localStorage.setItem('iqomahStartTime', iqomahStartTime);
            }

            let lastCountdownUpdate = 0;
            let isIqomahPlaying = true;
            let hasPlayedFinalBeep = false;
            let animationId;

            function updateIqomahAnimation(timestamp) {
                if (!isIqomahPlaying) {
                    cancelAnimationFrame(animationId);
                    return;
                }

                const currentTime = getCurrentTimeFromServer().getTime();
                let timeLeft;
                let progressPercentage;

                if (activePrayerStatus && activePrayerStatus.phase === 'iqomah') {
                    // Gunakan data real-time dari backend jika tersedia
                    timeLeft = activePrayerStatus.remainingSeconds;
                    progressPercentage = activePrayerStatus.progressPercentage;
                } else {
                    // Fallback ke perhitungan manual
                    const elapsedSeconds = (currentTime - iqomahStartTime) / 1000;
                    timeLeft = duration - elapsedSeconds;
                    progressPercentage = (elapsedSeconds / duration) * 100;
                }

                if (timeLeft <= 5 && !hasPlayedFinalBeep) {
                    playBeepSound(3);
                    hasPlayedFinalBeep = true;
                }

                if (timeLeft <= 0) {
                    $popup.css('display', 'none');
                    isIqomahPlaying = false;
                    cancelAnimationFrame(animationId);
                    clearAdzanState();
                    showFinalAdzanImage();
                    return;
                }

                $progress.css({
                    width: `${Math.min(Math.max(progressPercentage, 0), 100)}%`
                });

                if (currentTime - lastCountdownUpdate >= 1000) {
                    const hours = Math.floor(timeLeft / 3600);
                    const minutes = Math.floor((timeLeft % 3600) / 60);
                    const seconds = Math.floor(timeLeft % 60);

                    $countdown.text(
                        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );

                    lastCountdownUpdate = currentTime;
                }

                animationId = requestAnimationFrame(updateIqomahAnimation);
            }

            animationId = requestAnimationFrame(updateIqomahAnimation);

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
                // console.log('Restoring Adzan final image');
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
        }

        function updateAdzanImages() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                // console.error('Tidak dapat menentukan slug dari URL');
                return;
            }
            // console.log('Memperbarui gambar Adzan untuk slug:', slug);
            $.ajax({
                url: `/api/adzan/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // console.log('Respons API adzan untuk Adzan15:', response);
                    if (response.success) {
                        const oldAdzan15 = $('#adzan15').val();
                        $('#adzan15').val(response.data.adzan15);
                        if ($('#adzanImageDisplay').is(':visible') && oldAdzan15 !== response.data
                            .adzan15) {
                            const $imageElement = $('#currentAdzanImage');
                            $imageElement.css('opacity', '0');
                            setTimeout(() => {
                                $imageElement.attr('src', response.data.adzan15);
                                $imageElement.css('opacity', '1');
                                adzanImageSrc = response.data.adzan15;
                                localStorage.setItem('adzanImageSrc', adzanImageSrc);
                            }, 250);
                        }

                        // console.log('Gambar Adzan15 diperbarui');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data adzan untuk Adzan Images:', error, xhr
                        .responseText);
                }
            });
        }

        function displayAdzanImage(imageSrc, isRestored = false, duration = 60000) {
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
            if (currentPrayerName === "Jum'at" && getCurrentTimeFromServer().getDay() === 5) {
                return;
            }

            if (adzanImageStartTime && adzanImageEndTime) {
                const currentTime = getCurrentTimeFromServer().getTime();
                if (currentTime < adzanImageEndTime) {
                    return;
                }
            }

            const $adzan15 = $('#adzan15');
            let imageUrl = $adzan15.length && $adzan15.val() ? $adzan15.val() :
                '/images/other/lurus-rapat-shaf-default.webp';

            let duration = 60000; // Default 1 menit (dalam milidetik)
            if (activePrayerStatus && activePrayerStatus.phase === 'final') {
                // Gunakan remainingSeconds saja, bukan total durasi
                duration = activePrayerStatus.remainingSeconds * 1000;
            } else {
                // Gunakan durasi dinamis dari getPrayerDurations (sudah dalam detik)
                const prayerDurations = getPrayerDurations(currentPrayerName || 'shubuh');
                duration = prayerDurations.final * 1000; // Konversi detik ke milidetik
            }

            displayAdzanImage(imageUrl, false, duration);

            setTimeout(() => {
                hideAdzanImage();
            }, duration);
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

            // console.log('Memperbarui gambar Friday untuk slug:', slug);

            $.ajax({
                url: `/api/adzan/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // console.log('Respons API adzan:', response);
                    if (response.success && response.data) {
                        const previousAdzan = [];
                        for (let i = 7; i <= 12; i++) {
                            previousAdzan.push($(`#adzan${i}`).val() || '');
                        }

                        const adzanKeys = ['adzan7', 'adzan8', 'adzan9', 'adzan10', 'adzan11',
                            'adzan12'
                        ];
                        adzanKeys.forEach(key => {
                            if (response.data[key]) {
                                $(`#${key}`).val(response.data[key]);
                            }
                        });

                        window.fridayImages = [];
                        for (let i = 7; i <= 12; i++) {
                            const adzanValue = $(`#adzan${i}`).val();
                            if (adzanValue) {
                                window.fridayImages.push(adzanValue);
                            }
                        }

                        // console.log('Gambar Friday diperbarui, jumlah gambar:', window.fridayImages
                        //     .length);

                        if (window.fridayImages.length === 0 && fridayImageSliderInterval) {
                            clearInterval(fridayImageSliderInterval);
                            fridayImageSliderInterval = null;
                            $('#currentFridayImage').css('opacity', '0');
                            // console.log('Slider Friday dihentikan karena tidak ada gambar');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error saat mengambil data adzan:', error, xhr.responseText);
                }
            });
        }

        function startFridayImageSlider() {
            const $fridayImageElement = $('#currentFridayImage');
            if (!$fridayImageElement.length) {
                console.error('Elemen #currentFridayImage tidak ditemukan');
                return;
            }

            window.fridayImages = [];
            for (let i = 7; i <= 12; i++) {
                const adzanElement = $(`#adzan${i}`);
                if (adzanElement.length && adzanElement.val()) {
                    window.fridayImages.push(adzanElement.val());
                }
            }

            if (window.fridayImages.length === 0) {
                window.fridayImages = [
                    '/images/other/doa-setelah-adzan-default.webp',
                    '/images/other/doa-setelah-adzan-default.webp',
                    '/images/other/dilarang-bicara-saat-sholat-jumat-default.webp',
                    '/images/other/dilarang-bicara-saat-sholat-jumat-default.webp',
                    '/images/other/non-silent-hp-default.webp',
                    '/images/other/doa-masuk-masjid-default.webp'
                ];
                console.log('Menggunakan enam gambar default untuk slider Friday:', window.fridayImages);
            }

            if (!fridaySliderStartTime) {
                fridaySliderStartTime = getCurrentTimeFromServer().getTime();
                localStorage.setItem('fridaySliderStartTime', fridaySliderStartTime);
            }

            let lastIndex = -1;

            function updateFridayImage() {
                // Use six default images if no Friday images are available
                if (!window.fridayImages || window.fridayImages.length === 0) {
                    window.fridayImages = [
                        '/images/other/doa-setelah-adzan-default.webp',
                        '/images/other/doa-setelah-adzan-default.webp',
                        '/images/other/dilarang-bicara-saat-sholat-jumat-default.webp',
                        '/images/other/dilarang-bicara-saat-sholat-jumat-default.webp',
                        '/images/other/non-silent-hp-default.webp',
                        '/images/other/doa-masuk-masjid-default.webp'
                    ];
                    console.log('Menggunakan enam gambar default dalam updateFridayImage:', window
                        .fridayImages);
                }

                const now = getCurrentTimeFromServer().getTime();
                const elapsedMs = now - fridaySliderStartTime;
                const elapsedSeconds = Math.floor(elapsedMs / 1000);
                const currentIndex = Math.floor(elapsedSeconds / 10) % window.fridayImages.length;

                if (currentIndex !== lastIndex) {
                    lastIndex = currentIndex;
                    $fridayImageElement.css('opacity', '0');
                    setTimeout(() => {
                        $fridayImageElement.attr('src', window.fridayImages[currentIndex]);
                        $fridayImageElement.css('opacity', '1');
                    }, 250);
                }
            }

            updateFridayImage();
            if (fridayImageSliderInterval) {
                clearInterval(fridayImageSliderInterval);
            }
            fridayImageSliderInterval = setInterval(updateFridayImage, 1000);

            // Gunakan durasi dinamis dari activePrayerStatus atau fallback ke getPrayerDurations
            let displayDuration = 600000; // Default 10 menit

            if (activePrayerStatus && activePrayerStatus.phase === 'friday') {
                displayDuration = (activePrayerStatus.remainingSeconds + activePrayerStatus.elapsedSeconds) *
                    1000;
            } else {
                // Gunakan durasi dinamis dari getPrayerDurations
                const prayerDurations = getPrayerDurations('Jumat');
                if (prayerDurations && prayerDurations.jumat_slide) {
                    displayDuration = prayerDurations.jumat_slide * 1000; // Konversi detik ke milidetik
                }
            }

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
        }

        function displayFridayInfoPopup(data, isRestored = false) {
            const $popup = $('#fridayInfoPopup');
            const $dateElement = $('#fridayDate');
            const $officialsElement = $('#fridayOfficials');
            const now = getCurrentTimeFromServer();

            updateFridayInfoContent();

            if ($popup.css('display') !== 'flex') {
                $popup.css('display', 'flex');
            }

            let duration = 600000; // Default 10 menit (dalam milidetik)
            if (activePrayerStatus && activePrayerStatus.phase === 'friday') {
                duration = (activePrayerStatus.remainingSeconds + activePrayerStatus.elapsedSeconds) * 1000;
            }

            if (!isRestored) {
                const now = getCurrentTimeFromServer().getTime();
                fridayInfoStartTime = now;
                fridayInfoEndTime = now + duration;
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
                    !isAdzanPlaying && !adzanStartTime) {
                    if (prayer.name.toLowerCase().includes('shuruq') || prayer.name.toLowerCase()
                        .includes('syuruq') || prayer.name.toLowerCase().includes('terbit')) {
                        if (!isShuruqAlarmPlaying) {
                            // console.log('Shuruq time - playing beep sound only');
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
                url: `/api/slides/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // console.log('Respons API slides:', response);
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

                            if (window.slideUrls) {
                                window.slideUrls = newSlides.filter(url => url.trim() !== '');
                                // console.log('Slide diperbarui, jumlah slide:', window.slideUrls
                                //     .length);
                            }

                            $(document).trigger('slidesUpdated', [newSlides]);
                        } else {
                            // console.log('Tidak ada perubahan pada slide, update diabaikan');
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

                const now = getCurrentTimeFromServer();

                const slideDuration = 10000;

                const totalSeconds = (now.getMinutes() * 60) + now.getSeconds();
                const totalSlideTime = slideDuration * window.slideUrls.length;
                const cyclePosition = (totalSeconds * 1000 + now.getMilliseconds()) % totalSlideTime;
                const slideIndex = Math.floor(cyclePosition / slideDuration);

                $mosqueImageElement.css({
                    'background-image': `url("${window.slideUrls[slideIndex]}")`,
                });
            }

            updateSlide();
            setInterval(updateSlide, 1000);
        }

        manageSlideDisplay();
        updateScrollingText();

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
                console.log("Bulan/tahun berubah, memperbarui jadwal sholat");
                fetchPrayerTimes();
            }
        }, 60000);

        function updateFridayOfficials() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                // console.error('Tidak dapat menentukan slug dari URL');
                return;
            }

            // console.log('Memperbarui data petugas Jumat untuk slug:', slug);

            $.ajax({
                url: `/api/petugas/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // console.log('Respons API petugas:', response);
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
                        // console.log('Data petugas Jumat diperbarui');
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

            if (typeof moment !== 'undefined') {
                moment.locale('id');
                const hari = moment(now).format('dddd');
                const tanggalMasehi = moment(now).format('D MMMM YYYY');

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

        setInterval(function() {
            updateMosqueInfo();
            updateMarqueeText();
            updateSlides();
            updateFridayOfficials();
            updateFridayImages();
            updateIqomahImages();
            updateAdzanImages();
        }, 30000); // 30000 milidetik = 30 detik


        // document.addEventListener('visibilitychange', function() {
        //     if (document.visibilityState === 'visible') {
        //         console.log('Tab kembali aktif, menyinkronkan waktu server');
        //         syncServerTime();
        //         checkAndRestoreSessions();
        //         checkAndRestoreAdzanImage();
        //         checkAndRestoreFridayInfo();
        //         handlePrayerTimes();
        //     }
        // });

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

    });
</script>
