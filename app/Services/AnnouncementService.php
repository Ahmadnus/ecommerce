<?php

namespace App\Services;

use App\Models\Announcement;

/**
 * AnnouncementService — business logic for the admin announcements CRUD.
 * Never returns views/redirects.
 */
class AnnouncementService
{
    public function getAnnouncements()
    {
        return Announcement::orderBy('sort_order')->get();
    }

    public function create(array $data): Announcement
    {
        return Announcement::create($data);
    }

    public function update(Announcement $announcement, array $data): Announcement
    {
        $announcement->update($data);

        return $announcement;
    }

    public function delete(Announcement $announcement): void
    {
        $announcement->delete();
    }
}
