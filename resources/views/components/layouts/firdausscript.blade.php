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
        let serverTimestamp = parseInt($('#server-timestamp').val()) || Date.now();
        let pageLoadTimestamp = Date.now();
        const currentMonth = $('#current-month').val() || new Date().getMonth() + 1;
        const currentYear = $('#current-year').val() || new Date().getFullYear();

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
                        // console.log('Waktu server diperbarui dari:', response.data.source, new Date(
                        //     serverTimestamp).toISOString());
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
                // console.log('Waktu server diupdate setelah 3 detik halaman di muat');
            });
        }, 3000); // 3000 milidetik = 3 detik

        setInterval(() => {
            syncServerTime();
            // console.log('Waktu server diupdate setiap 1 menit');
        }, 60000); // 60000 milidetik = 1 menit

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
                    `${displayHours.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`
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
                const finalPhaseDuration = getFinalDuration(currentPrayerName || 'Dzuhur') / 1000;

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
                    const adzanDuration = getAdzanDuration(prayerToRestore.name);
                    const iqomahDuration = getIqomahDuration(prayerToRestore.name);
                    const finalPhaseDuration = getFinalDuration(prayerToRestore.name) / 1000;

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
            localStorage.removeItem('jumatAdzanShown');

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
        }

        function clearAllAdzanStates() {
            const keysToRemove = [
                'adzanStartTime', 'iqomahStartTime', 'currentPrayerName',
                'currentPrayerTime', 'jumatAdzanShown', 'shuruqAlarmTime',
                'fridayInfoStartTime', 'adzanImageStartTime'
            ];
            keysToRemove.forEach(key => localStorage.removeItem(key));
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

            if (prayerLower === 'shubuh' && durasiData.adzan_shubuh) {
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

            if (prayerLower === 'shubuh' && durasiData.iqomah_shubuh) {
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

        // Fungsi untuk mendapatkan durasi final berdasarkan nama sholat (dalam milidetik)
        function getFinalDuration(prayerName) {
            const durasiData = getDurasiData();
            const prayerLower = prayerName.toLowerCase();

            // Default durasi jika data tidak tersedia (dalam milidetik)
            const defaultDuration = 60 * 1000; // 60 detik

            if (!durasiData) return defaultDuration;

            if (prayerLower === 'shubuh' && durasiData.final_shubuh) {
                return durasiData.final_shubuh * 1000;
            } else if (prayerLower === 'dzuhur' && durasiData.final_dzuhur) {
                return durasiData.final_dzuhur * 1000;
            } else if (prayerLower === 'ashar' && durasiData.final_ashar) {
                return durasiData.final_ashar * 1000;
            } else if (prayerLower === 'maghrib' && durasiData.final_maghrib) {
                return durasiData.final_maghrib * 1000;
            } else if (prayerLower === 'isya' && durasiData.final_isya) {
                return durasiData.final_isya * 1000;
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

        function showAdzanPopup(prayerName, prayerTimeStr, isRestored = false) {
            const now = getCurrentTimeFromServer();
            const currentDate = now.getDate();
            const serverMonth = now.getMonth() + 1;
            const serverYear = now.getFullYear();
            const scheduleMonth = parseInt($('#current-month').val());
            const scheduleYear = parseInt($('#current-year').val());

            if (scheduleMonth !== serverMonth || scheduleYear !== serverYear) {
                // console.log('Jadwal tidak sesuai dengan tanggal server, memuat ulang halaman...');
                location.reload();
                return;
            }

            const $popup = $('#adzanPopup');
            const $title = $('#adzanTitle');
            const $progress = $('#adzanProgress');
            const $countdown = $('#adzanCountdown');

            $title.text(` ${prayerName}`);
            $popup.css('display', 'flex');

            // Putar alarm hanya saat popup adzan dimulai (bukan saat dipulihkan)
            if (!isRestored) {
                playBeepSound(3); // Alarm tetap diputar untuk semua adzan, termasuk Jumat
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
                const progressPercentage = (elapsedSeconds / duration) * 100;
                $progress.css({
                    width: `${Math.min(progressPercentage, 100)}%`
                });

                // Update countdown hanya setiap detik untuk efisiensi
                if (currentTime - lastCountdownUpdate >= 1000) {
                    const hours = Math.floor(timeLeft / 3600);
                    const minutes = Math.floor((timeLeft % 3600) / 60);
                    const seconds = Math.floor(timeLeft % 60);

                    $countdown.text(
                        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
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

        let iqomahImageSliderInterval = null;
        let iqomahSliderStartTime = localStorage.getItem('iqomahSliderStartTime') ? parseInt(localStorage
            .getItem('iqomahSliderStartTime')) : null;

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
                            // console.log('Gambar Iqomah berubah, memperbarui array:',
                            //     newIqomahImages);
                            window.iqomahImages = newIqomahImages;
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

            let previousIqomahImages = [...window.iqomahImages];

            if (!iqomahSliderStartTime) {
                iqomahSliderStartTime = getCurrentTimeFromServer().getTime();
                localStorage.setItem('iqomahSliderStartTime', iqomahSliderStartTime);
            }

            let lastIndex = -1;

            function updateIqomahImage() {
                if (!window.iqomahImages || window.iqomahImages.length === 0) {
                    window.iqomahImages = [
                        '/images/other/doa-setelah-adzan-default.webp',
                        '/images/other/doa-masuk-masjid-default.webp',
                        '/images/other/non-silent-hp-default.webp'
                    ];
                    console.warn('Array iqomahImages kosong, menggunakan gambar default:', window.iqomahImages);
                    previousIqomahImages = [...window.iqomahImages];
                }

                if (JSON.stringify(window.iqomahImages) !== JSON.stringify(previousIqomahImages)) {
                    console.log('Array iqomahImages berubah, memperbarui slider tanpa reset:', window
                        .iqomahImages);
                    previousIqomahImages = [...window.iqomahImages];
                }

                const now = getCurrentTimeFromServer().getTime();
                const elapsedMs = now - iqomahSliderStartTime;
                const elapsedSeconds = Math.floor(elapsedMs / 1000);
                const currentIndex = Math.floor(elapsedSeconds / 20) % window.iqomahImages.length;

                if (currentIndex !== lastIndex) {
                    lastIndex = currentIndex;

                    const img = new Image();
                    img.src = window.iqomahImages[currentIndex];
                    img.onload = () => {
                        $iqomahImageElement.css('opacity', '0');
                        setTimeout(() => {
                            $iqomahImageElement.attr('src', img.src);
                            $iqomahImageElement.css('opacity', '1');
                            // console.log('Gambar Iqomah diperbarui ke:', img.src);
                        }, 250);
                    };
                    img.onerror = () => {
                        console.error('Gagal memuat gambar:', img.src);
                        $iqomahImageElement.attr('src', '/images/other/doa-masuk-masjid-default.webp');
                        $iqomahImageElement.css('opacity', '1');
                    };
                }
            }

            updateIqomahImage();

            if (iqomahImageSliderInterval) {
                clearInterval(iqomahImageSliderInterval);
                console.log('Interval slider iqomah sebelumnya dihentikan');
            }
            iqomahImageSliderInterval = setInterval(updateIqomahImage, 1000);
        }

        function showIqomahPopup(prayerTimeStr, isRestored = false) {
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
                    playBeepSound(3);
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

                // Update progress bar (smooth animation setiap frame)
                const progressPercentage = (elapsedSeconds / duration) * 100;
                $progress.css({
                    width: `${Math.min(progressPercentage, 100)}%`
                });

                // Update countdown hanya setiap detik untuk efisiensi
                if (currentTime - lastCountdownUpdate >= 1000) {
                    const hours = Math.floor(timeLeft / 3600);
                    const minutes = Math.floor((timeLeft % 3600) / 60);
                    const seconds = Math.floor(timeLeft % 60);

                    $countdown.text(
                        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
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
        }

        function updateAdzanImages() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                return;
            }
            $.ajax({
                url: `/api/adzan/${slug}`,
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

            const duration = getFinalDuration(currentPrayerName || 'Dzuhur');

            displayAdzanImage(imageUrl, false, duration);

            adzanImageTimeout = setTimeout(() => {
                const $imageDisplay = $('#adzanImageDisplay');
                $imageDisplay.css('display', 'none');
                clearAdzanImageState();
                // console.log('Final adzan image ditutup setelah', duration / 1000, 'detik');
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
            window.cachedJumatSlideDuration = undefined;
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

            $.ajax({
                url: `/api/adzan/${slug}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const previousAdzan = [];
                        for (let i = 7; i <= 12; i++) {
                            previousAdzan.push($(`#adzan${i}`).val() || '');
                        }

                        const adzanKeys = ['adzan7', 'adzan8', 'adzan9', 'adzan10', 'adzan11',
                            'adzan12'
                        ];
                        adzanKeys.forEach(key => {
                            $(`#${key}`).val(response.data[key] || '');
                        });

                        window.fridayImages = [];
                        for (let i = 7; i <= 12; i++) {
                            const adzanValue = $(`#adzan${i}`).val();
                            if (adzanValue) {
                                window.fridayImages.push(adzanValue);
                            }
                        }

                        // Gunakan gambar default jika tidak ada gambar dari API
                        if (window.fridayImages.length === 0) {
                            window.fridayImages = [
                                '/images/other/doa-setelah-adzan-default.webp',
                                '/images/other/doa-masuk-masjid-default.webp',
                                '/images/other/dilarang-bicara-saat-sholat-jumat-default.webp',
                                '/images/other/non-silent-hp-default.webp'
                            ];
                            // console.log('Menggunakan gambar default karena respons API kosong:',
                            //     window.fridayImages);
                        }

                        // Pastikan slider tetap berjalan jika popup aktif
                        if ($('#fridayInfoPopup').is(':visible') && !fridayImageSliderInterval) {
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
            window.fridayImages = [];
            for (let i = 7; i <= 12; i++) {
                const adzanElement = $(`#adzan${i}`);
                if (adzanElement.val()) {
                    window.fridayImages.push(adzanElement.val());
                }
            }

            const $fridayImageElement = $('#currentFridayImage');
            if (!$fridayImageElement.length) {
                console.error('Elemen #currentFridayImage tidak ditemukan di DOM');
                return;
            }

            // Gunakan gambar default dengan jalur absolut jika tidak ada gambar dari database
            if (window.fridayImages.length === 0) {
                window.fridayImages = [
                    '/images/other/doa-setelah-adzan-default.webp',
                    '/images/other/doa-masuk-masjid-default.webp',
                    '/images/other/dilarang-bicara-saat-sholat-jumat-default.webp',
                    '/images/other/non-silent-hp-default.webp'
                ];
                console.log('Menggunakan 4 gambar default untuk slider Friday:', window.fridayImages);
            }

            if (!fridaySliderStartTime) {
                fridaySliderStartTime = getCurrentTimeFromServer().getTime();
                localStorage.setItem('fridaySliderStartTime', fridaySliderStartTime);
            }

            let lastIndex = -1;

            function updateFridayImage() {
                if (!window.fridayImages || window.fridayImages.length === 0) {
                    window.fridayImages = [
                        '/images/other/doa-setelah-adzan-default.webp',
                        '/images/other/doa-masuk-masjid-default.webp',
                        '/images/other/dilarang-bicara-saat-sholat-jumat-default.webp',
                        '/images/other/non-silent-hp-default.webp'
                    ];
                    console.log('Menggunakan 4 gambar default dalam updateFridayImage:', window
                        .fridayImages);
                }

                const now = getCurrentTimeFromServer().getTime();
                const elapsedMs = now - fridaySliderStartTime;
                const elapsedSeconds = Math.floor(elapsedMs / 1000);
                const currentIndex = Math.floor(elapsedSeconds / 20) % window.fridayImages.length;

                if (currentIndex !== lastIndex) {
                    lastIndex = currentIndex;

                    // Preload gambar untuk memastikan pemuatan berhasil
                    const img = new Image();
                    img.src = window.fridayImages[currentIndex];
                    img.onload = () => {
                        $fridayImageElement.css('opacity', '0');
                        setTimeout(() => {
                            $fridayImageElement.attr('src', img.src);
                            $fridayImageElement.css('opacity', '1');
                            console.log('Gambar Friday diperbarui ke:', img.src);
                        }, 250);
                    };
                    img.onerror = () => {
                        console.error('Gagal memuat gambar:', img.src);
                        // Fallback ke gambar default jika gagal
                        $fridayImageElement.attr('src', '/images/other/doa-masuk-masjid-default.webp');
                        $fridayImageElement.css('opacity', '1');
                    };
                }
            }

            updateFridayImage();

            if (fridayImageSliderInterval) {
                clearInterval(fridayImageSliderInterval);
            }
            fridayImageSliderInterval = setInterval(updateFridayImage, 1000);

            // Gunakan durasi dinamis untuk jumat slide
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
        }

        function displayFridayInfoPopup(data, isRestored = false) {
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
                            isShuruqAlarmPlaying = true;
                            localStorage.setItem('shuruqAlarmTime', currentTimeFormatted);
                            playBeepSound(2);
                            shuruqAlarmTimeout = setTimeout(() => {
                                clearShuruqAlarmState();
                            }, 60000);
                        }
                    } else if (prayer.name === "Jum'at" && fridayInfoStartTime && now.getTime() <
                        fridayInfoEndTime) {
                        // Jangan memulai adzan Jumat jika popup Friday Info aktif
                        clearAdzanState();
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

        // Objek global untuk menyimpan cache gambar
        window.imageCache = window.imageCache || {};

        function clearUnusedCache(currentUrls) {
            const maxCacheSize = 10; // Batas maksimum gambar di cache
            Object.keys(window.imageCache).forEach(url => {
                if (!currentUrls.includes(url)) {
                    delete window.imageCache[url];
                    console.log(`Gambar dihapus dari cache: ${url}`);
                }
            });
            // Jika cache masih terlalu besar, hapus gambar tertua
            const cachedUrls = Object.keys(window.imageCache);
            if (cachedUrls.length > maxCacheSize) {
                const urlsToRemove = cachedUrls.slice(0, cachedUrls.length - maxCacheSize);
                urlsToRemove.forEach(url => {
                    delete window.imageCache[url];
                    console.log(`Gambar lama dihapus dari cache: ${url}`);
                });
            }
        }

        // Fungsi untuk preload gambar
        function preloadImages(urls) {
            return Promise.all(urls.map(url => {
                return new Promise((resolve, reject) => {
                    // Cek apakah gambar sudah ada di cache
                    if (window.imageCache[url] && window.imageCache[url].complete) {
                        console.log(`Gambar sudah ada di cache: ${url}`);
                        resolve(window.imageCache[url]);
                        return;
                    }

                    const img = new Image();
                    img.src = url;

                    img.onload = () => {
                        // console.log(`Gambar berhasil dimuat: ${url}`);
                        window.imageCache[url] = img; // Simpan di cache
                        resolve(img);
                    };

                    img.onerror = () => {
                        console.warn(
                            `Gagal memuat gambar: ${url}, menggunakan default`);
                        const defaultUrl = '/images/other/slide-jws-default.jpg';
                        if (!window.imageCache[defaultUrl]) {
                            const defaultImg = new Image();
                            defaultImg.src = defaultUrl;
                            window.imageCache[defaultUrl] = defaultImg;
                        }
                        resolve(window.imageCache[defaultUrl]);
                    };
                });
            }));
        }

        function manageSlideDisplay() {
            const $mosqueImageElement = $('.mosque-image');
            if (!$mosqueImageElement.length) {
                console.warn('Elemen .mosque-image tidak ditemukan');
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
                console.warn('Tidak ada slide yang tersedia, menggunakan default');
                window.slideUrls = ['/images/other/slide-jws-default.jpg'];
            }

            // Fungsi untuk memulai slider setelah preload
            async function initSlider() {
                try {
                    // Preload semua gambar
                    await preloadImages(window.slideUrls);
                    // console.log('Semua gambar telah dimuat, memulai slider');

                    const slideDuration = 20000; // 10 detik

                    function updateSlide() {
                        if (window.slideUrls.length === 0) return;

                        const now = getCurrentTimeFromServer();
                        const totalSeconds = (now.getMinutes() * 60) + now.getSeconds();
                        const totalSlideTime = slideDuration * window.slideUrls.length;
                        const cyclePosition = (totalSeconds * 1000 + now.getMilliseconds()) %
                            totalSlideTime;
                        const slideIndex = Math.floor(cyclePosition / slideDuration);

                        // Gunakan URL dari slideUrls, dengan fallback ke default jika tidak ada di cache
                        const currentUrl = window.imageCache[window.slideUrls[slideIndex]]?.src ||
                            '/images/other/slide-jws-default.jpg';

                        // Terapkan transisi untuk pergantian gambar yang mulus
                        $mosqueImageElement.css({
                            'background-image': `url("${currentUrl}")`,
                            'transition': 'background-image 0.5s ease-in-out'
                        });

                        // console.log(`Slide diperbarui: Index ${slideIndex}, URL ${currentUrl}`);
                    }

                    // Jalankan updateSlide pertama
                    updateSlide();

                    // Atur interval untuk memeriksa waktu server setiap detik
                    setInterval(updateSlide, 1000);
                } catch (error) {
                    console.error('Error saat preload gambar:', error);
                    // Fallback: mulai slider dengan gambar default
                    window.slideUrls = ['/images/other/slide-jws-default.jpg'];
                    updateSlide();
                    setInterval(updateSlide, 1000);
                }
            }

            // Mulai slider
            initSlider();

            // Dengarkan event slidesUpdated untuk menangani perubahan slide
            $(document).on('slidesUpdated', async function(event, newSlides) {
                // console.log('Event slidesUpdated diterima, memperbarui slider');
                const newUrls = newSlides.filter(url => url.trim() !== '');
                if (newUrls.length === 0) {
                    console.warn('Tidak ada slide baru, menggunakan default');
                    newUrls.push('/images/other/slide-jws-default.jpg');
                }

                // Perbarui slideUrls hanya untuk URL baru yang belum ada di cache
                const urlsToPreload = newUrls.filter(url => !window.imageCache[url] || !window
                    .imageCache[url].complete);
                if (urlsToPreload.length > 0) {
                    // console.log(`Preload gambar baru: ${urlsToPreload}`);
                    await preloadImages(urlsToPreload);
                }

                window.slideUrls = newUrls;
                // console.log('SlideUrls diperbarui:', window.slideUrls);
            });
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
                retries: 3, // Maksimum jumlah percobaan
                retryDelay: 5000, // Jeda 5 detik antara percobaan
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
                error: function(xhr, status, error, retryCount = 0) {
                    console.error(
                        `Error saat mengambil data petugas (Percobaan ${retryCount + 1}):`,
                        error, xhr.responseText);
                    if (retryCount < this.retries) {
                        setTimeout(() => {
                            console.log(
                                `Mencoba ulang pengambilan data petugas (Percobaan ${retryCount + 2})...`
                            );
                            $.ajax({
                                ...this,
                                error: (xhr, status, error) => {
                                    this.error(xhr, status, error, retryCount +
                                        1);
                                }
                            });
                        }, this.retryDelay);
                    } else {
                        console.error(
                            'Maksimum percobaan pengambilan data petugas tercapai. Tidak ada percobaan ulang lebih lanjut.'
                        );
                    }
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

                let formattedDate =
                    `<span class="day-name" style="font-size: 5rem;">${hari}</span>, <br />${tanggalMasehi}`;


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
            updateFridayOfficials();
            updateFridayImages();
            updateIqomahImages();
            updateAdzanImages();
            // console.log(
            //     'Data Petugas Jumat, Slide Jumat, Gambar Iqomah, dan Final diperbarui setiap 37 Detik'
            // );
        }, 37000); // 37000 milidetik = 37 detik

        setInterval(function() {
            updateMosqueInfo();
            updateMarqueeText();
            updateSlides();
            // console.log('Data Masjid, marquee, dan slide diperbarui setiap 63 detik');
        }, 63000); // 63000 milidetik = 63 detik

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
