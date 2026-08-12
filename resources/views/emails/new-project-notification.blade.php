<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#ffffff;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#ffffff;">
<tr>
<td align="center" style="padding:24px 16px;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;">

    <tr>
        <td style="padding-bottom:20px;border-bottom:1px solid #E8EBF0;">
            <span style="color:#14171F;font-size:15px;font-weight:700;">CreatorSpot</span>
        </td>
    </tr>

    <tr>
        <td style="padding:24px 0 4px;">
            <p style="margin:0;color:#666B76;font-size:14px;line-height:1.6;">{{ __('Нов оглас во твоите категории:') }}</p>
        </td>
    </tr>

    <tr>
        <td style="padding:4px 0 16px;">
            <h1 style="margin:0;font-size:19px;font-weight:700;color:#14171F;line-height:1.4;">{{ $project->title }}</h1>
        </td>
    </tr>

    <tr>
        <td style="padding:0 0 20px;color:#444A54;font-size:14px;line-height:1.9;">
            {{ __('Категорија') }}: {{ $project->categories->pluck('name')->join(', ') }}<br>
            {{ __('Буџет') }}:
            @if ($project->budget_min || $project->budget_max)
                {{ $project->budget_min ?? '?' }}–{{ $project->budget_max ?? '?' }} EUR
            @else
                {{ __('Цена по договор') }}
            @endif
            <br>
            {{ __('Локација') }}: {{ $project->remote_ok ? __('Remote') : ($project->city?->name ?? $project->country?->name ?? '—') }}
        </td>
    </tr>

    <tr>
        <td style="padding:0 0 24px;">
            <a href="{{ route('projects.show', $project) }}"
                style="display:inline-block;color:#ffffff;background-color:#0B6FE0;font-weight:600;font-size:14px;padding:10px 20px;border-radius:6px;text-decoration:none;">
                {{ __('Погледни оглас') }}
            </a>
        </td>
    </tr>

    <tr>
        <td style="padding:20px 0 0;border-top:1px solid #E8EBF0;">
            <p style="margin:16px 0 0;color:#9AA0AB;font-size:12px;line-height:1.6;">
                {{ __('Го добиваш ова затоа што си верифициран креативец во оваа категорија на CreatorSpot. Известувањата можеш да ги исклучиш во твоите поставки.') }}
            </p>
        </td>
    </tr>

</table>

</td>
</tr>
</table>
</body>
</html>
