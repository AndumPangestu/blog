<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\PostCategoryController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


//auth
Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'login']);

//password
Route::post('/forgot-password', [PasswordController::class, 'sendResetLinkEmail']);
Route::get('/reset-password/{token}', [PasswordController::class, 'getToken'])->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'resetPassword']);

//email verification
Route::post('/email/verification-notification', [EmailVerificationController::class])->middleware(['auth:sanctum', 'throttle:6,1']);
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');


//posts
Route::get('/posts', [PostController::class, 'getAll']);
Route::get('/posts/{id}', [PostController::class, 'get']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    //users
    Route::get("/users/current", [UserController::class, 'get']);
    Route::patch('/users/current', [UserController::class, 'update']);


    //posts
    Route::post('/posts', [PostController::class, 'create']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'delete']);
    Route::get('/users/{user_id}/posts', [PostController::class, 'getUserPosts']);


    Route::get('/categories', [PostCategoryController::class, 'getAll']);
    Route::get('/categories/{id}', [PostCategoryController::class, 'get']);

    Route::middleware(['role:admin'])->group(function () {
        //post categories
        Route::post('/categories', [PostCategoryController::class, 'create']);
        Route::put('/categories/{id}', [PostCategoryController::class, 'update']);
        Route::delete('/categories/{id}', [PostCategoryController::class, 'delete']);
    });


    //borkmark
    Route::post('/bookmarks', [BookmarkController::class, 'create']); // Membuat bookmark
    Route::get('/bookmarks', [BookmarkController::class, 'get']); // Mendapatkan semua bookmark
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'delete']); // Menghapus bookmark

    //logout
    Route::post('/auth/logout', [UserController::class, 'logout']);
});
