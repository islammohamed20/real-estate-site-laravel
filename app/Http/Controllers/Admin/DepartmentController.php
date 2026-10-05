<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        return view('users.departments.index', [
            'departments' => Department::query()->withCount('users')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Department::query()->create($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('status', __('Department created successfully.'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments', 'name')->ignore($department)],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $department->update($validated);
        $department->users()->update(['department' => $department->name]);

        return back()->with('status', __('Department updated successfully.'));
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->users()->exists()) {
            return back()->withErrors(['department' => __('Move users to another department before deleting this department.')]);
        }

        $department->delete();

        return back()->with('status', __('Department deleted successfully.'));
    }
}
