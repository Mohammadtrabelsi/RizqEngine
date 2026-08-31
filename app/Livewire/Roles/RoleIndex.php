<?php

namespace App\Livewire\Roles;

use App\Services\RoleService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class RoleIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[Url(as: 'q')]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id, RoleService $roles): void
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $roles->delete($id);

        session()->flash('success', trans('user.role-deleted'));
    }

    public function render(RoleService $roles)
    {
        return view('livewire.roles.role-index', [
            'roles' => $roles->paginate($this->search),
        ]);
    }
}
