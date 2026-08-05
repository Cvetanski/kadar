<?php

namespace App\Http\Controllers;

use App\Mail\MeetingRequestMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MeetingRequestController extends Controller
{
    private const FOUNDER_EMAIL = 'cvetanskifootage@gmail.com';

    public function store(Request $request): JsonResponse
    {
        // Honeypot: real visitors never fill this hidden field.
        if ($request->filled('website')) {
            return response()->json(['message' => __('Испратено.')]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        Mail::to(self::FOUNDER_EMAIL)->send(new MeetingRequestMail(
            requesterName: $validated['name'],
            requesterEmail: $validated['email'],
            meetingDate: $validated['date'],
            meetingTime: $validated['time'],
            note: $validated['note'] ?? null,
        ));

        return response()->json(['message' => __('Барањето е испратено. Ќе добиеш потврда на email наскоро.')]);
    }
}
