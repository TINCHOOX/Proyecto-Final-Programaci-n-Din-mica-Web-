<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPresence;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function storeSession(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['bail', 'required', 'string', $this->validatePasswordInput(...)],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'El correo o la contraseña son incorrectos.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('inicio'));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $this->normalizeEmail($request);

        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['bail', 'required', 'string', $this->validatePasswordInput(...), 'confirmed', Password::min(8)],
        ]);

        Auth::login(User::create($attributes));
        $request->session()->regenerate();

        return redirect()->intended(route('inicio'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        UserPresence::query()->whereKey($request->user()->getKey())->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    }

    private function normalizeEmail(Request $request): void
    {
        $email = $request->input('email');

        if (is_string($email)) {
            $request->merge(['email' => mb_strtolower(trim($email))]);
        }
    }

    private function validatePasswordInput(string $attribute, string $value, Closure $fail): void
    {
        if (config('hashing.driver') === 'bcrypt' && (strlen($value) > 72 || str_contains($value, "\0"))) {
            $fail('La contraseña admite hasta 72 bytes y no puede incluir caracteres nulos.');
        }
    }
}
