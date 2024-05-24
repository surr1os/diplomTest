<?php

use App\Http\Controllers\CheckJwt;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/create-task', [TaskController::class, 'createTask']);
Route::post('/delete-task', [TaskController::class, 'deleteTask']);
Route::patch('/update-task', [TaskController::class, 'updateTaskStatus']);
Route::put('/update-taskTitle',[TaskController::class, 'updateTaskTitle']);

Route::post('/verify-token', [CheckJwt::class, 'verifyToken']);
