<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShortenedUrl>
 */
class ShortenedUrlFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'original_url' => $this->faker->url(),
            'short_code' => Str::random(6),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'click_count' => 0,
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'expires_at' => $this->faker->optional(0.2)->dateTimeBetween('now', '+1 year'), // 20% chance of expiration
        ];
    }

    /**
     * Indicate that the URL is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the URL is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the URL is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }

    /**
     * Indicate that the URL has no expiration.
     */
    public function noExpiration(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => null,
        ]);
    }
}
