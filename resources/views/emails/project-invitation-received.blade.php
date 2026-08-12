<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#F6F8FB;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F6F8FB;">
<tr>
<td align="center" style="padding:32px 16px;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:16px;border:1px solid #E8EBF0;overflow:hidden;">

    <tr>
        <td style="background-color:#0B6FE0;background:linear-gradient(135deg,#2D82E8,#0847A0);padding:24px 32px;">
            <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width:26px;height:26px;background:rgba(255,255,255,.22);border-radius:8px;text-align:center;vertical-align:middle;font-size:13px;">⚡</td>
                    <td style="padding-left:10px;color:#ffffff;font-size:19px;font-weight:800;letter-spacing:-0.02em;">CreatorSpot</td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:28px 32px 0;">
            <span style="display:inline-block;background:#EAF2FE;color:#0958B5;font-size:11.5px;font-weight:700;letter-spacing:.02em;padding:6px 12px;border-radius:999px;">
                📨 {{ __('Нова покана') }}
            </span>
        </td>
    </tr>

    <tr>
        <td style="padding:16px 32px 6px;">
            <h1 style="margin:0;font-size:21px;font-weight:800;color:#14171F;letter-spacing:-0.01em;line-height:1.35;">
                {{ __('Здраво :name,', ['name' => $recipient->name]) }}
            </h1>
        </td>
    </tr>
    <tr>
        <td style="padding:0 32px 24px;">
            <p style="margin:0 0 12px;color:#666B76;font-size:14px;line-height:1.6;">
                {{ __(':client те покани на проектот', ['client' => $invitation->client->name]) }} „{{ $invitation->project->title }}“ {{ __('на CreatorSpot.') }}
            </p>
            @if ($invitation->message)
                <p style="margin:0 0 12px;padding:14px 16px;background:#F6F8FB;border-radius:10px;color:#14171F;font-size:14px;line-height:1.6;white-space:pre-line;">
                    „{{ $invitation->message }}“
                </p>
            @endif
            <p style="margin:0;color:#666B76;font-size:14px;line-height:1.6;">
                {{ __('Погледни ја поканата на твојата контролна табла и одлучи дали сакаш да ја прифатиш.') }}
            </p>
        </td>
    </tr>

    <tr>
        <td align="center" style="padding:8px 32px 8px;">
            <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="background-color:#0B6FE0;background:linear-gradient(135deg,#2D82E8,#0958B5);border-radius:10px;">
                        <a href="{{ route('dashboard') }}"
                            style="display:inline-block;color:#ffffff;font-weight:700;font-size:14.5px;padding:13px 30px;text-decoration:none;">
                            {{ __('Погледни ја поканата →') }}
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:24px 32px 28px;border-top:1px solid #E8EBF0;margin-top:8px;">
            <p style="margin:20px 0 0;color:#9AA0AB;font-size:11.5px;line-height:1.6;">
                {{ __('Го добиваш ова затоа што некој клиент те покани на проект на CreatorSpot. Известувањата можеш да ги исклучиш во твоите поставки.') }}
            </p>
        </td>
    </tr>

</table>

</td>
</tr>
</table>
</body>
</html>
