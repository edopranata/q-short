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
        'title',
        'description',
        'click_count',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
        return url('/s/' . $this->short_code);
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
