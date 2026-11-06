<link href="{{ asset('theme/dist/css/tabler.min.css') }}" rel="stylesheet" />
<link href="{{ asset('theme/dist/css/tabler-flags.min.css') }}" rel="stylesheet" />
<link href="{{ asset('theme/dist/css/tabler-payments.min.css') }}" rel="stylesheet" />
<link href="{{ asset('theme/dist/css/tabler-vendors.min.css') }}" rel="stylesheet" />
<link href="{{ asset('theme/dist/css/demo.min.css') }}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css" rel="stylesheet" />
<script src="{{ asset('theme/dist/js/demo-theme.min.js') }}" defer></script>
<script src="{{ asset('js/theme-handler.js') }}"></script>
<style>
    @import url('https://rsms.me/inter/inter.css');

    :root {
        --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
    }

    body {
        font-feature-settings: "cv03", "cv04", "cv11";
    }

    /* Sidebar Putih dengan Teks dan Ikon Kontras Tinggi */
    #main-sidebar {
        background-color: #ffffff !important;
        box-shadow: none !important;
        overflow-x: hidden !important;
        scrollbar-width: none !important; /* Firefox */
        -ms-overflow-style: none !important; /* IE & Edge */
    }

    /* Sembunyikan scrollbar indikator di sidebar (WebKit: Chrome, Safari, Edge) */
    #main-sidebar::-webkit-scrollbar,
    #main-sidebar #sidebar-menu::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    #main-sidebar #sidebar-menu {
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
    }

    #main-sidebar .navbar-brand-text {
        color: #0f172a !important;
        font-weight: 700;
    }

    #main-sidebar .navbar-toggler {
        color: #1e293b !important;
    }

    #main-sidebar .text-muted.small.fw-bold {
        color: #64748b !important;
        font-size: 0.72rem;
        letter-spacing: 0.05em;
    }

    /* Menu Inactive: Teks dan Ikon Gelap Pekat (Kontras di atas putih) */
    #main-sidebar .nav-link {
        color: #334155 !important;
        font-weight: 500;
        transition: all 0.15s ease-in-out;
    }

    #main-sidebar .nav-link .nav-link-icon,
    #main-sidebar .nav-link .nav-link-icon.text-white {
        color: #475569 !important;
    }

    #main-sidebar .nav-link .nav-link-title,
    #main-sidebar .nav-link .nav-link-title.text-white {
        color: #1e293b !important;
    }

    /* Hover Menu Inactive */
    #main-sidebar .nav-item:not([style*="#1e5fa3"]):not([style*="linear-gradient"]) .nav-link:hover {
        background-color: #f1f5f9;
        border-radius: 0.5rem;
    }

    #main-sidebar .nav-item:not([style*="#1e5fa3"]):not([style*="linear-gradient"]) .nav-link:hover .nav-link-icon,
    #main-sidebar .nav-item:not([style*="#1e5fa3"]):not([style*="linear-gradient"]) .nav-link:hover .nav-link-icon.text-white,
    #main-sidebar .nav-item:not([style*="#1e5fa3"]):not([style*="linear-gradient"]) .nav-link:hover .nav-link-title,
    #main-sidebar .nav-item:not([style*="#1e5fa3"]):not([style*="linear-gradient"]) .nav-link:hover .nav-link-title.text-white {
        color: #0f172a !important;
    }

    /* Menu Active: Teks dan Ikon PUTIH di atas background solid biru aktif, border-radius sama dengan hover (0.5rem) */
    #main-sidebar .nav-item[style*="#1e5fa3"] {
        border-radius: 0.5rem !important;
    }

    #main-sidebar .nav-item[style*="#1e5fa3"] .nav-link {
        border-radius: 0.5rem !important;
    }

    #main-sidebar .nav-item[style*="#1e5fa3"] .nav-link,
    #main-sidebar .nav-item[style*="#1e5fa3"] .nav-link-icon,
    #main-sidebar .nav-item[style*="#1e5fa3"] .nav-link-icon.text-white,
    #main-sidebar .nav-item[style*="#1e5fa3"] .nav-link-title,
    #main-sidebar .nav-item[style*="#1e5fa3"] .nav-link-title.text-white,
    #main-sidebar .nav-item[style*="#1e5fa3"] .text-white {
        color: #ffffff !important;
    }

    #main-sidebar .nav-item[style*="#1e5fa3"] .nav-link:hover {
        background-color: transparent !important;
        color: #ffffff !important;
    }

    #main-sidebar .nav-item[style*="#1e5fa3"] .nav-link:hover .nav-link-icon,
    #main-sidebar .nav-item[style*="#1e5fa3"] .nav-link:hover .nav-link-title {
        color: #ffffff !important;
    }

    /* User Profile Dropdown di Mobile */
    #main-sidebar .navbar-nav.d-lg-none .nav-link {
        color: #0f172a !important;
    }

    @media (min-width: 992px) {
        :root {
            --tblr-sidebar-width: 18rem;
        }

        #main-sidebar.navbar-vertical.navbar-expand-lg {
            width: var(--tblr-sidebar-width, 18rem) !important;
            border-right: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        #main-sidebar.navbar-vertical.navbar-expand-lg ~ .page-wrapper {
            margin-left: var(--tblr-sidebar-width, 18rem) !important;
        }

        /* Navbar Brand tetap diam di bagian atas saat menu sidebar di-scroll & rata kiri */
        #main-sidebar .navbar-brand {
            position: sticky;
            top: 0;
            z-index: 1020;
            background-color: #ffffff !important;
            margin: 0 !important;
            padding: 0.875rem 1.25rem !important;
            width: 100% !important;
            border-bottom: 1px solid #e2e8f0;
            justify-content: flex-start !important;
            text-align: left !important;
            white-space: normal !important;
        }

        #main-sidebar .navbar-brand a {
            justify-content: flex-start !important;
            text-align: left !important;
            white-space: normal !important;
        }
    }

    @media (max-width: 991.98px) {
        #main-sidebar {
            border-bottom: 1px solid #e2e8f0 !important;
            border-right: none !important;
            box-shadow: none !important;
        }

        #main-sidebar #sidebar-menu {
            max-height: calc(100vh - 3.75rem);
            max-height: calc(100dvh - 3.75rem);
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            touch-action: pan-y;
        }
    }
</style>
