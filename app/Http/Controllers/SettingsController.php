<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdateCompanyProfileRequest;
use App\Models\CompanyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings.index', [
            'profile' => CompanyProfile::query()->firstOrCreate(['id' => 1], ['name' => config('app.name')]),
        ]);
    }

    public function update(UpdateCompanyProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Checkboxes are not submitted when unchecked, so normalize explicitly.
        $data['auto_purge_enabled'] = $request->boolean('auto_purge_enabled');

        $data['logo_path'] = $this->storeAsset($request->file('logo'), $data['logo_path'] ?? null, 'logos');
        $data['logo_light_path'] = $this->storeAsset($request->file('logo_light'), $data['logo_light_path'] ?? null, 'logos');
        $data['logo_dark_path'] = $this->storeAsset($request->file('logo_dark'), $data['logo_dark_path'] ?? null, 'logos');
        $data['stamp_path'] = $this->storeAsset($request->file('stamp'), $data['stamp_path'] ?? null, 'stamps');
        $data['favicon_path'] = $this->storeAsset($request->file('favicon'), $data['favicon_path'] ?? null, 'favicons');

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
