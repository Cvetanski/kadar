@props(['short' => false])

@php
    $currentOption = \App\Support\LocaleOptions::currentOptionKey(app()->getLocale(), request()->cookie('locale_option'));
@endphp

{{-- Plain <select> + vanilla onchange submit — several guest-facing pages (welcome,
     login, register, onboarding) are fully standalone and don't load Alpine/Tailwind
     via Vite, so this can't depend on either. --}}
<form method="POST" action="{{ route('locale.update') }}" style="display:inline-block;">
    @csrf
    <select name="option" onchange="this.form.submit()"
        style="font-family:inherit;font-size:16px;font-weight:600;color:#666B76;background:#fff;
            border:1px solid #E8EBF0;border-radius:8px;padding:7px 8px;cursor:pointer;color-scheme:light;">
        @foreach (\App\Support\LocaleOptions::orderedOptions($currentOption) as $key => $option)
            <option value="{{ $key }}" {{ $key === $currentOption ? 'selected' : '' }} style="background:#fff;color:#14171F;font-size:16px;">
                {{ $option['flag'] }} {{ $short ? strtoupper($option['locale']) : $option['label'] }}
            </option>
        @endforeach
    </select>
</form>
