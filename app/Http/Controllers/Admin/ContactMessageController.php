<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\ContactMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function __construct(
        private readonly ContactMessageService $messages,
    ) {}

    public function index(): View
    {
        $messages = $this->messages->getMessages();

        return view('admin.contact-messages.index', compact('messages'));
    }

    public function show(ContactMessage $contactMessage): View
    {
        $this->messages->prepareForShow($contactMessage);

        return view('admin.contact-messages.show', compact('contactMessage'));
    }

    public function markRead(ContactMessage $contactMessage): RedirectResponse
    {
        $this->messages->markRead($contactMessage);

        return back()->with('success', 'تم تعليم الرسالة كمقروءة.');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $this->messages->delete($contactMessage);

        return back()->with('success', 'تم حذف الرسالة.');
    }
}
