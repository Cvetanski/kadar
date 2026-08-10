{{ __('Нова порака') }}

{{ $chatMessage->sender->name }}: {{ __('Ти испрати нова порака на CreatorSpot.') }}

"{{ \Illuminate\Support\Str::limit($chatMessage->body, 280) }}"

{{ __('Одговори →') }} {{ route('messages.show', $chatMessage->conversation) }}
