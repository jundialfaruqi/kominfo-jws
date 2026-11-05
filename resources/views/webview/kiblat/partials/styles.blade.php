    <style>
        :root {
            --teal: #3cb4b4;
            --teal-lt: #54ccb4;
            --teal-dk: #0f7a6f;
            --blue-dk: #04223f;
            --blue: #0a4d7a;
            --blue-lt: #0f7bb0;
            --ink: #0b1b2b;
            --muted: #6b7c8f;
            --line: #e6eef5;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            overscroll-behavior: none;
            touch-action: none;
            position: fixed;
            inset: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #fff;
            background: #fff;
            user-select: none;
        }

        .app {
            width: 100%;
            max-width: 480px;
            height: 100%;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        main {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            position: relative;
        }

        .screen {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .screen[hidden] {
            display: none
        }

        /* ================= BERANDA / SHOLAT ================= */
        .screen--sky {
            background:
                linear-gradient(180deg, #03203c 0%, #063c63 14%, #0a5b8a 30%, #3f92bb 46%,
                    #a9d3e6 60%, #e7f3f9 72%, #ffffff 82%, #ffffff 100%);
        }

        .hero {
            padding: 14px 20px 0;
            flex-shrink: 0
        }

        .date-block {
            text-align: right
        }

        .greg {
            font-size: .76rem;
            font-weight: 600;
            color: rgba(255, 255, 255, .82);
            letter-spacing: .01em
        }

        .hijri {
            font-size: .98rem;
            font-weight: 800;
            color: #fff;
            margin-top: 3px;
            text-shadow: 0 1px 6px rgba(0, 0, 0, .18)
        }

        .np {
            margin-top: 12px;
            text-align: left
        }

        .np-label {
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .78);
        }

        .np-row {
            display: flex;
            align-items: baseline;
            gap: 14px;
            margin-top: 2px
        }

        .np-time {
            font-size: clamp(2rem, 4.5dvh, 2.7rem);
            font-weight: 800;
            line-height: 1;
            color: #fff;
            font-variant-numeric: tabular-nums;
            text-shadow: 0 2px 10px rgba(0, 0, 0, .22)
        }

        .np-remain {
            font-size: 1.02rem;
            font-weight: 700;
            color: #cdeeff;
            font-variant-numeric: tabular-nums
        }

        /* gambar Masjidil Haram di tengah beranda */
        .haram {
            position: relative;
            margin: 14px 20px 0;
            flex: 1;
            min-height: 100px;
            max-height: 280px;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 16px 34px rgba(4, 34, 63, .30);
            background: #0a3a5c;
        }

        .haram img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 42%;
            display: block
        }

        .haram::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(3, 32, 60, .06) 0%, rgba(3, 32, 60, 0) 42%, rgba(3, 32, 60, .55) 100%);
            transition: background .25s ease;
        }

        .haram.is-active::after {
            background: linear-gradient(180deg, rgba(3, 18, 34, .74) 0%, rgba(3, 18, 34, .82) 100%);
        }

        .haram figcaption {
            position: absolute;
            left: 14px;
            bottom: 10px;
            z-index: 2;
            font-size: .6rem;
            font-weight: 800;
            letter-spacing: .10em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .94);
            text-shadow: 0 1px 8px rgba(0, 0, 0, .65);
            transition: opacity .2s ease;
        }

        .haram.is-active figcaption {
            opacity: 0;
            pointer-events: none;
        }

        .haram-status {
            position: absolute;
            inset: 0;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            text-align: center;
        }

        .haram-status[hidden] {
            display: none;
        }

        .hs-top {
            font-size: clamp(.60rem, 1.55dvh, .80rem);
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .82);
            line-height: 1.15;
            text-shadow: 0 1px 6px rgba(0, 0, 0, .45);
        }

        .haram-status.is-syuruq .hs-top,
        .haram-status.is-dhuha .hs-top {
            letter-spacing: .10em;
        }

        .hs-name {
            font-size: clamp(1.30rem, 3.8dvh, 2.15rem);
            font-weight: 900;
            letter-spacing: .10em;
            text-transform: uppercase;
            color: #ffffff;
            line-height: 1.05;
            margin-top: clamp(3px, .6dvh, 6px);
            text-shadow: 0 2px 12px rgba(0, 0, 0, .55);
            max-width: 100%;
        }

        .haram-status.is-syuruq .hs-name {
            font-size: clamp(.78rem, 2.05dvh, 1.12rem);
            letter-spacing: .05em;
            line-height: 1.15;
        }

        .hs-sub {
            font-size: clamp(.58rem, 1.45dvh, .76rem);
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .86);
            line-height: 1.15;
            margin-top: clamp(2px, .5dvh, 5px);
            text-shadow: 0 1px 6px rgba(0, 0, 0, .45);
        }

        .hs-sub[hidden] {
            display: none;
        }

        .haram-status.is-imsak .hs-name {
            font-size: clamp(1.18rem, 3.3dvh, 1.85rem);
        }

        .haram-status.is-imsak .hs-bar-track {
            margin-top: clamp(5px, 1.1dvh, 10px);
        }

        .haram-status.is-imsak .hs-bottom {
            font-size: clamp(1.30rem, 3.8dvh, 2.15rem);
            margin-top: clamp(4px, .9dvh, 8px);
        }

        .hs-bar-track {
            width: min(82%, 280px);
            height: clamp(8px, 1.45dvh, 13px);
            background: rgba(255, 255, 255, .22);
            border-radius: 999px;
            overflow: hidden;
            margin-top: clamp(7px, 1.4dvh, 14px);
            flex-shrink: 0;
        }

        .haram-status.is-syuruq .hs-bar-track {
            width: min(88%, 310px);
        }

        .hs-bar-fill {
            height: 100%;
            width: 0%;
            background: #ffd600;
            border-radius: 999px;
            transition: width .35s linear;
        }

        .hs-bottom {
            font-family: "JetBrainsMono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: clamp(1.45rem, 4.3dvh, 2.45rem);
            font-weight: 800;
            color: #ffffff;
            line-height: 1;
            margin-top: clamp(6px, 1.15dvh, 11px);
            font-variant-numeric: tabular-nums;
            text-shadow: 0 2px 12px rgba(0, 0, 0, .55);
        }

        .haram-status.is-dhuha .hs-bottom {
            font-size: clamp(.92rem, 2.4dvh, 1.32rem);
            letter-spacing: .04em;
            white-space: nowrap;
        }

        @keyframes hsBlinkBar {
            0%, 49.9% {
                background-color: #ef4444;
            }
            50%, 100% {
                background-color: #ffd600;
            }
        }

        @keyframes hsBlinkText {
            0%, 49.9% {
                color: #ef4444;
            }
            50%, 100% {
                color: #ffd600;
            }
        }

        .haram-status.is-blinking .hs-bar-fill {
            animation: hsBlinkBar 1s steps(1, end) infinite;
        }

        .haram-status.is-blinking .hs-bottom {
            animation: hsBlinkText 1s steps(1, end) infinite;
        }

        .grid-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 16px 20px 0;
            flex-shrink: 0
        }

        .grid-head h2 {
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #0b3c5d;
            margin: 0
        }

        .city-link {
            border: 0;
            background: rgba(255, 255, 255, .75);
            font: inherit;
            font-size: .7rem;
            font-weight: 700;
            color: #0b3c5d;
            padding: 5px 11px;
            border-radius: 999px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(11, 60, 93, .10);
            max-width: 60%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pgrid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            padding: 10px 16px 0;
            flex-shrink: 0
        }

        .pcard {
            background: #fff;
            border: 1px solid #e4eef6;
            border-radius: 16px;
            padding: 10px 8px 9px;
            text-align: center;
            box-shadow: 0 6px 16px rgba(11, 64, 98, .10);
            transition: transform .15s;
        }

        .pcard .n {
            font-size: .63rem;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: #7f95a8
        }

        .pcard .t {
            font-size: 1.06rem;
            font-weight: 800;
            color: #0b3c5d;
            margin-top: 4px;
            font-variant-numeric: tabular-nums
        }

        .pcard .b {
            display: block;
            font-size: .55rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--teal-dk);
            margin-top: 3px;
            min-height: .7em
        }

        .pcard.active {
            background: linear-gradient(140deg, #1186bb, #0a4d7a);
            border-color: transparent;
            box-shadow: 0 10px 24px rgba(10, 77, 122, .34);
            transform: translateY(-2px);
        }

        .pcard.active .n {
            color: #bfe9ff
        }

        .pcard.active .t {
            color: #fff
        }

        .pcard.active .b {
            color: #bfe9ff
        }

        .src {
            padding: 10px 20px 10px;
            margin: 0;
            font-size: .66rem;
            line-height: 1.4;
            color: #5d7c92;
            flex-shrink: 0
        }

        .src b {
            color: #0b3c5d
        }

        /* ================= KIBLAT ================= */
        .screen--dark {
            background:
                radial-gradient(85% 38% at 50% 44%, rgba(4, 69, 127, .95) 0%, rgba(4, 40, 74, .55) 48%, transparent 72%),
                linear-gradient(180deg, #050f17 0%, #06182a 24%, #04284a 58%, #050f17 100%);
        }

        #scrKiblat {
            position: relative;
            justify-content: center;
            align-items: center;
            padding: 0
        }

        .qibla-head {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 3;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px 2px;
        }

        .qibla-title {
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: #9fc4d8
        }

        .qibla-city {
            font-size: .72rem;
            font-weight: 700;
            color: #cdeeff;
            max-width: 55%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .compass-wrap {
            position: relative;
            width: min(80vw, 46dvh, 340px);
            aspect-ratio: 1/1;
            margin: 0 auto;
            touch-action: none;
            cursor: grab;
        }

        .compass-wrap:active {
            cursor: grabbing
        }

        .compass-wrap::before {
            content: "";
            position: absolute;
            inset: -9%;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(84, 204, 180, .20) 0%, rgba(3, 69, 127, .10) 45%, transparent 68%);
            transition: background .35s;
        }

        .compass-wrap.aligned::before {
            background: radial-gradient(circle, rgba(84, 204, 180, .42) 0%, rgba(60, 180, 180, .16) 48%, transparent 70%);
        }

        .dial {
            position: absolute;
            inset: 0;
            will-change: transform;
            filter: drop-shadow(0 16px 26px rgba(0, 0, 0, .45))
        }

        .needle {
            position: absolute;
            inset: 0;
            will-change: transform;
            pointer-events: none;
            z-index: 2
        }

        .needle svg {
            width: 100%;
            height: 100%;
            display: block
        }

        .compass-wrap.aligned .needle {
            filter: drop-shadow(0 0 14px rgba(84, 204, 180, .9))
        }

        .hub {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 13%;
            height: 13%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: radial-gradient(circle at 34% 28%, #ffffff, #cfe9e6 42%, #3f8f8b);
            box-shadow: 0 0 0 5px rgba(5, 15, 23, .55), 0 6px 16px rgba(0, 0, 0, .5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
            z-index: 2;
        }

        .compass-logo {
            position: absolute;
            left: 50%;
            top: 60.5%;
            transform: translateX(-50%);
            width: 34%;
            height: auto;
            pointer-events: none;
            z-index: 1;
            display: block;
            object-fit: contain;
        }

        .fixed-mark {
            position: absolute;
            left: 50%;
            top: -6px;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 13px solid var(--teal-lt);
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, .6));
            z-index: 3;
        }

        .qibla-read {
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 9px;
            margin-top: 10px;
            font-variant-numeric: tabular-nums
        }

        .qibla-read b {
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff
        }

        .qibla-read span {
            font-size: .8rem;
            color: #9fc4d8
        }

        .verdict {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 80px;
            text-align: center;
            font-size: .92rem;
            line-height: 1.5;
            color: #c8dbe8;
            padding: 0 20px;
            min-height: 2.4em;
            width: 100%;
            max-width: 400px;
        }

        .verdict b {
            color: var(--teal-lt)
        }

        /* ================= PENGATURAN ================= */
        .screen--light {
            background: #fff;
            color: var(--ink);
            padding: 0 0 8px;
            justify-content: space-between
        }

        .set-wrap {
            padding: 12px 18px 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
            min-height: 0
        }

        .set-wrap h1 {
            margin: 0;
            font-size: 1.05rem;
            color: var(--ink)
        }

        .set-wrap .sub {
            margin: 2px 0 0;
            font-size: .72rem;
            color: var(--muted)
        }

        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 10px 12px;
            margin-top: 0;
            box-shadow: 0 6px 18px rgba(11, 60, 93, .06);
        }

        .card h3 {
            margin: 0 0 6px;
            font-size: .62rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted)
        }

        select {
            width: 100%;
            font: inherit;
            font-size: .82rem;
            padding: 7px 32px 7px 10px;
            border-radius: 10px;
            border: 1px solid #dbe6ee;
            background: #f7fafc;
            color: var(--ink);
            margin-top: 6px;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            background-image: linear-gradient(45deg, transparent 50%, #7f96ab 50%), linear-gradient(135deg, #7f96ab 50%, transparent 50%);
            background-position: calc(100% - 18px) 50%, calc(100% - 13px) 50%;
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
        }

        select:disabled {
            opacity: .5
        }

        .btn {
            width: 100%;
            font: inherit;
            font-size: .8rem;
            font-weight: 700;
            margin-top: 6px;
            padding: 8px 10px;
            border-radius: 10px;
            border: 1px solid #dbe6ee;
            background: #f5f9fc;
            color: #26445f;
            cursor: pointer;
        }

        .btn:active {
            transform: scale(.99)
        }

        .btn.primary {
            background: linear-gradient(135deg, var(--teal-lt), var(--teal));
            border-color: transparent;
            color: #053b39
        }

        .hint {
            font-size: .65rem;
            line-height: 1.35;
            color: var(--muted);
            margin: 6px 0 0
        }

        .kv {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-size: .74rem;
            color: #3d4f66;
            padding: 5px 0;
            border-bottom: 1px solid var(--line)
        }

        .kv:last-child {
            border-bottom: 0
        }

        .kv span:last-child {
            font-weight: 700;
            color: #16324a;
            text-align: right
        }

        .about {
            font-size: .62rem;
            line-height: 1.35;
            color: var(--muted);
            padding: 4px 18px 0;
            margin: 0
        }

        /* ================= NAV ================= */
        .nav {
            flex-shrink: 0;
            width: 100%;
            max-width: 480px;
            z-index: 20;
            display: flex;
            background: rgba(255, 255, 255, .97);
            backdrop-filter: blur(12px);
            border-top: 1px solid var(--line);
            padding: 8px 6px;
        }

        .nav button {
            flex: 1;
            border: 0;
            background: none;
            font: inherit;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            font-size: .62rem;
            font-weight: 700;
            color: #8a99a8;
            padding: 2px 0;
        }

        .nav button .ico {
            font-size: 1.18rem;
            line-height: 1;
            filter: grayscale(1);
            opacity: .6;
            transition: .2s
        }

        .nav button.active {
            color: var(--blue-lt)
        }

        .nav button.active .ico {
            filter: none;
            opacity: 1;
            transform: translateY(-1px)
        }
    </style>
