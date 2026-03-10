@php
    $getFirstImage = function ($content) {
        if (empty($content)) {
            return asset('nav-brand.png');
        }
        $doc = new DOMDocument();
        @$doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $images = $doc->getElementsByTagName('img');
        if ($images->length > 0) {
            return $images->item(0)->getAttribute('src');
        }
        return asset('nav-brand.png'); // Fallback image
    };
@endphp

<style>
    .article-content img {
        display: block;
        margin: 1rem auto;
        max-width: 100%;
        height: auto;
        border-radius: 10px;
    }

    .article-content {
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .article-content table {
        width: 100% !important;
        height: auto !important;
        display: block;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .article-content iframe,
    .article-content video {
        max-width: 100% !important;
    }
</style>

<div class="py-2 bg-white" style="margin-top: 70px; overflow-x: hidden;">
    <div class="container py-lg-5">
        <div class="row g-4 g-lg-5">
            {{-- Left Column: Article Content & Related --}}
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('welcome.index') }}"
                                class="text-decoration-none">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Berita</a></li>
                        <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 200px;">
                            {{ $article->title }}</li>
                    </ol>
                </nav>

                <div class="mb-4">
                    <span class="badge bg-blue-lt px-2 py-1 mb-2">
                        {{ $article->category->name ?? 'Berita' }}
                    </span>
                    <h1 class="display-5 fw-bold text-gov-dark mb-3">
                        {{ $article->title }}
                    </h1>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                        <div class="d-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                <path d="M16 3l0 4" />
                                <path d="M8 3l0 4" />
                                <path d="M4 11l16 0" />
                                <path d="M8 15h2v2h-2z" />
                            </svg>
                            {{ $article->published_at ? $article->published_at->format('d M Y H:i') : $article->created_at->format('d M Y H:i') }}
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            </svg>
                            Oleh: {{ $article->user->name ?? 'Admin' }}
                        </div>
                    </div>
                </div>

                <div class="article-content text-gov-dark lh-lg mb-5 pb-5 border-bottom">
                    {!! $article->content !!}
                </div>

                {{-- Related Articles Section --}}
                @if ($relatedArticles->count() > 0)
                    <div class="related-articles mt-5">
                        <h3 class="fw-bold text-gov-dark mb-4 d-flex align-items-center gap-2">
                            <span class="bg-primary rounded-pill" style="width: 4px; height: 24px;"></span>
                            Artikel Terkait
                        </h3>
                        <div class="row g-4">
                            @foreach ($relatedArticles as $related)
                                <div class="col-md-4">
                                    <div
                                        class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden article-card-sm">
                                        <div class="card-body p-3">
                                            <span
                                                class="badge bg-blue-lt mb-2 small">{{ $related->category->name ?? 'Berita' }}</span>
                                            <h5 class="fw-bold text-gov-dark mb-2 line-clamp-2"
                                                style="font-size: 0.95rem;">
                                                @php
                                                    $relDate = $related->published_at
                                                        ? $related->published_at->format('d-m-Y')
                                                        : $related->created_at->format('d-m-Y');
                                                @endphp
                                                <a href="{{ route('articles.show', ['date' => $relDate, 'slug' => $related->slug]) }}"
                                                    class="text-decoration-none text-dark">
                                                    {{ $related->title }}
                                                </a>
                                            </h5>
                                            <div class="text-muted small mt-2">
                                                {{ $related->published_at ? $related->published_at->format('d M Y') : $related->created_at->format('d M Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Column: Latest Sidebar --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px;">
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-gov-dark text-white py-3 border-0">
                            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-news">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M16 6h3a1 1 0 0 1 1 1v11a2 2 0 0 1 -4 0v-13a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a3 3 0 0 0 3 3h11" />
                                    <path d="M8 8l4 0" />
                                    <path d="M8 12l4 0" />
                                    <path d="M8 16l4 0" />
                                </svg>
                                Berita Terbaru
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach ($latestArticles as $latest)
                                    @php
                                        $latDate = $latest->published_at
                                            ? $latest->published_at->format('d-m-Y')
                                            : $latest->created_at->format('d-m-Y');
                                    @endphp
                                    <a href="{{ route('articles.show', ['date' => $latDate, 'slug' => $latest->slug]) }}"
                                        class="list-group-item list-group-item-action p-3 border-bottom">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-3">
                                                <div class="rounded-3 overflow-hidden" style="height: 70px;">
                                                    <img src="{{ $getFirstImage($latest->content) }}"
                                                        class="h-100 w-auto object-fit-cover"
                                                        alt="{{ $latest->title }}"
                                                        onerror="this.src='{{ asset('nav-brand.png') }}'">
                                                </div>
                                            </div>
                                            <div class="col-9">
                                                <h6 class="fw-bold mb-1 text-gov-dark line-clamp-2"
                                                    style="font-size: 0.85rem; line-height: 1.4;">
                                                    {{ $latest->title }}
                                                </h6>
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    {{ $latest->published_at ? $latest->published_at->format('d M Y') : $latest->created_at->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 text-center py-3">
                            <a href="{{ route('welcome.index') }}"
                                class="text-decoration-none text-gov-blue fw-bold small">
                                Lihat Semua Berita
                            </a>
                        </div>
                    </div>

                    {{-- Admin Quick Links / Info --}}
                    <div class="card border-0 shadow-sm rounded-4 bg-soft">
                        <div class="card-body p-4 text-center">
                            <img src="{{ asset('nav-brand.png') }}" width="40" class="mb-3" alt="JWS Logo">
                            <h6 class="fw-bold text-gov-dark">JWS Kota Pekanbaru</h6>
                            <p class="text-muted small mb-0">Aplikasi resmi masjid paripurna Pemerintah Kota Pekanbaru.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
