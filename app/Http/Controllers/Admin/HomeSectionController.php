<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeSectionController extends Controller
{
    public function index(): View
    {
        return view('dashboard.home-sections.index', [
            'sections' => HomeSection::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function edit(HomeSection $section): View
    {
        return view('dashboard.home-sections.edit', ['section' => $section]);
    }

    public function update(Request $request, HomeSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->storePublicly('home-sections', 'public');
            $validated['image_path'] = $path;
        }

        $section->update($validated);

        return redirect()->route('dashboard.home-sections.index')
            ->with('status', __('Section updated successfully.'));
    }

    public function toggle(HomeSection $section): RedirectResponse
    {
        $section->update(['is_active' => ! $section->is_active]);

        return back()->with('status', $section->is_active
            ? __('Section enabled.')
            : __('Section disabled.'));
    }

    public function moveUp(HomeSection $section): RedirectResponse
    {
        $previous = HomeSection::query()
            ->where('sort_order', '<', $section->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($previous !== null) {
            $this->swapOrder($section, $previous);
        }

        return back()->with('status', __('Section moved up.'));
    }

    public function moveDown(HomeSection $section): RedirectResponse
    {
        $next = HomeSection::query()
            ->where('sort_order', '>', $section->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next !== null) {
            $this->swapOrder($section, $next);
        }

        return back()->with('status', __('Section moved down.'));
    }

    private function swapOrder(HomeSection $a, HomeSection $b): void
    {
        $tmp = $a->sort_order;
        $a->update(['sort_order' => $b->sort_order]);
        $b->update(['sort_order' => $tmp]);
    }
}
