<?php

namespace App\Livewire\ProductCategories;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class CategoryForm extends Component
{
    use WithFileUploads;

    public ?int $categoryId = null;

    public string $category_code = '';

    public string $category_name = '';

    public string $description = '';

    public string $color = '#6c757d';

    public bool $is_active = true;

    public $image;

    public ?string $existingImageUrl = null;

    public function mount(?Category $category = null, ?CategoryService $categories = null): void
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        if ($category && $category->exists) {
            $this->categoryId = $category->id;
            $this->category_code = (string) $category->category_code;
            $this->category_name = (string) $category->category_name;
            $this->description = (string) $category->description;
            $this->color = (string) ($category->color ?: '#6c757d');
            $this->is_active = (bool) $category->is_active;
            $this->existingImageUrl = $category->image_url;
        } else {
            $this->category_code = ($categories ?? app(CategoryService::class))->nextCode();
        }
    }

    protected function rules(): array
    {
        return [
            'category_code' => [
                'required',
                Rule::unique('categories', 'category_code')->ignore($this->categoryId),
            ],
            'category_name' => ['required'],
            'description' => ['nullable', 'string', 'max:2000'],
            'color' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function save(CategoryService $categories)
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        $data = $this->validate();

        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($this->categoryId) {
            $categories->update($this->categoryId, $data, $image);
            session()->flash('info', trans('product.product-category-updated'));
        } else {
            $categories->create($data, $image);
            session()->flash('success', trans('product.product-category-created'));
        }

        return redirect()->route('product-categories.index');
    }

    public function render()
    {
        return view('livewire.product-categories.category-form');
    }
}
