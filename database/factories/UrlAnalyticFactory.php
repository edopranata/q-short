<?php

namespace Database\Factories;

use App\Models\ShortenedUrl;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UrlAnalytic>
 */
class UrlAnalyticFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera'];
        $platforms = ['Windows', 'macOS', 'Linux', 'iOS', 'Android'];
        $devices = ['desktop', 'mobile', 'tablet'];
        $countries = ['US', 'GB', 'CA', 'AU', 'DE', 'FR', 'JP', 'CN', 'IN', 'BR'];
        $cities = ['New York', 'London', 'Tokyo', 'Paris', 'Sydney', 'Toronto', 'Berlin'];
        $referrers = [
            'https://google.com',
            'https://twitter.com',
            'https://facebook.com',
            'https://linkedin.com',
            'https://reddit.com',
            null // Direct traffic
        ];

        $clickedAt = $this->faker->dateTimeBetween('-30 days', 'now');

        return [
            'shortened_url_id' => ShortenedUrl::factory(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'referer' => $this->faker->randomElement($referrers),
            'country' => $this->faker->randomElement($countries),
            'city' => $this->faker->randomElement($cities),
            'device_type' => $this->faker->randomElement($devices),
            'browser' => $this->faker->randomElement($browsers),
            'platform' => $this->faker->randomElement($platforms),
            'clicked_at' => $clickedAt,
            'created_at' => $clickedAt,
            'updated_at' => $clickedAt,
        ];
    }

    /**
     * Indicate that the analytic is from a mobile device.
     */
    public function mobile(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'mobile',
            'platform' => $this->faker->randomElement(['iOS', 'Android']),
        ]);
    }

    /**
     * Indicate that the analytic is from a desktop device.
     */
    public function desktop(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'desktop',
            'platform' => $this->faker->randomElement(['Windows', 'macOS', 'Linux']),
        ]);
    }

    /**
     * Indicate that the analytic has no referrer (direct traffic).
     */
    public function directTraffic(): static
    {
        return $this->state(fn (array $attributes) => [
            'referer' => null,
        ]);
    }

    /**
     * Indicate that the analytic is from a specific country.
     */
    public function fromCountry(string $country): static
    {
        return $this->state(fn (array $attributes) => [
            'country' => $country,
        ]);
    }
}
