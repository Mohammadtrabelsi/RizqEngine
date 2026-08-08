<?php

namespace App\Livewire\ProductCategories;

use App\Models\Category;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CategoryForm extends Component
{
    public ?int $categoryId = null;

    public string $category_code = '';

    public string $category_name = '';

    public function mount(?Category $category = null): void
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        if ($category && $category->exists) {
            $this->categoryId = $category->id;
            $this->category_code = (string) $category->category_code;
            $this->category_name = (string) $category->category_name;
        } else {
            $this->category_code = 'CA_'.str_pad((string) (Category::max('id') + 1), 2, '0', STR_PAD_LEFT);
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

    public function save()
    {
        abort_if(Gate::denies('access_product_categories'), 403);

        $data = $this->validate();

        if ($this->categoryId) {
            Category::findOrFail($this->categoryId)->update($data);
            session()->flash('info', trans('product.product-category-updated'));
        } else {
            Category::create($data);
            session()->flash('success', trans('product.product-category-created'));
        }

        return redirect()->route('product-categories.index');
    }

    public function render()
    {
        return view('livewire.product-categories.category-form');
    }
}
