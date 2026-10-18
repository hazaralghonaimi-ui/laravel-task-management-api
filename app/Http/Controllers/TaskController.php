<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Auth::user()->tasks;
        return response()->json($tasks, 200);
    }
    public function getTasksByPriority()
    {
        $tasks = Auth::user()->tasks()->orderByRaw("FIELD(priority,'high','medium','low')")->get();
        return response()->json($tasks, 200);
    }

    public function store(StoreTaskRequest $request)
    {
        $user_id = Auth::user()->id;
        $validatedData = $request->validated();
        $validatedData['user_id'] = $user_id;
        $task = Task::create($validatedData);
        return response()->json($task, 201);
    }

    public function update(UpdateTaskRequest $request, int $id)
    {
        $user_id = Auth::user()->id;
        $task = Task::findOrFail($id);
        if ($task->user_id != $user_id)
            return response()->json(['message' => 'Unauthurized'], 200);
        $task->update($request->validated());
        return response()->json($task, 200);
    }

    public function show(int $id)
    {
        $user_id = Auth::user()->id;
        $task = Task::find($id);
        if ($task->user_id != $user_id)
            return response()->json(['message' => 'Unauthurized'], 200);
        return response()->json($task, 200);
    }

    public function destroy(int $id)
    {
        try {

            $user_id = Auth::user()->id;
            $task = Task::findOrFail($id);
            if ($task->user_id != $user_id)
                return response()->json(['message' => 'Unauthurized'], 200);
            $task->delete();
            return response()->json(['message' => 'Task deleted successfully'], 200);
        } catch (ModelNotFoundException $m) {
            return response()->json([
                'error' => 'Task Not Found',
                'details' => $m->getMessage()
            ], 404);
        } catch (Exception $m) {
            return response()->json([
                'error' => 'something went wrong while deleting the task',
                'details' => $m->getMessage()
            ], 404);
        }
    }
    public function getTaskUser(int $id)
    {
        $user = Task::findOrFail($id)->user;
        return response()->json($user, 200);
    }
    public function addCategoriesToTask(Request $request, int $taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->categories()->attach($request->category_id);
        return response()->json('Category attached successfully', 200);
    }
    public function getTaskCategories(int $taskId)
    {
        $categories = Task::findOrFail($taskId)->categories;
        return response()->json($categories, 200);
    }
    public function getCatorgiesTasks(int $categoryId)
    {
        $tasks = Category::findOrFail($categoryId)->tasks;
        return response()->json($tasks, 200);
    }
    public function addTasksToCategory(Request $request, int $categoryId)
    {
        $categories = Category::findOrFail($categoryId);
        $categories->tasks()->attach($request->task_id);
        return response()->json('Task attached successfully', 200);
    }
    public function getAllTasks()
    {
        $tasks = Task::all();
        return response()->json($tasks, 200);
    }
    public function addToFavorites(int $taskId)
    {
        Task::findOrFail($taskId);
        Auth::user()->favoriteTasks()->syncWithoutDetaching($taskId);
        return response()->json(['message' => 'Task added to favorites'], 200);
    }
    public function removeFromFavorites(int $taskId)
    {
        Auth::user()->favoriteTasks()->detach($taskId);
        return response()->json(['message' => 'Task removed from favorites'], 200);
    }
    public function getFavoriteTasks()
    {
        $user_id = Auth::user()->id;
        $favoriteTasks = User::findOrFail($user_id)->favoriteTasks()->orderByRaw("FIELD(priority,'high','medium','low')")->get();
        return response()->json($favoriteTasks, 200);
    }
}
