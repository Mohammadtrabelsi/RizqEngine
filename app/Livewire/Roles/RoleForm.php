<?php

namespace App\Livewire\Roles;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleForm extends Component
{
    public ?int $roleId = null;

    public string $name = '';

    public array $permissions = [];

    public bool $selectAll = false;

    public function mount(?Role $role = null): void
    {
        abort_if(Gate::denies('access_user_management'), 403);

        if ($role && $role->exists) {
            $this->roleId = $role->id;
            $this->name = (string) $role->name;
            $this->permissions = $role->permissions->pluck('name')->all();
        }
    }

    public function updatedSelectAll($value): void
    {
        $this->permissions = $value ? Permission::pluck('name')->all() : [];
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
        ];
    }

    public function save()
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $this->validate();

        if ($this->roleId) {
            $role = Role::findOrFail($this->roleId);
            $role->update(['name' => $this->name]);
            $role->syncPermissions($this->permissions);

            session()->flash('success', trans('user.role-updated'));
        } else {
            $role = Role::create(['name' => $this->name]);
            $role->givePermissionTo($this->permissions);

            session()->flash('success', trans('user.role-created'));
        }

        return redirect()->route('roles.index');
    }

    public function render()
    {
        return view('livewire.roles.role-form');
    }
}
