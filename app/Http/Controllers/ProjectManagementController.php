<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UnitStatus;
use App\Models\Building;
use App\Models\Crm\CrmDeal;
use App\Models\Floor;
use App\Models\InstallmentPlan;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;
use App\Services\PushNotificationService;
use App\Support\Features;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectManagementController extends Controller
{
    public function index(): View
    {
        return view('dashboard.projects', [
            'projects' => Project::query()
                ->withCount([
                    'units',
                    'buildings',
                    'floors',
                    'units as available_units_count' => fn ($query) => $query->where('status', UnitStatus::Available->value),
                    'units as reserved_units_count' => fn ($query) => $query->where('status', UnitStatus::Reserved->value),
                    'units as sold_units_count' => fn ($query) => $query->where('status', UnitStatus::Sold->value),
                ])
                ->with(['buildings' => fn ($query) => $query->orderBy('sort_order')->withCount('floors')])
                ->latest()
                ->paginate(8),
            'stats' => [
                'projects' => Project::query()->count(),
                'units' => Unit::query()->count(),
                'available_units' => Unit::query()->where('status', UnitStatus::Available->value)->count(),
                'featured_units' => Unit::query()->where('featured', true)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('dashboard.projects.form', [
            'project' => null,
            'statuses' => ['draft' => __('Draft'), 'launching' => __('Launching'), 'active' => __('Active'), 'sold' => __('Sold Out')],
            'buildingsJson' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'alpha_dash', Rule::unique('projects', 'slug')],
            'code' => ['nullable', 'string', 'max:50'],
            'price_per_meter' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:8192'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:8192'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string'],
            'main_image' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'map_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'map_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['required', 'string', 'in:draft,launching,active,sold'],
            'featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
        ]);

        // Images are handled separately; never mass-assign them.
        unset($validated['cover_image'], $validated['images'], $validated['remove_images'], $validated['main_image']);

        $slugInput = trim((string) ($validated['slug'] ?? ''));
        $slug = $slugInput !== '' ? $this->uniqueSlug($slugInput) : $this->uniqueSlug($validated['name']);
        $code = ($validated['code'] ?? null) ?: $this->uniqueCode($validated['name']);

        $project = Project::query()->create([
            ...$validated,
            'slug' => $slug,
            'code' => $code,
            'published_at' => $validated['published_at'] ?? (in_array($validated['status'], ['active', 'launching'], true) ? now() : null),
        ]);

        $addedImages = $this->handleImages($request, $project, 'projects');
        $this->syncMainImage($request, $project, 'cover_image_path', 'cover_image', "projects/{$project->id}/cover", $addedImages);

        $this->syncBuildingsAndFloors($request, $project);

        // Ensure a default building and floor exist so units can be added immediately.
        $this->ensureDefaultBuildingAndFloor($project);

        return redirect()->route('dashboard.projects.index')->with('status', __('Project created successfully.'));
    }

    public function edit(Project $project): View
    {
        $project->load([
            'buildings' => fn ($q) => $q->orderBy('sort_order')->with([
                'floors' => fn ($fq) => $fq->orderByDesc('number')->with([
                    'units' => fn ($uq) => $uq->orderBy('sort_order')->orderBy('unit_number')
                ])
            ]),
            'units' => fn ($uq) => $uq->with(['building', 'floor'])->orderBy('sort_order')->orderBy('unit_number')
        ]);

        return view('dashboard.projects.form', [
            'project' => $project,
            'statuses' => ['draft' => __('Draft'), 'launching' => __('Launching'), 'active' => __('Active'), 'sold' => __('Sold Out')],
            'buildingsJson' => $project->buildings
                ->map(fn (Building $building) => [
                    'id' => $building->id,
                    'name' => $building->name,
                    'code' => $building->code,
                    'floors_count' => max(1, $building->floors->count()),
                ])
                ->values()
                ->all(),
            'units' => $project->units,
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:100', 'alpha_dash', Rule::unique('projects', 'slug')->ignore($project->id)],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('projects', 'code')->ignore($project->id)],
            'price_per_meter' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:8192'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:8192'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string'],
            'main_image' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'map_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'map_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['required', 'string', 'in:draft,launching,active,sold'],
            'featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'published_at' => ['nullable', 'date'],
        ]);

        // Images are handled separately; never mass-assign them.
        unset($validated['cover_image'], $validated['images'], $validated['remove_images'], $validated['main_image']);

        $slugInput = trim((string) ($validated['slug'] ?? ''));
        if ($slugInput !== '') {
            $validated['slug'] = $this->uniqueSlug($slugInput, $project->id);
        } elseif ($project->name !== $validated['name']) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $project->id);
        }

        $addedImages = $this->handleImages($request, $project, 'projects');
        $this->syncMainImage($request, $project, 'cover_image_path', 'cover_image', "projects/{$project->id}/cover", $addedImages);

        $project->update([
            ...$validated,
            'published_at' => $validated['published_at'] ?? $project->published_at,
        ]);

        $this->syncBuildingsAndFloors($request, $project);

        return redirect()->route('dashboard.projects.edit', $project)->with('status', __('Project updated successfully.'));
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        if ($this->hasProjectRelatedRecords($project)) {
            return back()->withErrors([
                'delete' => __('This project cannot be deleted because it has related units, offers, reservations, or deals.'),
            ]);
        }

        $project->delete();

        // Soft-delete its buildings together; they come back when the project is restored.
        Building::query()->where('project_id', $project->id)->update(['deleted_by' => auth()->id()]);
        Building::query()->where('project_id', $project->id)->delete();

        return redirect()->route('dashboard.projects.index')->with('status', __('Project deleted successfully.'));
    }

    public function createUnit(Project $project): View
    {
        return view('dashboard.projects.units.form', [
            'project' => $project,
            'unit' => null,
            'buildings' => $project->buildings()->with('floors')->orderBy('sort_order')->get(),
            'floors' => $project->floors()->with('building')->orderBy('building_id')->orderBy('number')->get(),
            'statuses' => collect(UnitStatus::cases())->mapWithKeys(fn ($status) => [$status->value => __($status->label())]),
            'unitTypes' => $this->unitTypes(),
            'availableFeatures' => Features::list(),
        ]);
    }

    public function storeUnit(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'building_id' => [
                'required',
                'integer',
                Rule::exists('buildings', 'id')->where(fn ($query) => $query->where('project_id', $project->id)),
            ],
            'floor_id' => [
                'required',
                'integer',
                Rule::exists('floors', 'id')->where(fn ($query) => $query
                    ->where('project_id', $project->id)
                    ->where('building_id', $request->input('building_id'))),
            ],
            'unit_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('units', 'unit_number')
                    ->where(fn ($query) => $query->where('floor_id', $request->input('floor_id'))->whereNull('deleted_at')),
            ],
            'unit_type' => ['nullable', 'string', 'max:50'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:8192'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string'],
            'main_image' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:8192'],
            'floor_plan' => ['nullable', 'file', 'max:20480'],
            'remove_floor_plan' => ['nullable', 'boolean'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:255'],
            'map_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'map_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'bedrooms' => ['required', 'integer', 'min:0'],
            'bathrooms' => ['required', 'integer', 'min:0'],
            'area' => ['required', 'numeric', 'min:0'],
            'garden_area' => ['numeric', 'min:0'],
            'roof_area' => ['numeric', 'min:0'],
            'balcony_area' => ['numeric', 'min:0'],
            'terrace_count' => ['integer', 'min:0'],
            'price_per_meter' => ['required', 'numeric', 'min:0'],
            'garden_price' => ['numeric', 'min:0'],
            'roof_price' => ['numeric', 'min:0'],
            'excellence_percent' => ['numeric', 'min:0', 'max:100'],
            'status' => ['required', Rule::enum(UnitStatus::class)],
            'featured' => ['boolean'],
            'hidden_from_website' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'delivery_date' => ['nullable', 'date'],
        ]);

        // Images are handled separately; never mass-assign them.
        unset($validated['images'], $validated['remove_images'], $validated['main_image'], $validated['thumbnail']);

        $floor = Floor::query()->findOrFail($validated['floor_id']);
        $building = $floor->building;

        $area = (float) $validated['area'];
        $pricePerMeter = (float) $validated['price_per_meter'];
        $gardenPrice = (float) ($validated['garden_price'] ?? 0);
        $roofPrice = (float) ($validated['roof_price'] ?? 0);
        $currentPrice = ($area * $pricePerMeter) + $gardenPrice + $roofPrice;

        $unit = Unit::query()->create([
            ...$validated,
            'project_id' => $project->id,
            'phase_id' => $building->phase_id,
            'building_id' => $building->id,
            'current_price' => $currentPrice,
            'floor_id' => $floor->id,
        ]);

        $addedImages = $this->handleImages($request, $unit, 'units');
        $this->syncMainImage($request, $unit, 'thumbnail', 'thumbnail', "units/{$unit->id}/thumbnails", $addedImages);
        $this->handleFloorPlan($request, $unit);

        return redirect()->route('dashboard.projects.units.edit', [$project, $unit])->with('status', __('Unit created successfully.'));
    }

    public function editUnit(Project $project, Unit $unit): View
    {
        return view('dashboard.projects.units.form', [
            'project' => $project,
            'unit' => $unit,
            'buildings' => $project->buildings()->with('floors')->orderBy('sort_order')->get(),
            'floors' => $project->floors()->with('building')->orderBy('building_id')->orderBy('number')->get(),
            'statuses' => collect(UnitStatus::cases())->mapWithKeys(fn ($status) => [$status->value => __($status->label())]),
            'unitTypes' => $this->unitTypes(),
            'availableFeatures' => Features::list(),
        ]);
    }

    public function updateUnit(Request $request, Project $project, Unit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'building_id' => [
                'required',
                'integer',
                Rule::exists('buildings', 'id')->where(fn ($query) => $query->where('project_id', $project->id)),
            ],
            'floor_id' => [
                'required',
                'integer',
                Rule::exists('floors', 'id')->where(fn ($query) => $query
                    ->where('project_id', $project->id)
                    ->where('building_id', $request->input('building_id'))),
            ],
            'unit_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('units', 'unit_number')
                    ->where(fn ($query) => $query->where('floor_id', $request->input('floor_id'))->whereNull('deleted_at'))
                    ->ignore($unit->id),
            ],
            'unit_type' => ['nullable', 'string', 'max:50'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,gif', 'max:8192'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string'],
            'main_image' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:8192'],
            'floor_plan' => ['nullable', 'file', 'max:20480'],
            'remove_floor_plan' => ['nullable', 'boolean'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:255'],
            'map_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'map_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'bedrooms' => ['required', 'integer', 'min:0'],
            'bathrooms' => ['required', 'integer', 'min:0'],
            'area' => ['required', 'numeric', 'min:0'],
            'garden_area' => ['numeric', 'min:0'],
            'roof_area' => ['numeric', 'min:0'],
            'balcony_area' => ['numeric', 'min:0'],
            'terrace_count' => ['integer', 'min:0'],
            'price_per_meter' => ['required', 'numeric', 'min:0'],
            'garden_price' => ['numeric', 'min:0'],
            'roof_price' => ['numeric', 'min:0'],
            'excellence_percent' => ['numeric', 'min:0', 'max:100'],
            'status' => ['required', Rule::enum(UnitStatus::class)],
            'featured' => ['boolean'],
            'hidden_from_website' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'delivery_date' => ['nullable', 'date'],
        ]);

        // Images are handled separately; never mass-assign them.
        unset($validated['images'], $validated['remove_images'], $validated['main_image'], $validated['thumbnail']);

        $floor = Floor::query()->findOrFail($validated['floor_id']);
        $building = $floor->building;

        // The submitted building must match the building the floor belongs to.
        abort_unless($building !== null && (int) $building->id === (int) $validated['building_id'], 422);

        $area = (float) $validated['area'];
        $pricePerMeter = (float) $validated['price_per_meter'];
        $gardenPrice = (float) ($validated['garden_price'] ?? 0);
        $roofPrice = (float) ($validated['roof_price'] ?? 0);
        $currentPrice = ($area * $pricePerMeter) + $gardenPrice + $roofPrice;

        $addedImages = $this->handleImages($request, $unit, 'units');
        $this->syncMainImage($request, $unit, 'thumbnail', 'thumbnail', "units/{$unit->id}/thumbnails", $addedImages);
        $this->handleFloorPlan($request, $unit);

        $oldStatus = $unit->status->value;

        $unit->update([
            ...$validated,
            'project_id' => $project->id,
            'phase_id' => $building->phase_id,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'current_price' => $currentPrice,
        ]);

        // Push notification: unit status changed to reserved/sold
        $newStatus = $unit->status->value;
        if ($oldStatus !== $newStatus && in_array($newStatus, ['reserved', 'sold'])) {
            $statusLabel = $newStatus === 'reserved' ? 'تم الحجز' : 'تم البيع';
            app(PushNotificationService::class)->notifyCrmEvent(
                '🏢 '.$statusLabel,
                $project->name.' — '.$unit->unit_number.' ('.$unit->status->label().')',
                '/real-statement-control/projects/'.$project->id.'/edit'
            );
        }

        return redirect()->route('dashboard.projects.units.edit', [$project, $unit])->with('status', __('Unit updated successfully.'));
    }

    public function destroyUnit(Project $project, Unit $unit): RedirectResponse
    {
        $this->authorize('delete', $unit);

        if ($this->hasUnitRelatedRecords($unit)) {
            return back()->withErrors([
                'delete' => __('This unit cannot be deleted because it has related offers, reservations, deals, or installment plans.'),
            ]);
        }

        $unit->delete();

        return redirect()->route('dashboard.projects.index')->with('status', __('Unit deleted successfully.'));
    }

    /**
     * Soft-deleted projects, units and buildings, with restore / permanent delete.
     */
    public function trash(): View
    {
        return view('dashboard.trash.index', [
            'trashedProjects' => Project::onlyTrashed()
                ->withCount([
                    'units' => fn ($query) => $query->withTrashed(),
                    'buildings' => fn ($query) => $query->withTrashed(),
                ])
                ->orderByDesc('deleted_at')
                ->get(),
            'trashedUnits' => Unit::onlyTrashed()
                ->with(['project', 'building'])
                ->orderByDesc('deleted_at')
                ->get(),
            'trashedBuildings' => Building::onlyTrashed()
                ->with('project')
                ->orderByDesc('deleted_at')
                ->get(),
        ]);
    }

    public function restoreProject(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);
        $this->assertOwnTrashedRecord($project);

        $project->restore();

        // Bring the project's buildings back with it.
        Building::onlyTrashed()->where('project_id', $project->id)->restore();

        return back()->with('status', __('Project restored successfully.'));
    }

    public function forceDeleteProject(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);
        $this->assertOwnTrashedRecord($project);

        if ($this->hasProjectRelatedRecords($project)) {
            return back()->withErrors([
                'delete' => __('This project cannot be permanently deleted because it has related units, offers, reservations, or deals.'),
            ]);
        }

        $project->forceDelete();

        return back()->with('status', __('Project permanently deleted.'));
    }

    public function restoreUnit(Unit $unit): RedirectResponse
    {
        $this->authorize('delete', $unit);
        $this->assertOwnTrashedRecord($unit);

        $unit->restore();

        // Bring the unit's project and building back if they were trashed too.
        if ($unit->project_id && Project::onlyTrashed()->whereKey($unit->project_id)->exists()) {
            Project::withTrashed()->find($unit->project_id)?->restore();
        }
        if ($unit->building_id && Building::onlyTrashed()->whereKey($unit->building_id)->exists()) {
            Building::withTrashed()->find($unit->building_id)?->restore();
        }

        return back()->with('status', __('Unit restored successfully.'));
    }

    public function forceDeleteUnit(Unit $unit): RedirectResponse
    {
        $this->authorize('delete', $unit);
        $this->assertOwnTrashedRecord($unit);

        if ($this->hasUnitRelatedRecords($unit)) {
            return back()->withErrors([
                'delete' => __('This unit cannot be permanently deleted because it has related offers, reservations, deals, or installment plans.'),
            ]);
        }

        $unit->forceDelete();

        return back()->with('status', __('Unit permanently deleted.'));
    }

    public function restoreBuilding(Building $building): RedirectResponse
    {
        $this->authorize('delete', $building);
        $this->assertOwnTrashedRecord($building);

        $building->restore();

        // Bring the building's project back if it was trashed too.
        if ($building->project_id && Project::onlyTrashed()->whereKey($building->project_id)->exists()) {
            Project::withTrashed()->find($building->project_id)?->restore();
        }

        return back()->with('status', __('Building restored successfully.'));
    }

    public function forceDeleteBuilding(Building $building): RedirectResponse
    {
        $this->authorize('delete', $building);
        $this->assertOwnTrashedRecord($building);

        if (Unit::withTrashed()->where('building_id', $building->id)->exists()) {
            return back()->withErrors([
                'delete' => __('This building cannot be permanently deleted because it contains units.'),
            ]);
        }

        $building->forceDelete();

        return back()->with('status', __('Building permanently deleted.'));
    }

    /**
     * Upload new images and remove marked ones, storing relative paths on the model.
     * Returns the list of newly stored image paths in order.
     */
    private function handleImages(Request $request, Project|Unit $model, string $folder): array
    {
        $existing = is_array($model->images) ? array_values($model->images) : [];
        // Keep only string image paths; discard any malformed entries.
        $existing = array_values(array_filter($existing, 'is_string'));
        $added = [];

        // Remove images the user checked for deletion.
        $remove = $request->input('remove_images', []);
        if (is_array($remove) && $remove !== []) {
            foreach ($existing as $index => $path) {
                if (in_array($path, $remove, true)) {
                    Storage::disk('public')->delete($path);
                    unset($existing[$index]);
                }
            }
            $existing = array_values($existing);
        }

        // Store newly uploaded images.
        $files = $request->file('images', []);
        if (is_array($files) && $files !== []) {
            foreach ($files as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }
                $path = $file->storePublicly("{$folder}/{$model->id}", 'public');
                $existing[] = $path;
                $added[] = $path;
            }
        }

        $model->forceFill(['images' => $existing === [] ? null : $existing])->save();

        return $added;
    }

    /**
     * Sync the model's main image column: a dedicated file upload takes precedence,
     * then a selected existing/new gallery image, then clear if a gallery main image
     * was removed.
     */
    private function syncMainImage(Request $request, Project|Unit $model, string $column, string $fileInput, string $folder, array $addedImagePaths): void
    {
        $file = $request->file($fileInput);

        if ($file instanceof UploadedFile) {
            $old = $model->{$column};
            if (is_string($old) && $old !== '' && str_starts_with($old, $folder.'/')) {
                Storage::disk('public')->delete($old);
            }

            $path = $file->storePublicly($folder, 'public');
            $model->forceFill([$column => $path])->save();

            return;
        }

        $selected = $request->input('main_image');

        if (is_string($selected) && $selected !== '') {
            // Existing image in the gallery.
            $images = is_array($model->images) ? array_values(array_filter($model->images, 'is_string')) : [];
            if (in_array($selected, $images, true)) {
                $model->forceFill([$column => $selected])->save();

                return;
            }

            // Newly uploaded image referenced by index.
            if (str_starts_with($selected, 'new:')) {
                $index = (int) substr($selected, 4);
                if (isset($addedImagePaths[$index])) {
                    $model->forceFill([$column => $addedImagePaths[$index]])->save();

                    return;
                }
            }
        }

        // If the current main image was a gallery image that has now been removed, clear it.
        $current = $model->{$column};
        $images = is_array($model->images) ? array_values(array_filter($model->images, 'is_string')) : [];
        $modelId = $model->getKey();
        $isGalleryImage = is_string($current) && $current !== '' && $modelId !== null
            && ! str_starts_with($current, $folder.'/')
            && str_contains($current, '/'.$modelId.'/');

        if ($isGalleryImage && ! in_array($current, $images, true)) {
            $model->forceFill([$column => null])->save();
        }
    }

    /**
     * Store, replace or remove the unit's floor plan file (image, PDF, DWG, DXF…).
     * The file lives in storage/app/public/units/{id}/floor-plans.
     */
    private function handleFloorPlan(Request $request, Unit $unit): void
    {
        $file = $request->file('floor_plan');

        if ($file instanceof UploadedFile) {
            $old = $unit->floor_plan_path;
            if (is_string($old) && $old !== '' && str_starts_with($old, 'units/'.$unit->id.'/floor-plans/')) {
                Storage::disk('public')->delete($old);
            }

            $path = $file->storePublicly("units/{$unit->id}/floor-plans", 'public');
            $unit->forceFill(['floor_plan_path' => $path])->save();

            return;
        }

        if ($request->boolean('remove_floor_plan')) {
            $old = $unit->floor_plan_path;
            if (is_string($old) && $old !== '') {
                Storage::disk('public')->delete($old);
            }
            $unit->forceFill(['floor_plan_path' => null])->save();
        }
    }

    /**
     * Common unit types offered by the site.
     */
    private function unitTypes(): array
    {
        return [
            'Apartment' => __('Apartment'),
            'Villa' => __('Villa'),
            'Penthouse' => __('Penthouse'),
            'Duplex' => __('Duplex'),
            'Studio' => __('Studio'),
            'Townhouse' => __('Townhouse'),
            'Twin House' => __('Twin House'),
            'Chalet' => __('Chalet'),
            'Office' => __('Office'),
            'Shop' => __('Shop'),
            'Land' => __('Land'),
            'Roof' => __('Roof'),
            'Other' => __('Other'),
        ];
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while (Project::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function uniqueCode(string $name): string
    {
        $base = strtoupper(Str::substr(preg_replace('/[^A-Za-z]/', '', $name) ?: 'PRJ', 0, 6)).'-'.now()->year;
        $code = $base;
        $i = 2;

        while (Project::query()->where('code', $code)->exists()) {
            $code = $base.'-'.$i++;
        }

        return $code;
    }

    private function hasProjectRelatedRecords(Project $project): bool
    {
        return $project->units()->exists()
            || Offer::query()->where('project_id', $project->id)->exists()
            || Reservation::query()->whereHas('unit', fn ($query) => $query->where('project_id', $project->id))->exists()
            || CrmDeal::query()->where('project_id', $project->id)->exists()
            || InstallmentPlan::query()->where('project_id', $project->id)->exists();
    }

    private function hasUnitRelatedRecords(Unit $unit): bool
    {
        return Offer::query()->where('unit_id', $unit->id)->exists()
            || Reservation::query()->where('unit_id', $unit->id)->exists()
            || CrmDeal::query()->where('unit_id', $unit->id)->exists()
            || InstallmentPlan::query()->where('unit_id', $unit->id)->exists();
    }

    /**
     * Sync the project's buildings and their floors from the form payload.
     * Buildings can have 1-10 floors; floor 0 is the ground floor.
     */
    private function syncBuildingsAndFloors(Request $request, Project $project): void
    {
        $submitted = $request->input('buildings', []);

        if (! is_array($submitted)) {
            return;
        }

        $keptBuildingIds = [];
        $order = 1;

        foreach ($submitted as $data) {
            if (! is_array($data)) {
                continue;
            }

            $name = trim((string) ($data['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $floorsCount = min(10, max(1, (int) ($data['floors_count'] ?? 1)));
            $buildingId = isset($data['id']) && is_numeric($data['id']) ? (int) $data['id'] : null;

            $building = $buildingId !== null
                ? Building::query()->where('project_id', $project->id)->find($buildingId)
                : null;

            if ($building === null) {
                $building = Building::query()->create([
                    'project_id' => $project->id,
                    'phase_id' => null,
                    'name' => $name,
                    'code' => 'BLD-'.str_pad((string) $order, 2, '0', STR_PAD_LEFT),
                    'status' => 'active',
                    'sort_order' => $order,
                ]);
            } else {
                $building->update([
                    'name' => $name,
                    'sort_order' => $order,
                ]);
            }

            $keptBuildingIds[] = $building->id;
            $this->syncFloors($building, $floorsCount);
            $order++;
        }

        // Remove buildings that were removed from the form (only when they have no units).
        $buildingsToDelete = Building::query()
            ->where('project_id', $project->id)
            ->whereNotIn('id', $keptBuildingIds)
            ->whereDoesntHave('floors.units');

        $buildingsToDelete->update(['deleted_by' => auth()->id()]);
        $buildingsToDelete->delete();
    }

    /**
     * Ensure the building has exactly floors 0..$count-1 (0 = ground).
     * Extra floors are only removed when they contain no units.
     */
    private function syncFloors(Building $building, int $count): void
    {
        $existing = $building->floors()->pluck('number')->all();

        for ($i = 0; $i < $count; $i++) {
            if (! in_array($i, $existing, true)) {
                Floor::query()->create([
                    'project_id' => $building->project_id,
                    'phase_id' => $building->phase_id,
                    'building_id' => $building->id,
                    'number' => $i,
                    'name' => $i === 0 ? __('Ground') : __('Floor :number', ['number' => $i]),
                    'sort_order' => $i,
                ]);
            }
        }

        Floor::query()
            ->where('building_id', $building->id)
            ->where('number', '>=', $count)
            ->whereDoesntHave('units')
            ->delete();
    }

    private function ensureDefaultBuildingAndFloor(Project $project): void
    {
        $building = $project->buildings()->first();

        if (! $building) {
            $building = Building::query()->create([
                'project_id' => $project->id,
                'phase_id' => null,
                'name' => __('Main Building'),
                'code' => 'MAIN',
                'status' => 'active',
                'sort_order' => 1,
            ]);
        }

        if (! $building->floors()->first()) {
            Floor::query()->create([
                'project_id' => $project->id,
                'phase_id' => null,
                'building_id' => $building->id,
                'number' => 0,
                'name' => __('Ground'),
                'sort_order' => 0,
            ]);
        }
    }

    /**
     * Ensure Data Entry users can only restore or permanently delete
     * project-related records that they themselves deleted.
     */
    private function assertOwnTrashedRecord(Model $model): void
    {
        $user = auth()->user();

        if ($user === null || $user->hasAnyRole(['Administrator', 'Sales Manager'])) {
            return;
        }

        if ($user->hasRole('Data Entry') && (int) $model->deleted_by !== (int) $user->id) {
            abort(403, __('You can only manage records that you deleted.'));
        }
    }
}
