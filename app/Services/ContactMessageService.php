<?php

namespace App\Services;

use App\Models\ContactMessage;

/**
 * ContactMessageService — business logic for the admin contact messages
 * inbox. Never returns views/redirects.
 */
class ContactMessageService
{
    /**
     * Create a message from the public contact form.
     * $validated: name, email, phone, subject, message. $userId: auth id or null.
     */
    public function createFromContactForm(array $validated, ?int $userId): ContactMessage
    {
        return ContactMessage::create([
            'user_id' => $userId,
            'name'    => $validated['name'],
            'email'   => $validated['email'] ?? null,
            'phone'   => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? 'رسالة من صفحة اتصل بنا',
            'message' => $validated['message'],
            'is_read' => false,
        ]);
    }

    public function getMessages()
    {
        return ContactMessage::with('user')
            ->latest()
            ->paginate(20);
    }

    /**
     * Load relations for the detail view and mark unread messages as read.
     */
    public function prepareForShow(ContactMessage $message): ContactMessage
    {
        $message->load('user');

        if (! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        return $message;
    }

    public function markRead(ContactMessage $message): void
    {
        $message->update(['is_read' => true]);
    }

    public function delete(ContactMessage $message): void
    {
        $message->delete();
    }
}
