<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\TaskRequest;
use App\Models\Crm\CrmDeal;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Task;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = Task::query()
            ->with(['taskable', 'assignee', 'creator'])
            ->when(! auth()->user()->hasAnyPermission(['view all tasks', 'manage crm']), function (Builder $q): void {
                $q->where(function (Builder $scope): void {
                    $scope->where('assigned_to', auth()->id())
                        ->orWhere('created_by', auth()->id());
                });
            })
            ->when($request->filled('search'), function (Builder $q, $search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->when($request->filled('status'), fn (Builder $q, $status) => $q->where('status', $status))
            ->when($request->filled('assigned'), fn (Builder $q, $assigned) => $q->where('assigned_to', $assigned))
            ->when($request->filled('related_type'), function (Builder $q) use ($request) {
                $type = match ($request->input('related_type')) {
                    'lead' => Lead::class,
                    'customer' => Customer::class,
                    'deal' => CrmDeal::class,
                    'project' => Project::class,
                    'unit' => Unit::class,
                    default => null,
                };

                if ($type) {
                    $q->where('taskable_type', $type);
                }
            });

        $tasks = $query->latest()->paginate(15)->withQueryString();

        return view('crm.tasks.index', [
            'tasks' => $tasks,
            'users' => User::active()->pluck('name', 'id'),
            'filters' => $request->only(['search', 'status', 'assigned', 'related_type']),
        ]);
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $taskable = match ($validated['related_type']) {
            'lead' => Lead::query()->find($validated['related_id']),
            'customer' => Customer::query()->find($validated['related_id']),
            'deal' => CrmDeal::query()->find($validated['related_id']),
            'project' => Project::query()->find($validated['related_id']),
            'unit' => Unit::query()->find($validated['related_id']),
        };

        abort_unless($taskable, 404);
        $this->authorize('view', $taskable);

        $validated['created_by'] = auth()->id();
        $validated['taskable_type'] = $taskable::class;
        $validated['taskable_id'] = $taskable->id;
        unset($validated['related_type'], $validated['related_id']);

        Task::query()->create($validated);

        return back()->with('status', __('Task created successfully.'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'status' => ['nullable', 'in:open,in_progress,completed,cancelled'],
            'due_at' => ['nullable', 'date'],
            'reminder' => ['nullable', 'boolean'],
        ]);

        if (isset($validated['status'])) {
            $validated['completed_at'] = $validated['status'] === 'completed' ? now() : null;
        }

        $task->update($validated);

        return back()->with('status', __('Task updated successfully.'));
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorizeTask($task);

        $task->delete();

        return back()->with('status', __('Task deleted successfully.'));
    }

    public function complete(Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json(['message' => __('Task marked as completed.'), 'task' => $task->fresh()]);
    }

    private function authorizeTask(Task $task): void
    {
        if (auth()->user()->hasAnyPermission(['manage crm', 'edit all tasks', 'delete tasks'])) {
            return;
        }

        abort_unless(
            $task->assigned_to === auth()->id() || $task->created_by === auth()->id(),
            403,
        );
    }
}
