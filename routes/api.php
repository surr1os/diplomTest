<?php

use App\Http\Controllers\CheckJwt;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/tasks', [TaskController::class, 'index']);
Route::get('/users', [UserController::class, 'getAllUsers']);
Route::post('/create-task', [TaskController::class, 'createTask']);
Route::post('/delete-task', [TaskController::class, 'deleteTask']);
Route::post('/new-list', [TaskController::class, 'createTaskList']);
Route::post('/delete-list', [TaskController::class, 'deleteTaskList']);
Route::patch('/update-task', [TaskController::class, 'updateTaskStatus']);
Route::put('/update-taskTitle',[TaskController::class, 'updateTaskTitle']);
Route::patch('/update-priority', [TaskController::class, 'updateGroupPriority']);
Route::patch('/update-executiondate', [TaskController::class, 'updateExecutionDate']);
Route::patch('/update-executor', [TaskController::class, 'updateExecutor']);
Route::patch('/update-grouptitle', [TaskController::class, 'updateGroupTitle']);

Route::post('/verify-token', [CheckJwt::class, 'verifyToken']);
