<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\task;
use Carbon\Carbon;
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
            $groupPriority = $tasks->first()->group_priority;
            $execution_date = $tasks->first()->execution_date;
            $executor = $tasks->first()->executor;
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
                'group_priority' => $groupPriority,
                'execution_date' => $execution_date,
                'executor' => $executor,
                'tasks' => $taskList
            ];
        }

        return response()->json($response);
    }

    public function createTask(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string',
            'groupId' => 'required|string',
            'userId' => 'required|string',
            'group_priority' => 'required|string',
            'execution_date' => 'required|string',
            'groupTitle' => 'required|string'
        ]);

        $task = new Task();
        $task->taskId = Str::uuid(); // Генерация ID
        $task->title = $data['title'];
        $task->groupId = $data['groupId'];
        $task->groupTitle = $data['groupTitle'];
        $task->userId = $data['userId'];
        $task->group_priority = $data['group_priority'];
        $task->completed = false;
        $task->created_at = now();
        $task->updated_at = now();
        $task->execution_date = $data['execution_date'];
        $task->executor = null;
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

    public function updateTaskStatus(Request $request): JsonResponse
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

    public function updateTaskTitle(Request $request): JsonResponse
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

    public function updateGroupPriority(Request $request): JsonResponse
    {
        $groupId = $request->input('groupId');
        $priority = $request->input('priority');
        $tasks = Task::where('groupId', $groupId)->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'Задачи не найдены для данного groupId'], 404);
        }

        foreach ($tasks as $task) {
            $task->update(['group_priority' => $priority]);
        }

        return response()->json(['message' => 'Приоритет задач успешно обновлен'], 200);
    }

    public function updateExecutionDate(Request $request): JsonResponse
    {
        $groupId = $request->input('groupId');
        $date = $request->input('date');
        $tasks = Task::where('groupId', $groupId)->get();
        $formattedDate = date('Y-m-d H:i:s', strtotime($date));
        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'Задачи не найдены для данного groupId'], 404);
        }

        foreach ($tasks as $task) {
            $task->update(['execution_date' => $formattedDate]);
        }

        return response()->json(['message' => 'Срок задач успешно обновлен'], 200);
    }

    public function createTaskList(Request $request): JsonResponse
    {
        $taskListData = $request->input('list');
        $newTasks = $taskListData['tasks'];
        $userId = $request->input('userId');
        $groupId = $taskListData['groupId'];
        $groupTitle = $taskListData['groupTitle'];
        $groupPriority = $taskListData['group_priority'];
        $executionDate = Carbon::parse($taskListData['execution_date'])->toDateTimeString();

        foreach ($newTasks as $newTask) {
            Task::create([
                'taskId' => $newTask['taskId'], // Генерируем новый uuid для каждой задачи
                'title' => $newTask['title'],
                'completed' => $newTask['completed'],
                'group_priority' => $groupPriority,
                'groupId' => $groupId,
                'groupTitle' => $groupTitle,
                'userId' => $userId,
                'execution_date' => $executionDate,
                'executor' => null
            ]);
        }

        return response()->json(['message' => 'Список задач успешно создан!'], 200);
    }

    public function deleteTaskList(Request $request): JsonResponse
    {
        $groupId = $request->input('groupId');
        Task::where('groupId', $groupId)->delete();
        return response()->json(['message' => 'Список задач успешно удален!'], 200);
    }

    public function updateExecutor(Request $request): JsonResponse
    {
        $groupId = $request->input('groupId');
        $executor = $request->input('executor');
        $tasks = Task::where('groupId', $groupId)->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'Задачи не найдены для данного groupId'], 404);
        }

        foreach ($tasks as $task) {
            $task->update(['executor' => $executor]);
        }
        return response()->json(['message' => 'Исполнитель успешно изменен!'], 200);
    }

    public function updateGroupTitle(Request $request) : JsonResponse
    {
        $groupId = $request->input('groupId');
        $groupTitle = $request->input('groupTitle');
        $tasks = Task::where('groupId', $groupId)->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'Задачи не найдены для данного groupId'], 404);
        }

        foreach ($tasks as $task) {
            $task->update(['groupTitle' => $groupTitle]);
        }
        return response()->json(['message' => 'Исполнитель успешно изменен!'], 200);
    }
}
