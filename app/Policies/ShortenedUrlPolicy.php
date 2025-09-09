<?php

namespace App\Policies;

use App\Models\ShortenedUrl;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShortenedUrlPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('urls.index');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ShortenedUrl $shortenedUrl): bool
    {
        return $user->can('urls.show') && 
               ($user->id === $shortenedUrl->user_id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('urls.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ShortenedUrl $shortenedUrl): bool
    {
        return $user->can('urls.update') && 
               ($user->id === $shortenedUrl->user_id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ShortenedUrl $shortenedUrl): bool
    {
        return $user->can('urls.destroy') && 
               ($user->id === $shortenedUrl->user_id || $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ShortenedUrl $shortenedUrl): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ShortenedUrl $shortenedUrl): bool
    {
        return $user->hasRole('admin');
    }
}
