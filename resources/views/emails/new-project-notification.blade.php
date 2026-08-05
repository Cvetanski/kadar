<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color:#14171F; line-height:1.6;">
    <h2 style="margin-bottom:4px;">{{ __('Нов оглас во твоите категории') }} 🎯</h2>
    <p style="color:#666B76;margin-top:0;">{{ __('Објавен е нов оглас што одговара на категориите што ги следиш на KADAR.') }}</p>

    <table cellpadding="6" cellspacing="0" style="margin:16px 0;">
        <tr><td style="font-weight:700;">{{ __('Наслов') }}:</td><td>{{ $project->title }}</td></tr>
        <tr><td style="font-weight:700;">{{ __('Категорија') }}:</td><td>{{ $project->categories->pluck('name')->join(', ') }}</td></tr>
        <tr>
            <td style="font-weight:700;">{{ __('Буџет') }}:</td>
            <td>
                @if ($project->budget_min || $project->budget_max)
                    {{ $project->budget_min ?? '?' }}–{{ $project->budget_max ?? '?' }} EUR
                @else
                    {{ __('Цена по договор') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="font-weight:700;">{{ __('Локација') }}:</td>
            <td>{{ $project->remote_ok ? __('Remote') : ($project->city?->name ?? $project->country?->name ?? '—') }}</td>
        </tr>
    </table>

    <p>
        <a href="{{ route('projects.show', $project) }}" style="display:inline-block;background:#0B6FE0;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:700;">
            {{ __('Погледни оглас →') }}
        </a>
    </p>

    <p style="color:#9AA0AB;font-size:12px;margin-top:24px;">
        {{ __('Го добиваш ова затоа што си верифициран креативец во оваа категорија на KADAR. Известувањата можеш да ги исклучиш во твоите поставки.') }}
    </p>
</body>
</html>
