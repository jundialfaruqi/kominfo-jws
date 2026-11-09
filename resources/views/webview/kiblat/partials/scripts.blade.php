    <script>
        (function() {
            "use strict";

            /* ================= data & rumus ================= */
            const KAABA = {
                lat: 21.4225,
                lon: 39.8262
            };
            const EARTH_R = 6371.0088;
            @include('webview.kiblat.partials.cities-data')

            const toRad = d => d * Math.PI / 180;
            const toDeg = r => r * 180 / Math.PI;
            const norm = d => ((d % 360) + 360) % 360;

            function qiblaBearing(lat, lon) {
                const f1 = toRad(lat),
                    f2 = toRad(KAABA.lat),
                    dl = toRad(KAABA.lon - lon);
                const y = Math.sin(dl);
                const x = Math.cos(f1) * Math.tan(f2) - Math.sin(f1) * Math.cos(dl);
                return norm(toDeg(Math.atan2(y, x)));
            }

            function distanceKm(lat, lon) {
                const f1 = toRad(lat),
                    f2 = toRad(KAABA.lat);
                const df = f2 - f1,
                    dl = toRad(KAABA.lon - lon);
                const a = Math.sin(df / 2) ** 2 + Math.cos(f1) * Math.cos(f2) * Math.sin(dl / 2) ** 2;
                return 2 * EARTH_R * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            function nearestCity(lat, lon) {
                let best = null,
                    bd = Infinity;
                for (const c of CITIES) {
                    const df = toRad(c[2] - lat),
                        dl = toRad(c[3] - lon);
                    const a = Math.sin(df / 2) ** 2 + Math.cos(toRad(lat)) * Math.cos(toRad(c[2])) * Math.sin(dl / 2) **
                        2;
                    if (a < bd) {
                        bd = a;
                        best = c;
                    }
                }
                return best;
            }

            const POINTS = ["Utara", "Timur Laut", "Timur", "Tenggara", "Selatan", "Barat Daya", "Barat", "Barat Laut"];
            const pointName = b => POINTS[Math.round(norm(b) / 45) % 8];

            /* ================= state ================= */
            const state = {
                lat: null,
                lon: null,
                city: null,
                qibla: null,
                heading: 0,
                target: 0,
                sensorOn: false,
                dragging: false,
                jadwal: {
                    today: null,
                    tomorrow: null
                },
                msgUntil: 0
            };

            const $ = id => document.getElementById(id);
            const wrap = $("wrap"),
                dial = $("dial"),
                needle = $("needle"),
                verdict = $("verdict");

            /* ================= kompas ================= */
            function buildDial() {
                const S = 100,
                    C = 50,
                    R = 46;
                let ticks = "",
                    labels = "";
                for (let a = 0; a < 360; a += 5) {
                    const major = a % 30 === 0,
                        cardinal = a % 90 === 0;
                    const r1 = R,
                        r2 = R - (cardinal ? 6 : major ? 4.2 : 2.6);
                    const rad = toRad(a - 90);
                    const x1 = C + r1 * Math.cos(rad),
                        y1 = C + r1 * Math.sin(rad);
                    const x2 = C + r2 * Math.cos(rad),
                        y2 = C + r2 * Math.sin(rad);
                    ticks += `<line x1="${x1.toFixed(2)}" y1="${y1.toFixed(2)}" x2="${x2.toFixed(2)}" y2="${y2.toFixed(2)}"
        stroke="${cardinal ? "#54ccb4" : major ? "rgba(160,200,220,.75)" : "rgba(150,180,200,.32)"}"
        stroke-width="${cardinal ? 1.7 : major ? 1.0 : .55}" stroke-linecap="round"/>`;
                }
                const dirs = {
                    0: "U",
                    90: "T",
                    180: "S",
                    270: "B"
                };
                for (let a = 0; a < 360; a += 30) {
                    const rad = toRad(a - 90),
                        r = R - 11;
                    const x = C + r * Math.cos(rad),
                        y = C + r * Math.sin(rad);
                    if (dirs[a]) {
                        labels += `<text x="${x.toFixed(2)}" y="${(y+2.2).toFixed(2)}" text-anchor="middle"
          font-size="6.6" font-weight="800" fill="#eaf7f5">${dirs[a]}</text>`;
                    } else {
                        labels += `<text x="${x.toFixed(2)}" y="${(y+1.6).toFixed(2)}" text-anchor="middle"
          font-size="3.5" font-weight="600" fill="rgba(200,220,232,.55)">${a}</text>`;
                    }
                }
                dial.innerHTML = `
      <svg viewBox="0 0 ${S} ${S}" style="width:100%;height:100%;display:block">
        <defs>
          <radialGradient id="face" cx="50%" cy="36%" r="72%">
            <stop offset="0%" stop-color="#0b2b46"/>
            <stop offset="62%" stop-color="#07203a"/>
            <stop offset="100%" stop-color="#04162a"/>
          </radialGradient>
          <linearGradient id="rim" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#6fe0d6"/>
            <stop offset="45%" stop-color="#2f9d9d"/>
            <stop offset="100%" stop-color="#0b4a5e"/>
          </linearGradient>
        </defs>
        <circle cx="${C}" cy="${C}" r="${R+1.4}" fill="url(#rim)"/>
        <circle cx="${C}" cy="${C}" r="${R}" fill="url(#face)" stroke="rgba(255,255,255,.10)" stroke-width=".35"/>
        <circle cx="${C}" cy="${C}" r="${R-15.5}" fill="none" stroke="rgba(84,204,180,.28)" stroke-width=".35"/>
        <circle cx="${C}" cy="${C}" r="${R-20.5}" fill="none" stroke="rgba(84,204,180,.16)" stroke-width=".3" stroke-dasharray="1.2 1.8"/>
        ${ticks}${labels}
      </svg>`;
            }

            function buildNeedle() {
                const S = 100,
                    C = 50;
                // jarum: satu segitiga hijau penuh (kedua sisi terisi), tanpa ekor
                const tipY = 6,
                    baseY = 50,
                    halfW = 5.5;
                needle.innerHTML = `
      <svg viewBox="0 0 ${S} ${S}">
        <defs>
          <linearGradient id="head" gradientUnits="userSpaceOnUse" x1="0" y1="${tipY}" x2="0" y2="${baseY}">
            <stop offset="0%" stop-color="#8ff5e4"/>
            <stop offset="55%" stop-color="#3cb4b4"/>
            <stop offset="100%" stop-color="#12867a"/>
          </linearGradient>
        </defs>
        <polygon points="${C},${tipY} ${C+halfW},${baseY} ${C-halfW},${baseY}" fill="url(#head)"/>
      </svg>`;
            }

            buildDial();
            buildNeedle();

            function frame() {
                const d = norm(state.target - state.heading + 180) - 180;
                state.heading = norm(state.heading + d * 0.15);
                dial.style.transform = "rotate(" + (-state.heading) + "deg)";

                if (state.qibla !== null) {
                    // jarum di luar dial → sudut layar = bearing kiblat − heading
                    needle.style.transform = "rotate(" + (state.qibla - state.heading) + "deg)";
                    const off = norm(state.qibla - state.heading + 180) - 180;
                    const ok = Math.abs(off) < 5;
                    wrap.classList.toggle("aligned", ok);
                    if (Date.now() > state.msgUntil) {
                        if (ok) {
                            verdict.innerHTML = `<b>Arah kiblat tepat ✓</b> — panah hijau lurus ke atas layar.`;
                            if (navigator.vibrate && !frame._buzz) {
                                frame._buzz = true;
                                navigator.vibrate(60);
                            }
                        } else {
                            frame._buzz = false;
                            const deg = Math.abs(off);
                            verdict.innerHTML = state.sensorOn ?
                                `Putar perangkat ke <b>${off > 0 ? "kanan" : "kiri"}</b> sekitar <b>${deg < 1 ? deg.toFixed(1) : Math.round(deg)}°</b><br>hingga panah hijau di atas.` :
                                `Seret kompas hingga panah hijau berada di atas. Selisih <b>${Math.round(deg)}°</b>.`;
                        }
                    }
                }
                requestAnimationFrame(frame);
            }
            requestAnimationFrame(frame);

            function say(html, ms) {
                state.msgUntil = Date.now() + (ms || 5000);
                verdict.innerHTML = html;
            }

            /* ================= lokasi ================= */
            function setLocation(lat, lon, city, approx) {
                state.lat = lat;
                state.lon = lon;
                state.city = city || nearestCity(lat, lon);
                state.qibla = qiblaBearing(lat, lon);

                const label = state.city[1] + (approx ? " (terdekat)" : "");
                $("cityLink").textContent = label;
                $("cityKiblat").textContent = label;
                $("kvCity").textContent = label + " — " + state.city[0];

                $("bearingVal").textContent = state.qibla.toFixed(1) + "°";
                $("bearingDir").textContent = pointName(state.qibla);
                $("kvCoord").textContent = lat.toFixed(4) + ", " + lon.toFixed(4);
                $("kvDist").textContent = Math.round(distanceKm(lat, lon)).toLocaleString("id-ID") + " km";
                $("kvQibla").textContent = state.qibla.toFixed(1) + "° " + pointName(state.qibla);

                try {
                    localStorage.setItem("salaam:loc", JSON.stringify({
                        lat,
                        lon,
                        name: state.city[1],
                        prov: state.city[0],
                        id: state.city[4]
                    }));
                } catch (e) {}
                syncSelectors(state.city[0], state.city[1]);
                loadJadwal(state.city[4]);
            }

            function useGPS() {
                const btn = $("btnGps"),
                    hint = $("gpsHint");
                const setStatus = (html, ms) => {
                    if (hint) hint.innerHTML = html;
                    say(html, ms);
                };
                if (!navigator.geolocation) {
                    setStatus("Perangkat tidak mendukung geolokasi. Pilih kab/kota manual di tab Pengaturan.", 8000);
                    return;
                }
                btn.disabled = true;
                btn.textContent = "⏳ Mencari lokasi…";
                setStatus("Sedang mencari lokasi GPS perangkat…", 15000);

                const onSuccess = p => {
                    btn.disabled = false;
                    btn.textContent = "📍 Lokasi Saya";
                    const c = nearestCity(p.coords.latitude, p.coords.longitude);
                    setLocation(p.coords.latitude, p.coords.longitude, c, true);
                    $("kvAcc").textContent = "±" + Math.round(p.coords.accuracy) + " m";
                    setStatus(`Lokasi ditemukan: <b>${c[1]}</b>.`, 6000);
                };

                const onError = err => {
                    btn.disabled = false;
                    btn.textContent = "📍 Lokasi Saya";
                    const msg = err && err.code === 1 ?
                        "Izin lokasi ditolak. Aktifkan izin lokasi di browser/HP, atau pilih kab/kota manual." :
                        "Gagal mengambil lokasi (" + (err ? err.message : "timeout") +
                        "). Pastikan GPS aktif atau pilih manual.";
                    setStatus(msg, 8000);
                };

                navigator.geolocation.getCurrentPosition(
                    onSuccess,
                    err => {
                        // Jika izin ditolak (code 1), langsung tampilkan pesan; jika timeout/unavailable, fallback ke low accuracy
                        if (err && err.code === 1) {
                            onError(err);
                            return;
                        }
                        navigator.geolocation.getCurrentPosition(
                            onSuccess,
                            onError, {
                                enableHighAccuracy: false,
                                timeout: 10000,
                                maximumAge: 300000
                            }
                        );
                    }, {
                        enableHighAccuracy: true,
                        timeout: 8000,
                        maximumAge: 60000
                    }
                );
            }

            /* ================= dropdown kab/kota ================= */
            const provinces = [...new Set(CITIES.map(c => c[0]))].sort((a, b) => a.localeCompare(b, "id"));

            function fillProvinces() {
                $("provSelect").innerHTML = '<option value="">— Pilih provinsi —</option>' +
                    provinces.map(p => `<option value="${p}">${p}</option>`).join("");
            }

            function fillCities(prov) {
                const cs = $("citySelect");
                const list = CITIES.filter(c => c[0] === prov).sort((a, b) => a[1].localeCompare(b[1], "id"));
                cs.innerHTML = '<option value="">— Pilih kabupaten/kota —</option>' +
                    list.map(c => `<option value="${c[1]}">${c[1]}</option>`).join("");
                cs.disabled = !prov;
            }

            function syncSelectors(prov, name) {
                $("provSelect").value = prov;
                if ($("citySelect").dataset.prov !== prov) {
                    fillCities(prov);
                    $("citySelect").dataset.prov = prov;
                }
                $("citySelect").value = name;
            }

            fillProvinces();
            fillCities("");

            // Chrome/Safari bisa memulihkan nilai <select> saat reload tanpa aksi pengguna.
            // Flag ini memastikan hanya pilihan pengguna yang benar-benar mengubah lokasi.
            let userPicking = false;
            ["pointerdown", "touchstart", "keydown"].forEach(ev => {
                $("provSelect").addEventListener(ev, () => {
                    userPicking = true;
                }, true);
                $("citySelect").addEventListener(ev, () => {
                    userPicking = true;
                }, true);
            });

            $("provSelect").addEventListener("change", () => {
                if (!userPicking) {
                    if (state.city) syncSelectors(state.city[0], state.city[1]);
                    return;
                }
                fillCities($("provSelect").value);
            });

            $("citySelect").addEventListener("change", () => {
                const c = CITIES.find(x => x[0] === $("provSelect").value && x[1] === $("citySelect").value);
                if (!c) return;
                if (!userPicking) {
                    if (state.city) syncSelectors(state.city[0], state.city[1]);
                    return;
                }
                userPicking = false;
                setLocation(c[2], c[3], c, false);
                $("kvAcc").textContent = "—";
            });

            /* ================= sensor kompas ================= */
            function onOrient(e) {
                let h = null,
                    acc = null;
                if (typeof e.webkitCompassHeading === "number") {
                    h = e.webkitCompassHeading;
                    acc = e.webkitCompassAccuracy;
                } else if (e.alpha !== null && e.absolute) {
                    h = norm(360 - e.alpha);
                }
                if (h === null) return;
                state.sensorOn = true;
                state.target = h;
                if (acc !== null && acc > 0) $("kvAcc").textContent = "±" + acc.toFixed(0) + "°";
            }

            // Jembatan untuk Aplikasi Native (iOS / Android / Flutter)
            window.updateNativeCompass = function(heading, acc) {
                const h = typeof heading === "string" ? parseFloat(heading) : heading;
                if (typeof h !== "number" || isNaN(h)) return;
                state.sensorOn = true;
                state.target = norm(h);
                if (typeof acc === "number" && acc > 0) {
                    $("kvAcc").textContent = "±" + Math.round(acc) + "°";
                }
            };

            function enableSensor() {
                const OD = window.DeviceOrientationEvent || {};
                const done = () => {
                    window.addEventListener("deviceorientationabsolute", onOrient, true);
                    window.addEventListener("deviceorientation", onOrient, true);
                    say("Sensor aktif. Kalibrasi: gerakkan ponsel membentuk angka <b>8</b>.", 8000);
                };
                if (typeof OD.requestPermission === "function") {
                    OD.requestPermission().then(r => {
                            if (r === "granted") {
                                done();
                            } else {
                                sensorAsked = false;
                                say("Izin sensor ditolak. Cek Pengaturan Safari atau seret kompas manual.", 8000);
                            }
                        })
                        .catch(() => {
                            sensorAsked = false;
                            say("Sensor tidak dapat diakses di aplikasi ini. Silakan seret kompas manual.", 8000);
                        });
                } else if ("ondeviceorientation" in window) {
                    done();
                } else {
                    say("Sensor kompas tidak tersedia. Seret kompas secara manual.", 8000);
                }
            }
            $("btnSensor2").addEventListener("click", enableSensor);

            // Sensor diaktifkan otomatis saat kompas pertama kali disentuh
            // (iOS Safari mewajibkan gestur pengguna click / touchend untuk meminta izin).
            let sensorAsked = false;
            const triggerSensor = () => {
                if (sensorAsked || state.sensorOn) return;
                sensorAsked = true;
                enableSensor();
            };
            $("scrKiblat").addEventListener("click", triggerSensor, true);
            $("scrKiblat").addEventListener("touchend", triggerSensor, { passive: true });
            window.addEventListener("deviceorientationabsolute", onOrient, true);
            window.addEventListener("deviceorientation", onOrient, true);

            /* ================= seret kompas ================= */
            let dragStart = 0,
                dragBase = 0;
            wrap.addEventListener("pointerdown", e => {
                if (state.sensorOn) return;
                state.dragging = true;
                wrap.setPointerCapture(e.pointerId);
                const r = wrap.getBoundingClientRect();
                dragStart = Math.atan2(e.clientY - r.top - r.height / 2, e.clientX - r.left - r.width / 2);
                dragBase = state.target;
            });
            wrap.addEventListener("pointermove", e => {
                if (!state.dragging) return;
                const r = wrap.getBoundingClientRect();
                const a = Math.atan2(e.clientY - r.top - r.height / 2, e.clientX - r.left - r.width / 2);
                const delta = norm(toDeg(a - dragStart) + 180) - 180;
                state.target = norm(dragBase - delta);
            });
            ["pointerup", "pointercancel"].forEach(ev => wrap.addEventListener(ev, () => state.dragging = false));

            /* ================= jadwal sholat (Kemenag) ================= */
            const CARDS = [
                ["subuh", "Subuh"],
                ["terbit", "Syuruq"],
                ["dzuhur", "Dzuhur"],
                ["ashar", "Ashar"],
                ["maghrib", "Maghrib"],
                ["isya", "Isya"]
            ];
            const NEXTABLE = ["subuh", "dzuhur", "ashar", "maghrib", "isya"];
            const LABEL = {
                subuh: "Subuh",
                dzuhur: "Dzuhur",
                ashar: "Ashar",
                maghrib: "Maghrib",
                isya: "Isya"
            };
            const API = "https://api.myquran.com/v2/sholat/jadwal";

            async function fetchDay(cityId, date) {
                const y = date.getFullYear(),
                    m = date.getMonth() + 1,
                    d = date.getDate();
                const key = `salaam:jz:${cityId}:${y}-${m}-${d}`;
                try {
                    const cached = localStorage.getItem(key);
                    if (cached) return JSON.parse(cached);
                } catch (e) {}
                const res = await fetch(`${API}/${cityId}/${y}/${m}/${d}`);
                if (!res.ok) throw new Error("HTTP " + res.status);
                const json = await res.json();
                if (!json || !json.data || !json.data.jadwal) throw new Error("format");
                try {
                    localStorage.setItem(key, JSON.stringify(json.data.jadwal));
                } catch (e) {}
                return json.data.jadwal;
            }

            function at(date, hhmm) {
                const [h, m] = hhmm.split(":").map(Number);
                const t = new Date(date);
                t.setHours(h, m, 0, 0);
                return t;
            }

            function nextPrayer() {
                const now = new Date();
                const cands = [];
                [
                    ["today", 0],
                    ["tomorrow", 1]
                ].forEach(([k, off]) => {
                    const j = state.jadwal[k];
                    if (!j) return;
                    const base = new Date();
                    base.setDate(base.getDate() + off);
                    base.setHours(0, 0, 0, 0);
                    NEXTABLE.forEach(key => {
                        if (!j[key]) return;
                        const t = at(base, j[key]);
                        if (t > now) cands.push({
                            key,
                            label: LABEL[key],
                            time: t,
                            off
                        });
                    });
                });
                cands.sort((a, b) => a.time - b.time);
                return cands[0] || null;
            }

            function fmtRemain(ms) {
                const s = Math.max(0, Math.floor(ms / 1000));
                const h = Math.floor(s / 3600),
                    m = Math.floor((s % 3600) / 60),
                    x = s % 60;
                return [h, m, x].map(v => String(v).padStart(2, "0")).join(":");
            }

            function fmtMMSS(totalSec) {
                const s = Math.max(0, Math.ceil(totalSec));
                const m = Math.floor(s / 60),
                    x = s % 60;
                return [m, x].map(v => String(v).padStart(2, "0")).join(":");
            }

            /* ================= status banner adzan & alert ================= */
            const PHASE_DURATION_SEC = 240; // Default 4 menit (240 detik)
            const IMSAK_DURATION_SEC = 600; // Khusus Imsak 10 menit (600 detik)
            const getPhaseDurationSec = key => key === "imsak" ? IMSAK_DURATION_SEC : PHASE_DURATION_SEC;
            const PHASES = [{
                    key: "imsak",
                    name: "IMSAK",
                    type: "alert"
                },
                {
                    key: "subuh",
                    name: "SUBUH",
                    type: "adzan"
                },
                {
                    key: "terbit",
                    name: "SYURUQ",
                    type: "alert"
                },
                {
                    key: "dhuha",
                    name: "DHUHA",
                    type: "alert"
                },
                {
                    key: "dzuhur",
                    name: "DZUHUR",
                    type: "adzan"
                },
                {
                    key: "ashar",
                    name: "ASHAR",
                    type: "adzan"
                },
                {
                    key: "maghrib",
                    name: "MAGHRIB",
                    type: "adzan"
                },
                {
                    key: "isya",
                    name: "ISYA",
                    type: "adzan"
                }
            ];

            const PREVIEW_MAP = {
                imsak: {
                    key: "imsak",
                    name: "IMSAK",
                    type: "alert",
                    forceFriday: false
                },
                subuh: {
                    key: "subuh",
                    name: "SUBUH",
                    type: "adzan",
                    forceFriday: false
                },
                subuh: {
                    key: "subuh",
                    name: "SUBUH",
                    type: "adzan",
                    forceFriday: false
                },
                syuruq: {
                    key: "terbit",
                    name: "SYURUQ",
                    type: "alert",
                    forceFriday: false
                },
                shuruq: {
                    key: "terbit",
                    name: "SYURUQ",
                    type: "alert",
                    forceFriday: false
                },
                terbit: {
                    key: "terbit",
                    name: "SYURUQ",
                    type: "alert",
                    forceFriday: false
                },
                dhuha: {
                    key: "dhuha",
                    name: "DHUHA",
                    type: "alert",
                    forceFriday: false
                },
                dzuhur: {
                    key: "dzuhur",
                    name: "DZUHUR",
                    type: "adzan",
                    forceFriday: false
                },
                jumat: {
                    key: "dzuhur",
                    name: "JUM'AT",
                    type: "adzan",
                    forceFriday: true
                },
                ashar: {
                    key: "ashar",
                    name: "ASHAR",
                    type: "adzan",
                    forceFriday: false
                },
                maghrib: {
                    key: "maghrib",
                    name: "MAGHRIB",
                    type: "adzan",
                    forceFriday: false
                },
                adzan: {
                    key: "maghrib",
                    name: "MAGHRIB",
                    type: "adzan",
                    forceFriday: false
                },
                isya: {
                    key: "isya",
                    name: "ISYA",
                    type: "adzan",
                    forceFriday: false
                }
            };

            const previewState = (function initPreview() {
                try {
                    const params = new URLSearchParams(window.location.search);
                    const raw = (params.get("preview") || "").trim().toLowerCase();
                    if (!raw || !PREVIEW_MAP[raw]) return null;
                    const cfg = PREVIEW_MAP[raw];
                    const totalSec = getPhaseDurationSec(cfg.key);
                    const customSec = parseInt(params.get("sec") || "", 10);
                    const remainSec = (!isNaN(customSec) && customSec > 0 && customSec <= totalSec) ?
                        customSec :
                        totalSec;
                    const nowMs = Date.now();
                    const startMs = nowMs - (totalSec - remainSec) * 1000;
                    const endMs = startMs + totalSec * 1000;
                    return {
                        ...cfg,
                        startMs,
                        endMs,
                        totalSec
                    };
                } catch (e) {
                    return null;
                }
            })();

            const TIME_RE = /^\d{1,2}:\d{2}$/;

            function getActiveBannerPhase(now) {
                const nowMs = now.getTime();
                const j = state.jadwal.today;
                const dhuhaTime = (j && TIME_RE.test(j.dhuha || "")) ? j.dhuha : "06:18";
                const dzuhurTime = (j && TIME_RE.test(j.dzuhur || "")) ? j.dzuhur : "12:03";

                if (previewState && nowMs < previewState.endMs) {
                    return {
                        name: previewState.name,
                        type: previewState.type,
                        startMs: previewState.startMs,
                        endMs: previewState.endMs,
                        totalSec: previewState.totalSec,
                        dhuhaTime,
                        dzuhurTime
                    };
                }

                if (!j) return null;

                let matched = null;
                for (const p of PHASES) {
                    const hhmm = j[p.key];
                    if (!hhmm || !TIME_RE.test(hhmm)) continue;
                    const totalSec = getPhaseDurationSec(p.key);
                    const startMs = at(now, hhmm).getTime();
                    const endMs = startMs + totalSec * 1000;
                    if (nowMs >= startMs && nowMs < endMs) {
                        const isFriday = now.getDay() === 5;
                        const displayName = (p.name === "DZUHUR" && isFriday) ? "JUM'AT" : p.name;
                        matched = {
                            name: displayName,
                            type: p.type,
                            startMs,
                            endMs,
                            totalSec,
                            dhuhaTime,
                            dzuhurTime
                        };
                    }
                }
                return matched;
            }

            function renderBanner(now) {
                const banner = $("haramBanner"),
                    box = $("haramStatus"),
                    topEl = $("hsTop"),
                    nameEl = $("hsName"),
                    subEl = $("hsSub"),
                    barEl = $("hsBarFill"),
                    botEl = $("hsBottom");
                if (!banner || !box) return;

                const phase = getActiveBannerPhase(now);
                if (!phase) {
                    banner.classList.remove("is-active");
                    box.hidden = true;
                    box.classList.remove("is-imsak", "is-syuruq", "is-dhuha", "is-blinking");
                    if (subEl) subEl.hidden = true;
                    return;
                }

                const remainFloatSec = Math.max(0, (phase.endMs - now.getTime()) / 1000);
                const remainSec = Math.ceil(remainFloatSec);
                const progressPct = Math.min(100, Math.max(0, ((phase.totalSec - remainFloatSec) / phase.totalSec) *
                    100));

                const isImsak = phase.name === "IMSAK";
                const isSyuruq = phase.name === "SYURUQ";
                const isDhuha = phase.name === "DHUHA";
                const isBlinking = !isDhuha && remainSec <= 10 && remainSec > 0;

                let topLabel = "WAKTU";
                if (phase.type === "adzan") {
                    topLabel = "WAKTUNYA ADZAN";
                } else if (isDhuha) {
                    topLabel = "WAKTU SHOLAT SUNAH";
                } else if (isSyuruq) {
                    topLabel = "WAKTU SYURUQ";
                }

                const mainTitle = isSyuruq ? "MENUJU SHOLAT SUNAH SYURUQ" : phase.name;
                const bottomText = isDhuha ? `${phase.dhuhaTime} s/d ${phase.dzuhurTime}` : fmtMMSS(remainSec);

                banner.classList.add("is-active");
                box.hidden = false;
                box.classList.toggle("is-imsak", isImsak);
                box.classList.toggle("is-syuruq", isSyuruq);
                box.classList.toggle("is-dhuha", isDhuha);
                box.classList.toggle("is-blinking", isBlinking);

                if (topEl.textContent !== topLabel) topEl.textContent = topLabel;
                if (nameEl.textContent !== mainTitle) nameEl.textContent = mainTitle;
                if (subEl) {
                    subEl.hidden = !isImsak;
                    if (isImsak && subEl.textContent !== "MENUJU ADZAN SHUBUH") {
                        subEl.textContent = "MENUJU ADZAN SHUBUH";
                    }
                }
                barEl.style.width = `${progressPct.toFixed(2)}%`;
                if (botEl.textContent !== bottomText) botEl.textContent = bottomText;
            }

            let lastNextKey = null;

            function renderGrid() {
                const box = $("prayerGrid");
                const j = state.jadwal.today;
                if (!j) {
                    box.innerHTML = '<p class="hint" style="grid-column:1/-1;text-align:center;color:#2f6485">' +
                        'Pilih lokasi di tab <b>Pengaturan</b> untuk memuat jadwal sholat.</p>';
                    return;
                }
                const nx = nextPrayer();
                lastNextKey = nx ? `${nx.key}:${nx.off}` : "";
                box.innerHTML = CARDS.map(([key, label]) => {
                    const active = nx && nx.key === key;
                    const badge = active ? (nx.off === 1 ? "besok" : "berikutnya") : "";
                    const t = active && nx.off === 1 ? state.jadwal.tomorrow[key] : j[key];
                    return `<div class="pcard${active ? " active" : ""}">
        <div class="n">${label}</div>
        <div class="t">${t || j[key] || "--:--"}</div>
        <span class="b">${badge}</span>
      </div>`;
                }).join("");
            }

            function tick() {
                const now = new Date();
                const nx = nextPrayer();
                const curNextKey = nx ? `${nx.key}:${nx.off}` : "";
                if (state.jadwal.today && lastNextKey !== null && curNextKey !== lastNextKey) {
                    renderGrid();
                }
                document.querySelectorAll("[data-cd-label]").forEach(el =>
                    el.textContent = nx ? `Menuju Salat ${nx.label}` : "Menunggu jadwal");
                document.querySelectorAll("[data-cd-time]").forEach(el =>
                    el.textContent = nx ? nx.time.toTimeString().slice(0, 5) : "--:--");
                document.querySelectorAll("[data-cd-remain]").forEach(el =>
                    el.textContent = nx ? fmtRemain(nx.time - now.getTime()) : "--:--:--");
                renderBanner(now);
            }

            const HIJRI_MONTHS = [
                "Muharram", "Safar", "Rabiul Awal", "Rabiul Akhir",
                "Jumadil Awal", "Jumadil Akhir", "Rajab", "Sya'ban",
                "Ramadhan", "Syawal", "Dzulkaidah", "Dzulhijjah"
            ];

            function hijriString(d) {
                for (const loc of ["en-u-ca-islamic-umalqura", "id-ID-u-ca-islamic-umalqura", "en-u-ca-islamic"]) {
                    try {
                        const parts = new Intl.DateTimeFormat(loc, {
                            day: "numeric",
                            month: "numeric",
                            year: "numeric"
                        }).formatToParts(d);
                        const day = parseInt((parts.find(p => p.type === "day") || {}).value, 10);
                        const month = parseInt((parts.find(p => p.type === "month") || {}).value, 10);
                        const year = parseInt((parts.find(p => p.type === "year") || {}).value, 10);
                        if (day && month >= 1 && month <= 12 && year && year < 1700) {
                            return `${day} ${HIJRI_MONTHS[month - 1]} ${year} H`;
                        }
                    } catch (e) {}
                }
                // Fallback perhitungan kalender Hijriah jika browser tidak mendukung Intl islamic calendar
                const jd = Math.floor(d.getTime() / 86400000) + 2440588;
                const l = jd - 1948440 + 10632;
                const n = Math.floor((l - 1) / 10631);
                const l2 = l - 10631 * n + 354;
                const j = (Math.floor((10985 - l2) / 5316)) * (Math.floor((50 * l2) / 17719)) +
                    (Math.floor(l2 / 5670)) * (Math.floor((43 * l2) / 15238));
                const l3 = l2 - (Math.floor((30 - j) / 15)) * (Math.floor((17719 * j) / 50)) -
                    (Math.floor(j / 16)) * (Math.floor((15238 * j) / 43)) + 29;
                const month = Math.floor((24 * l3) / 709);
                const day = l3 - Math.floor((709 * month) / 24);
                const year = 30 * n + j - 30;
                return `${day} ${HIJRI_MONTHS[(month - 1) % 12]} ${year} H`;
            }

            let lastDayKey = "";

            function updateDates() {
                const d = new Date();
                const dayKey = `${d.getFullYear()}-${d.getMonth() + 1}-${d.getDate()}`;
                $("gregDate").textContent = d.toLocaleDateString("id-ID", {
                    weekday: "long",
                    day: "numeric",
                    month: "long",
                    year: "numeric"
                });
                $("hijriDate").textContent = hijriString(d);
                if (lastDayKey && lastDayKey !== dayKey && state.city) {
                    loadJadwal(state.city[4]);
                }
                lastDayKey = dayKey;
            }

            async function loadJadwal(cityId) {
                const today = new Date();
                const tomorrow = new Date(Date.now() + 864e5);
                $("jadwalNote").innerHTML = "Memuat jadwal <b>Kemenag</b>…";
                try {
                    const [a, b] = await Promise.all([fetchDay(cityId, today), fetchDay(cityId, tomorrow)]);
                    state.jadwal = {
                        today: a,
                        tomorrow: b
                    };
                    $("jadwalNote").innerHTML = "Sumber jadwal: <b>Kemenag</b> (api.myquran.com) — " + (a.tanggal ||
                        "");
                } catch (err) {
                    state.jadwal = {
                        today: null,
                        tomorrow: null
                    };
                    $("jadwalNote").innerHTML =
                        "Jadwal tidak dapat dimuat. Periksa koneksi internet, lalu pilih ulang lokasi.";
                }
                renderGrid();
                tick();
            }

            /* ================= tab ================= */
            const SCREENS = {
                sholat: "scrSholat",
                kiblat: "scrKiblat",
                pengaturan: "scrPengaturan"
            };

            function setTab(tab) {
                Object.keys(SCREENS).forEach(t => {
                    $(SCREENS[t]).hidden = (t !== tab);
                });
                document.querySelectorAll(".nav button").forEach(b =>
                    b.classList.toggle("active", b.dataset.tab === tab));
                window.scrollTo({
                    top: 0,
                    behavior: "instant" in window ? "instant" : "auto"
                });
            }
            document.querySelectorAll(".nav button").forEach(b =>
                b.addEventListener("click", () => setTab(b.dataset.tab)));
            $("cityLink").addEventListener("click", () => setTab("pengaturan"));

            /* ================= kunci zoom & scroll ================= */
            ["gesturestart", "gesturechange", "gestureend"].forEach(ev => {
                document.addEventListener(ev, e => e.preventDefault(), {
                    passive: false
                });
            });
            document.addEventListener("touchstart", e => {
                if (e.touches.length > 1) e.preventDefault();
            }, {
                passive: false
            });
            document.addEventListener("touchmove", e => {
                if (e.touches.length > 1 || (typeof e.scale === "number" && e.scale !== 1)) {
                    e.preventDefault();
                }
            }, {
                passive: false
            });
            let lastTouchEnd = 0;
            document.addEventListener("touchend", e => {
                const now = Date.now();
                if (now - lastTouchEnd <= 300) e.preventDefault();
                lastTouchEnd = now;
            }, {
                passive: false
            });
            document.addEventListener("dblclick", e => e.preventDefault(), {
                passive: false
            });
            document.addEventListener("wheel", e => {
                if (e.ctrlKey) e.preventDefault();
            }, {
                passive: false
            });
            document.addEventListener("keydown", e => {
                if ((e.ctrlKey || e.metaKey) && ["+", "-", "=", "0"].includes(e.key)) {
                    e.preventDefault();
                }
            });

            /* ================= init ================= */
            $("btnGps").addEventListener("click", useGPS);

            (function restore() {
                updateDates();
                let saved = null;
                try {
                    saved = JSON.parse(localStorage.getItem("salaam:loc") || "null");
                } catch (e) {}
                if (saved && typeof saved.lat === "number") {
                    const c = CITIES.find(x => x[1] === saved.name && x[0] === saved.prov) || nearestCity(saved.lat,
                        saved.lon);
                    setLocation(saved.lat, saved.lon, c, false);
                } else if (previewState) {
                    const pku = CITIES.find(x => x[1] === "Kota Pekanbaru") || CITIES[0];
                    setLocation(pku[2], pku[3], pku, false);
                    setTab("sholat");
                } else {
                    setTab("pengaturan");
                    say("Pilih provinsi &amp; kab/kota, atau tekan <b>Lokasi Saya</b>.", 12000);
                }
                setInterval(tick, 1000);
                setInterval(updateDates, 60000);
                tick();
            })();
        })();
    </script>
