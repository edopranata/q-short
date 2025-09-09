<?php

namespace App\Http\Controllers;

use App\Models\ShortenedUrl;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ShortenedUrlController extends Controller
{
    use AuthorizesRequests;
    
    // Middleware will be applied in routes

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $userId = Auth::id();
        
        $urls = ShortenedUrl::where('user_id', $userId)
            ->with('analytics')
            ->latest()
            ->paginate(10);

        // Calculate stats
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

        return Inertia::render('Urls/Index', [
            'urls' => $urls,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Urls/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'original_url' => 'required|url|max:2048',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $shortenedUrl = ShortenedUrl::create([
            'user_id' => Auth::id(),
            'original_url' => $validated['original_url'],
            'short_code' => ShortenedUrl::generateShortCode(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'expires_at' => $validated['expires_at'],
        ]);

        return redirect()->route('urls.index')
            ->with('success', 'URL shortened successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ShortenedUrl $url): Response
    {
        $this->authorize('view', $url);
        
        $analytics = $url->analytics()
            ->selectRaw('DATE(clicked_at) as date, COUNT(*) as clicks')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        return Inertia::render('Urls/Show', [
            'url' => $url->load('analytics'),
            'dailyAnalytics' => $analytics,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShortenedUrl $url): Response
    {
        $this->authorize('update', $url);
        
        return Inertia::render('Urls/Edit', [
            'url' => $url,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShortenedUrl $url)
    {
        $this->authorize('update', $url);
        
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $url->update($validated);

        return redirect()->route('urls.index')
            ->with('success', 'URL updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShortenedUrl $url)
    {
        $this->authorize('delete', $url);
        
        $url->delete(); 

        return redirect()->route('urls.index')
            ->with('success', 'URL deleted successfully!');
    }
}
