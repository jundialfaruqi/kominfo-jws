<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
    /* Font Faces */
    @font-face {
        font-family: 'PlusJakartaSansDisplay';
        src: url('{{ asset('fonts/PlusJakartaSansDisplay/PlusJakartaSansDisplay-Bold.otf') }}') format('opentype');
        font-weight: 700;
        font-style: normal;
        font-display: swap;
    }
    @font-face {
        font-family: 'PlusJakartaSansDisplay';
        src: url('{{ asset('fonts/PlusJakartaSansDisplay/PlusJakartaSansDisplay-Regular.otf') }}') format('opentype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }
    @font-face {
        font-family: 'PlusJakartaSansText';
        src: url('{{ asset('fonts/PlusJakartaSansText/PlusJakartaSansText-Bold.otf') }}') format('opentype');
        font-weight: 700;
        font-style: normal;
        font-display: swap;
    }
    @font-face {
        font-family: 'PlusJakartaSansText';
        src: url('{{ asset('fonts/PlusJakartaSansText/PlusJakartaSansText-Regular.otf') }}') format('opentype');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }
    @font-face {
        font-family: 'LedDot';
        src: url('{{ asset('fonts/LedDot/LedDot.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
        font-display: swap;
    }

    /* Core Styling - Apple Light Aesthetic */
    body::before {
        display: none !important;
    }

    body {
        padding-top: 0;
        font-family: 'PlusJakartaSansText', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background-color: #f5f5f7;
        color: #1d1d1f;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    h1, h2, h3, h4, h5, h6, .navbar-brand, .font-display, .fw-bold {
        font-family: 'PlusJakartaSansDisplay', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .font-led {
        font-family: 'LedDot', monospace !important;
    }

    /* Color Utilities */
    .bg-gov { background-color: #0071e3; }
    .bg-gov-dark { background-color: #fbfbfd; }
    .text-gov-dark { color: #1d1d1f; }
    .bg-soft { background-color: #ffffff; }

    .btn-gov-blue {
        background-color: #0071e3;
        color: #ffffff;
        border: none;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }
    .btn-gov-blue:hover {
        background-color: #0077ed;
        color: #ffffff;
        transform: scale(1.02);
    }
    
    .btn-outline-light {
        color: #1d1d1f;
        border-color: #d2d2d7;
        background-color: transparent;
        transition: all 0.2s ease;
    }
    .btn-outline-light:hover {
        color: #0071e3;
        border-color: #0071e3;
        background-color: rgba(0, 113, 227, 0.05);
    }

    /* Navbar Glassmorphism */
    .navbar {
        background: rgba(255, 255, 255, 0.72) !important;
        backdrop-filter: saturate(180%) blur(20px);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: none;
        padding-top: 14px;
        padding-bottom: 14px;
        transition: background 0.3s ease;
    }

    .navbar-brand-text {
        font-family: 'PlusJakartaSansDisplay', sans-serif;
        font-weight: 700;
        font-size: 1.2rem;
        color: #1d1d1f;
    }

    /* Hero Section */
    .hero {
        padding-top: 9rem !important;
        padding-bottom: 6rem !important;
        background: #ffffff;
        color: #1d1d1f !important;
        overflow: hidden;
        position: relative;
    }

    .hero::before {
        content: "";
        position: absolute;
        top: -10%;
        left: -5%;
        width: 50%;
        height: 60%;
        background: radial-gradient(circle, rgba(0,113,227,0.06) 0%, rgba(255,255,255,0) 70%);
        z-index: 0;
    }

    .hero .container {
        position: relative;
        z-index: 1;
    }

    .hero h1 {
        font-size: 3.5rem;
        letter-spacing: -0.02em;
        line-height: 1.05;
        background: linear-gradient(135deg, #1d1d1f 0%, #434344 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1.5rem !important;
    }
    
    @media (max-width: 768px) {
        .hero h1 { font-size: 2.5rem; }
    }

    .hero p {
        font-size: 1.15rem;
        color: #86868b;
        line-height: 1.6;
        margin-bottom: 2rem !important;
    }

    .hero-composite {
        width: 100%;
        max-width: 480px;
        margin: 0 auto;
        display: block;
        filter: drop-shadow(0 20px 40px rgba(0,0,0,0.08));
        transition: transform 0.5s cubic-bezier(0.1, 0.8, 0.2, 1);
    }
    
    .hero-composite:hover {
        transform: translateY(-10px);
    }

    /* Cards & Glass Containers */
    .prayer-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.04);
        border-radius: 20px !important;
        box-shadow: 0 4px 24px rgba(0,0,0,0.03);
        transition: all 0.3s cubic-bezier(0.1, 0.8, 0.2, 1);
        padding: 16px;
        min-height: 100px;
    }
    
    .prayer-card:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 12px 32px rgba(0,0,0,0.08);
    }

    .prayer-card.active {
        background: #0071e3;
        border-color: #0071e3;
        box-shadow: 0 12px 32px rgba(0, 113, 227, 0.25);
    }

    .prayer-label {
        font-family: 'PlusJakartaSansDisplay', sans-serif;
        font-size: 0.85rem;
        color: #86868b;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
    }
    
    .prayer-card.active .prayer-label {
        color: rgba(255,255,255,0.8);
    }

    .prayer-time {
        font-family: 'LedDot', monospace;
        font-size: 2.4rem;
        color: #1d1d1f;
        line-height: 1;
        letter-spacing: 1px;
    }
    
    .prayer-card.active .prayer-time {
        color: #ffffff;
    }

    .prayer-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #f5f5f7;
        color: #1d1d1f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        margin-left: 12px;
    }

    .prayer-card.active .prayer-icon {
        background: rgba(255,255,255,0.2);
        color: #ffffff;
    }

    @media (max-width: 768px) {
        .prayer-card {
            padding: 12px;
            min-height: 80px;
        }
        .prayer-time {
            font-size: 1.6rem;
        }
        .prayer-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            font-size: 1rem;
            margin-left: 8px;
        }
        .prayer-label {
            font-size: 0.75rem;
            margin-bottom: 2px;
        }
    }

    /* Header Info (Hari Ini & Countdown) */
    .header-date-time {
        font-size: 1.1rem;
    }
    .header-date-time .clock-icon {
        font-size: 1.2rem;
    }
    .header-date-time .clock-text {
        font-size: 1.4rem;
        color: #1d1d1f;
        position: relative;
        top: 1px;
    }
    .countdown-next-label {
        font-size: 0.85rem;
    }
    .countdown-next-time {
        font-size: 2.8rem;
    }

    @media (max-width: 768px) {
        .header-date-time {
            font-size: 0.9rem;
        }
        .header-date-time .clock-icon {
            font-size: 1rem;
        }
        .header-date-time .clock-text {
            font-size: 1.15rem;
        }
        .countdown-next-label {
            font-size: 0.75rem;
        }
        .countdown-next-time {
            font-size: 2rem;
        }
    }

    /* Countdown Banners */
    .countdown-banner {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.04);
        border-radius: 24px;
        padding: 12px 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 70px;
    }

    .countdown-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: #f5f5f7;
        color: #1d1d1f;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .countdown-label {
        font-family: 'PlusJakartaSansDisplay', sans-serif;
        font-size: 0.85rem;
        color: #86868b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .countdown-time {
        font-family: 'LedDot', monospace;
        font-size: 2.2rem;
        color: #0071e3;
        line-height: 1;
        letter-spacing: 2px;
    }
    
    #current-time {
        color: #1d1d1f;
    }

    .header-title {
        font-family: 'PlusJakartaSansDisplay', sans-serif;
        font-weight: 700;
        font-size: 1.25rem;
        color: #1d1d1f;
    }
    
    /* Table & News Cards */
    .schedule-table {
        background: #ffffff;
        border-radius: 24px;
        padding: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.02);
        border: 1px solid rgba(0,0,0,0.04);
    }

    .schedule-table table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 16px;
        overflow: hidden;
    }
    
    .schedule-table thead th {
        background: #f5f5f7;
        color: #86868b;
        font-weight: 700;
        border-bottom: none;
        padding: 16px;
        font-family: 'PlusJakartaSansDisplay', sans-serif;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
    }
    
    .schedule-table tbody td {
        padding: 18px 16px;
        border-bottom: 1px solid rgba(0,0,0,0.03);
        color: #1d1d1f;
        font-family: 'LedDot', monospace;
        font-size: 1.4rem;
        letter-spacing: 1px;
        vertical-align: middle;
    }
    
    .schedule-table tbody tr:last-child td {
        border-bottom: none;
    }

    .schedule-table tbody tr.today td {
        background-color: #0071e3;
        color: #ffffff;
        border-color: #0071e3;
        font-weight: 700;
    }
    
    .schedule-table tbody tr.today:hover td {
        background-color: #0062c3;
        color: #ffffff;
    }

    .schedule-table tbody td:first-child {
        font-family: 'PlusJakartaSansText', sans-serif;
        font-size: 1.05rem;
        letter-spacing: normal;
        font-weight: 600;
    }
    
    .schedule-table tbody tr:hover {
        background-color: #fbfbfd;
    }

    /* Article Cards */
    .article-card {
        border-radius: 24px;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 4px 24px rgba(0,0,0,0.03);
        background: #ffffff;
        overflow: hidden;
        transition: transform 0.3s cubic-bezier(0.1, 0.8, 0.2, 1), box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .article-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0,0,0,0.08);
    }

    .article-card img {
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }

    .article-card .card-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
    }
    
    .article-title {
        font-family: 'PlusJakartaSansDisplay', sans-serif;
        font-weight: 700;
        color: #1d1d1f;
        font-size: 1.25rem;
        line-height: 1.4;
        margin-bottom: 12px;
    }

    .article-card p.text-muted {
        color: #86868b !important;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: auto; /* push button to bottom */
    }

    .btn-outline-gov-blue {
        color: #0071e3;
        border-color: #0071e3;
        border-radius: 20px;
        padding: 8px 20px;
        font-weight: 600;
    }
    
    .btn-outline-gov-blue:hover {
        background-color: #0071e3;
        color: #ffffff;
    }

    /* Leaflet Map */
    #map {
        border-radius: 24px;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 4px 24px rgba(0,0,0,0.03);
        overflow: hidden;
    }

    /* Gallery Carousel */
    .gallery-carousel {
        border-radius: 32px;
        overflow: hidden;
        box-shadow: 0 24px 48px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.03);
        transform: translateZ(0); /* Anti-aliasing pada sudut membulat */
    }
    
    .gallery-carousel .carousel-item img {
        height: 560px;
        object-fit: cover;
        width: 100%;
    }
    
    @media (max-width: 768px) {
        .gallery-carousel .carousel-item img {
            height: 300px;
        }
    }

    /* Footer */
    footer {
        background: #1d1d1f !important;
        color: #f5f5f7 !important;
        padding: 5rem 0 3rem 0;
    }
    
    footer .text-gov-dark {
        color: #f5f5f7 !important;
    }
    
    footer h5 {
        color: #ffffff;
        font-family: 'PlusJakartaSansDisplay', sans-serif;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }
    
    footer a {
        color: #86868b !important;
        transition: color 0.2s ease;
        text-decoration: none;
    }
    
    footer a:hover {
        color: #ffffff !important;
    }
    
    footer hr {
        border-color: rgba(255,255,255,0.1);
        margin: 2rem 0;
    }

    footer .small {
        color: #86868b;
    }
</style>
