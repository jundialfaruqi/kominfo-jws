<?php

namespace App\Livewire\ArticleCategories;

use App\Models\ArticleCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ArticleCategoryIndex extends Component
{
    use WithPagination;

    #[Title('Kategori Berita')]

    public $search;
    public $paginate = 10;
    protected $paginationTheme = 'bootstrap';

    public $categoryId;
    public $name;

    public $isEdit = false;
    public $showForm = false;
    public $showTable = true;
    public $deleteCategoryId;
    public $deleteCategoryName;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    protected $messages = [
        'name.required' => 'Nama Kategori wajib diisi!',
        'name.max'      => 'Nama Kategori maksimal 255 karakter!',
        'name.string'   => 'Nama Kategori harus berupa teks!',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ArticleCategory::with('user');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $categoriesList = $query->orderBy('name', 'asc')
            ->paginate($this->paginate);

        return view('livewire.article-categories.index', [
            'categoriesList' => $categoriesList,
        ]);
    }

    public function showAddForm()
    {
        $this->resetValidation();
        $this->reset(['categoryId', 'name']);
        $this->isEdit   = false;
        $this->showForm = true;
        $this->showTable = false;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $category = ArticleCategory::findOrFail($id);

        $this->categoryId = $category->id;
        $this->name       = $category->name;

        $this->isEdit   = true;
        $this->showForm = true;
        $this->showTable = false;
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->showTable = true;
        $this->resetValidation();
        $this->reset(['categoryId', 'name']);
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEdit) {
                $category = ArticleCategory::findOrFail($this->categoryId);
            } else {
                $category = new ArticleCategory();
                $category->user_id = Auth::id();
            }

            $category->name = $this->name;
            $category->save();

            $this->dispatch('success', $this->isEdit ? 'Kategori berhasil diperbarui!' : 'Kategori berhasil ditambahkan!');
            $this->cancelForm();
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $category = ArticleCategory::findOrFail($id);
        $this->deleteCategoryId = $category->id;
        $this->deleteCategoryName = $category->name;
    }

    public function destroyCategory()
    {
        try {
            ArticleCategory::findOrFail($this->deleteCategoryId)->delete();
            $this->dispatch('closeDeleteModal');
            $this->dispatch('success', 'Kategori berhasil dihapus!');
        } catch (\Exception $e) {
            $this->dispatch('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
