<?php

namespace App\Http\Controllers;

use App\Models\ShortenedUrl;
use App\Models\UrlAnalytic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display analytics overview for user's URLs.
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();
        
        // Get user's URLs with analytics
        $urlsQuery = ShortenedUrl::where('user_id', $user->id)
            ->with(['analytics' => function ($query) {
                $query->where('clicked_at', '>=', now()->subDays(30));
            }]);

        // Apply filters
        if ($request->filled('search')) {
            $urlsQuery->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('original_url', 'like', '%' . $request->search . '%');
            });
        }

        $urls = $urlsQuery->paginate(10);

        // Get summary statistics
        $totalUrls = ShortenedUrl::where('user_id', $user->id)->count();
        $totalClicks = ShortenedUrl::where('user_id', $user->id)->sum('click_count');
        $clicksToday = UrlAnalytic::whereHas('shortenedUrl', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereDate('clicked_at', today())->count();
        
        $clicksThisMonth = UrlAnalytic::whereHas('shortenedUrl', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereMonth('clicked_at', now()->month)->count();

        // Get daily clicks for the last 30 days
        $dailyClicks = UrlAnalytic::whereHas('shortenedUrl', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('clicked_at', '>=', now()->subDays(30))
        ->selectRaw('DATE(clicked_at) as date, COUNT(*) as clicks')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Get top performing URLs
        $topUrls = ShortenedUrl::where('user_id', $user->id)
            ->orderBy('click_count', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Analytics/Index', [
            'urls' => $urls,
            'stats' => [
                'total_urls' => $totalUrls,
                'total_clicks' => $totalClicks,
                'clicks_today' => $clicksToday,
                'clicks_this_month' => $clicksThisMonth,
            ],
            'daily_clicks' => $dailyClicks,
            'top_urls' => $topUrls,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show detailed analytics for a specific URL.
     */
    public function show(ShortenedUrl $shortenedUrl): Response
    {
        $this->authorize('view', $shortenedUrl);

        // Get analytics data for the last 30 days
        $analytics = $shortenedUrl->analytics()
            ->where('clicked_at', '>=', now()->subDays(30))
            ->get();

        // Daily clicks
        $dailyClicks = $analytics
            ->groupBy(function ($item) {
                return $item->clicked_at->format('Y-m-d');
            })
            ->map(function ($group) {
                return $group->count();
            });

        // Browser statistics
        $browserStats = $analytics
            ->groupBy('browser')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc();

        // Platform statistics
        $platformStats = $analytics
            ->groupBy('platform')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc();

        // Device type statistics
        $deviceStats = $analytics
            ->groupBy('device_type')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc();

        // Country statistics
        $countryStats = $analytics
            ->whereNotNull('country')
            ->groupBy('country')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take(10);

        // Referrer statistics
        $referrerStats = $analytics
            ->whereNotNull('referer')
            ->groupBy('referer')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take(10);

        return Inertia::render('Analytics/Show', [
            'url' => $shortenedUrl,
            'analytics' => [
                'daily_clicks' => $dailyClicks,
                'browser_stats' => $browserStats,
                'platform_stats' => $platformStats,
                'device_stats' => $deviceStats,
                'country_stats' => $countryStats,
                'referrer_stats' => $referrerStats,
            ],
            'total_clicks' => $analytics->count(),
        ]);
    }
}
