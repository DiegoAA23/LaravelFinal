<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8)
                    ->mixedCase()   // Requiere al menos una letra mayúscula y una minúscula
                    ->letters()     // Requiere al menos una letra
                    ->numbers()     // Requiere al menos un número
                    ->symbols(),    // Requiere al menos un símbolo
                'confirmed',
                function ($attribute, $value, $fail) use ($user) {
                    if (Hash::check($value, $user->password)) {
                        $fail('The new password must be different from the current password.');
                    }
                },
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
