<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        return view('dashboard.banners.index', [
            'banners' => Banner::query()
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.banners.form', [
            'banner' => null,
            'effects' => ['fade' => __('Fade'), 'slide' => __('Slide'), 'zoom' => __('Zoom')],
            'positions' => ['hero' => __('Home hero slider')],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBanner($request);

        $banner = Banner::query()->create([
            ...$validated,
            'image_path' => $this->storeImage($request->file('image'), null),
        ]);

        return redirect()->route('dashboard.banners.index')
            ->with('status', __('Banner created successfully.'));
    }

    public function edit(Banner $banner): View
    {
        return view('dashboard.banners.form', [
            'banner' => $banner,
            'effects' => ['fade' => __('Fade'), 'slide' => __('Slide'), 'zoom' => __('Zoom')],
            'positions' => ['hero' => __('Home hero slider')],
        ]);
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $validated = $this->validateBanner($request);

        $banner->update([
            ...$validated,
            'image_path' => $this->storeImage($request->file('image'), $banner->image_path),
        ]);

        return redirect()->route('dashboard.banners.index')
            ->with('status', __('Banner updated successfully.'));
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return back()->with('status', __('Banner deleted successfully.'));
    }

    private function validateBanner(Request $request): array
    {
        return $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:8192'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'position' => ['required', 'string', 'in:hero'],
            'effect' => ['required', 'string', 'in:fade,slide,zoom'],
            'slide_duration' => ['required', 'integer', 'min:2', 'max:30'],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
    }

    private function storeImage(?UploadedFile $file, ?string $existingPath): ?string
    {
        if ($file !== null) {
            return $file->storePublicly('banners', 'public');
        }

        return $existingPath;
    }
}
