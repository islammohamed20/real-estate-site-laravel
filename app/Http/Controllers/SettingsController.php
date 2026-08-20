<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdateCompanyProfileRequest;
use App\Models\CompanyProfile;
use App\Support\NotificationRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        return view('settings.index', [
            'profile' => CompanyProfile::query()->firstOrCreate(['id' => 1], ['name' => config('app.name')]),
            'notificationTypes' => NotificationRegistry::types(),
            'notificationUser' => $user,
            // Computed here (not via a Blade @php block) because the Blade
            // compiler leaves multiline @php containing `?->` unprocessed.
            'userPrefs' => $user?->notification_preferences ?? [],
            'allowedTypes' => $user?->allowedNotificationTypes() ?? [],
        ]);
    }

    /**
     * Save the signed-in user's personal notification preferences (opt-outs).
     */
    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $request->validate([
            'disabled_notifications' => ['nullable', 'array'],
            'disabled_notifications.*' => ['string', 'in:'.implode(',', array_keys(NotificationRegistry::types()))],
        ]);

        $request->user()->forceFill([
            'notification_preferences' => $request->input('disabled_notifications', []),
        ])->save();

        return back()->with('status', __('Notification preferences saved successfully.'));
    }

    public function update(UpdateCompanyProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Checkboxes are not submitted when unchecked, so normalize explicitly.
        $data['auto_purge_enabled'] = $request->boolean('auto_purge_enabled');

        // Set default values for required fields if not provided or null
        if (! isset($data['maintenance_percent']) || $data['maintenance_percent'] === null) {
            $data['maintenance_percent'] = 0.00;
        }
        if (! isset($data['trash_retention_days']) || $data['trash_retention_days'] === null) {
            $data['trash_retention_days'] = 30;
        }

        $data['logo_path'] = $this->storeAsset($request->file('logo'), $data['logo_path'] ?? null, 'logos');
        $data['logo_light_path'] = $this->storeAsset($request->file('logo_light'), $data['logo_light_path'] ?? null, 'logos');
        $data['logo_dark_path'] = $this->storeAsset($request->file('logo_dark'), $data['logo_dark_path'] ?? null, 'logos');
        $data['stamp_path'] = $this->storeAsset($request->file('stamp'), $data['stamp_path'] ?? null, 'stamps');
        $data['favicon_path'] = $this->storeAsset($request->file('favicon'), $data['favicon_path'] ?? null, 'favicons');
        $data['seo_image_path'] = $this->storeAsset($request->file('seo_image'), $data['seo_image_path'] ?? null, 'seo');

        CompanyProfile::query()->updateOrCreate(['id' => 1], $data);

        return back()->with('status', __('Settings updated successfully.'));
    }

    private function storeAsset(?UploadedFile $file, ?string $existingPath, string $folder): ?string
    {
        if ($file !== null) {
            $path = $file->storePublicly("company-assets/{$folder}", 'public');

            return Storage::disk('public')->url($path);
        }

        return $existingPath;
    }
}
