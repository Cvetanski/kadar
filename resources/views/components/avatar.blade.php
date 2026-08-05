@props(['user', 'size' => 'w-8 h-8', 'textSize' => 'text-xs'])

@if ($user->avatar_url)
    <span {{ $attributes->merge(['class' => "$size $textSize rounded-full hidden items-center justify-center font-bold text-white flex-shrink-0"]) }}
        style="background: linear-gradient(135deg, {{ $user->avatar_gradient }})">
        {{ $user->initials }}
    </span>
    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
        {{ $attributes->merge(['class' => "$size rounded-full object-cover flex-shrink-0"]) }}
        onerror="this.style.display='none';this.previousElementSibling.classList.remove('hidden');this.previousElementSibling.classList.add('flex');">
@else
    <span {{ $attributes->merge(['class' => "$size $textSize rounded-full flex items-center justify-center font-bold text-white flex-shrink-0"]) }}
        style="background: linear-gradient(135deg, {{ $user->avatar_gradient }})">
        {{ $user->initials }}
    </span>
@endif
