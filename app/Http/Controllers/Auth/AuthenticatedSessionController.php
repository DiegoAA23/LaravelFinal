<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $users = User::all();
        foreach ($users as $user) {
            try {
                $decryptedEmail = Crypt::decryptString($user->email);
                if ($decryptedEmail === $request->email) {
                    if (Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
                        $request->session()->regenerate();
                        return redirect()->intended(route('dashboard'));
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        throw ValidationException::withMessages([
            'email' => ['These credentials do not match our records.'],
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
