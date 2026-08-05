<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>404 — {{ __('Страницата не е пронајдена') }} | KADAR</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    color-scheme:light;
    --bg:#FFFFFF; --border:#E8EBF0; --text:#14171F; --text-dim:#666B76; --text-faint:#9AA0AB;
    --blue:#0B6FE0; --blue-dark:#0958B5; --blue-soft:#EAF2FE;
    --shadow-sm:0 1px 2px rgba(20,23,31,0.05); --shadow-md:0 8px 24px rgba(20,23,31,0.08);
    --font:'Inter', sans-serif;
  }
  *{margin:0;padding:0;box-sizing:border-box;}
  select,option{background:#fff;color:var(--text);}
  body{
    background:var(--bg);color:var(--text);font-family:var(--font);-webkit-font-smoothing:antialiased;
    background-image:radial-gradient(circle, rgba(20,23,31,0.12) 1.4px, transparent 1.4px);
    background-size:22px 22px;
    min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;
    text-align:center;padding:20px;
  }
  a{text-decoration:none;color:inherit;}
  .logo{display:flex;align-items:center;gap:8px;font-weight:800;font-size:18px;position:absolute;top:32px;left:32px;}
  .logo .sq{width:22px;height:22px;border-radius:6px;background:linear-gradient(135deg,#2D82E8,#0847A0);
    display:flex;align-items:center;justify-content:center;}
  .logo .sq span{width:7px;height:7px;border-radius:2px;background:#fff;}

  .code{font-size:88px;font-weight:800;letter-spacing:-0.03em;
    background:linear-gradient(135deg,#2D82E8,#0958B5);-webkit-background-clip:text;background-clip:text;color:transparent;}
  h1{font-size:22px;font-weight:800;margin:8px 0 10px;}
  p{font-size:15px;color:var(--text-dim);max-width:380px;line-height:1.6;margin-bottom:32px;}
  .actions{display:flex;gap:12px;}
  .btn{display:inline-flex;align-items:center;gap:8px;font-weight:700;font-size:14px;
    padding:11px 20px;border-radius:10px;border:1px solid transparent;transition:all .15s ease;}
  .btn-primary{background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;box-shadow:var(--shadow-sm);}
  .btn-primary:hover{transform:translateY(-1px);box-shadow:var(--shadow-md);}
  .btn-ghost{color:var(--text);border-color:var(--border);}
  .btn-ghost:hover{border-color:var(--text-faint);}
</style>
</head>
<body>
  <a href="{{ route('welcome') }}" class="logo"><span class="sq"><span></span></span>KADAR<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;">Beta</span></a>
  <div class="code">404</div>
  <h1>{{ __('Оваа страница не постои') }}</h1>
  <p>{{ __('Можеби линкот е погрешен или страницата е преместена. Провери го URL-то или врати се на почетна.') }}</p>
  <div class="actions">
    <a href="{{ route('welcome') }}" class="btn btn-primary">{{ __('Кон почетна →') }}</a>
    <a href="{{ route('creators.index') }}" class="btn btn-ghost">{{ __('Барај креативци') }}</a>
  </div>
</body>
</html>
