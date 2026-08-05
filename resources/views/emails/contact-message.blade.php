<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color:#14171F; line-height:1.6;">
    <h2 style="margin-bottom:4px;">Нова контакт порака преку KADAR</h2>
    <p style="color:#666B76;margin-top:0;">Некој пополни ја контакт формата на страницата.</p>

    <table cellpadding="6" cellspacing="0" style="margin:16px 0;">
        <tr><td style="font-weight:700;">Име:</td><td>{{ $contactMessage->name }}</td></tr>
        <tr><td style="font-weight:700;">Email:</td><td>{{ $contactMessage->email }}</td></tr>
        <tr><td style="font-weight:700;">Категорија:</td><td>{{ $contactMessage->category }}</td></tr>
        @if ($contactMessage->user)
            <tr><td style="font-weight:700;">Корисник:</td><td>{{ $contactMessage->user->name }} (#{{ $contactMessage->user->id }})</td></tr>
        @endif
    </table>

    <p style="font-weight:700;margin-bottom:4px;">Порака:</p>
    <p style="white-space:pre-line;">{{ $contactMessage->message }}</p>

    <p style="color:#9AA0AB;font-size:12px;margin-top:24px;">
        Одговори директно на овој мејл за да стапиш во контакт со {{ $contactMessage->name }} ({{ $contactMessage->email }}).
    </p>
</body>
</html>
