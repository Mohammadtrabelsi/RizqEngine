<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class UsersController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    public function index()
    {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user.users.index');

    }

    public function create()
    {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user.users.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255|confirmed',
        ]);

        $data['is_active'] = $request->is_active;

        $this->users->create($data, $request->role, $request->has('image') ? $request->image : null);

        session()->flash('success', trans('user.user-created'));

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ]);

        $data['is_active'] = $request->is_active;

        $this->users->update($user, $data, $request->role, $request->has('image') ? $request->image : null);

        session()->flash('info', trans('user.user-updated'));

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $this->users->deleteModel($user);

        session()->flash('warning', trans('user.user-deleted'));

        return redirect()->route('users.index');
    }
}
