<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()->with(['roles.permissions', 'permissions'])->latest()->paginate(20),
            'roleOptions' => Role::query()->orderBy('name')->pluck('name', 'name'),
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function create(): View
    {
        return view('users.form', [
            'user' => null,
            'roleOptions' => Role::query()->orderBy('name')->pluck('name', 'name'),
            'permissionGroups' => $this->permissionGroups(),
            'rolePermissions' => $this->rolePermissions(),
            'dashboardSectionOptions' => User::DASHBOARD_SECTIONS,
            'selectedDashboardSections' => array_keys(User::DASHBOARD_SECTIONS),
            'departmentOptions' => Department::query()->orderByDesc('is_active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
            'dashboard_sections' => ['nullable', 'array'],
            'dashboard_sections.*' => ['string', 'in:'.implode(',', array_keys(User::DASHBOARD_SECTIONS))],
        ]);

        $department = isset($validated['department_id'])
            ? Department::query()->find($validated['department_id'])
            : null;
        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department' => $department?->name ?? ($validated['department'] ?? null),
            'department_id' => $department?->id,
            'password' => $validated['password'],
            'is_active' => $validated['is_active'] ?? true,
            'dashboard_sections' => array_values(array_unique($validated['dashboard_sections'] ?? [])),
            'email_verified_at' => now(),
        ]);

        $user->syncRoles([$validated['role']]);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('dashboard.users.index')->with('status', __('User created successfully.'));
    }

    public function edit(User $user): View
    {
        return view('users.form', [
            'user' => $user->load(['roles', 'permissions']),
            'roleOptions' => Role::query()->orderBy('name')->pluck('name', 'name'),
            'permissionGroups' => $this->permissionGroups(),
            'rolePermissions' => $this->rolePermissions(),
            'dashboardSectionOptions' => User::DASHBOARD_SECTIONS,
            'selectedDashboardSections' => $user->dashboard_sections ?? array_keys(User::DASHBOARD_SECTIONS),
            'departmentOptions' => Department::query()->orderByDesc('is_active')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
            'dashboard_sections' => ['nullable', 'array'],
            'dashboard_sections.*' => ['string', 'in:'.implode(',', array_keys(User::DASHBOARD_SECTIONS))],
        ]);

        if ($this->isProtectedUser($user) && (
            strtolower($validated['email']) !== strtolower($user->email)
            || $validated['role'] !== 'Administrator'
            || ! ($validated['is_active'] ?? false)
        )) {
            return back()->withErrors([
                'user' => __('The main administrator account cannot have its email, role, or active status changed.'),
            ])->withInput();
        }

        $department = isset($validated['department_id'])
            ? Department::query()->find($validated['department_id'])
            : null;
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'department' => $department?->name ?? ($validated['department'] ?? null),
            'department_id' => $department?->id,
            'is_active' => $validated['is_active'] ?? false,
            'dashboard_sections' => $this->isProtectedUser($user)
                ? array_keys(User::DASHBOARD_SECTIONS)
                : array_values(array_unique($validated['dashboard_sections'] ?? [])),
        ];
        if (filled($validated['password'] ?? null)) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);
        if (! $this->isProtectedUser($user)) {
            $user->syncRoles([$validated['role']]);
            $user->syncPermissions($validated['permissions'] ?? []);
        }

        return redirect()->route('dashboard.users.index')->with('status', __('User updated successfully.'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        if ($this->isProtectedUser($user)) {
            return back()->withErrors([
                'role' => __('The role of the main administrator account cannot be changed.'),
            ]);
        }

        $user->syncRoles([$validated['role']]);

        return back()->with('status', __('Role updated successfully.'));
    }

    public function disable(User $user): RedirectResponse
    {
        $user->update([
            'is_active' => false,
            'force_logout_at' => now(),
        ]);

        return back()->with('status', __('User disabled.'));
    }

    public function enable(User $user): RedirectResponse
    {
        $user->update([
            'is_active' => true,
        ]);

        return back()->with('status', __('User enabled.'));
    }

    public function forceLogout(User $user): RedirectResponse
    {
        $user->update([
            'force_logout_at' => now(),
        ]);

        return back()->with('status', __('User will be forced to log in again on the next request.'));
    }

    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        if ($this->isProtectedUser($user)) {
            return back()->withErrors([
                'permissions' => __('The permissions of the main administrator account cannot be changed.'),
            ]);
        }

        $user->syncPermissions($validated['permissions'] ?? []);

        return back()->with('status', __('Permissions updated successfully.'));
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => $validated['password']]);

        return back()->with('status', __('Password reset for :name.', ['name' => $user->name]));
    }

    /**
     * Soft-delete a user account — it moves to the trash where it can be
     * restored or permanently deleted by an administrator.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($this->isProtectedUser($user)) {
            return back()->withErrors([
                'user' => __('The main administrator account cannot be deleted.'),
            ]);
        }

        if ($user->id === (int) auth()->id()) {
            return back()->withErrors([
                'user' => __('You cannot delete your own account.'),
            ]);
        }

        $user->delete();

        return back()->with('status', __('User moved to trash.'));
    }

    /**
     * The main administrator account is protected so it can never be locked
     * out or have its permissions/role degraded, not even by itself.
     */
    private function isProtectedUser(User $user): bool
    {
        return strtolower($user->email) === 'admin@venecia-dev.com';
    }

    /**
     * Group the available permissions by their module (the last word of the permission name),
     * e.g. "edit all leads" -> "Leads".
     */
    /**
     * Map each role to its granted permissions, used by the live sidebar
     * preview on the user creation form.
     *
     * @return array<string, list<string>>
     */
    private function rolePermissions(): array
    {
        return Role::query()->with('permissions')->get()
            ->mapWithKeys(fn (Role $role) => [$role->name => $role->permissions->pluck('name')->all()])
            ->all();
    }

    private function permissionGroups(): array
    {
        $permissions = Permission::query()->orderBy('name')->pluck('name')->all();

        $groups = [];
        foreach ($permissions as $permission) {
            $words = explode(' ', $permission);
            $module = ucwords(end($words) ?: $permission);
            $groups[$module][] = $permission;
        }

        ksort($groups);

        return $groups;
    }
}
