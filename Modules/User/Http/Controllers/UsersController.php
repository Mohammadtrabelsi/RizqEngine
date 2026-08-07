<?php

namespace Modules\User\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Upload\Entities\Upload;

class UsersController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $users = User::latest()->paginate(12);

        return view('user::users.index', compact('users'));
    }

    public function create()
    {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user::users.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => $request->is_active,
        ]);

        $user->assignRole($request->role);

        if ($request->has('image')) {
            $tempFile = Upload::where('folder', $request->image)->first();

            if ($tempFile) {
                $user->addMedia(Storage::path('public/temp/'.$request->image.'/'.$tempFile->filename))->toMediaCollection('avatars');

                Storage::deleteDirectory('public/temp/'.$request->image);
                $tempFile->delete();
            }
        }

        session()->flash('success', trans('user.user-created'));

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        return view('user::users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->is_active,
        ]);

        $user->syncRoles($request->role);

        if ($request->has('image')) {
            $tempFile = Upload::where('folder', $request->image)->first();

            if ($user->getFirstMedia('avatars')) {
                $user->getFirstMedia('avatars')->delete();
            }

            if ($tempFile) {
                $user->addMedia(Storage::path('public/temp/'.$request->image.'/'.$tempFile->filename))->toMediaCollection('avatars');

                Storage::deleteDirectory('public/temp/'.$request->image);
                $tempFile->delete();
            }
        }

        session()->flash('info', trans('user.user-updated'));

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        abort_if(Gate::denies('access_user_management'), 403);

        $user->delete();

        session()->flash('warning', trans('user.user-deleted'));

        return redirect()->route('users.index');
    }
}
