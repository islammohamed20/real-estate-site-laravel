<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('public.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // A customer may sign in with either their email or their phone number.
        $customer = Customer::query()
            ->where('email', $data['login'])
            ->orWhere('phone', $data['login'])
            ->first();

        if ($customer === null) {
            return back()->withErrors([
                'login' => __('The provided credentials are incorrect.'),
            ])->onlyInput('login');
        }

        if (empty($customer->password)) {
            return back()->withErrors([
                'login' => __('No password is set for this account yet — please register to create one.'),
            ])->onlyInput('login');
        }

        if (! Hash::check($data['password'], $customer->password)) {
            return back()->withErrors([
                'login' => __('The provided credentials are incorrect.'),
            ])->onlyInput('login');
        }

        Auth::guard('customer')->login($customer, $request->boolean('remember'));

        $request->session()->regenerate();

        $redirectUrl = $request->input('redirect') ?? route('customer.account');
        return redirect()->intended($redirectUrl);
    }

    public function showRegister(): View
    {
        return view('public.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Match an existing CRM record first (by phone, then by email) so a
        // customer already in the CRM is linked instead of being blocked by a
        // unique constraint — they just get a portal password.
        $customer = Customer::query()->where('phone', $data['phone'])->first()
            ?? (! empty($data['email']) ? Customer::query()->where('email', $data['email'])->first() : null);

        if ($customer !== null) {
            if (! empty($customer->password)) {
                return back()->withErrors([
                    'phone' => __('An account already exists for this phone or email — please log in instead.'),
                ])->withInput();
            }

            // Existing CRM customer without a portal password — link the account.
            $customer->update([
                'name' => $data['name'],
                'occupation' => $data['occupation'] ?? $customer->occupation,
                'email' => $data['email'] ?? $customer->email,
                'password' => $data['password'],
            ]);
        } else {
            $customer = Customer::create([
                'name' => $data['name'],
                'occupation' => $data['occupation'] ?? null,
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'password' => $data['password'],
                'source' => 'portal',
            ]);
        }

        Auth::guard('customer')->login($customer);

        $request->session()->regenerate();

        $redirectUrl = $request->input('redirect') ?? route('customer.account');
        return redirect()->intended($redirectUrl);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
}
