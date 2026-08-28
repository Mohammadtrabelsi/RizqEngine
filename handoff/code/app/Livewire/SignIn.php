<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SignIn extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';

    public bool $remember = false;

    /**
     * "Keep me signed in on this register" persists the session for shared
     * till hardware; pair it with a store policy on idle lock.
     */
    public function authenticate()
    {
        $this->validate();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => __('pos.auth.failed'),
            ]);
        }

        session()->regenerate();

        return redirect()->route('dashboard', ['locale' => app()->getLocale()]);
    }

    public function render()
    {
        return view('livewire.sign-in')->layout('layouts.guest');
    }
}
