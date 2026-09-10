<?php

namespace App\Http\Controllers;

use App\Rules\MatchCurrentPassword;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    public function edit()
    {
        return view('user.profile');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.auth()->id(),
        ]);

        $this->users->updateProfile(
            auth()->user(),
            $data,
            $request->has('image') ? $request->image : null,
        );

        session()->flash('success', trans('user.profile-updated'));

        return back();
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'max:255', new MatchCurrentPassword],
            'password' => ['required', 'max:255', 'confirmed', Password::defaults()],
        ]);

        $this->users->updatePassword(auth()->user(), $request->password);

        session()->flash('success', trans('user.password-updated'));

        return back();
    }
}
