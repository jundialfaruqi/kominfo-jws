<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap');

    body::before {
        display: none !important;
    }

    body {
        padding-top: 0;
        font-family: Arial, Helvetica, sans-serif;
    }

    .bg-gov {
        background-color: #172433;
    }

    .bg-gov-dark {
        background-color: #172433;
    }

    .text-gov-dark {
        color: #172433;
    }

    .bg-soft {
        background-color: #f7f8fb;
    }

    .brand-mark {
        width: 24px;
        height: 24px;
        display: inline-block;
        background-image: linear-gradient(90deg, #0ea5a3 0%, #1f7ae0 60%, #3b82f6 100%);
        border-radius: 6px;
    }

    .hero-gradient {
        background-image: linear-gradient(90deg, #0ea5a3 0%, #1f7ae0 60%, #3b82f6 100%);
    }

    .hero {
        padding-top: 6rem !important;
        margin-top: 0 !important;
    }

    .leaders {
        gap: 1rem;
    }

    .leaders-overlap {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        gap: 0;
    }

    .leader-card {
        position: relative;
        display: inline-block;
    }

    .leader-card.mayor {
        z-index: 2;
    }

    .leader-card.deputy {
        z-index: 1;
        margin-left: -110px;
        margin-top: 4px;
    }

    @media (min-width: 992px) {
        .leader-card.deputy {
            margin-left: -150px;
        }
    }

    .leaders-meta {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: -36px;
        position: relative;
        z-index: 3;
    }

    .leader-entry {
        background-color: rgba(255, 255, 255, 0.94);
        border: 1px solid #e2e6ee;
        border-radius: 10px;
        padding: 4px 10px;
        max-width: 340px;
    }

    .leader-entry .leader-name {
        color: #172433;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 2px;
        line-height: 1.25;
        min-height: calc(1.25em * 1.6);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .leader-entry .leader-role {
        color: #0154a6;
        font-weight: 600;
        font-size: 0.875rem;
        letter-spacing: 0.02em;
    }

    .leader-card {
        max-width: 320px;
    }

    @media (min-width: 992px) {
        .leader-card.portrait-card {
            max-width: 360px;
        }

        .leaders-meta {
            gap: 10px;
            margin-top: -48px;
        }
    }

    .leaders-meta .leader-entry:first-child {
        margin-right: -6px;
    }

    .leaders-meta .leader-entry:last-child {
        margin-left: -6px;
    }

    .leader-card img {
        width: 100%;
        height: auto;
    }

    .hero-composite {
        width: 85%;
        display: block;
        margin: 0 auto;
    }

    @media (min-width: 992px) {
        .hero-composite {
            width: 75%;
            margin-left: auto;
            margin-right: 0;
        }
    }

    .prayer-card {
        border: 1px solid #e6e8ef;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .prayer-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }

    .prayer-card.active {
        border-color: #1f7ae0;
        box-shadow: 0 8px 18px rgba(31, 122, 224, 0.12);
    }

    .prayer-label {
        font-weight: 700;
        color: #172433;
        margin-bottom: 2px;
        letter-spacing: .02em;
    }

    .prayer-time {
        font-size: 1.25rem;
        font-weight: 600;
        color: #172433;
    }

    .prayer-time.active {
        color: #1f7ae0;
    }

    .prayer-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(31, 122, 224, .12);
        color: #1f7ae0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 12px;
        flex: 0 0 auto;
    }

    .prayer-icon i {
        font-size: 1.25rem;
    }

    .countdown-banner {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 10px;
        border: 1px solid #e2e6ee;
        background: #f7fbff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        width: 100%;
        min-height: 56px;
    }

    .countdown-content {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        text-align: right;
    }

    .countdown-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(31, 122, 224, .12);
        color: #1f7ae0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .countdown-icon i {
        font-size: 1.6rem;
    }

    .countdown-label {
        font-weight: 700;
        color: #172433;
        letter-spacing: .02em;
        margin-bottom: 4px;
        font-size: .85rem;
        font-family: 'JetBrains Mono', monospace;
    }

    .countdown-time {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1f7ae0;
        line-height: 1;
        font-variant-numeric: tabular-nums;
        font-family: 'JetBrains Mono', monospace;
    }

    @media (min-width: 992px) {
        .countdown-time {
            font-size: 1.4rem;
        }

        .countdown-banner {
            width: auto;
            min-width: 320px;
        }

        .countdown-label {
            font-size: .9rem;
        }
    }

    .header-title {
        width: 100%;
        font-weight: 800;
        color: #172433;
    }

    @media (min-width: 992px) {
        .header-title {
            width: auto;
            min-width: 220px;
            font-size: 1.1rem;
        }
    }

    .header-title-banner {
        flex: 1 1 auto;
        padding-left: 0;
        padding-right: 0;
        gap: 4px;
        justify-content: center;
    }

    .header-title-banner .flex-grow-1 {
        flex: 0 0 auto;
    }

    @media (min-width: 992px) {
        .header-title-banner {
            min-width: 0;
        }
    }

    .portrait-badge {
        padding: 0;
        border-radius: 0;
        background: none;
    }

    .portrait-img {
        background: none;
    }

    .leader-meta {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 12px;
        background-color: #ffffff;
        border: 1px solid #e2e6ee;
        width: 100%;
        max-width: 360px;
        margin-top: 0;
    }

    .leader-meta .leader-name {
        color: #172433;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 2px;
        line-height: 1.25;
        min-height: calc(1.25em * 2);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .leader-card img {
        display: block;
        margin-bottom: 0;
    }

    .leader-meta .leader-role {
        display: block;
        padding: 0;
        border: none;
        background: none;
        color: #0154a6;
        font-weight: 600;
        font-size: 0.875rem;
        letter-spacing: 0.02em;
    }

    .leader-name {
        font-weight: 600;
        color: #ffffff;
    }

    .btn-gov-blue {
        background-color: #0154a6;
        color: #ffffff;
        border-color: #0154a6;
    }

    .btn-gov-blue:hover {
        background-color: #084c8f;
        border-color: #084c8f;
        color: #ffffff;
    }

    .footer a.btn:not(.rounded-circle) {
        border-radius: 999px;
    }

    .footer a.btn.rounded-circle {
        border-radius: 50% !important;
        width: 2.5rem !important;
        height: 2.5rem !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        line-height: 1 !important;
    }
</style>
<style>
    .schedule-table .table-responsive {
        max-height: none;
    }

    .schedule-table table {
        border: 1px solid #e6e8ef;
        box-shadow: none;
    }

    .schedule-month-table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .schedule-table thead th {
        position: sticky;
        top: 0;
        background: #f7f8fb;
        color: #172433;
        font-weight: 700;
        border-bottom: 1px solid #e6e8ef;
        z-index: 1;
    }

    .schedule-table thead th,
    .schedule-table tbody td {
        padding: .5rem .75rem;
    }

    .schedule-table tbody tr.today {
        background: #eaf3ff;
    }

    .schedule-table tbody tr.today td {
        font-weight: 600;
    }

    .schedule-month-table tbody tr:hover {
        background: #f9fbff;
    }

    .schedule-table .time-cell {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        color: #172433;
    }

    .schedule-table .time-cell.fardhu {
        color: #0154a6;
    }
</style>
<style>
    .gallery-carousel .carousel-item {
        border: 1px solid #e6e8ef;
        border-radius: 12px;
        overflow: hidden;
        background: #f7f8fb;
    }

    .gallery-carousel img {
        width: 100%;
        height: 42vh;
        max-height: 520px;
        min-height: 220px;
        display: block;
        object-fit: cover;
    }

    @media (min-width: 992px) {
        .gallery-carousel img {
            height: 420px;
        }
    }

    @media (min-width: 1400px) {
        .gallery-carousel img {
            height: 520px;
        }
    }
</style>
