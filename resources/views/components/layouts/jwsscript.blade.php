<!-- Moment.js core -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>

<!-- Moment Hijri -->
<script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.0/moment-hijri.min.js"></script>

<!-- Locale Indonesia -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>

<!-- jQuery -->
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
</script>
