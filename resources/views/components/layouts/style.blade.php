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

    @media (max-width: 991.98px) {
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
