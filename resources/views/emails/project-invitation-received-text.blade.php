{{ __('Здраво :name,', ['name' => $recipient->name]) }}

{{ __(':client те покани на проектот', ['client' => $invitation->client->name]) }} „{{ $invitation->project->title }}“ {{ __('на CreatorSpot.') }}

@if ($invitation->message)
„{{ $invitation->message }}“

@endif
{{ __('Погледни ја поканата на твојата контролна табла и одлучи дали сакаш да ја прифатиш.') }}

{{ __('Погледни ја поканата →') }} {{ route('dashboard') }}
