<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Mailer\Transport\RoundRobinTransport;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('profile')->group(function () {
        Route::put('', [ProfileController::class, 'update']);
        Route::post('', [ProfileController::class, 'store']);
        Route::get('', [ProfileController::class, 'show']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/{id}/profile', [UserController::class, 'getProfile']);
        Route::get('', [UserController::class, 'GetUser']);
        Route::get('/{id}/tasks', [UserController::class, 'getUserTasks']);
    });

    Route::apiResource('tasks', TaskController::class);

    Route::prefix('task')->group(function () {
        Route::get('/all', [TaskController::class, 'getAllTasks'])->middleware('CheckUser');
        Route::get('/{id}/user', [TaskController::class, 'getTaskUser']);
        Route::get('/ordered', [TaskController::class, 'getTasksByPriority']);
        Route::post('/{taskId}/categories', [TaskController::class, 'addCategoriesToTask']);
        Route::get('/{taskId}/categories', [TaskController::class, 'getTaskCategories']);

        Route::post('/{id}/favorite', [TaskController::class, 'addToFavorites']);
        Route::delete('/{id}/favorite', [TaskController::class, 'removeFromFavorites']);
        Route::get('/favorites', [TaskController::class, 'getFavoriteTasks']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/{categoryId}/tasks', [TaskController::class, 'getCatorgiesTasks']);
    });
});
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
