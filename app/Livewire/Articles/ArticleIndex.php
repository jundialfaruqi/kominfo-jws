<?php

namespace App\Livewire\Articles;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ArticleIndex extends Component
{
    use WithPagination;

    #[Title('Daftar Berita')]

    public $search;
    public $paginate = 10;
    protected $paginationTheme = 'bootstrap';

    public $articleId;
    public $title;
    public $article_category_id;
    public $description;
    public $content;
    public $status = 'Draft';
    public $published_at;

    public $isEdit = false;
    public $showForm = false;
    public $showTable = true;
    public $deleteArticleId;
    public $deleteArticleTitle;

    protected $rules = [
        'title'               => 'required|string|max:255',
        'article_category_id' => 'required|exists:article_categories,id',
        'description'         => 'nullable|string',
        'content'             => 'nullable|string',
        'status'              => 'required|in:Draft,Published',
        'published_at'        => 'nullable|date',
    ];

    protected $messages = [
        'title.required'               => 'Judul Berita wajib diisi!',
        'article_category_id.required' => 'Kategori wajib dipilih!',
        'article_category_id.exists'   => 'Kategori tidak valid!',
        'status.required'              => 'Status wajib dipilih!',
        'status.in'                    => 'Status tidak valid!',
    ];

    public function mount()
    {
        $this->published_at = now()->format('Y-m-d\TH:i');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Article::with(['user', 'category']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('category', function($cat) {
                      $cat->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $articlesList = $query->orderBy('created_at', 'desc')
            ->paginate($this->paginate);

        $categories = ArticleCategory::orderBy('name', 'asc')->get();

        return view('livewire.articles.index', [
            'articlesList' => $articlesList,
            'categories'   => $categories,
        ]);
    }

    public function showAddForm()
    {
        $this->resetValidation();
        $this->reset(['articleId', 'title', 'article_category_id', 'description', 'content', 'status']);
        $this->published_at = now()->format('Y-m-d\TH:i');
        $this->isEdit   = false;
        $this->showForm = true;
        $this->showTable = false;
        $this->dispatch('initSummernote');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $article = Article::findOrFail($id);

        $this->articleId           = $article->id;
        $this->title               = $article->title;
        $this->article_category_id = $article->article_category_id;
        $this->description         = $article->description;
        $this->content             = $article->content;
        $this->status              = $article->status;
        $this->published_at        = $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');

        $this->isEdit   = true;
        $this->showForm = true;
        $this->showTable = false;
        $this->dispatch('initSummernote', content: $this->content);
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->showTable = true;
        $this->resetValidation();
        $this->reset(['articleId', 'title', 'article_category_id', 'description', 'content', 'status', 'published_at']);
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit) {
                $article = Article::findOrFail($this->articleId);
            } else {
                $article = new Article();
                $article->user_id = Auth::id();
                // Slug from title
                $article->slug = Str::slug($this->title);
                
                // Ensure slug uniqueness (optional but good practice)
                $originalSlug = $article->slug;
                $count = 1;
                while (Article::where('slug', $article->slug)->exists()) {
                    $article->slug = $originalSlug . '-' . $count++;
                }
            }

            $article->article_category_id = $this->article_category_id;
            $article->title               = $this->title;
            $article->description         = $this->description;
            $article->content             = $this->content;
            $article->status              = $this->status;
            $article->published_at        = $this->published_at;
            $article->save();

            $this->dispatch('success', $this->isEdit ? 'Berita berhasil diperbarui!' : 'Berita berhasil ditambahkan!');
            $this->cancelForm();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $article = Article::findOrFail($id);
        $this->deleteArticleId    = $article->id;
        $this->deleteArticleTitle = $article->title;
    }

    public function destroyArticle()
    {
        try {
            Article::findOrFail($this->deleteArticleId)->delete();
            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Berita berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
