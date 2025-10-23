/* update-slider.js
   Compatible with Blade that renders:
   @foreach ($newSlider as $index => $slide)
     <img id="slide{{ $index + 1 }}" src="..." style="display:none">
   @endforeach
*/

(function () {
    'use strict';

    // CONFIG
    const DEFAULT_IMG = '/images/other/slide-jws-default.jpg';
    const SLIDE_ID_PREFIX = 'slide'; // selector [id^=slide]
    const SLIDE_CONTAINER_ID = '#slide-container'; // optional container in DOM

    // small helper: ensure global imageCache exists
    if (!window.imageCache) window.imageCache = {};

    // preload images (returns Promise)
    function newPreloadImages(urls = []) {
        if (!window.imageCache) window.imageCache = {};
        const promises = urls.map(url => {
            return new Promise((resolve) => {
                try {
                    const cached = window.imageCache[url];
                    if (cached && cached.complete) return resolve(url);

                    const img = new Image();
                    img.onload = () => {
                        img.complete = true;
                        window.imageCache[url] = img;
                        resolve(url);
                    };
                    img.onerror = () => {
                        // store the Image object anyway (complete false)
                        window.imageCache[url] = img;
                        resolve(url);
                    };
                    img.src = url;
                    // store reference immediately (in case other checks run)
                    window.imageCache[url] = img;
                } catch (e) {
                    // resolve even on unexpected error to avoid blocking
                    resolve(url);
                }
            });
        });

        return Promise.all(promises);
        // log preload results
        console.log('updateSlider: preload images selesai', window.imageCache);
    }

    // clear cache entries not present in keepUrls
    function newClearUnusedCache(keepUrls = []) {
        if (!window.imageCache) return;
        const keepSet = new Set(keepUrls.map(u => String(u)));
        Object.keys(window.imageCache).forEach(u => {
            if (!keepSet.has(u)) {
                try {
                    delete window.imageCache[u];
                } catch (e) { /* ignore */ }
            }
        });
    }

    // compare two arrays strictly (length + item equality)
    function arraysEqual(a, b) {
        if (!Array.isArray(a) || !Array.isArray(b)) return false;
        if (a.length !== b.length) return false;
        for (let i = 0; i < a.length; i++) {
            if (a[i] !== b[i]) return false;
        }
        return true;
    }

    // show images with small staggered fadeIn after preload
    function revealSlides() {
        const imgs = $(`img[id^=${SLIDE_ID_PREFIX}]`);
        imgs.hide();
        imgs.each(function (i) {
            $(this).delay(80 * i).fadeIn(240);
        });
    }

    // build / find container for slide img elements
    function findSlideContainer(existingImgs) {
        let container = $(SLIDE_CONTAINER_ID);
        if (container.length) return container;
        if (existingImgs && existingImgs.length) return $(existingImgs[0]).parent();
        return $('body');
    }

    // MAIN: updateSlider (exposed on window)
    async function updateSlider() {
        const slug = window.location.pathname.replace(/^\//, '');
        if (!slug) {
            console.error('updateSlider: tidak dapat menentukan slug dari URL');
            return;
        }

        $.ajax({
            url: `/api/new_slider/${slug}`,
            method: 'GET',
            dataType: 'json',
            success: async function (response) {
                try {
                    console.log('API new_slider response:', response);

                    if (!response || !response.success) {
                        console.warn('updateSlider: response tidak sukses atau kosong', response);
                        return;
                    }

                    // normalize response.data -> newSlider (array of slots)
                    let newSlider = Array.isArray(response.data) ? response.data.slice() : [];
                    newSlider = newSlider.map(s => (s == null ? '' : String(s).trim()));

                    // current images in DOM (by id prefix)
                    const existingImgs = $(`[id^=${SLIDE_ID_PREFIX}]`).toArray();
                    const previousSlides = existingImgs.map(el => {
                        const $el = $(el);
                        if ($el.is('img')) return $el.attr('src') || '';
                        if ($el.is('input')) return $el.val() || '';
                        return $el.attr('data-slide-url') || '';
                    });

                    // determine if changed
                    const hasChange = !arraysEqual(previousSlides, newSlider);

                    if (!hasChange) {
                        console.log('updateSlider: tidak ada perubahan pada slide, update diabaikan');
                        // still cleanup cache
                        const actualUrls = newSlider.map(u => (u || '').toString().trim()).filter(u => u !== '');
                        newClearUnusedCache(actualUrls);
                        return;
                    }

                    console.log('updateSlider list:', newSlider);

                    // determine container to append/update slide elements
                    const container = findSlideContainer(existingImgs);
                    const existingElements = container.find(`[id^=${SLIDE_ID_PREFIX}]`).toArray();
                    const preferredTag = existingElements.length
                        ? existingElements[0].tagName.toLowerCase()
                        : 'img';

                    // prepare urls
                    const sanitizedSlots = newSlider.length
                        ? newSlider.map(url => (url && url !== '' ? url : DEFAULT_IMG))
                        : [DEFAULT_IMG];
                    let actualUrls = newSlider.filter(url => url !== '');
                    if (actualUrls.length === 0) {
                        actualUrls = [DEFAULT_IMG];
                    }

                    const targetLength = Math.max(sanitizedSlots.length, existingElements.length);
                    const updatedElements = [];

                    for (let idx = 0; idx < targetLength; idx++) {
                        const rawUrl = newSlider[idx] || '';
                        const displayUrl = sanitizedSlots[idx] || DEFAULT_IMG;

                        let element = existingElements[idx];
                        if (!element) {
                            element = (preferredTag === 'input')
                                ? $('<input>', {
                                    type: 'hidden',
                                    id: `${SLIDE_ID_PREFIX}${idx + 1}`
                                })[0]
                                : $('<img>', {
                                    id: `${SLIDE_ID_PREFIX}${idx + 1}`,
                                    alt: `Slide ${idx + 1}`,
                                    style: 'object-fit: stretch; width: 100%; height: 100%; display: none;'
                                })[0];
                            container.append(element);
                        }

                        const $element = $(element);
                        $element.attr('id', `${SLIDE_ID_PREFIX}${idx + 1}`);
                        $element.attr('data-slide-url', displayUrl);

                        if ($element.is('img')) {
                            $element.attr('src', displayUrl);
                            $element.attr('alt', `Slide ${idx + 1}`);
                            $element.css({
                                objectFit: 'stretch',
                                width: '100%',
                                height: '100%',
                                display: 'none'
                            });
                            $element.prop('value', rawUrl || displayUrl);
                        } else {
                            if (!$element.attr('type')) {
                                $element.attr('type', 'hidden');
                            }
                            $element.val(rawUrl || displayUrl);
                        }

                        updatedElements.push(element);
                        // log
                        console.log(`updateSlider: slide diperbarui, id: ${$(element).attr('id')}, url: ${displayUrl}`);
                    }

                    // remove any leftover elements beyond the updated set
                    container.find(`[id^=${SLIDE_ID_PREFIX}]`).each(function () {
                        const id = $(this).attr('id');
                        if (!updatedElements.includes(this)) {
                            $(this).remove();
                        }
                    });

                    // determine which urls need preload (unique list)
                    const urlsToPreload = Array.from(new Set(sanitizedSlots)).filter(url => {
                        return !window.imageCache || !window.imageCache[url] || !window.imageCache[url].complete;
                    });

                    if (urlsToPreload.length > 0) {
                        try {
                            await newPreloadImages(urlsToPreload);
                        } catch (e) {
                            console.warn('updateSlider: preloadImages gagal', e);
                        }
                    }

                    // set global state
                    window.slideUrls = actualUrls.slice();

                    // reveal images
                    revealSlides();

                    // trigger event for other listeners
                    $(document).trigger('slidesUpdated', [newSlider, actualUrls]);

                    // cleanup cache not used
                    const cacheKeep = actualUrls.length ? actualUrls : [DEFAULT_IMG];
                    newClearUnusedCache(cacheKeep);

                    console.log(`updateSlider: slide diperbarui, total slides: ${actualUrls.length}`);
                } catch (err) {
                    console.error('updateSlider: error saat memproses response', err);
                }
            },
            error: function (xhr, status, error) {
                console.error('updateSlider: Error saat mengambil data slide:', error, xhr && xhr.responseText);
            }
        });
    }

    // expose to global so other scripts can call updateSlider()
    window.updateSlider = updateSlider;

    // option: auto-run once on script load (uncomment if desired)
    $(document).ready(() => { updateSlider(); });

})();
