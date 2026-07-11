<?php

namespace App\Http\Controllers;

use App\Services\ContactMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct(
        private readonly ContactMessageService $messages,
    ) {}

    public function create(): View
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $this->messages->createFromContactForm($validated, auth()->id());

        return back()->with('success', 'تم إرسال رسالتك بنجاح، سنرد عليك قريباً.');
    }
}
