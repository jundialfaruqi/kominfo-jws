document.addEventListener("DOMContentLoaded", () => {
    // Ambil waktu server dari input hidden
    const serverTimestamp = parseInt(document.getElementById('server-timestamp').value);
    const pageLoadTimestamp = Date.now();
    const currentMonth = document.getElementById('current-month')?.value || new Date().getMonth() + 1;
    const currentYear = document.getElementById('current-year')?.value || new Date().getFullYear();

    // Get active prayer time status if available
    const activePrayerStatusElement = document.getElementById('active-prayer-status');
    let activePrayerStatus = null;
    if (activePrayerStatusElement && activePrayerStatusElement.value) {
        try {
            activePrayerStatus = JSON.parse(activePrayerStatusElement.value);
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

    // Mendapatkan jadwal sholat secara dinamis jika diperlukan
    async function fetchPrayerTimes() {
        try {
            const now = getCurrentTimeFromServer();
            const month = now.getMonth() + 1; // JavaScript bulan dimulai dari 0
            const year = now.getFullYear();

            // Format bulan dengan leading zero
            const monthFormatted = month.toString().padStart(2, '0');

            const url = `https://raw.githubusercontent.com/lakuapik/jadwalsholatorg/master/adzan/pekanbaru/${year}/${monthFormatted}.json`;

            // Hanya ambil data baru jika berbeda bulan/tahun dari data yang saat ini dimuat
            if (month !== parseInt(currentMonth) || year !== parseInt(currentYear)) {
                console.log(`Mengambil data jadwal baru: ${url}`);
                const response = await fetch(url);
                if (response.ok) {
                    const data = await response.json();

                    // Simpan data baru (dalam kasus ini, Anda mungkin perlu me-reload halaman atau
                    // mengimplementasikan logika untuk memperbarui UI tanpa reload)
                    console.log("Data bulan baru tersedia, memuat ulang halaman...");
                    location.reload(); // Solusi sederhana: reload halaman untuk mendapatkan data baru
                    return data;
                } else {
                    console.error("Gagal mengambil data jadwal sholat");
                    return null;
                }
            }
            return null;
        } catch (error) {
            console.error("Error saat mengambil jadwal sholat:", error);
            return null;
        }
    }

    // Canvas-based Analog Clock
    const canvas = document.getElementById('analogClock');
    const ctx = canvas.getContext('2d');
    let clockRadius = canvas.width / 2 - 10;
    let clockCenter = { x: canvas.width / 2, y: canvas.height / 2 };

    function drawClock() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

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
            ctx.moveTo(
                clockCenter.x + Math.cos(angle) * tickStart,
                clockCenter.y + Math.sin(angle) * tickStart
            );
            ctx.lineTo(
                clockCenter.x + Math.cos(angle) * tickEnd,
                clockCenter.y + Math.sin(angle) * tickEnd
            );
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
                ctx.moveTo(
                    clockCenter.x + Math.cos(angle) * tickStart,
                    clockCenter.y + Math.sin(angle) * tickStart
                );
                ctx.lineTo(
                    clockCenter.x + Math.cos(angle) * tickEnd,
                    clockCenter.y + Math.sin(angle) * tickEnd
                );
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

        const clockText = document.querySelector(".clock-text");
        if (clockText) {
            const displayHours = now.getHours();
            clockText.innerHTML = `${displayHours.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}<br>KOMINFO`;
        }
    }

    function drawHand(angle, length, width, color) {
        ctx.beginPath();
        ctx.moveTo(clockCenter.x, clockCenter.y);
        ctx.lineTo(
            clockCenter.x + Math.cos(angle) * length,
            clockCenter.y + Math.sin(angle) * length
        );
        ctx.strokeStyle = color;
        ctx.lineWidth = width;
        ctx.lineCap = 'round';
        ctx.stroke();
    }

    function animateClock() {
        drawClock();
        requestAnimationFrame(animateClock);
    }

    if (canvas && ctx) {
        animateClock();

        window.addEventListener('resize', function () {
            const clockContainer = document.querySelector('.clock-container');
            if (clockContainer) {
                const containerWidth = clockContainer.offsetWidth;
                const containerHeight = clockContainer.offsetHeight;

                canvas.width = containerWidth;
                canvas.height = containerHeight;

                clockRadius = Math.min(canvas.width, canvas.height) / 2 - 10;
                clockCenter = { x: canvas.width / 2, y: canvas.height / 2 };
            }
        });
    }

    function updateDate() {
        const dateElement = document.querySelector('.date-item');
        const now = getCurrentTimeFromServer();

        // Pastikan moment.js tersedia
        if (typeof moment !== 'undefined') {
            moment.locale('id');

            // Ambil bagian nama hari secara terpisah agar bisa di-styling
            const hari = moment(now).format('dddd');
            const tanggalMasehi = moment(now).format('D MMMM YYYY');

            const masehi = `<span class="day-name">${hari}</span>, ${tanggalMasehi}`;

            // Pastikan moment-hijri tersedia
            if (typeof moment().iDate === 'function') {
                const hijriDate = moment(now).iDate();
                const hijriMonth = moment(now).iMonth();
                const hijriYear = moment(now).iYear();

                const bulanHijriyahID = [
                    'Muharam', 'Safar', 'Rabiulawal', 'Rabiulakhir', 'Jumadilawal', 'Jumadilakhir',
                    'Rajab', 'Syaban', 'Ramadhan', 'Syawal', 'Zulkaidah', 'Zulhijah'
                ];

                const hijri = `${hijriDate} ${bulanHijriyahID[hijriMonth]} ${hijriYear}H`;

                if (dateElement) {
                    dateElement.innerHTML = `${masehi} / ${hijri}`;
                }
            } else {
                // Fallback jika moment-hijri tidak tersedia
                if (dateElement) {
                    dateElement.innerHTML = masehi;
                    console.warn("moment-hijri tidak tersedia");
                }
            }
        } else {
            console.warn("moment.js tidak tersedia");
        }

        // Cek apakah tanggal sudah berganti hari
        const currentDate = now.getDate();
        const storedDate = parseInt(localStorage.getItem('lastCheckedDate') || '0');

        if (currentDate !== storedDate) {
            console.log("Tanggal berubah, memeriksa apakah perlu memperbarui jadwal sholat");
            localStorage.setItem('lastCheckedDate', currentDate);

            // Gunakan timeout untuk menghindari reload berulang kali
            setTimeout(() => {
                location.reload(); // Reload halaman untuk mendapatkan jadwal hari baru
            }, 1000);
        }

        // Cek apakah bulan berubah setiap jam 00:00-00:05
        if (now.getHours() === 0 && now.getMinutes() <= 5) {
            const currentMonthNow = now.getMonth() + 1;
            const storedMonth = parseInt(localStorage.getItem('lastCheckedMonth') || '0');

            if (currentMonthNow !== storedMonth) {
                console.log("Bulan berubah, memperbarui jadwal sholat");
                localStorage.setItem('lastCheckedMonth', currentMonthNow);

                // Ambil data jadwal baru untuk bulan ini
                fetchPrayerTimes();
            }
        }
    }

    function updateScrollingText() {
        const scrollingTextElement = document.querySelector('.scrolling-text');
        const marqueeTexts = [
            document.getElementById('marquee1')?.value || '',
            document.getElementById('marquee2')?.value || '',
            document.getElementById('marquee3')?.value || '',
            document.getElementById('marquee4')?.value || '',
            document.getElementById('marquee5')?.value || '',
            document.getElementById('marquee6')?.value || ''
        ].filter(text => text.trim() !== '');

        if (!scrollingTextElement || marqueeTexts.length === 0) return;

        // Gabungkan semua marquee dengan separator
        const combinedText = marqueeTexts.join(' <span class="separator">•</span> ');

        // Update konten scrolling text
        scrollingTextElement.innerHTML = `<p>${combinedText}</p>`;

        // Gunakan waktu server untuk sinkronisasi
        const now = getCurrentTimeFromServer();
        const currentSeconds = now.getSeconds();
        const currentMilliseconds = now.getMilliseconds();

        // Hitung offset awal berdasarkan waktu server
        const totalOffset = (currentSeconds * 1000 + currentMilliseconds) % 60000;

        scrollingTextElement.style.animationDelay = `-${totalOffset}ms`;
    }

    // Fungsi untuk update marquee
    function initMarquee() {
        updateScrollingText();

        // Perbarui setiap menit untuk mengakomodasi perubahan marquee
        setInterval(updateScrollingText, 60000);
    }

    // Adzan functionality
    const beepSound = new Audio('/sounds/alarm/beep.mp3');
    let adzanTimeout = null;
    let iqomahTimeout = null;
    let adzanImageTimeout = null;
    let currentAdzanIndex = 0;
    let isAdzanPlaying = false;
    let isShuruqAlarmPlaying = false;
    let shuruqAlarmTimeout = null;

    // Retrieve state from localStorage if exists
    let adzanStartTime = localStorage.getItem('adzanStartTime') ? parseInt(localStorage.getItem('adzanStartTime')) : null;
    let iqomahStartTime = localStorage.getItem('iqomahStartTime') ? parseInt(localStorage.getItem('iqomahStartTime')) : null;
    let currentPrayerName = localStorage.getItem('currentPrayerName') || null;
    let currentPrayerTime = localStorage.getItem('currentPrayerTime') || null;

    // Check if we have active Adzan or Iqomah sessions and restore them
    function checkAndRestoreSessions() {
        const now = getCurrentTimeFromServer().getTime();

        // First check if we have server-provided active prayer status
        if (activePrayerStatus) {
            console.log('Processing active prayer status from server');
            const { phase, prayerName, prayerTime, elapsedSeconds, remainingSeconds, progress, isFriday } = activePrayerStatus;

            // Handle based on the phase
            if (phase === 'adzan') {
                console.log(`Active adzan phase detected for ${prayerName}, elapsed: ${elapsedSeconds}s, remaining: ${remainingSeconds}s`);
                // Set the start time based on elapsed seconds
                adzanStartTime = now - (elapsedSeconds * 1000);
                localStorage.setItem('adzanStartTime', adzanStartTime);
                localStorage.setItem('currentPrayerName', prayerName);
                localStorage.setItem('currentPrayerTime', prayerTime);
                currentPrayerName = prayerName;
                currentPrayerTime = prayerTime;

                // Show the adzan popup with the current progress
                showAdzanPopup(prayerName, prayerTime, true);
                return;
            }
            else if (phase === 'iqomah') {
                console.log(`Active iqomah phase detected for ${prayerName}, elapsed: ${elapsedSeconds}s, remaining: ${remainingSeconds}s`);
                // Calculate when adzan started (3 minutes before iqomah)
                adzanStartTime = now - (elapsedSeconds * 1000);
                // Iqomah starts 3 minutes (180 seconds) after adzan
                iqomahStartTime = adzanStartTime + (180 * 1000);

                localStorage.setItem('adzanStartTime', adzanStartTime);
                localStorage.setItem('iqomahStartTime', iqomahStartTime);
                localStorage.setItem('currentPrayerName', prayerName);
                localStorage.setItem('currentPrayerTime', prayerTime);
                currentPrayerName = prayerName;
                currentPrayerTime = prayerTime;

                // Show the iqomah popup
                showIqomahPopup(prayerTime, true);
                return;
            }
            else if (phase === 'final') {
                console.log(`Final adzan image phase detected for ${prayerName}`);
                // Show the final adzan image
                showFinalAdzanImage();
                return;
            }

            // Special handling for Friday
            if (isFriday) {
                console.log('Friday prayer info detected');
                const now = getCurrentTimeFromServer();
                const options = { weekday: 'long', day: '2-digit', month: '2-digit', year: '2-digit' };
                const formattedDate = now.toLocaleDateString('id-ID', options);

                const khatib = document.getElementById('khatib')?.value;
                const imam = document.getElementById('imam')?.value;
                const muadzin = document.getElementById('muadzin')?.value;

                const fridayData = {
                    date: formattedDate,
                    khatib: khatib,
                    imam: imam,
                    muadzin: muadzin
                };

                // Display the Friday info popup
                displayFridayInfoPopup(fridayData);
                return;
            }
        }

        // Fall back to localStorage-based restoration if no server data
        // Check if we have an active Adzan session
        if (adzanStartTime && !iqomahStartTime) {
            const adzanElapsedSeconds = (now - adzanStartTime) / 1000;
            const adzanDuration = 3 * 60; // 3 minutes

            if (adzanElapsedSeconds < adzanDuration) {
                // Adzan is still active, restore it
                console.log('Restoring Adzan session from localStorage');
                showAdzanPopup(currentPrayerName, currentPrayerTime, true);
            } else {
                // Adzan should be finished, start Iqomah
                console.log('Adzan finished, starting Iqomah from localStorage');
                iqomahStartTime = adzanStartTime + (adzanDuration * 1000);
                localStorage.setItem('iqomahStartTime', iqomahStartTime);
                showIqomahPopup(currentPrayerTime, true);
            }
        }
        // Check if we have an active Iqomah session
        else if (iqomahStartTime) {
            const iqomahElapsedSeconds = (now - iqomahStartTime) / 1000;
            const iqomahDuration = 420; // 7 minutes

            if (iqomahElapsedSeconds < iqomahDuration) {
                // Iqomah is still active, restore it
                console.log('Restoring Iqomah session from localStorage');
                showIqomahPopup(currentPrayerTime, true);
            } else {
                // Iqomah should be finished, clear state
                console.log('Iqomah finished, clearing state');
                clearAdzanState();
            }
        }
    }

    // Function to clear Adzan state
    function clearAdzanState() {
        adzanStartTime = null;
        iqomahStartTime = null;
        isAdzanPlaying = false;
        localStorage.removeItem('adzanStartTime');
        localStorage.removeItem('iqomahStartTime');
        localStorage.removeItem('currentPrayerName');
        localStorage.removeItem('currentPrayerTime');
        localStorage.removeItem('iqomahSliderStartTime');

        // Clear any existing timeouts and intervals
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

    // Function to clear Shuruq alarm state
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

    // Fungsi untuk menghitung waktu yang tepat berdasarkan menit saat adzan
    function calculateSyncStartTime(prayerTimeStr) {
        // Ambil waktu server
        const serverTime = getCurrentTimeFromServer();

        // Parse waktu sholat
        const [prayerHours, prayerMinutes] = prayerTimeStr.split(':').map(Number);

        // Buat objek Date untuk waktu sholat pada hari ini
        const prayerTime = new Date(serverTime);
        prayerTime.setHours(prayerHours, prayerMinutes, 0, 0);

        return prayerTime.getTime();
    }

    function showAdzanPopup(prayerName, prayerTimeStr, isRestored = false) {
        const popup = document.getElementById('adzanPopup');
        const title = document.getElementById('adzanTitle');
        const progress = document.getElementById('adzanProgress');
        const countdown = document.getElementById('adzanCountdown');

        title.textContent = ` ${prayerName}`;
        popup.style.display = 'flex';

        // Only play sound if this is a new Adzan, not a restored one
        if (!isRestored) {
            playBeepSound(3);
            progress.style.width = '0%';
        } else if (activePrayerStatus && activePrayerStatus.phase === 'adzan') {
            // If restored from server data, set initial progress
            progress.style.width = `${activePrayerStatus.progress}%`;
        }

        // Gunakan waktu sholat yang tepat sebagai titik awal sinkronisasi
        if (!adzanStartTime) {
            if (isRestored && activePrayerStatus && activePrayerStatus.phase === 'adzan') {
                // If we have server data, use the elapsed time to calculate start time
                const now = getCurrentTimeFromServer().getTime();
                adzanStartTime = now - (activePrayerStatus.elapsedSeconds * 1000);
            } else {
                adzanStartTime = calculateSyncStartTime(prayerTimeStr);
            }
            // Save to localStorage for persistence across reloads
            localStorage.setItem('adzanStartTime', adzanStartTime);
            localStorage.setItem('currentPrayerName', prayerName);
            localStorage.setItem('currentPrayerTime', prayerTimeStr);
        }

        const duration = 3 * 60; // 3 minutes in seconds
        let lastUpdateTime = getCurrentTimeFromServer().getTime();

        // Set isAdzanPlaying flag
        isAdzanPlaying = true;

        // Use requestAnimationFrame for smoother animation
        function updateAdzan(timestamp) {
            const currentTime = getCurrentTimeFromServer().getTime();
            const elapsedSeconds = (currentTime - adzanStartTime) / 1000;
            const timeLeft = duration - elapsedSeconds;

            if (timeLeft <= 0) {
                popup.style.display = 'none';
                isAdzanPlaying = false;

                // Cek apakah ini adzan Jum'at
                if (prayerName === "Jum'at") {
                    // Jika Jum'at, jangan tampilkan Iqomah dan Final Adzan Image
                    // Hanya bersihkan state adzan
                    clearAdzanState();
                } else {
                    // Jika bukan Jum'at, lanjutkan dengan Iqomah seperti biasa
                    // Don't reset adzanStartTime as we need it for Iqomah
                    // Instead, we'll update localStorage with Iqomah start time
                    showIqomahPopup(prayerTimeStr);
                }
                return;
            }

            // Smooth progress bar update
            progress.style.width = `${(elapsedSeconds / duration) * 100}%`;
            progress.style.transition = 'width 0.3s linear';

            // Update countdown text - refresh once per second for efficiency
            if (currentTime - lastUpdateTime >= 1000) {
                const hours = Math.floor(timeLeft / 3600);
                const minutes = Math.floor((timeLeft % 3600) / 60);
                const seconds = Math.floor(timeLeft % 60);
                countdown.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                lastUpdateTime = currentTime;
            }

            // Continue animation loop
            requestAnimationFrame(updateAdzan);
        }

        // Start the animation loop
        requestAnimationFrame(updateAdzan);
    }

    // Function to start the Iqomah image slider
    function startIqomahImageSlider() {
        // Get dynamic adzan images from hidden inputs
        const iqomahImages = [];

        // Add default image
        iqomahImages.push('/images/other/doa-setelah-azan.png');

        // Add dynamic adzan images from the data
        for (let i = 1; i <= 6; i++) {
            const adzanElement = document.getElementById(`adzan${i}`);
            if (adzanElement && adzanElement.value) {
                iqomahImages.push(adzanElement.value);
            }
        }

        const iqomahImageElement = document.getElementById('currentIqomahImage');

        // If this is a new slider session, set the start time
        if (!iqomahSliderStartTime) {
            iqomahSliderStartTime = getCurrentTimeFromServer().getTime();
            localStorage.setItem('iqomahSliderStartTime', iqomahSliderStartTime);
        }

        // Calculate which image should be showing based on elapsed time
        const now = getCurrentTimeFromServer().getTime();
        const elapsedMs = now - iqomahSliderStartTime;
        const elapsedSeconds = Math.floor(elapsedMs / 1000);
        const currentIndex = Math.floor(elapsedSeconds / 10) % iqomahImages.length;

        // Set the current image based on elapsed time
        iqomahImageElement.src = iqomahImages[currentIndex];

        // Calculate time until next image change
        const msUntilNextChange = 10000 - (elapsedMs % 10000);

        // Schedule the first change at the exact time it should happen
        setTimeout(() => {
            // Update to the next image
            const nextIndex = (currentIndex + 1) % iqomahImages.length;
            iqomahImageElement.src = iqomahImages[nextIndex];

            // Then start the regular interval
            iqomahImageSliderInterval = setInterval(() => {
                // Calculate which image should be showing based on current time
                const currentTime = getCurrentTimeFromServer().getTime();
                const elapsedMs = currentTime - iqomahSliderStartTime;
                const elapsedSeconds = Math.floor(elapsedMs / 1000);
                const imageIndex = Math.floor(elapsedSeconds / 10) % iqomahImages.length;

                iqomahImageElement.src = iqomahImages[imageIndex];
            }, 10000); // 10 seconds
        }, msUntilNextChange);
    }

    function showIqomahPopup(prayerTimeStr, isRestored = false) {
        const popup = document.getElementById('iqomahPopup');
        const progress = document.getElementById('iqomahProgress');
        const countdown = document.getElementById('iqomahCountdown');

        popup.style.display = 'flex';

        // Set initial progress based on whether this is a restored session
        if (!isRestored) {
            progress.style.width = '0%';
        } else if (activePrayerStatus && activePrayerStatus.phase === 'iqomah') {
            // If restored from server data, set initial progress
            progress.style.width = `${activePrayerStatus.progress}%`;
        }

        // Start the image slider
        startIqomahImageSlider();

        // Gunakan waktu adzan + durasi adzan sebagai titik awal iqomah
        if (!iqomahStartTime) {
            if (isRestored && activePrayerStatus && activePrayerStatus.phase === 'iqomah') {
                // If we have server data, calculate iqomah start time based on elapsed seconds
                const now = getCurrentTimeFromServer().getTime();
                // Iqomah starts 3 minutes (180 seconds) after adzan
                // We need to calculate when adzan started first
                adzanStartTime = now - ((activePrayerStatus.elapsedSeconds + 180) * 1000);
                iqomahStartTime = adzanStartTime + (180 * 1000);
                localStorage.setItem('adzanStartTime', adzanStartTime);
            } else {
                // Jika adzanStartTime tidak ada, hitung dari waktu sholat
                if (!adzanStartTime) {
                    adzanStartTime = calculateSyncStartTime(prayerTimeStr);
                    localStorage.setItem('adzanStartTime', adzanStartTime);
                }
                // Iqomah dimulai setelah adzan selesai (3 menit)
                iqomahStartTime = adzanStartTime + (3 * 60 * 1000);
            }
            // Save to localStorage for persistence across reloads
            localStorage.setItem('iqomahStartTime', iqomahStartTime);
        }

        const duration = 420; // 7 minutes in seconds
        let lastUpdateTime = getCurrentTimeFromServer().getTime();

        let isIqomahPlaying = true;
        let hasPlayedFinalBeep = false; // Flag untuk beep 2 detik sebelum selesai

        function updateIqomah() {
            if (!isIqomahPlaying) return;

            const currentTime = getCurrentTimeFromServer().getTime();
            const elapsedSeconds = (currentTime - iqomahStartTime) / 1000;
            const timeLeft = duration - elapsedSeconds;

            // Putar beep jika waktu tinggal 4 detik atau kurang
            if (timeLeft <= 4 && !hasPlayedFinalBeep) {
                playBeepSound(3);
                hasPlayedFinalBeep = true;
            }

            if (timeLeft <= 0) {
                popup.style.display = 'none';
                isIqomahPlaying = false;
                clearAdzanState(); // Clear all state when Iqomah is done
                showFinalAdzanImage();
                return;
            }

            // Update progress bar
            progress.style.width = `${(elapsedSeconds / duration) * 100}%`;
            progress.style.transition = 'width 0.3s linear';

            // Update countdown setiap 1 detik
            if (currentTime - lastUpdateTime >= 1000) {
                const hours = Math.floor(timeLeft / 3600);
                const minutes = Math.floor((timeLeft % 3600) / 60);
                const seconds = Math.floor(timeLeft % 60);
                countdown.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                lastUpdateTime = currentTime;
            }

            requestAnimationFrame(updateIqomah);
        }

        requestAnimationFrame(updateIqomah);

        return function cancelIqomah() {
            isIqomahPlaying = false;
            popup.style.display = 'none';
        };
    }

    // Variables to track final Adzan image state
    let adzanImageStartTime = localStorage.getItem('adzanImageStartTime') ? parseInt(localStorage.getItem('adzanImageStartTime')) : null;
    let adzanImageEndTime = localStorage.getItem('adzanImageEndTime') ? parseInt(localStorage.getItem('adzanImageEndTime')) : null;
    let adzanImageSrc = localStorage.getItem('adzanImageSrc') || null;

    // Function to check and restore final Adzan image
    function checkAndRestoreAdzanImage() {
        const now = getCurrentTimeFromServer().getTime();

        if (adzanImageStartTime && adzanImageEndTime && now >= adzanImageStartTime && now < adzanImageEndTime && adzanImageSrc) {
            console.log('Restoring Adzan final image');
            displayAdzanImage(adzanImageSrc, true);
        } else if (adzanImageEndTime && now >= adzanImageEndTime) {
            // Clear Adzan image state if it's past end time
            clearAdzanImageState();
        }
    }

    // Function to clear Adzan image state
    function clearAdzanImageState() {
        adzanImageStartTime = null;
        adzanImageEndTime = null;
        adzanImageSrc = null;
        localStorage.removeItem('adzanImageStartTime');
        localStorage.removeItem('adzanImageEndTime');
        localStorage.removeItem('adzanImageSrc');
    }

    // Function to display Adzan image
    function displayAdzanImage(imageSrc, isRestored = false) {
        const imageDisplay = document.getElementById('adzanImageDisplay');
        const imageElement = document.getElementById('currentAdzanImage');

        imageElement.src = imageSrc;
        imageDisplay.style.display = 'flex';

        // If this is a new display (not restored), calculate and store end time
        if (!isRestored) {
            const now = getCurrentTimeFromServer().getTime();
            adzanImageStartTime = now;
            adzanImageEndTime = now + (60000); // 1 minutes
            adzanImageSrc = imageSrc;

            localStorage.setItem('adzanImageStartTime', adzanImageStartTime);
            localStorage.setItem('adzanImageEndTime', adzanImageEndTime);
            localStorage.setItem('adzanImageSrc', imageSrc);
        }

        // Calculate remaining time for timeout
        const remainingTime = adzanImageEndTime - getCurrentTimeFromServer().getTime();

        // Only set timeout if there's remaining time
        if (remainingTime > 0) {
            setTimeout(() => {
                imageDisplay.style.display = 'none';
                clearAdzanImageState();
            }, remainingTime);
        } else {
            imageDisplay.style.display = 'none';
            clearAdzanImageState();
        }
    }

    function showFinalAdzanImage() {
        // Check if we already have an active Adzan image display
        if (adzanImageStartTime && adzanImageEndTime) {
            const currentTime = getCurrentTimeFromServer().getTime();
            if (currentTime < adzanImageEndTime) {
                return; // Adzan image is already being displayed
            }
        }

        const adzan15 = document.querySelector('[id="adzan15"]');
        if (adzan15 && adzan15.value) {
            displayAdzanImage(adzan15.value);
        }
    }

    // Variables to track Friday info state
    let fridayInfoStartTime = localStorage.getItem('fridayInfoStartTime') ? parseInt(localStorage.getItem('fridayInfoStartTime')) : null;
    let fridayInfoEndTime = localStorage.getItem('fridayInfoEndTime') ? parseInt(localStorage.getItem('fridayInfoEndTime')) : null;
    let fridayInfoData = localStorage.getItem('fridayInfoData') ? JSON.parse(localStorage.getItem('fridayInfoData')) : null;
    let fridayImageSliderInterval = null;
    let fridaySliderStartTime = localStorage.getItem('fridaySliderStartTime') ? parseInt(localStorage.getItem('fridaySliderStartTime')) : null;

    // Variables to track Iqomah image slider state
    let iqomahImageSliderInterval = null;
    let iqomahSliderStartTime = localStorage.getItem('iqomahSliderStartTime') ? parseInt(localStorage.getItem('iqomahSliderStartTime')) : null;

    // Function to check and restore Friday info popup
    function checkAndRestoreFridayInfo() {
        const now = getCurrentTimeFromServer().getTime();

        if (fridayInfoStartTime && fridayInfoEndTime && now >= fridayInfoStartTime && now < fridayInfoEndTime) {
            console.log('Restoring Friday info popup');
            displayFridayInfoPopup(fridayInfoData, true);
        } else if (fridayInfoEndTime && now >= fridayInfoEndTime) {
            // Clear Friday info state if it's past end time
            clearFridayInfoState();
        }
    }

    // Function to clear Friday info state
    function clearFridayInfoState() {
        fridayInfoStartTime = null;
        fridayInfoEndTime = null;
        fridayInfoData = null;
        fridaySliderStartTime = null;
        localStorage.removeItem('fridayInfoStartTime');
        localStorage.removeItem('fridayInfoEndTime');
        localStorage.removeItem('fridayInfoData');
        localStorage.removeItem('fridaySliderStartTime');

        // Clear the image slider interval if it exists
        if (fridayImageSliderInterval) {
            clearInterval(fridayImageSliderInterval);
            fridayImageSliderInterval = null;
        }
    }

    // Function to start the Friday image slider
    function startFridayImageSlider() {
        // Get dynamic adzan images from hidden inputs
        const fridayImages = [];

        // Add default image
        fridayImages.push('/images/other/doa-setelah-azan.png');

        // Add dynamic adzan images from the data
        for (let i = 1; i <= 6; i++) {
            const adzanElement = document.getElementById(`adzan${i}`);
            if (adzanElement && adzanElement.value) {
                fridayImages.push(adzanElement.value);
            }
        }

        const fridayImageElement = document.getElementById('currentFridayImage');

        // If this is a new slider session, set the start time
        if (!fridaySliderStartTime) {
            fridaySliderStartTime = getCurrentTimeFromServer().getTime();
            localStorage.setItem('fridaySliderStartTime', fridaySliderStartTime);
        }

        // Calculate which image should be showing based on elapsed time
        const now = getCurrentTimeFromServer().getTime();
        const elapsedMs = now - fridaySliderStartTime;
        const elapsedSeconds = Math.floor(elapsedMs / 1000);
        const currentIndex = Math.floor(elapsedSeconds / 10) % fridayImages.length;

        // Set the current image based on elapsed time
        fridayImageElement.src = fridayImages[currentIndex];

        // Calculate time until next image change
        const msUntilNextChange = 10000 - (elapsedMs % 10000);

        // Schedule the first change at the exact time it should happen
        setTimeout(() => {
            // Update to the next image
            const nextIndex = (currentIndex + 1) % fridayImages.length;
            fridayImageElement.src = fridayImages[nextIndex];

            // Then start the regular interval
            fridayImageSliderInterval = setInterval(() => {
                // Calculate which image should be showing based on current time
                const currentTime = getCurrentTimeFromServer().getTime();
                const elapsedMs = currentTime - fridaySliderStartTime;
                const elapsedSeconds = Math.floor(elapsedMs / 1000);
                const imageIndex = Math.floor(elapsedSeconds / 10) % fridayImages.length;

                fridayImageElement.src = fridayImages[imageIndex];
            }, 10000); // 10 seconds
        }, msUntilNextChange);
    }

    // Function to display Friday info popup
    function displayFridayInfoPopup(data, isRestored = false) {
        const popup = document.getElementById('fridayInfoPopup');
        const dateElement = document.getElementById('fridayDate');
        const officialsElement = document.getElementById('fridayOfficials');

        // Format date using the same format as date-item
        const now = getCurrentTimeFromServer();

        // Use moment.js for consistent formatting
        if (typeof moment !== 'undefined') {
            moment.locale('id');

            // Get day name and date in Masehi format
            const hari = moment(now).format('dddd');
            const tanggalMasehi = moment(now).format('D MMMM YYYY');
            let formattedDate = `<span class="day-name">${hari}</span>, ${tanggalMasehi}`;

            // Add Hijri date if available
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

            dateElement.innerHTML = formattedDate;
        } else {
            // Fallback if moment.js is not available
            dateElement.textContent = data.date;
        }

        // Set officials
        let officialsHtml = '<table class="responsive-table">';

        if (data.khatib) {
            officialsHtml += `
      <tr>
          <td style="font-weight: bold;">KHATIB</td>
          <td>:</td>
          <td>${data.khatib}</td>
      </tr>`;
        }

        if (data.imam) {
            officialsHtml += `
      <tr>
          <td style="font-weight: bold;">IMAM</td>
          <td>:</td>
          <td>${data.imam}</td>
      </tr>`;
        }

        if (data.muadzin) {
            officialsHtml += `
      <tr>
          <td style="font-weight: bold;">MUADZIN</td>
          <td>:</td>
          <td>${data.muadzin}</td>
      </tr>`;
        }

        officialsHtml += '</table>';

        officialsElement.innerHTML = officialsHtml;
        popup.style.display = 'flex';

        // Start the image slider
        startFridayImageSlider();

        // If this is a new display (not restored), calculate and store end time
        if (!isRestored) {
            const now = getCurrentTimeFromServer().getTime();
            fridayInfoStartTime = now;
            fridayInfoEndTime = now + (600000); // 10 minutes (600,000 ms)

            localStorage.setItem('fridayInfoStartTime', fridayInfoStartTime);
            localStorage.setItem('fridayInfoEndTime', fridayInfoEndTime);
            localStorage.setItem('fridayInfoData', JSON.stringify(data));
        }

        // Calculate remaining time for timeout
        const remainingTime = fridayInfoEndTime - getCurrentTimeFromServer().getTime();

        // Only set timeout if there's remaining time
        if (remainingTime > 0) {
            setTimeout(() => {
                popup.style.display = 'none';
                clearFridayInfoState();
            }, remainingTime);
        } else {
            popup.style.display = 'none';
            clearFridayInfoState();
        }
    }

    // Flag to track if adzan for Jum'at has been shown
    let jumatAdzanShown = localStorage.getItem('jumatAdzanShown') === 'true';

    function showFridayInfo() {
        const now = getCurrentTimeFromServer();
        const dayOfWeek = now.getDay();

        // If we already have active Friday info, don't show it again
        if (fridayInfoStartTime && fridayInfoEndTime) {
            const currentTime = now.getTime();
            if (currentTime < fridayInfoEndTime) {
                return; // Friday info is already being displayed
            }
        }

        if (dayOfWeek === 5) { // Friday
            const prayerTimes = document.querySelectorAll('.prayer-time');
            const jumatTime = Array.from(prayerTimes).find(time =>
                time.querySelector('.prayer-name').textContent.includes('Jum\'at')
            );

            if (jumatTime) {
                const jumatTimeValue = jumatTime.querySelector('.prayer-time-value').textContent;
                const [hours, minutes] = jumatTimeValue.split(':').map(Number);
                const jumatTimeInMinutes = hours * 60 + minutes;
                const currentTimeInMinutes = now.getHours() * 60 + now.getMinutes();
                const currentTimeFormatted = `${now.getHours().toString().padStart(2, "0")}:${now.getMinutes().toString().padStart(2, "0")}`;

                // Check if it's exactly Jum'at prayer time and adzan hasn't been shown yet
                if (jumatTimeValue === currentTimeFormatted && !isAdzanPlaying && !jumatAdzanShown) {
                    // Show adzan popup for Jum'at
                    showAdzanPopup('Jum\'at', jumatTimeValue);
                    jumatAdzanShown = true;
                    localStorage.setItem('jumatAdzanShown', 'true');
                    return; // Exit to prevent showing Friday info popup immediately
                }

                // Check if we're within 10 minutes after Jum'at prayer time and adzan has been shown
                if (currentTimeInMinutes >= jumatTimeInMinutes && currentTimeInMinutes <= jumatTimeInMinutes + 10 && !isAdzanPlaying) {
                    // Prepare data for Friday info
                    const options = { weekday: 'long', day: '2-digit', month: '2-digit', year: '2-digit' };
                    const formattedDate = now.toLocaleDateString('id-ID', options);

                    const khatib = document.getElementById('khatib')?.value;
                    const imam = document.getElementById('imam')?.value;
                    const muadzin = document.getElementById('muadzin')?.value;

                    const fridayData = {
                        date: formattedDate,
                        khatib: khatib,
                        imam: imam,
                        muadzin: muadzin
                    };

                    // Display the popup
                    displayFridayInfoPopup(fridayData);
                }
            }
        } else {
            // Reset the Jum'at adzan flag if it's not Friday
            jumatAdzanShown = false;
            localStorage.removeItem('jumatAdzanShown');
        }
    }

    // Fungsi utama untuk mengelola jadwal sholat
    function handlePrayerTimes() {
        const now = getCurrentTimeFromServer();
        const currentTimeInMinutes = now.getHours() * 60 + now.getMinutes();
        const currentTimeFormatted = `${now.getHours().toString().padStart(2, "0")}:${now.getMinutes().toString().padStart(2, "0")}`;

        // Periksa apakah hari ini adalah Jumat (5) dan update label Dzuhur jika perlu
        const isFriday = now.getDay() === 5; // 0 = Minggu, 5 = Jumat dalam JavaScript

        // Ambil elemen prayer-times dari PHP
        const prayerTimesElements = document.querySelectorAll('.prayer-time');
        if (!prayerTimesElements.length) return;

        // Format data prayer times dari elemen-elemen tersebut
        const prayerTimes = [];
        prayerTimesElements.forEach((element, index) => {
            const nameElement = element.querySelector('.prayer-name');
            const timeElement = element.querySelector('.prayer-time-value');

            if (nameElement && timeElement) {
                // Ubah nama Dzuhur menjadi Jum'at jika hari ini Jumat
                // Biasanya Dzuhur adalah index ke-2 (setelah Shubuh dan Shuruq)
                if (isFriday && index === 2 && nameElement.textContent.trim() === "Dzuhur") {
                    nameElement.textContent = "Jum'at";
                }

                const name = nameElement.textContent.trim();
                const time = timeElement.textContent.trim();
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

        // PERUBAHAN: Tentukan waktu sholat yang aktif saat ini dan waktu sholat berikutnya
        let activePrayerIndex = -1;
        let nextPrayerIndex = -1;

        // Cari waktu sholat berikutnya (yang belum terjadi)
        let nextPrayerTime = Infinity;
        for (let i = 0; i < prayerTimes.length; i++) {
            // Jika waktu sholat ini belum terjadi dan lebih dekat dari waktu sholat berikutnya yang sudah ditemukan
            if (prayerTimes[i].timeInMinutes > currentTimeInMinutes &&
                (nextPrayerTime === Infinity || prayerTimes[i].timeInMinutes < nextPrayerTime)) {
                nextPrayerIndex = i;
                nextPrayerTime = prayerTimes[i].timeInMinutes;
            }
        }

        // Jika tidak ada waktu sholat berikutnya hari ini, berarti waktu sholat berikutnya adalah yang pertama (Shubuh)
        if (nextPrayerIndex === -1) {
            nextPrayerIndex = 0;
        }

        // Cari waktu sholat yang aktif (waktu sholat terakhir yang sudah lewat)
        let lastPrayerTime = -1;
        for (let i = 0; i < prayerTimes.length; i++) {
            // Jika waktu sholat ini sudah terjadi dan lebih baru dari waktu sholat aktif yang sudah ditemukan
            if (prayerTimes[i].timeInMinutes <= currentTimeInMinutes && prayerTimes[i].timeInMinutes > lastPrayerTime) {
                activePrayerIndex = i;
                lastPrayerTime = prayerTimes[i].timeInMinutes;
            }
        }

        // Jika tidak ada waktu sholat yang sudah lewat, berarti waktu aktif adalah waktu sholat terakhir dari hari sebelumnya (Isya)
        if (activePrayerIndex === -1) {
            activePrayerIndex = prayerTimes.length - 1;
        }

        // Highlight waktu sholat aktif dan berikutnya
        prayerTimesElements.forEach((element, index) => {
            element.classList.remove('active');
            element.classList.remove('next-prayer');

            if (index === activePrayerIndex) {
                element.classList.add('active');
            }

            if (index === nextPrayerIndex) {
                element.classList.add('next-prayer');
            }
        });

        // Update countdown timer
        const nextPrayer = prayerTimes[nextPrayerIndex];
        let timeDiffInMinutes = nextPrayer.timeInMinutes - currentTimeInMinutes;

        // Jika waktu sholat berikutnya adalah di hari berikutnya
        if (timeDiffInMinutes < 0) {
            timeDiffInMinutes += 24 * 60; // Tambah 24 jam
        }

        // PERBAIKAN: Perhitungan countdown yang akurat dengan mempertimbangkan detik
        const totalSecondsRemaining = timeDiffInMinutes * 60 - now.getSeconds();

        const hours = Math.floor(totalSecondsRemaining / 3600);
        const minutes = Math.floor((totalSecondsRemaining % 3600) / 60);
        const seconds = totalSecondsRemaining % 60;

        // Format waktu countdown
        const countdownFormatted = `${hours.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")}:${seconds.toString().padStart(2, "0")}`;

        // Update elemen countdown
        const nextPrayerLabel = document.getElementById("next-prayer-label");
        const countdownValue = document.getElementById("countdown-value");

        if (nextPrayerLabel) {
            nextPrayerLabel.textContent = nextPrayer.name;
        }

        if (countdownValue) {
            countdownValue.textContent = countdownFormatted;
        }

        // Check if current time matches prayer time
        prayerTimes.forEach((prayer, index) => {
            if (prayer.time === currentTimeFormatted && !isAdzanPlaying) {
                // Special handling for Shuruq - only play beep sound twice
                if (prayer.name.toLowerCase().includes('shuruq') || prayer.name.toLowerCase().includes('syuruq') || prayer.name.toLowerCase().includes('terbit')) {
                    // Only play if not already playing
                    if (!isShuruqAlarmPlaying) {
                        console.log('Shuruq time - playing beep sound only');
                        isShuruqAlarmPlaying = true;

                        // Save the time to localStorage to prevent replaying
                        localStorage.setItem('shuruqAlarmTime', currentTimeFormatted);

                        playBeepSound(2);

                        // Set a timeout to clear the state after 1 minute
                        shuruqAlarmTimeout = setTimeout(() => {
                            clearShuruqAlarmState();
                        }, 60000); // 1 minute
                    }
                } else {
                    // For all other prayer times, show the adzan popup
                    showAdzanPopup(prayer.name, prayer.time);
                }
            }
        });

        // Check for Friday info
        showFridayInfo();
    }

    // Function to manage slide display
    function manageSlideDisplay() {
        const mosqueImageElement = document.querySelector('.mosque-image');
        if (!mosqueImageElement) return;

        // Retrieve slide URLs from hidden inputs
        const slideUrls = [
            document.getElementById('slide1')?.value || '',
            document.getElementById('slide2')?.value || '',
            document.getElementById('slide3')?.value || '',
            document.getElementById('slide4')?.value || '',
            document.getElementById('slide5')?.value || '',
            document.getElementById('slide6')?.value || ''
        ].filter(url => url.trim() !== ''); // Remove empty URLs

        // If no slides, keep the existing background
        if (slideUrls.length === 0) return;

        let currentSlideIndex = 0;

        function updateSlide() {
            // Get current server time
            const now = getCurrentTimeFromServer();
            const currentSeconds = now.getSeconds();

            // Calculate slide index based on server time seconds
            // Divide 60 seconds by number of slides to determine display duration
            const slideDuration = Math.floor(60 / slideUrls.length);
            currentSlideIndex = Math.floor(currentSeconds / slideDuration) % slideUrls.length;

            // Update background image
            mosqueImageElement.style.backgroundImage = `url("${slideUrls[currentSlideIndex]}")`;
            mosqueImageElement.style.backgroundSize = 'cover';
            mosqueImageElement.style.backgroundPosition = 'center';
        }

        // Initial update
        updateSlide();

        // Update every second to sync with server time
        setInterval(updateSlide, 1000);
    }

    manageSlideDisplay();

    updateScrollingText();
    setInterval(updateScrollingText, 60000); // update tiap menit bila perlu

    // Check for any active Adzan or Iqomah sessions on page load
    checkAndRestoreSessions();

    // Check for any active Friday info popup on page load
    checkAndRestoreFridayInfo();

    // Check for any active Adzan final image on page load
    checkAndRestoreAdzanImage();

    // Check if we have a recent Shuruq alarm to prevent replaying
    const savedShuruqTime = localStorage.getItem('shuruqAlarmTime');
    if (savedShuruqTime) {
        const now = getCurrentTimeFromServer();
        const currentTimeFormatted = `${now.getHours().toString().padStart(2, "0")}:${now.getMinutes().toString().padStart(2, "0")}`;

        // If the current time is still the same as the saved Shuruq time, set the flag
        if (savedShuruqTime === currentTimeFormatted) {
            isShuruqAlarmPlaying = true;

            // Set a timeout to clear the state after 1 minute from page load
            shuruqAlarmTimeout = setTimeout(() => {
                clearShuruqAlarmState();
            }, 60000); // 1 minute
        } else {
            // If it's a different time, clear the saved state
            clearShuruqAlarmState();
        }
    }

    // Inisialisasi dan interval untuk update
    handlePrayerTimes();
    setInterval(handlePrayerTimes, 1000); // Update setiap detik untuk countdown yang akurat

    updateDate(); // langsung jalan tanpa tunggu
    setInterval(updateDate, 60000); // Update tanggal setiap menit

    // Panggil fungsi inisialisasi marquee
    initMarquee();

    // Periksa perubahan bulan/tahun setiap jam
    setInterval(() => {
        const now = getCurrentTimeFromServer();
        // Konversi ke format yang bisa dibandingkan
        const currentMonthYear = `${now.getFullYear()}-${(now.getMonth() + 1).toString().padStart(2, '0')}`;
        const storedMonthYear = `${currentYear}-${currentMonth.toString().padStart(2, '0')}`;

        if (currentMonthYear !== storedMonthYear) {
            console.log("Bulan/tahun berubah, memperbarui jadwal sholat");
            fetchPrayerTimes();
        }
    }, 3600000); // Periksa setiap jam


});