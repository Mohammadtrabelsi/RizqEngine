<form wire:submit="save">
    <div class="mb-4">
        <label for="category_name">{{ __('expense-category.category_name') }} <span class="text-danger">*</span></label>
        <input class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('category_name') !border-red-500 @enderror" type="text" wire:model="category_name">
        @error('category_name') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
    </div>
    <div class="mb-4">
        <label for="category_description">{{ __('expense-category.description') }}</label>
        <textarea class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm leading-normal text-slate-900 placeholder:text-slate-400 transition-colors focus:border-indigo-500 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 @error('category_description') !border-red-500 @enderror" wire:model="category_description" rows="5"></textarea>
        @error('category_description') <span class="block w-full mt-1 text-xs text-red-500 d-block">{{ $message }}</span> @enderror
    </div>
    <div class="mb-4 mb-0">
        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-transparent px-4 py-2 text-sm font-medium leading-tight text-slate-900 transition-colors cursor-pointer select-none no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-45 disabled:cursor-default bg-indigo-600 !text-white border-indigo-600 hover:bg-indigo-700 hover:border-indigo-700">
            {{ $categoryId ? __('app.update') : __('app.create') }} <i class="bi bi-check"></i>
        </button>
    </div>
</form>
