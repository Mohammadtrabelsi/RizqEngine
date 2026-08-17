<?php

namespace App\Livewire\Roles;

use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

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

    public function delete(int $id): void
    {
        abort_if(Gate::denies('access_user_management'), 403);

        Role::findOrFail($id)->delete();

        session()->flash('success', trans('user.role-deleted'));
    }

    public function render()
    {
        $roles = Role::query()
            ->with('permissions')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.roles.role-index', compact('roles'));
    }
}
