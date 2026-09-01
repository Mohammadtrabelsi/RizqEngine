<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function __construct(private readonly RoleService $roles) {}

    public function index()
    {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user.roles.index');

    }

    public function create()
    {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user.roles.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
        ]);

        $this->roles->create($data['name'], $data['permissions']);

        session()->flash('success', trans('user.role-created'));

        return redirect()->route('roles.index');
    }

    public function edit(Role $role)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
        ]);

        $this->roles->update($role->id, $data['name'], $data['permissions']);

        session()->flash('success', trans('user.role-updated'));

        return redirect()->route('roles.index');
    }

    public function destroy(Role $role)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $this->roles->delete($role->id);

        session()->flash('success', trans('user.role-deleted'));

        return redirect()->route('roles.index');
    }
}
