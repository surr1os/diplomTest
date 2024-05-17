<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function createTask()
    {

    }
}
