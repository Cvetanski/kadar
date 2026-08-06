<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('Контакт') }} — CreatorSpot</title>
<meta name="description" content="{{ __('Стапи во контакт со тимот на CreatorSpot.') }}">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:title" content="{{ __('Контакт') }} — CreatorSpot">
<meta property="og:description" content="{{ __('Стапи во контакт со тимот на CreatorSpot.') }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="CreatorSpot">
<meta property="og:locale" content="{{ \App\Support\LocaleOptions::ogLocale(app()->getLocale()) }}">
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
  a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible{outline:2px solid var(--blue);outline-offset:2px;}

  .top{display:flex;justify-content:center;padding:32px 0 0;}
  .logo{display:flex;align-items:center;gap:8px;font-weight:800;font-size:18px;letter-spacing:-0.01em;}
  .logo .sq{width:22px;height:22px;border-radius:6px;background:linear-gradient(135deg,#2D82E8,#0847A0);
    display:flex;align-items:center;justify-content:center;}
  .logo .sq span{width:7px;height:7px;border-radius:2px;background:#fff;}

  .wrap{display:flex;justify-content:center;padding:56px 20px 80px;}
  .card{
    width:100%;max-width:480px;background:var(--surface);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow-lg);padding:36px 32px;
  }
  .card h1{font-size:22px;font-weight:800;letter-spacing:-0.01em;text-align:center;margin-bottom:6px;}
  .card .sub{font-size:14px;color:var(--text-dim);text-align:center;margin-bottom:28px;}

  label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;margin-top:16px;}
  label:first-of-type{margin-top:0;}
  input[type=text], input[type=email], select, textarea{
    width:100%;padding:11px 14px;border:1px solid var(--border);border-radius:10px;
    font-family:var(--font);font-size:14.5px;background:var(--bg-soft);color:var(--text);
    transition:border-color .15s ease;
  }
  input[type=text]:focus, input[type=email]:focus, select:focus, textarea:focus{border-color:var(--blue);background:#fff;}
  @media(max-width:640px){input[type=text], input[type=email], select, textarea{font-size:16px;}}
  input.has-error, select.has-error, textarea.has-error{border-color:var(--error);}
  textarea{resize:vertical;min-height:120px;}

  .field-error{color:var(--error);font-size:12.5px;margin-top:6px;}
  .status-banner{background:var(--green-soft);color:var(--green);font-size:13.5px;font-weight:600;
    padding:10px 14px;border-radius:10px;margin-bottom:20px;text-align:center;}

  .btn-submit{
    width:100%;margin-top:24px;padding:12px;border:none;border-radius:10px;
    background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;font-weight:700;
    font-size:14.5px;font-family:var(--font);cursor:pointer;box-shadow:var(--shadow-sm);
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .btn-submit:hover{transform:translateY(-1px);box-shadow:var(--shadow-md);}

  .back{position:absolute;top:32px;left:32px;font-size:13.5px;color:var(--text-dim);display:flex;align-items:center;gap:6px;}
  .back:hover{color:var(--text);}
  @media(max-width:520px){.back{position:static;display:inline-flex;margin:0 0 0 20px;}}
</style>
</head>
<body>

<a href="{{ route('welcome') }}" class="back">← {{ __('Назад кон почетна') }}</a>

<div style="position:absolute;top:32px;right:32px;">
  <x-language-switcher />
</div>

<div class="top">
  <a href="{{ route('welcome') }}" class="logo"><img src="{{ asset('images/logo2.svg') }}" alt="CreatorSpot" style="width:66px;height:66px;border-radius:6px;object-fit:contain;">CreatorSpot<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;">Beta</span></a>
</div>

<div class="wrap">
  <div class="card">
    <h1>{{ __('Контактирај нѐ') }}</h1>
    <p class="sub">{{ __('Прашање, проблем или деловна соработка? Пиши ни, одговараме до 48 часа.') }}</p>

    @if (session('status'))
      <div class="status-banner">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('contact.store') }}">
      @csrf

      <label for="name">{{ __('Име') }}</label>
      <input type="text" id="name" name="name" class="{{ $errors->has('name') ? 'has-error' : '' }}" value="{{ old('name', $prefillName) }}">
      @error('name') <div class="field-error">{{ $message }}</div> @enderror

      <label for="email">Email</label>
      <input type="email" id="email" name="email" class="{{ $errors->has('email') ? 'has-error' : '' }}" value="{{ old('email', $prefillEmail) }}">
      @error('email') <div class="field-error">{{ $message }}</div> @enderror

      <label for="category">{{ __('Категорија') }}</label>
      <select id="category" name="category" class="{{ $errors->has('category') ? 'has-error' : '' }}">
        <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>{{ __('Општо прашање') }}</option>
        <option value="bug_report" {{ old('category') === 'bug_report' ? 'selected' : '' }}>{{ __('Пријава на бug') }}</option>
        <option value="user_report" {{ old('category') === 'user_report' ? 'selected' : '' }}>{{ __('Пријава на корисник') }}</option>
        <option value="business" {{ old('category') === 'business' ? 'selected' : '' }}>{{ __('Деловна соработка') }}</option>
      </select>
      @error('category') <div class="field-error">{{ $message }}</div> @enderror

      <label for="message">{{ __('Порака') }}</label>
      <textarea id="message" name="message" class="{{ $errors->has('message') ? 'has-error' : '' }}">{{ old('message') }}</textarea>
      @error('message') <div class="field-error">{{ $message }}</div> @enderror

      <button type="submit" class="btn-submit">{{ __('Испрати порака') }}</button>
    </form>
  </div>
</div>

</body>
</html>
