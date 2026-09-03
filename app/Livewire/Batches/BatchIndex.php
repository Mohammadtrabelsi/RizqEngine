<?php

namespace App\Livewire\Batches;

use App\Services\BatchService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BatchIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, BatchService $batches): void
    {
        abort_if(Gate::denies('delete_batches'), 403);

        $batches->delete($id);

        session()->flash('warning', __('batches.batch_deleted'));
    }

    public function render(BatchService $batches)
    {
        abort_if(Gate::denies('access_batches'), 403);

        return view('livewire.batches.batch-index', [
            'batches' => $batches->paginate($this->search),
        ])->layout('components.layouts.admin', ['title' => __('batches.batches')]);
    }
}
