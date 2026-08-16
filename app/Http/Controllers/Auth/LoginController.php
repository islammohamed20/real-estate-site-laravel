<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginHistory;
use App\Models\User;
use App\Notifications\CrmActivityNotification;
use App\Services\Security\DeviceDetector;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, DeviceDetector $deviceDetector): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $ip = $request->ip();
            $deviceName = $deviceDetector->deviceName($request->userAgent());

            LoginHistory::create([
                'user_id' => null,
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
                'device_name' => $deviceName,
                'device_type' => $deviceDetector->deviceType($request->userAgent()),
                'location' => null,
                'is_successful' => false,
                'logged_in_at' => now(),
            ]);

            $this->notifyFailedLogin($request, $ip, $deviceName);

            return back()->withErrors([
                'email' => __('The provided credentials are incorrect.'),
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        session(['auth_initiated_at' => now()->timestamp]);

        $user = $request->user();
        $user?->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        LoginHistory::create([
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_name' => $deviceDetector->deviceName($request->userAgent()),
            'device_type' => $deviceDetector->deviceType($request->userAgent()),
            'location' => null,
            'is_successful' => true,
            'logged_in_at' => now(),
        ]);

        // Users with two-factor authentication enabled must pass the one-time
        // TOTP / recovery-code challenge before entering the dashboard.
        if ($user?->two_factor_enabled) {
            $request->session()->put('2fa:user:id', $user->id);

            return redirect()->route('2fa.verify');
        }

        return redirect()->intended(route('dashboard.home'));
    }

    /**
     * Notify administrators & sales managers about a failed login attempt.
     * Dedupes by email + IP over a 5-minute window so brute-force runs
     * don't flood the notification bell.
     */
    private function notifyFailedLogin(Request $request, string $ip, string $deviceName): void
    {
        $email = strtolower(trim((string) $request->input('email')));

        if ($email === '') {
            return;
        }

        $recent = DB::table('notifications')
            ->where('type', CrmActivityNotification::class)
            ->where('data->email', $email)
            ->where('data->ip', $ip)
            ->where('created_at', '>', now()->subMinutes(5))
            ->exists();

        if ($recent) {
            return;
        }

        CrmActivityNotification::notifyManagers(new CrmActivityNotification(
            type: 'login_failed',
            payload: [
                'email' => $email,
                'ip' => $ip,
                'device' => $deviceName,
                'known_user' => User::query()->where('email', $email)->exists(),
            ],
        ));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user) {
            LoginHistory::query()
                ->where('user_id', $user->id)
                ->where('is_successful', true)
                ->whereNull('logged_out_at')
                ->latest('logged_in_at')
                ->first()
                ?->update(['logged_out_at' => now()]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
