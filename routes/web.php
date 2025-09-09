<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortenedUrlController;
use App\Http\Controllers\UrlRedirectController;
use App\Models\ShortenedUrl;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $userId = Auth::id();
    $stats = [
        'total_urls' => ShortenedUrl::where('user_id', $userId)->count(),
        'total_clicks' => ShortenedUrl::where('user_id', $userId)->sum('click_count'),
        'today_clicks' => ShortenedUrl::where('user_id', $userId)
            ->whereHas('analytics', function($query) {
                $query->whereDate('clicked_at', today());
            })
            ->withCount(['analytics as today_clicks' => function($query) {
                $query->whereDate('clicked_at', today());
            }])
            ->get()
            ->sum('today_clicks'),
        'active_urls' => ShortenedUrl::where('user_id', $userId)
            ->where('is_active', true)
            ->count()
    ];
    
    return Inertia::render('Dashboard', [
        'stats' => $stats
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // URL Management Routes
    Route::resource('urls', ShortenedUrlController::class);
    
    // Analytics Routes
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/{shortenedUrl}', [AnalyticsController::class, 'show'])->name('analytics.show');
    
    // API Routes for AJAX requests
    Route::get('/api/stats', function () {
        $userId = Auth::id();
        return response()->json([
            'total_urls' => ShortenedUrl::where('user_id', $userId)->count(),
            'total_clicks' => ShortenedUrl::where('user_id', $userId)->sum('click_count'),
            'today_clicks' => ShortenedUrl::where('user_id', $userId)
                ->whereHas('analytics', function($query) {
                    $query->whereDate('clicked_at', today());
                })
                ->withCount(['analytics as today_clicks' => function($query) {
                    $query->whereDate('clicked_at', today());
                }])
                ->get()
                ->sum('today_clicks'),
            'active_urls' => ShortenedUrl::where('user_id', $userId)
                ->where('is_active', true)
                ->count()
        ]);
    })->name('api.stats');
});

// Public redirect route (no auth required)
Route::get('/s/{shortCode}', [UrlRedirectController::class, 'redirect'])->name('url.redirect');

require __DIR__.'/auth.php';
