<footer class="footer footer-transparent d-print-none">
    <div class="container-xl">
        <div class="row text-start align-items-center justify-content-center ">
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        <span class="fw-bold">
                            &copy; {{ date('Y') }}
                            <a wire:navigate href="{{ route('dashboard.index') }}">JWS -
                                Diskominfo Pekanbaru</a>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
