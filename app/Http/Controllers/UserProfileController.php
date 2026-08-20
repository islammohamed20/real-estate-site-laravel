<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    public function index(): View
    {
        return view('dashboard.profile.index', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'avatar_path' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $update = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department' => $validated['department'] ?? null,
            'avatar_path' => $validated['avatar_path'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $update['password'] = Hash::make($validated['password']);
        }

        $user->update($update);

        return back()->with('status', __('Profile updated successfully.'));
    }
}
