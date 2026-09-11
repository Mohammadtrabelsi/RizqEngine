<?php

namespace App\Livewire\Users;

use App\Services\UserService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $role = '';

    #[Url]
    public string $status = '';

    public function mount(): void
    {
        abort_if(Gate::denies('access_user_management'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['role', 'status']);
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
            'users' => $users->paginate($this->search, [
                'role' => $this->role,
                'status' => $this->status,
            ]),
            'roles' => Role::where('name', '!=', 'Super Admin')->get(),
        ]);
    }
}
