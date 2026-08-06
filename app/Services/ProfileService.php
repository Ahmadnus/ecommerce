<?php

namespace App\Services;

use App\Models\User;

/**
 * ProfileService — business logic for the user profile page.
 * Session/auth handling stays in the controller.
 */
class ProfileService
{
    /**
     * The user's most recent rental bookings for the profile page.
     */
    public function getRecentBookings(User $user, int $limit = 5)
    {
        return $user->bookings()
            ->with('vehicle')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Update profile fields; password only when provided (model cast hashes it).
     */
    public function updateProfile(User $user, array $data): User
    {
        $user->name = $data['name'];
        $user->phone = $data['phone'];

        if (!empty($data['password'])) {
            $user->password = $data['password']; // Laravel 11+ سيقوم بعمل Hash تلقائياً بناءً على المودل
        }

        $user->save();

        return $user;
    }

    public function deleteAccount(User $user): void
    {
        $user->delete();
    }
}
