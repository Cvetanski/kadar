<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('Регистрирај се') }} — KADAR</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    color-scheme:light;
    --bg:#FFFFFF; --bg-soft:#F6F8FB; --surface:#FFFFFF; --border:#E8EBF0;
    --text:#14171F; --text-dim:#666B76; --text-faint:#9AA0AB;
    --blue:#0B6FE0; --blue-dark:#0958B5; --blue-soft:#EAF2FE;
    --green:#17A673; --green-soft:#E7F8F1;
    --error:#DC2626;
    --shadow-sm:0 1px 2px rgba(20,23,31,0.05), 0 1px 1px rgba(20,23,31,0.04);
    --shadow-md:0 8px 24px rgba(20,23,31,0.08), 0 2px 6px rgba(20,23,31,0.04);
    --shadow-lg:0 20px 50px rgba(20,23,31,0.12), 0 4px 12px rgba(20,23,31,0.06);
    --radius:16px; --font:'Inter', sans-serif;
  }
  *{margin:0;padding:0;box-sizing:border-box;}
  select,option{background:#fff;color:var(--text);}
  body{
    background:var(--bg);color:var(--text);font-family:var(--font);-webkit-font-smoothing:antialiased;
    background-image:radial-gradient(circle, rgba(20,23,31,0.12) 1.4px, transparent 1.4px);
    background-size:22px 22px;
    min-height:100vh;
  }
  a{text-decoration:none;color:inherit;}
  a:focus-visible, button:focus-visible, input:focus-visible{outline:2px solid var(--blue);outline-offset:2px;}

  .top-bar{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:10px;
    padding:28px 20px 0;max-width:1160px;margin:0 auto;}
  .logo{display:flex;align-items:center;gap:8px;font-weight:800;font-size:18px;letter-spacing:-0.01em;justify-self:center;}
  .logo .sq{width:22px;height:22px;border-radius:6px;background:linear-gradient(135deg,#2D82E8,#0847A0);
    display:flex;align-items:center;justify-content:center;flex-shrink:0;}
  .logo .sq span{width:7px;height:7px;border-radius:2px;background:#fff;}
  .back{font-size:13.5px;color:var(--text-dim);display:flex;align-items:center;gap:6px;justify-self:start;}
  .back:hover{color:var(--text);}
  .lang-slot{justify-self:end;}
  @media(max-width:480px){.top-bar{padding:18px 14px 0;gap:6px;}.back{font-size:12.5px;}.logo{font-size:16px;}}

  .wrap{display:flex;justify-content:center;padding:32px 20px 80px;}
  .card{
    width:100%;max-width:460px;background:var(--surface);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow-lg);padding:36px 32px;
  }
  @media(max-width:420px){.card{padding:28px 20px;}.role-grid{gap:8px;}.role-card{padding:14px 10px;}}
  .card h1{font-size:22px;font-weight:800;letter-spacing:-0.01em;text-align:center;margin-bottom:6px;}
  .card .sub{font-size:14px;color:var(--text-dim);text-align:center;margin-bottom:28px;}

  .role-label{font-size:13px;font-weight:600;margin-bottom:10px;}
  .role-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:8px;}
  .role-card{
    border:1.5px solid var(--border);border-radius:12px;padding:16px 14px;cursor:pointer;
    text-align:center;transition:border-color .15s ease, background .15s ease;display:block;
  }
  .role-card input{display:none;}
  .role-card .icon{font-size:22px;margin-bottom:8px;}
  .role-card .title{font-weight:700;font-size:14px;margin-bottom:3px;}
  .role-card .desc{font-size:12px;color:var(--text-dim);line-height:1.4;}
  .role-card.selected{border-color:var(--blue);background:var(--blue-soft);}
  .role-card.selected .title{color:var(--blue-dark);}

  label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;margin-top:16px;}
  label:first-of-type{margin-top:0;}
  input[type=text], input[type=email], input[type=password]{
    width:100%;padding:11px 14px;border:1px solid var(--border);border-radius:10px;
    font-family:var(--font);font-size:14.5px;background:var(--bg-soft);
    transition:border-color .15s ease;
  }
  input[type=text]:focus, input[type=email]:focus, input[type=password]:focus{border-color:var(--blue);background:#fff;}
  @media(max-width:640px){input[type=text], input[type=email], input[type=password]{font-size:16px;}}
  input.has-error{border-color:var(--error);}

  .field-error{color:var(--error);font-size:12.5px;margin-top:6px;}

  .btn-submit{
    width:100%;margin-top:24px;padding:12px;border:none;border-radius:10px;
    background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;font-weight:700;
    font-size:14.5px;font-family:var(--font);cursor:pointer;box-shadow:var(--shadow-sm);
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .btn-submit:hover{transform:translateY(-1px);box-shadow:var(--shadow-md);}

  .btn-google{
    width:100%;display:flex;align-items:center;justify-content:center;gap:10px;padding:11px;
    border:1px solid var(--border);border-radius:10px;background:#fff;color:var(--text);
    font-weight:600;font-size:14px;font-family:var(--font);cursor:pointer;
    transition:background .15s ease, border-color .15s ease;
  }
  .btn-google:hover{background:var(--bg-soft);border-color:var(--text-faint);}

  .divider{display:flex;align-items:center;gap:12px;margin:24px 0;color:var(--text-faint);font-size:12.5px;}
  .divider::before, .divider::after{content:'';flex:1;height:1px;background:var(--border);}

  .terms{font-size:12px;color:var(--text-faint);text-align:center;margin-top:14px;line-height:1.5;}
  .terms a{color:var(--text-dim);text-decoration:underline;}

  .footer-link{text-align:center;font-size:14px;color:var(--text-dim);margin-top:20px;}
  .footer-link a{color:var(--blue);font-weight:600;}
  .footer-link a:hover{color:var(--blue-dark);}


</style>
</head>
<body>

<div class="top-bar">
  <a href="{{ route('welcome') }}" class="back">← {{ __('Назад кон почетна') }}</a>
  <a href="{{ route('welcome') }}" class="logo"><span class="sq"><span></span></span>KADAR<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;">Beta</span></a>
  <div class="lang-slot"><x-language-switcher short /></div>
</div>

<div class="wrap">
  <div class="card">
    <h1>{{ __('Креирај профил') }}</h1>
    <p class="sub">{{ __('Бесплатно · Без картичка') }}</p>

    <a href="{{ route('auth.google.redirect') }}" class="btn-google">
      <svg width="18" height="18" viewBox="0 0 18 18">
        <path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 0 1-1.8 2.72v2.26h2.92c1.7-1.57 2.68-3.88 2.68-6.62z"/>
        <path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.92-2.26c-.81.54-1.84.86-3.04.86-2.34 0-4.32-1.58-5.03-3.7H.96v2.33A9 9 0 0 0 9 18z"/>
        <path fill="#FBBC05" d="M3.97 10.72A5.4 5.4 0 0 1 3.68 9c0-.6.1-1.18.29-1.72V4.95H.96A9 9 0 0 0 0 9c0 1.45.35 2.83.96 4.05l3.01-2.33z"/>
        <path fill="#EA4335" d="M9 3.58c1.32 0 2.51.45 3.44 1.35l2.59-2.59C13.46.89 11.43 0 9 0A9 9 0 0 0 .96 4.95l3.01 2.33C4.68 5.16 6.66 3.58 9 3.58z"/>
      </svg>
      {{ __('Продолжи со Google') }}
    </a>

    <div class="divider">{{ __('или') }}</div>

    <form method="POST" action="{{ route('register') }}">
      @csrf

      <div class="role-label">{{ __('Што бараш на KADAR?') }}</div>
      <div class="role-grid">
        <label class="role-card{{ old('role', 'client') === 'client' ? ' selected' : '' }}" id="role-client" onclick="selectRole('client')">
          <input type="radio" name="role" value="client" {{ old('role', 'client') === 'client' ? 'checked' : '' }}>
          <div class="icon">🔍</div>
          <div class="title">{{ __('Барам креативец') }}</div>
          <div class="desc">{{ __('Отвори проект или пребарувај директно') }}</div>
        </label>
        <label class="role-card{{ old('role') === 'creator' ? ' selected' : '' }}" id="role-creator" onclick="selectRole('creator')">
          <input type="radio" name="role" value="creator" {{ old('role') === 'creator' ? 'checked' : '' }}>
          <div class="icon">🎬</div>
          <div class="title">{{ __('Сум креативец') }}</div>
          <div class="desc">{{ __('Изгради профил и најди проекти') }}</div>
        </label>
      </div>
      @error('role')
        <div class="field-error" style="margin-bottom:16px;">{{ $message }}</div>
      @enderror

      <label for="name">{{ __('Име и презиме или username') }}</label>
      <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Марија Стојановска"
        class="{{ $errors->get('name') ? 'has-error' : '' }}" required autofocus autocomplete="name">
      @error('name')
        <div class="field-error">{{ $message }}</div>
      @enderror

      <label for="email">{{ __('Е-пошта') }}</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="име@пример.мк"
        class="{{ $errors->get('email') ? 'has-error' : '' }}" required autocomplete="username">
      @error('email')
        <div class="field-error">{{ $message }}</div>
      @enderror

      <label for="password">{{ __('Лозинка') }}</label>
      <input type="password" id="password" name="password" placeholder="{{ __('Најмалку 8 карактери') }}"
        class="{{ $errors->get('password') ? 'has-error' : '' }}" required autocomplete="new-password">
      @error('password')
        <div class="field-error">{{ $message }}</div>
      @enderror

      <button type="submit" class="btn-submit">{{ __('Регистрирај се бесплатно →') }}</button>
    </form>

    <p class="terms">{{ __('Со регистрација се согласуваш со') }} <a href="{{ route('legal.terms') }}" target="_blank" rel="noopener">{{ __('Условите') }}</a> {{ __('и') }} <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener">{{ __('Приватноста') }}</a> {{ __('на KADAR.') }}</p>
    <p class="footer-link">{{ __('Веќе имаш акаунт?') }} <a href="{{ route('login') }}">{{ __('Најави се') }}</a></p>
  </div>
</div>

<script>
function selectRole(role){
  document.getElementById('role-client').classList.toggle('selected', role === 'client');
  document.getElementById('role-creator').classList.toggle('selected', role === 'creator');
}
</script>

</body>
</html>
