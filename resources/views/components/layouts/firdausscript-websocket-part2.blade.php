function getCurrentTimeFromServer() {
            const now = Date.now();
            const elapsedSinceSync = now - pageLoadTimestamp;
            return new Date(serverTimestamp + elapsedSinceSync);
        }

        // WebSocket-specific update functions
        function updateMarqueeTextFromWebSocket(text) {
            const $marqueeElement = $('.marquee-text');
            if ($marqueeElement.length && $marqueeElement.text() !== text) {
                $marqueeElement.text(text);
                console.log('Marquee text updated via WebSocket');
            }
        }

        function updateSlidesFromWebSocket(slides) {
            if (Array.isArray(slides) && slides.length > 0) {
                const newUrls = slides.filter(url => url && url.trim() !== '');
                if (JSON.stringify(window.slideUrls) !== JSON.stringify(newUrls)) {
                    window.slideUrls = newUrls;
                    $(document).trigger('slidesUpdated', [newUrls]);
                    console.log('Slides updated via WebSocket');
                }
            }
        }

        function updateJumbotronFromWebSocket(jumbotronData) {
            let updated = false;
            if (jumbotronData.jumbo1 && $('#jumbo1').val() !== jumbotronData.jumbo1) {
                $('#jumbo1').val(jumbotronData.jumbo1);
                updated = true;
            }
            if (jumbotronData.jumbo2 && $('#jumbo2').val() !== jumbotronData.jumbo2) {
                $('#jumbo2').val(jumbotronData.jumbo2);
                updated = true;
            }
            if (jumbotronData.jumbo3 && $('#jumbo3').val() !== jumbotronData.jumbo3) {
                $('#jumbo3').val(jumbotronData.jumbo3);
                updated = true;
            }
            if (jumbotronData.jumbo4 && $('#jumbo4').val() !== jumbotronData.jumbo4) {
                $('#jumbo4').val(jumbotronData.jumbo4);
                updated = true;
            }
            if (jumbotronData.jumbo5 && $('#jumbo5').val() !== jumbotronData.jumbo5) {
                $('#jumbo5').val(jumbotronData.jumbo5);
                updated = true;
            }
            if (jumbotronData.jumbo6 && $('#jumbo6').val() !== jumbotronData.jumbo6) {
                $('#jumbo6').val(jumbotronData.jumbo6);
                updated = true;
            }
            
            if (updated) {
                $(document).trigger('jumbotronUpdated');
                console.log('Jumbotron updated via WebSocket');
            }
        }

        function updatePetugasFromWebSocket(petugasData) {
            let updated = false;
            if (petugasData.khatib && $('#khatib').val() !== petugasData.khatib) {
                $('#khatib').val(petugasData.khatib);
                updated = true;
            }
            if (petugasData.imam && $('#imam').val() !== petugasData.imam) {
                $('#imam').val(petugasData.imam);
                updated = true;
            }
            if (petugasData.muadzin && $('#muadzin').val() !== petugasData.muadzin) {
                $('#muadzin').val(petugasData.muadzin);
                updated = true;
            }
            
            if (updated) {
                // Update Friday info content if popup is visible
                if ($('#fridayInfoPopup').is(':visible')) {
                    updateFridayInfoContent();
                }
                console.log('Petugas updated via WebSocket');
            }
        }

        function updateAdzanImagesFromWebSocket(images) {
            // Update adzan images logic here
            console.log('Adzan images updated via WebSocket');
        }

        function updateIqomahImagesFromWebSocket(images) {
            // Update iqomah images logic here
            console.log('Iqomah images updated via WebSocket');
        }

        function updateMosqueInfoFromWebSocket(mosqueInfo) {
            // Update mosque information logic here
            console.log('Mosque info updated via WebSocket');
        }

        // Original functions (modified to work with WebSocket)
        function updateAndPlayAudio() {
            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) {
                console.error('Tidak dapat menentukan slug dari URL');
                return;
            }

            // Use cached URLs if available from WebSocket, otherwise fetch via AJAX
            if (cachedAudioUrls.length > 0) {
                processAudioUrls(cachedAudioUrls);
            } else {
                $.ajax({
                    url: `/api/audio/${slug}`,
                    method: 'GET',
                    dataType: 'json',
                    timeout: 15000,
                    success: function(response) {
                        if (response.success && response.data && response.data.urls) {
                            cachedAudioUrls = response.data.urls;
                            processAudioUrls(response.data.urls);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching audio:', error);
                    }
                });
            }
        }

        function processAudioUrls(urls) {
            if (!Array.isArray(urls) || urls.length === 0) {
                console.log('Tidak ada audio untuk diputar');
                return;
            }

            const validUrls = urls.filter(url => url && url.trim() !== '');
            if (validUrls.length === 0) {
                console.log('Tidak ada URL audio yang valid');
                return;
            }

            // Audio playing logic here (same as original)
            console.log('Processing audio URLs:', validUrls);
        }

        function updateMarqueeText() {
            if (wsConnected) {
                // Skip AJAX call if WebSocket is connected
                return;
            }

            $.ajax({
                url: '/api/marquee',
                method: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success && response.data && response.data.text) {
                        updateMarqueeTextFromWebSocket(response.data.text);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching marquee:', error);
                }
            });
        }

        function updateSlides() {
            if (wsConnected) {
                // Skip AJAX call if WebSocket is connected
                return;
            }

            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) return;

            $.ajax({
                url: `/api/slides/${slug}`,
                method: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success && response.data && response.data.slides) {
                        updateSlidesFromWebSocket(response.data.slides);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching slides:', error);
                }
            });
        }

        function updateJumbotronData() {
            if (wsConnected) {
                // Skip AJAX call if WebSocket is connected
                return;
            }

            $.ajax({
                url: '/api/jumbotron',
                method: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success && response.data) {
                        updateJumbotronFromWebSocket(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching jumbotron:', error);
                }
            });
        }

        function updateFridayOfficials() {
            if (wsConnected) {
                // Skip AJAX call if WebSocket is connected
                return;
            }

            const slug = window.location.pathname.replace(/^\//, '');
            if (!slug) return;

            $.ajax({
                url: `/api/petugas/${slug}`,
                method: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success && response.data) {
                        updatePetugasFromWebSocket(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching petugas:', error);
                }
            });
        }

        function updateMosqueInfo() {
            if (wsConnected) {
                // Skip AJAX call if WebSocket is connected
                return;
            }

            // Original mosque info update logic here
            console.log('Updating mosque info via AJAX fallback');
        }

        // Initialize WebSocket connection
        initializeWebSocket();

        // Initial data load
        syncServerTime();
        updateAndPlayAudio();
        updateMarqueeText();
        updateSlides();
        updateJumbotronData();
        updateFridayOfficials();

        // Continue with original initialization code...
        // (Include all the remaining original functions here)
    });
</script>