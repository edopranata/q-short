<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ShortenedUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
        'custom_slug',
        'is_custom',
        'title',
        'description',
        'click_count',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_custom' => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'short_url',
    ];

    /**
     * Get the user that owns the shortened URL.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the analytics for this URL.
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(UrlAnalytic::class);
    }

    /**
     * Generate a unique short code.
     */
    public static function generateShortCode(): string
    {
        do {
            $shortCode = Str::random(6);
        } while (self::where('short_code', $shortCode)->exists());

        return $shortCode;
    }

    /**
     * Get the full shortened URL.
     */
    public function getShortUrlAttribute(): string
    {
        $slug = $this->is_custom && $this->custom_slug ? $this->custom_slug : $this->short_code;
        return url('/s/' . $slug);
    }

    /**
     * Check if the URL is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Increment click count.
     */
    public function incrementClicks(): void
    {
        $this->increment('click_count');
    }

    /**
     * Validate custom slug format.
     */
    public static function validateCustomSlug(string $slug): bool
    {
        // Allow alphanumeric, hyphens, and underscores, 3-50 characters
        return preg_match('/^[a-zA-Z0-9_-]{3,50}$/', $slug);
    }

    /**
     * Check if custom slug is available.
     */
    public static function isCustomSlugAvailable(string $slug, ?int $excludeId = null): bool
    {
        $query = self::where('custom_slug', $slug)
                    ->orWhere('short_code', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }

    /**
     * Get blacklisted slugs.
     */
    public static function getBlacklistedSlugs(): array
    {
        return [
            'admin', 'api', 'www', 'mail', 'ftp', 'localhost', 'dashboard',
            'login', 'register', 'logout', 'profile', 'settings', 'help',
            'about', 'contact', 'terms', 'privacy', 'support', 'blog',
            'news', 'home', 'index', 'create', 'edit', 'delete', 'update',
            'show', 'list', 'search', 'analytics', 'stats', 'report'
        ];
    }

    /**
     * Check if slug is blacklisted.
     */
    public static function isSlugBlacklisted(string $slug): bool
    {
        return in_array(strtolower($slug), self::getBlacklistedSlugs());
    }

    /**
     * Scope for active URLs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }
}
