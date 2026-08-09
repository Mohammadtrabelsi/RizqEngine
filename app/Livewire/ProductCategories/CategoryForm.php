<?php

namespace App\Livewire\ProductCategories;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CategoryForm extends Component
{
    public ?int $categoryId = null;

    public string $category_code = '';

    public string $category_name = '';

    public function mount(?Category $category = null, ?CategoryService $categories = null): void
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        if ($category && $category->exists) {
            $this->categoryId = $category->id;
            $this->category_code = (string) $category->category_code;
            $this->category_name = (string) $category->category_name;
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
        ];
    }

    public function save(CategoryService $categories)
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        $data = $this->validate();

        if ($this->categoryId) {
            $categories->update($this->categoryId, $data);
            session()->flash('info', trans('product.product-category-updated'));
        } else {
            $categories->create($data);
            session()->flash('success', trans('product.product-category-created'));
        }

        return redirect()->route('product-categories.index');
    }

    public function render()
    {
        return view('livewire.product-categories.category-form');
    }
}
