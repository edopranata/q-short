<?php

namespace App\Http\Controllers;

use App\Models\ShortenedUrl;
use App\Models\UrlAnalytic;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jenssegers\Agent\Agent;

class UrlRedirectController extends Controller
{
    /**
     * Redirect to the original URL and track analytics.
     */
    public function redirect(Request $request, string $shortCode)
    {
        $shortenedUrl = ShortenedUrl::where('short_code', $shortCode)
            ->active()
            ->first();

        if (!$shortenedUrl) {
            abort(404, 'Short URL not found or expired');
        }

        // Track analytics
        $this->trackAnalytics($request, $shortenedUrl);

        // Increment click count
        $shortenedUrl->incrementClicks();

        // Redirect to original URL
        return redirect($shortenedUrl->original_url, 302);
    }

    /**
     * Track analytics for the URL click.
     */
    private function trackAnalytics(Request $request, ShortenedUrl $shortenedUrl): void
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        UrlAnalytic::create([
            'shortened_url_id' => $shortenedUrl->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'device_type' => $this->getDeviceType($agent),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
            'clicked_at' => now(),
        ]);
    }

    /**
     * Determine device type from user agent.
     */
    private function getDeviceType(Agent $agent): string
    {
        if ($agent->isMobile()) {
            return 'mobile';
        } elseif ($agent->isTablet()) {
            return 'tablet';
        } elseif ($agent->isDesktop()) {
            return 'desktop';
        }

        return 'unknown';
    }
}
