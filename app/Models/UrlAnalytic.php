<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UrlAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'shortened_url_id',
        'ip_address',
        'user_agent',
        'referer',
        'country',
        'city',
        'device_type',
        'browser',
        'platform',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the shortened URL that owns this analytic record.
     */
    public function shortenedUrl(): BelongsTo
    {
        return $this->belongsTo(ShortenedUrl::class);
    }

    /**
     * Scope for analytics within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('clicked_at', [$startDate, $endDate]);
    }

    /**
     * Scope for analytics by country.
     */
    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }
}
