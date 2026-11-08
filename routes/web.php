<?php

use App\Domain\Admin\Http\Controllers\PageController;
use App\Domain\Admin\Http\Controllers\PublicPageController;
use App\Domain\Admin\Http\Controllers\UserController;
use App\Domain\Auth\Http\Controllers\AuthController;
use App\Domain\Catalog\Http\Controllers\CategoryController;
use App\Domain\Catalog\Http\Controllers\CourseController;
use App\Domain\Catalog\Http\Controllers\CourseRequirementController;
use App\Domain\Catalog\Http\Controllers\LessonController;
use App\Domain\Catalog\Http\Controllers\MediaController;
use App\Domain\Catalog\Http\Controllers\ModuleController;
use App\Domain\Catalog\Http\Controllers\PublicCourseController;
use App\Domain\Catalog\Http\Controllers\QuizController;
use App\Domain\Catalog\Http\Controllers\UploadController;
use App\Domain\Catalog\Models\Course;
use App\Domain\Learning\Http\Controllers\CertificateController;
use App\Domain\Learning\Http\Controllers\DashboardController;
use App\Domain\Learning\Http\Controllers\EnrollmentController;
use App\Domain\Learning\Http\Controllers\PlayerController;
use App\Domain\Learning\Http\Controllers\ProfileController;
use App\Domain\Payment\Http\Controllers\OrderController;
use App\Domain\Payment\Http\Controllers\PaymentController;
use App\Domain\Payment\Http\Controllers\WebhookController;
use App\Domain\Settings\Http\Controllers\SettingController;
use App\Domain\Settings\Http\Controllers\SponsorController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');

    // ---- Halaman publik ----
    Route::get('/kursus', [PublicCourseController::class, 'index'])->name('catalog.index');
    Route::get('/kursus/{slug}', [PublicCourseController::class, 'show'])->name('catalog.show');
    Route::post('/kursus/{slug}/daftar', [EnrollmentController::class, 'store'])
        ->middleware('auth', 'throttle:enroll')->name('enroll.store');
    Route::get('/media/thumb/{path}', [MediaController::class, 'thumbnail'])
        ->name('media.thumbnail')->where('path', '.*');
    Route::get('/tentang', fn () => view('pages.tentang'))->name('tentang');
    Route::get('/jadwal', fn () => view('pages.jadwal'))->name('jadwal');
    Route::get('/panduan', fn () => view('pages.panduan'))->name('panduan');
    Route::get('/syarat', [PublicPageController::class, 'terms'])->name('syarat');
    Route::get('/privasi', [PublicPageController::class, 'privacy'])->name('privasi');
    Route::get('/verifikasi', [CertificateController::class, 'verify'])->name('verifikasi');
    Route::get('/verifikasi/{code}', [CertificateController::class, 'verify'])->name('certificates.verify');
});

Route::get('/en', function () {
    return redirect()->route('home-id');
})->name('home-en');

Route::get('/id', function () {
    return view('home');
})->name('home-id');

Route::get('/sitemap.xml', function () {
    $courses = Course::withoutGlobalScope('organization')
        ->where('status', 'published')
        ->orderBy('updated_at', 'desc')
        ->get(['slug', 'updated_at']);

    return response()
        ->view('seo.sitemap', compact('courses'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');
Route::get('/en', function () {
    app()->setLocale('en');
    session(['locale' => 'en']);

    return view('home');
})->name('home-en');

Route::get('/id', function () {
    app()->setLocale('id');
    session(['locale' => 'id']);

    return view('home');
})->name('home-id');

Route::get('/bayar/{order}', [PaymentController::class, 'show'])
    ->middleware('auth')->name('payments.show');
Route::post('/webhooks/midtrans', [WebhookController::class, 'handle'])
    ->name('webhooks.midtrans');

Route::get('/login', [AuthController::class, 'login'])
    ->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate'])
    ->name('login.post')->middleware('guest', 'throttle:login');
Route::get('/register', [AuthController::class, 'register'])
    ->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'store'])
    ->name('register.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// ---- Belajar (learner) ----
Route::get('/belajar', [DashboardController::class, 'index'])
    ->middleware('auth')->name('learning.dashboard');
Route::get('/belajar/pencapaian', [DashboardController::class, 'achievements'])
    ->middleware('auth')->name('learning.achievements');
Route::get('/belajar/profil', [ProfileController::class, 'edit'])
    ->middleware('auth')->name('learning.profile.edit');
Route::put('/belajar/profil', [ProfileController::class, 'update'])
    ->middleware('auth')->name('learning.profile.update');
Route::get('/belajar/{course}/{lesson}', [PlayerController::class, 'show'])
    ->name('player.show');
Route::post('/belajar/{course}/{lesson}/selesai', [PlayerController::class, 'complete'])
    ->middleware('auth')->name('player.complete');
Route::post('/belajar/{course}/{lesson}/kuis', [PlayerController::class, 'submitQuiz'])
    ->middleware('auth')->name('player.quiz.submit');
Route::get('/sertifikat/{code}', [CertificateController::class, 'show'])
    ->middleware('auth')->name('certificates.show');
Route::get('/sertifikat/{code}/pdf', [CertificateController::class, 'download'])
    ->middleware('auth')->name('certificates.pdf');

// ---- Admin (RBAC) ----
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('pages', [PageController::class, 'index'])->name('pages.index');
    Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::put('pages/{page}', [PageController::class, 'update'])->name('pages.update');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('courses', CourseController::class);
    Route::put('courses/{course}/requirement', [CourseRequirementController::class, 'update'])
        ->name('courses.requirement.update');
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
    Route::post('lessons/{lesson}/quiz/questions', [QuizController::class, 'storeQuestion'])
        ->name('lessons.quiz.questions.store');
    Route::delete('quiz-questions/{question}', [QuizController::class, 'destroyQuestion'])
        ->name('quiz-questions.destroy');
    Route::put('quiz-questions/{question}', [QuizController::class, 'updateQuestion'])
        ->name('quiz-questions.update');

    Route::get('payments', [OrderController::class, 'index'])->name('payments.index');
    Route::get('payments/{order}', [OrderController::class, 'show'])->name('payments.show');

    Route::resource('users', UserController::class)->except(['show']);

    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/test-email', [SettingController::class, 'sendTestEmail'])
        ->name('settings.test-email');
    Route::post('settings/test-midtrans', [SettingController::class, 'testMidtrans'])
        ->name('settings.test-midtrans');
    Route::post('settings/favicon', [SettingController::class, 'updateFavicon'])
        ->name('settings.update-favicon');

    Route::resource('sponsors', SponsorController::class)->except(['show']);

    Route::get('uploads/{path}', [UploadController::class, 'show'])
        ->middleware(['auth', 'signed', 'throttle:uploads'])
        ->name('uploads.show')
        ->where('path', '.*');
});
