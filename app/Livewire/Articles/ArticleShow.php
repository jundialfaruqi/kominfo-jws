<?php

namespace App\Livewire\Articles;

use App\Models\Article;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

class ArticleShow extends Component
{
    public $article;

    #[Layout('components.layouts.article')]
    public function mount($date, $slug)
    {
        $this->article = Article::with(['category', 'user'])
            ->where('slug', $slug)
            ->where('status', 'Published')
            ->firstOrFail();
    }

    public function render()
    {
        $relatedArticles = Article::where('article_category_id', $this->article->article_category_id)
            ->where('id', '!=', $this->article->id)
            ->where('status', 'Published')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        $latestArticles = Article::where('status', 'Published')
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get();

        return view('livewire.articles.article-show', [
            'relatedArticles' => $relatedArticles,
            'latestArticles' => $latestArticles,
        ])->title($this->article->title . ' - JWS Kota Pekanbaru');
    }
}
