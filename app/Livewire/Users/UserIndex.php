<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_user_management'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        abort_if(Gate::denies('access_user_management'), 403);

        User::findOrFail($id)->delete();

        session()->flash('warning', trans('user.user-deleted'));
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $term = '%'.$this->search.'%';
                $query->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            })
            ->latest()
            ->paginate(12);

        return view('livewire.users.user-index', compact('users'));
    }
}
