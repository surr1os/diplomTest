<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\task;
use Faker\Core\Uuid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function React\Promise\all;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user_id = $request->input('userId');
        $tasks = Task::where('userId', $user_id)->get();
        $groupedTasks = $tasks->groupBy('groupId');

        $response = [];
        foreach ($groupedTasks as $groupId => $tasks) {
            $groupTitle = $tasks->first()->groupTitle;
            $taskList = $tasks->map(function ($task) {
                return [
                    'title' => $task->title,
                    'taskId' => $task->taskId,
                    'completed' => $task->completed
                ];
            })->toArray(); // Преобразуем коллекцию в массив

            $response[] = [
                'groupTitle' => $groupTitle,
                'groupId' => $groupId,
                'tasks' => $taskList
            ];
        }

        return response()->json($response);
    }

    public function createTask(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'groupId' => 'required|string',
            'userId' => 'required|string',
            'groupTitle' => 'required|string'
        ]);

        $task = new Task();
        $task->taskId = Str::uuid(); // Генерация ID
        $task->title = $data['title'];
        $task->groupId = $data['groupId'];
        $task->groupTitle = $data['groupTitle'];
        $task->userId = $data['userId'];
        $task->completed = false;
        $task->created_at = now();
        $task->updated_at = now();

        $task->save();

        return response()->json($task, 201);
    }

    public function deleteTask(Request $request): JsonResponse
    {
        $taskId = $request->input('taskId');

        $task = Task::where('taskId', $taskId)->first();

        if (!$task) {
            return response()->json(['message' => 'Задача не найдена'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Задача успешно удалена'], 200);
    }

    public function updateTaskStatus(Request $request)
    {
        $taskId = $request->input('taskId');
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['message' => 'Задача не найдена'], 404);
        }

        $task->update([
            'completed' => !$task->completed
        ]);

        return response()->json(['message' => 'Статус задачи успешно обновлен'], 200);
    }

    public function updateTaskTitle(Request $request)
    {
        $taskId = $request->input('taskId');
        $taskTitle = $request->input('title');

        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['message' => 'Задача не найдена'], 404);
        }

        $task->update([
            'title' => $taskTitle
        ]);

        return response()->json(['message' => 'Заголовок задачи успешно обновлен'], 200);
    }
}
