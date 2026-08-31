<?php

namespace App\Livewire\Users;

use App\Services\UserService;
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

    public function delete(int $id, UserService $users): void
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $users->delete($id);

        session()->flash('warning', trans('user.user-deleted'));
    }

    public function render(UserService $users)
    {
        return view('livewire.users.user-index', [
            'users' => $users->paginate($this->search),
        ]);
    }
}
