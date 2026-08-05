<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(Request $request): View
    {
        $user = $request->user();

        return view('contact.create', [
            'prefillName' => $user?->name,
            'prefillEmail' => $user?->email,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'category' => ['required', 'in:general,bug_report,user_report,business'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $contactMessage = ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'category' => $validated['category'],
            'message' => $validated['message'],
            'user_id' => $request->user()?->id,
        ]);

        Mail::to(config('mail.admin_email'))->send(new ContactMessageMail($contactMessage));

        return redirect()->route('contact.create')->with('status', __('Ви благодариме, ќе одговориме до 48 часа.'));
    }
}
