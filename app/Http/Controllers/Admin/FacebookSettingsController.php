<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacebookSetting;
use App\Services\FacebookMessengerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacebookSettingsController extends Controller
{
    public function index(): View
    {
        $settings = FacebookSetting::query()->firstOrCreate(
            ['id' => 1],
            ['is_active' => false]
        );

        return view('dashboard.facebook-settings', [
            'settings' => $settings,
            'webhookUrl' => url('/webhook/facebook'),
            'verifyToken' => $settings->verify_token ?? 'venecia_fb_verify',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page_id' => ['required', 'string', 'max:50'],
            'access_token' => ['required', 'string', 'max:500'],
            'verify_token' => ['required', 'string', 'max:64'],
            'app_secret' => ['nullable', 'string', 'max:64'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        FacebookSetting::query()->updateOrCreate(
            ['id' => 1],
            $validated
        );

        return back()->with('status', __('Facebook Messenger settings updated successfully.'));
    }

    public function test(): \Illuminate\Http\JsonResponse
    {
        $settings = FacebookSetting::active();

        if (! $settings) {
            return response()->json(['success' => false, 'message' => __('Facebook Messenger is not configured.')]);
        }

        $service = app(FacebookMessengerService::class);

        return response()->json([
            'success' => true,
            'message' => __('Facebook Messenger is configured and active.'),
            'page_id' => $settings->page_id,
            'webhook_url' => url('/webhook/facebook'),
        ]);
    }
}
