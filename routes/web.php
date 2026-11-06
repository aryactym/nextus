<?php

use App\Domain\Auth\Http\Controllers\AuthController;
use App\Domain\Catalog\Http\Controllers\CategoryController;
use App\Domain\Catalog\Http\Controllers\CourseController;
use App\Domain\Catalog\Http\Controllers\LessonController;
use App\Domain\Catalog\Http\Controllers\ModuleController;
use App\Domain\Catalog\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (request()->getPreferredLanguage() === 'en') {
        return view('home-en');
    }

    return view('home');
})->name('home');

// Placeholder verifikasi sertifikat (halaman penuh dikerjakan di Fase 4)
Route::get('/verifikasi', function () {
    return redirect()->route('home');
})->name('verifikasi');

Route::get('/en', function () {
    return view('home-en');
})->name('home-en');

Route::get('/id', function () {
    return view('home');
})->name('home-id');

// ---- Auth (sederhana, fase 1) ----
Route::get('/login', [AuthController::class, 'login'])
    ->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate'])
    ->name('login.post')->middleware('guest');
Route::get('/register', [AuthController::class, 'register'])
    ->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'store'])
    ->name('register.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// ---- Admin (RBAC) ----
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('courses', CourseController::class)->except(['show']);
    Route::post('courses/{course}/modules', [ModuleController::class, 'store'])
        ->name('courses.modules.store');
    Route::put('modules/{module}', [ModuleController::class, 'update'])
        ->name('modules.update');
    Route::delete('modules/{module}', [ModuleController::class, 'destroy'])
        ->name('modules.destroy');
    Route::post('modules/{module}/lessons', [LessonController::class, 'store'])
        ->name('modules.lessons.store');
    Route::put('lessons/{lesson}', [LessonController::class, 'update'])
        ->name('lessons.update');
    Route::delete('lessons/{lesson}', [LessonController::class, 'destroy'])
        ->name('lessons.destroy');

    Route::get('uploads/{path}', [UploadController::class, 'show'])
        ->name('uploads.show')
        ->where('path', '.*');
});
