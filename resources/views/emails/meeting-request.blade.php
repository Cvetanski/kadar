<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color:#14171F; line-height:1.6;">
    <h2 style="margin-bottom:4px;">Ново барање за состанок преку KADAR</h2>
    <p style="color:#666B76;margin-top:0;">Некој сакаше да закаже состанок со тебе преку страницата „За нас".</p>

    <table cellpadding="6" cellspacing="0" style="margin:16px 0;">
        <tr><td style="font-weight:700;">Име:</td><td>{{ $requesterName }}</td></tr>
        <tr><td style="font-weight:700;">Email:</td><td>{{ $requesterEmail }}</td></tr>
        <tr><td style="font-weight:700;">Датум:</td><td>{{ $meetingDate }}</td></tr>
        <tr><td style="font-weight:700;">Време:</td><td>{{ $meetingTime }}</td></tr>
    </table>

    @if ($note)
        <p style="font-weight:700;margin-bottom:4px;">Порака:</p>
        <p style="white-space:pre-line;">{{ $note }}</p>
    @endif

    <p style="color:#9AA0AB;font-size:12px;margin-top:24px;">
        Одговори директно на овој мејл за да стапиш во контакт со {{ $requesterName }} ({{ $requesterEmail }}).
    </p>
</body>
</html>
