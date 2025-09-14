<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\QuestionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home Route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// Notification Routes
Route::prefix('notifications')->name('notifications.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::post('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/{notification}/unread', [\App\Http\Controllers\NotificationController::class, 'markAsUnread'])->name('mark-unread');
    Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    
    // API endpoints
    Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::get('/recent', [\App\Http\Controllers\NotificationController::class, 'recent'])->name('recent');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Job Management
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');
    Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
    Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{job}', [JobController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy');
    Route::post('/jobs/{job}/publish', [JobController::class, 'publish'])->name('jobs.publish');
    Route::post('/jobs/{job}/close', [JobController::class, 'close'])->name('jobs.close');
    
    // Question Management
    Route::get('/jobs/{job}/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/jobs/{job}/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::get('/jobs/{job}/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/jobs/{job}/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/jobs/{job}/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    Route::post('/jobs/{job}/questions/reorder', [QuestionController::class, 'reorder'])->name('questions.reorder');
});

// Recruiter Routes (will be added later)
Route::prefix('recruiter')->name('recruiter.')->middleware(['auth', 'recruiter'])->group(function () {
    // Recruiter routes will be added here
});

// Candidate Routes
Route::prefix('candidate')->name('candidate.')->middleware(['auth', 'candidate'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Candidate\DashboardController::class, 'index'])->name('dashboard');
    
    // Job Management
    Route::get('/jobs', [\App\Http\Controllers\Candidate\JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{job}', [\App\Http\Controllers\Candidate\JobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{job}/apply', [\App\Http\Controllers\Candidate\JobController::class, 'apply'])->name('jobs.apply');
    
    // Interview Management
    Route::get('/interview/{application}/start', [\App\Http\Controllers\Candidate\InterviewController::class, 'start'])->name('interview.start');
    Route::get('/interview/{application}/question/{question}', [\App\Http\Controllers\Candidate\InterviewController::class, 'question'])->name('interview.question');
    Route::post('/interview/{application}/response/{question}', [\App\Http\Controllers\Candidate\InterviewController::class, 'submitResponse'])->name('interview.submit');
    Route::get('/interview/{application}/next', [\App\Http\Controllers\Candidate\InterviewController::class, 'nextQuestion'])->name('interview.next');
    Route::get('/interview/{application}/completed', [\App\Http\Controllers\Candidate\InterviewController::class, 'completed'])->name('interview.completed');
});

// Video Routes (Secure video streaming)
Route::prefix('video')->name('video.')->middleware(['auth'])->group(function () {
    Route::get('/stream/{response}', [\App\Http\Controllers\VideoController::class, 'stream'])->name('stream');
    Route::get('/metadata/{response}', [\App\Http\Controllers\VideoController::class, 'metadata'])->name('metadata');
    Route::get('/download/{response}', [\App\Http\Controllers\VideoController::class, 'download'])->name('download');
    Route::get('/thumbnail/{response}', [\App\Http\Controllers\VideoController::class, 'thumbnail'])->name('thumbnail');
    Route::post('/batch-metadata', [\App\Http\Controllers\VideoController::class, 'batchMetadata'])->name('batch-metadata');
});

// Recruiter Routes
Route::prefix('recruiter')->name('recruiter.')->middleware(['auth', 'recruiter'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Recruiter\DashboardController::class, 'index'])->name('dashboard');
    
    // Job Management
    Route::get('/jobs', [\App\Http\Controllers\Recruiter\JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{job}', [\App\Http\Controllers\Recruiter\JobController::class, 'show'])->name('jobs.show');
    Route::get('/jobs/{job}/applications', [\App\Http\Controllers\Recruiter\JobController::class, 'applications'])->name('jobs.applications');
    Route::get('/jobs/{job}/analytics', [\App\Http\Controllers\Recruiter\JobController::class, 'analytics'])->name('jobs.analytics');
    
    // Application Management
    Route::get('/applications/{application}', [\App\Http\Controllers\Recruiter\ApplicationController::class, 'show'])->name('applications.show');
    Route::get('/applications/{application}/review', [\App\Http\Controllers\Recruiter\ApplicationController::class, 'review'])->name('applications.review');
    Route::post('/applications/{application}/review', [\App\Http\Controllers\Recruiter\ApplicationController::class, 'submitReview'])->name('applications.submit-review');
    Route::put('/applications/{application}/review/{review}', [\App\Http\Controllers\Recruiter\ApplicationController::class, 'updateReview'])->name('applications.update-review');
    Route::get('/applications/{application}/export', [\App\Http\Controllers\Recruiter\ApplicationController::class, 'export'])->name('applications.export');
    
    // Response Rating
    Route::post('/responses/{response}/rate', [\App\Http\Controllers\Recruiter\ResponseController::class, 'rate'])->name('responses.rate');
});
