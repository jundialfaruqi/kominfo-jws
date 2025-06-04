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
                    $('.mosque-name-highlight').text(response.data.name);
                    $('.mosque-address').text(response.data.address);

                    $('.logo-container').empty();
                    if (response.data.logo_masjid) {
                        $('.logo-container').append(
                            `<img src="${response.data.logo_masjid}" alt="Logo Masjid" class="logo logo-masjid">`
                        );
                    }

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
                timeout: 5000,
                success: function(response) {
                    if (response.success && response.timestamp) {
                        const endTime = Date.now();
                        const latency = (endTime - startTime) / 2;
                        serverTimestamp = parseInt(response.timestamp) + latency;
                        pageLoadTimestamp = endTime;
                        // console.log('Waktu server diperbarui:', new Date(serverTimestamp)
                        //     .toISOString());
                    }
                    if (callback) callback();
                },
                error: function(xhr, status, error) {
                    // console.error('Error saat menyinkronkan waktu server:', error);
                    console.warn('Menggunakan waktu lokal sebagai cadangan');
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

        syncServerTime(() => {
            checkAndRestoreSessions();
        });

        setInterval(() => {
            syncServerTime();
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

            $scrollingTextElement.css('animation', 'none');

            $scrollingTextElement.html(`<p>${combinedText}</p>`);

            if ($scrollingTextElement[0]) {
                $scrollingTextElement[0].offsetHeight;
            }
            const textLength = combinedText.length;
            const baseDuration = 60;
            const calculatedDuration = Math.max(baseDuration, textLength / 10);

            const containerWidth = $scrollingTextElement.parent().width();
            const textWidth = $scrollingTextElement.find('p').width();
            const totalDistance = textWidth + containerWidth;
            let animationProgress;
            if (currentPosition === 0) {
                const now = getCurrentTimeFromServer();
                const currentSeconds = now.getSeconds();
                const currentMilliseconds = now.getMilliseconds();
                const totalOffset = (currentSeconds * 1000 + currentMilliseconds) % 60000;
                animationProgress = totalOffset / 60000;
            } else {
                animationProgress = Math.abs(currentPosition) / totalDistance;
            }

            $scrollingTextElement.css({
                'animation': `scrollText ${calculatedDuration}s linear infinite`,
                'animation-delay': `-${animationProgress * calculatedDuration}s`
            });
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
                // console.log('Processing active prayer status from server');
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
                    // console.log(
                    //     `Active adzan phase detected for ${prayerName}, elapsed: ${elapsedSeconds}s, remaining: ${remainingSeconds}s`
                    // );
                    adzanStartTime = nowTime - (elapsedSeconds * 1000);
                    localStorage.setItem('adzanStartTime', adzanStartTime);
                    localStorage.setItem('currentPrayerName', prayerName);
                    localStorage.setItem('currentPrayerTime', prayerTime);
                    currentPrayerName = prayerName;
                    currentPrayerTime = prayerTime;
                    showAdzanPopup(prayerName, prayerTime, true);
                    return;
                } else if (phase === 'iqomah') {
                    // console.log(
                    //     `Active iqomah phase detected for ${prayerName}, elapsed: ${elapsedSeconds}s, remaining: ${remainingSeconds}s`
                    // );
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
                    // console.log(`Final adzan image phase detected for ${prayerName}`);
                    showFinalAdzanImage();
                    return;
                }

                if (isFriday) {
                    // console.log('Friday prayer info detected');
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
                const adzanDuration = 3 * 60;

                if (adzanElapsedSeconds < adzanDuration) {
                    // console.log('Restoring Adzan session from localStorage');
                    showAdzanPopup(currentPrayerName, currentPrayerTime, true);
                } else {
                    // console.log('Adzan finished, starting Iqomah from localStorage');
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
                const iqomahDuration = 420;
                const finalPhaseDuration = 60;

                if (iqomahElapsedSeconds < iqomahDuration) {
                    // console.log('Restoring Iqomah session from localStorage');
                    showIqomahPopup(currentPrayerTime, true);
                } else if (iqomahElapsedSeconds < iqomahDuration + finalPhaseDuration) {
                    // console.log('Iqomah finished, restoring final phase from localStorage');
                    showFinalAdzanImage();
                } else {
                    // console.log('Iqomah and final phase finished, clearing state');
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
                    // console.log(`Waktu shalat ${prayerToRestore.name} terlewat, memulai pemulihan`);
                    const timeDiffSeconds = (currentTimeInMinutes - prayerToRestore.timeInMinutes) * 60;
                    const adzanDuration = 3 * 60;
                    const iqomahDuration = 7 * 60;
                    const finalPhaseDuration = 60;

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
                            // console.log('Waktu shalat sudah lewat lebih dari 10 menit, tidak ada pemulihan');
                            clearAdzanState();
                        }
                    } else {
                        // console.log('Waktu shalat Jumat terlewat, memulai slider gambar Jumat');
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

            if (!isRestored) {
                playBeepSound(3);
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

            const duration = 3 * 60;
            let lastUpdateTime = getCurrentTimeFromServer().getTime();
            isAdzanPlaying = true;
            let adzanUpdateTimeout = null;

            function updateAdzan() {
                const currentTime = getCurrentTimeFromServer().getTime();
                const elapsedSeconds = (currentTime - adzanStartTime) / 1000;
                const timeLeft = duration - elapsedSeconds;

                if (timeLeft <= 0) {
                    $popup.css('display', 'none');
                    isAdzanPlaying = false;
                    if (adzanUpdateTimeout) {
                        clearTimeout(adzanUpdateTimeout);
                        adzanUpdateTimeout = null;
                    }
                    if (prayerName === "Jum'at" && now.getDay() === 5) {
                        updateFridayImages();
                        startFridayImageSlider();
                        const $fridayPopup = $('#fridayInfoPopup');
                        if ($fridayPopup.length) {
                            $fridayPopup.css('display', 'flex');
                        }
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

                adzanUpdateTimeout = setTimeout(updateAdzan, 1000);
            }

            updateAdzan();
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
                            $('#currentIqomahImage').css('opacity', '0');
                            // console.log('Slider Iqomah dihentikan karena tidak ada gambar');
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
            window.iqomahImages = [];
            for (let i = 1; i <= 6; i++) {
                const adzanElement = $(`#adzan${i}`);
                if (adzanElement.val()) {
                    window.iqomahImages.push(adzanElement.val());
                }
            }

            const $iqomahImageElement = $('#currentIqomahImage');

            if (window.iqomahImages.length === 0) {
                console.warn('Tidak ada gambar Iqomah yang tersedia, slider tidak dimulai');
                $iqomahImageElement.css('opacity', '0');
                return;
            }

            if (!iqomahSliderStartTime) {
                iqomahSliderStartTime = getCurrentTimeFromServer().getTime();
                localStorage.setItem('iqomahSliderStartTime', iqomahSliderStartTime);
            }

            let lastIndex = -1;

            function updateIqomahImage() {
                if (!window.iqomahImages || window.iqomahImages.length === 0) {
                    if (iqomahImageSliderInterval) {
                        clearInterval(iqomahImageSliderInterval);
                        iqomahImageSliderInterval = null;
                        $iqomahImageElement.css('opacity', '0');
                        // console.log('Slider Iqomah dihentikan karena tidak ada gambar');
                    }
                    return;
                }

                const now = getCurrentTimeFromServer().getTime();
                const elapsedMs = now - iqomahSliderStartTime;
                const elapsedSeconds = Math.floor(elapsedMs / 1000);
                const currentIndex = Math.floor(elapsedSeconds / 10) % window.iqomahImages.length;

                if (currentIndex !== lastIndex) {
                    lastIndex = currentIndex;

                    $iqomahImageElement.css('opacity', '0');
                    setTimeout(() => {
                        $iqomahImageElement.attr('src', window.iqomahImages[currentIndex]);
                        $iqomahImageElement.css('opacity', '1');
                    }, 250);
                }
            }

            updateIqomahImage();

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
            const iqomahInterval = setInterval(() => {
                if (!isIqomahPlaying) {
                    clearInterval(iqomahInterval);
                    return;
                }

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
                    clearInterval(iqomahInterval);
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
            }, 1000);

            return function cancelIqomah() {
                isIqomahPlaying = false;
                $popup.css('display', 'none');
                clearInterval(iqomahInterval);
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

        function showFinalAdzanImage(duration = 60000) {
            if (currentPrayerName === "Jum'at" && getCurrentTimeFromServer().getDay() === 5) {
                // console.log('Tidak menampilkan final adzan image untuk adzan Jum\'at');
                return;
            }

            // Cek apakah gambar final masih aktif
            if (adzanImageStartTime && adzanImageEndTime) {
                const currentTime = getCurrentTimeFromServer().getTime();
                if (currentTime < adzanImageEndTime) {
                    // console.log('Gambar final masih aktif, menunggu selesai');
                    return;
                }
            }

            const $adzan15 = $('#adzan15');
            if ($adzan15.length && $adzan15.val()) {
                displayAdzanImage($adzan15.val(), false, duration);
            } else {
                console.warn(
                    'Elemen #adzan15 tidak ditemukan atau nilainya kosong, menggunakan gambar default');
                displayAdzanImage('/images/other/lurus-dan-rapatkan-shaf-sholat.png', false, duration);
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
                console.warn('Tidak ada gambar Friday yang tersedia, slider tidak dimulai');
                $fridayImageElement.css('opacity', '0');
                return;
            }

            if (!fridaySliderStartTime) {
                fridaySliderStartTime = getCurrentTimeFromServer().getTime();
                localStorage.setItem('fridaySliderStartTime', fridaySliderStartTime);
            }

            let lastIndex = -1;

            function updateFridayImage() {
                if (!window.fridayImages || window.fridayImages.length === 0) {
                    if (fridayImageSliderInterval) {
                        clearInterval(fridayImageSliderInterval);
                        fridayImageSliderInterval = null;
                        $fridayImageElement.css('opacity', '0');
                        // console.log('Slider Friday dihentikan karena tidak ada gambar');
                    }
                    return;
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

            const displayDuration = 600000;
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
            if (!isRestored) {
                const now = getCurrentTimeFromServer().getTime();
                fridayInfoStartTime = now;
                fridayInfoEndTime = now + 600000; // 10 menit
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
                    'background-size': 'cover',
                    'background-position': 'center',
                    'transition': 'background-image 0.5s ease-in-out'
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
        // checkDayChange();
        // setInterval(checkDayChange, 60000);
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
        }, 30000);

        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                // console.log('Tab kembali aktif, menyinkronkan waktu server');
                syncServerTime();
                checkAndRestoreSessions();
                checkAndRestoreAdzanImage();
                checkAndRestoreFridayInfo();
                handlePrayerTimes();
            }
        });

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
