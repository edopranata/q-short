<?php

namespace Database\Seeders;

use App\Models\ShortenedUrl;
use App\Models\UrlAnalytic;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShortenedUrlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run RolePermissionSeeder first.');
            return;
        }

        // Sample URLs to shorten
        $sampleUrls = [
            [
                'original_url' => 'https://laravel.com/docs/11.x/installation',
                'title' => 'Laravel Installation Guide',
                'description' => 'Complete guide to installing Laravel framework'
            ],
            [
                'original_url' => 'https://vuejs.org/guide/introduction.html',
                'title' => 'Vue.js Introduction',
                'description' => 'Getting started with Vue.js framework'
            ],
            [
                'original_url' => 'https://tailwindcss.com/docs/installation',
                'title' => 'Tailwind CSS Setup',
                'description' => 'How to install and configure Tailwind CSS'
            ],
            [
                'original_url' => 'https://github.com/spatie/laravel-permission',
                'title' => 'Laravel Permission Package',
                'description' => 'Role and permission management for Laravel'
            ],
            [
                'original_url' => 'https://inertiajs.com/server-side-setup',
                'title' => 'Inertia.js Server Setup',
                'description' => 'Setting up Inertia.js on the server side'
            ],
            [
                'original_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'title' => 'Never Gonna Give You Up',
                'description' => 'Classic Rick Astley music video'
            ],
            [
                'original_url' => 'https://docs.docker.com/get-started/',
                'title' => 'Docker Getting Started',
                'description' => 'Learn Docker basics and containerization'
            ],
            [
                'original_url' => 'https://www.php.net/manual/en/getting-started.php',
                'title' => 'PHP Getting Started',
                'description' => 'Official PHP documentation for beginners'
            ],
            [
                'original_url' => 'https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide',
                'title' => 'JavaScript Guide',
                'description' => 'Comprehensive JavaScript programming guide'
            ],
            [
                'original_url' => 'https://www.mysql.com/products/community/',
                'title' => 'MySQL Community Edition',
                'description' => 'Free and open-source database management system'
            ]
        ];

        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera'];
        $platforms = ['Windows', 'macOS', 'Linux', 'iOS', 'Android'];
        $devices = ['desktop', 'mobile', 'tablet'];
        $countries = ['US', 'GB', 'CA', 'AU', 'DE', 'FR', 'JP', 'CN', 'IN', 'BR'];
        $referrers = [
            'https://google.com',
            'https://twitter.com',
            'https://facebook.com',
            'https://linkedin.com',
            'https://reddit.com',
            null // Direct traffic
        ];

        foreach ($users as $user) {
            // Create 3-8 URLs per user
            $urlCount = rand(3, 8);
            $userUrls = collect($sampleUrls)->shuffle()->take($urlCount);
            
            foreach ($userUrls as $urlData) {
                $shortenedUrl = ShortenedUrl::create([
                    'user_id' => $user->id,
                    'original_url' => $urlData['original_url'],
                    'short_code' => $this->generateUniqueShortCode(),
                    'title' => $urlData['title'],
                    'description' => $urlData['description'],
                    'click_count' => 0,
                    'is_active' => rand(1, 10) > 1, // 90% active
                    'expires_at' => rand(1, 5) === 1 ? now()->addDays(rand(30, 365)) : null, // 20% have expiration
                    'created_at' => now()->subDays(rand(1, 90)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);

                // Generate analytics data for each URL
                $clickCount = rand(0, 500);
                $shortenedUrl->update(['click_count' => $clickCount]);

                // Create individual analytics records
                for ($i = 0; $i < $clickCount; $i++) {
                    $clickedAt = now()->subDays(rand(0, 30))
                        ->subHours(rand(0, 23))
                        ->subMinutes(rand(0, 59));

                    UrlAnalytic::create([
                        'shortened_url_id' => $shortenedUrl->id,
                        'ip_address' => $this->generateRandomIP(),
                        'user_agent' => $this->generateUserAgent(),
                        'referer' => $referrers[array_rand($referrers)],
                        'country' => $countries[array_rand($countries)],
                        'city' => $this->generateRandomCity(),
                        'device_type' => $devices[array_rand($devices)],
                        'browser' => $browsers[array_rand($browsers)],
                        'platform' => $platforms[array_rand($platforms)],
                        'clicked_at' => $clickedAt,
                        'created_at' => $clickedAt,
                        'updated_at' => $clickedAt,
                    ]);
                }
            }
        }

        $this->command->info('Created shortened URLs with analytics data for all users.');
    }

    /**
     * Generate a unique short code.
     */
    private function generateUniqueShortCode(): string
    {
        do {
            $shortCode = Str::random(6);
        } while (ShortenedUrl::where('short_code', $shortCode)->exists());

        return $shortCode;
    }

    /**
     * Generate a random IP address.
     */
    private function generateRandomIP(): string
    {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 255);
    }

    /**
     * Generate a realistic user agent string.
     */
    private function generateUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36'
        ];

        return $userAgents[array_rand($userAgents)];
    }

    /**
     * Generate a random city name.
     */
    private function generateRandomCity(): string
    {
        $cities = [
            'New York', 'London', 'Tokyo', 'Paris', 'Sydney', 'Toronto', 'Berlin',
            'Mumbai', 'São Paulo', 'Moscow', 'Dubai', 'Singapore', 'Los Angeles',
            'Chicago', 'Amsterdam', 'Barcelona', 'Rome', 'Seoul', 'Bangkok', 'Cairo'
        ];

        return $cities[array_rand($cities)];
    }
}
