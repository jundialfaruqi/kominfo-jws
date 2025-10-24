/* ============================================================================
   slider-updater.js
   - Combined updateSlider + manageSlideDisplay
   - Compatible with Blade:
     <div class="mosque-image">
         @foreach ($slidePaths as $i => $url)
           <img id="slide{{ $i+1 }}" src="{{ $url }}" style="display:none">
         @endforeach
     </div>
   ============================================================================
*/
(function () {
    'use strict';

    // CONFIG
    const DEFAULT_IMG = '/images/other/slide-jws-default.jpg';
    const SLIDE_ID_PREFIX = 'slide';
    const SLIDE_CONTAINER_ID = '#slide-container'; // optional: fallback if no .mosque-image children
    const SLIDER_REFRESH_INTERVAL = 15 * 1000; // ms between API refreshes

    // global caches/state
    if (!window.imageCache) window.imageCache = {};
    if (!window.slideUrls) window.slideUrls = [];
    let sliderUpdateInFlight = false;
    let lastSliderFetch = 0;

    // ---------- Helpers ----------
    async function newPreloadImages(urls = []) {
        if (!window.imageCache) window.imageCache = {};
        const promises = urls.map(url => {
            return new Promise((resolve) => {
                try {
                    url = String(url);
                    const cached = window.imageCache[url];
                    if (cached && cached.complete) return resolve(url);

                    const img = new Image();
                    img.onload = () => {
                        img.complete = true;
                        window.imageCache[url] = img;
                        resolve(url);
                    };
                    img.onerror = () => {
                        window.imageCache[url] = img;
                        resolve(url);
                    };
                    img.src = url;
                    window.imageCache[url] = img;
                } catch (e) {
                    resolve(url);
                }
            });
        });

        return Promise.all(promises).then(results => {
            console.debug('✅ preload finished, cached keys:', Object.keys(window.imageCache));
            return results;
        });
    }

    function newClearUnusedCache(keepUrls = []) {
        if (!window.imageCache) return;
        const keepSet = new Set(keepUrls.map(u => String(u)));
        Object.keys(window.imageCache).forEach(u => {
            if (!keepSet.has(u)) {
                try { delete window.imageCache[u]; } catch (e) { /* ignore */ }
            }
        });
    }

    function arraysEqual(a, b) {
        if (!Array.isArray(a) || !Array.isArray(b)) return false;
        if (a.length !== b.length) return false;
        for (let i = 0; i < a.length; i++) if (a[i] !== b[i]) return false;
        return true;
    }

    function findSlideContainer(existingImgs) {
        // prefer a parent .mosque-image if exists and it's the parent of existing images
        if ($('.mosque-image').length) return $('.mosque-image').first();
        const c = $(SLIDE_CONTAINER_ID);
        if (c.length) return c;
        if (existingImgs && existingImgs.length) return $(existingImgs[0]).parent();
        return $('body');
    }

    function revealSlides() {
        const imgs = $(`img[id^=${SLIDE_ID_PREFIX}]`);
        if (!imgs.length) {
            console.warn('revealSlides: no images found');
            return;
        }

        imgs.hide();
        imgs.each(function (i) {
            const el = this;
            const $img = $(el);
            const isLoaded = el.complete && el.naturalWidth > 0;
            if (isLoaded) {
                $img.delay(80 * i).fadeIn(220);
            } else {
                $img.off('load.updateSlider').on('load.updateSlider', function () {
                    $(this).delay(80 * i).fadeIn(220);
                }).off('error.updateSlider').on('error.updateSlider', function () {
                    console.warn('revealSlides: load error for', $img.attr('src'));
                    $(this).attr('src', DEFAULT_IMG).delay(80 * i).fadeIn(220);
                });
            }
        });

        // safety fallback
        setTimeout(() => {
            const visible = imgs.filter(':visible').length;
            if (visible === 0) {
                imgs.each(function (i) {
                    $(this).attr('src', $(this).attr('src') || DEFAULT_IMG).show();
                });
            }
        }, 1500);
    }

    // Expose standardized names so other modules can call them
    window.preloadImages = window.preloadImages || newPreloadImages;
    window.clearUnusedCache = window.clearUnusedCache || newClearUnusedCache;

    // ---------- updateSlider (main fetcher) ----------
    async function updateSlider(force = false) {
        const now = Date.now();
        if (!force && sliderUpdateInFlight) {
            console.debug('updateSlider: request skipped, fetch already in progress');
            return;
        }
        if (!force && now - lastSliderFetch < 1000) {
            return;
        }

        sliderUpdateInFlight = true;
        const slug = window.location.pathname.replace(/^\//, '');
        if (!slug) {
            console.error('updateSlider: cannot determine slug from URL');
            sliderUpdateInFlight = false;
            return;
        }

        try {
            const requestUrl = `/api/new_slider/${slug}?_=${Date.now()}`;
            const response = await $.ajax({
                url: requestUrl,
                method: 'GET',
                dataType: 'json',
                cache: false
            });

            if (!response || !response.success) {
                console.warn('updateSlider: response not successful', response);
                return;
            }

            // normalisasi output jadi array slot-ordered
            let rawSlider = response.data ?? [];
            if (!Array.isArray(rawSlider)) {
                rawSlider = Object.values(rawSlider);
            }

            const newSlider = rawSlider
                .map(s => (s == null ? '' : String(s).trim()));

            // current DOM src list
            const existingImgs = $(`[id^=${SLIDE_ID_PREFIX}]`).toArray();
            const previous = existingImgs.map(img => $(img).attr('src') || '');

            // if identical, do nothing (but cleanup cache)
            const changed = !arraysEqual(previous, newSlider);
            if (!changed) {
                const actual = newSlider.filter(u => u && u.trim() !== '');
                window.clearUnusedCache(actual);
                console.debug('updateSlider: no change');
                return;
            }

            console.info('updateSlider: change detected, rebuilding slides', newSlider);

            // container where images should live (prefer .mosque-image)
            const container = findSlideContainer(existingImgs);
            const sanitized = newSlider.length
                ? newSlider.map(u => (u && u !== '' ? u : DEFAULT_IMG))
                : [DEFAULT_IMG];
            const hasRealImage = sanitized.some(u => u && u !== DEFAULT_IMG);
            const preloadTargets = sanitized.filter(u => {
                if (!u || u === '') return false;
                if (!hasRealImage) return true;
                return u !== DEFAULT_IMG;
            });

            // remove old slides
            container.find(`img[id^=${SLIDE_ID_PREFIX}]`).remove();

            // append new images (hidden)
            sanitized.forEach((url, idx) => {
                const isPlaceholder = !url || url === DEFAULT_IMG;
                const $img = $('<img>', {
                    id: `${SLIDE_ID_PREFIX}${idx + 1}`,
                    src: isPlaceholder && hasRealImage ? DEFAULT_IMG : url || DEFAULT_IMG,
                    alt: `Slide ${idx + 1}`,
                    style: 'object-fit: stretch; width:100%; height:100%; display:none;'
                });
                if (isPlaceholder && hasRealImage) {
                    $img.attr('data-empty', 'true');
                } else {
                    $img.removeAttr('data-empty');
                }
                container.append($img);
            });

            // preload images
            if (preloadTargets.length) {
                try { await window.preloadImages(preloadTargets); } catch (e) { console.warn('updateSlider: preload failed', e); }
            } else {
                // ensure at least default cached
                await window.preloadImages([DEFAULT_IMG]);
            }

            window.slideUrls = sanitized.slice();

            // reveal
            revealSlides();

            // notify listeners: (rawSlots, actualUrls)
            $(document).trigger('slidesUpdated', [sanitized.slice(), preloadTargets.slice()]);

            // cleanup cache
            window.clearUnusedCache(window.slideUrls.length ? window.slideUrls : [DEFAULT_IMG]);

            console.info(`updateSlider: updated slides (${window.slideUrls.length})`);
        } catch (err) {
            console.error('updateSlider: unexpected error', err);
        } finally {
            sliderUpdateInFlight = false;
            lastSliderFetch = Date.now();
        }
    }

    // ---------- manageSlideDisplay (uses window.slideUrls to update backgrounds) ----------
    function manageSlideDisplay() {
        const $mosqueImageElement = $('.mosque-image').first();
        const $jumbotronImageElement = $('#jumbotronImage').first();
        if (!$mosqueImageElement.length || !$jumbotronImageElement.length) {
            console.warn('manageSlideDisplay: .mosque-image or #jumbotronImage not found — skipping');
            return;
        }

        // read initial slideUrls from DOM images
        function readSlideUrlsFromDOM() {
            const urls = $(`img[id^=${SLIDE_ID_PREFIX}]`).map((i, e) => $(e).attr('src') || '').get().filter(u => u && u.trim() !== '');
            return urls.length ? urls : [DEFAULT_IMG];
        }

        function readJumbotronUrlsFromInputs() {
            return [
                $('#jumbo1').val(), $('#jumbo2').val(),
                $('#jumbo3').val(), $('#jumbo4').val(),
                $('#jumbo5').val(), $('#jumbo6').val()
            ].filter(u => u && u.trim() !== '');
        }

        window.slideUrls = readSlideUrlsFromDOM();
        window.jumbotronUrls = readJumbotronUrlsFromInputs();

        // slider loop (time-based)
        const slideDuration = 20000; // ms

        function getNow() {
            // if you have server-synced time function, use it; otherwise use client time
            if (typeof getCurrentTimeFromServer === 'function') return getCurrentTimeFromServer();
            return new Date();
        }

        function updateSlideDisplay() {
            const isJumbotronActive = $('#jumbo_is_active').val() === 'true' && window.jumbotronUrls && window.jumbotronUrls.length > 0;

            if (!window.slideUrls || window.slideUrls.length === 0) window.slideUrls = [DEFAULT_IMG];
            if (!window.jumbotronUrls) window.jumbotronUrls = [];

            const now = getNow();
            const totalSeconds = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();
            const slideCycle = slideDuration * window.slideUrls.length;
            const totalCycle = isJumbotronActive ? slideCycle + slideDuration : slideCycle;
            const cyclePos = (totalSeconds * 1000 + now.getMilliseconds()) % totalCycle;
            const imageIndex = Math.floor(cyclePos / slideDuration);

            let currentUrl;
            if (isJumbotronActive && imageIndex === window.slideUrls.length) {
                // jumbotron slot
                const jidx = Math.floor(((totalSeconds * 1000 + now.getMilliseconds()) / totalCycle)) % Math.max(1, window.jumbotronUrls.length);
                currentUrl = (window.jumbotronUrls && window.jumbotronUrls[jidx]) || DEFAULT_IMG;
                $mosqueImageElement.hide();
                $jumbotronImageElement.css({
                    'background-image': `url("${currentUrl}")`,
                    'display': 'block',
                    'transition': 'background-image 0.5s ease-in-out'
                });
                $('.jumbotron-progress-bar').css({ width: '0%', animation: `progressAnimation ${slideDuration}ms linear forwards` });
            } else {
                const sidx = (imageIndex % window.slideUrls.length);
                currentUrl = window.slideUrls[sidx] || DEFAULT_IMG;
                $jumbotronImageElement.hide();
                $mosqueImageElement.css({
                    'background-image': `url("${currentUrl}")`,
                    'display': 'block',
                    'transition': 'background-image 0.5s ease-in-out'
                });
                $('.jumbotron-progress-bar').css({ animation: 'none', width: '0%' });
            }

            // cleanup cache
            try { window.clearUnusedCache([...window.slideUrls, ...window.jumbotronUrls]); } catch (e) { /* ignore */ }

            $(document).trigger('slideUpdated');
        }

        // start loop
        updateSlideDisplay();
        setInterval(updateSlideDisplay, 1000);

        // listen for slide updates from updateSlider
        $(document).on('slidesUpdated', async function (e, sanitizedSlots, preloadTargets) {
            const incoming = Array.isArray(sanitizedSlots) && sanitizedSlots.length
                ? sanitizedSlots.slice()
                : [DEFAULT_IMG];
            window.slideUrls = incoming;

            // preload newly received urls
            const toPreloadSource = Array.isArray(preloadTargets) && preloadTargets.length
                ? preloadTargets
                : incoming;
            const toPreload = toPreloadSource.filter(u => !window.imageCache[u] || !window.imageCache[u].complete);
            if (toPreload.length) {
                try { await window.preloadImages(toPreload); } catch (err) { console.warn('manageSlideDisplay preload error', err); }
            }

            try { window.clearUnusedCache([...window.slideUrls, ...window.jumbotronUrls]); } catch (e) { /* ignore */ }

            console.debug('manageSlideDisplay: slideUrls updated via event', window.slideUrls);
            updateSlideDisplay();
        });

        // listen jumbotron updates (if you still use inputs)
        $(document).on('jumbotronUpdated', async function () {
            window.jumbotronUrls = readJumbotronUrlsFromInputs();
            const toPreload = window.jumbotronUrls.filter(u => !window.imageCache[u] || !window.imageCache[u].complete);
            if (toPreload.length) {
                try { await window.preloadImages(toPreload); } catch (err) { console.warn('manageSlideDisplay jumbotron preload error', err); }
            }
            try { window.clearUnusedCache([...window.slideUrls, ...window.jumbotronUrls]); } catch (e) { /* ignore */ }
        });
    }

    // ---------- init on ready ----------
    $(document).ready(() => {
        // run manage first so it reads current DOM images
        try { manageSlideDisplay(); } catch (e) { console.error('manageSlideDisplay init error', e); }

        // then fetch updates
        try { updateSlider(true); } catch (e) { console.error('updateSlider init error', e); }

        // schedule periodic refresh so new slides appear without manual reload
        setInterval(() => {
            try { updateSlider(); } catch (e) { console.error('updateSlider interval error', e); }
        }, SLIDER_REFRESH_INTERVAL);
    });

    // expose functions globally for debugging/manual calls
    window.updateSlider = updateSlider;
    window.manageSlideDisplay = manageSlideDisplay;
    window.newPreloadImages = newPreloadImages;
    window.newClearUnusedCache = newClearUnusedCache;

})();
