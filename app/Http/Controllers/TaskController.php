<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()
            ->tasks()
            ->orderByDesc('id')
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'task' => ['required','string','max:255'],
            'priority' => ['required','in:low,medium,high'],
            'due_date' => ['nullable','date'],
            'done' => ['boolean'],
        ]);
        $task = $request->user()->tasks()->create($data + ['done' => $data['done'] ?? false]);
        return response()->json($task, 201);
    }

    public function show(Request $request, Task $task)
    {
        $this->authorize('view', $task);
        return $task;
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $data = $request->validate([
            'task' => ['sometimes','string','max:255'],
            'priority' => ['sometimes','in:low,medium,high'],
            'due_date' => ['nullable','date'],
            'done' => ['sometimes','boolean'],
        ]);
        $task->update($data);
        return $task->fresh();
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return response()->noContent();
    }
}
