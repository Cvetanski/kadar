<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('Избери улога') }} — CreatorSpot</title>
<meta name="robots" content="noindex, nofollow">
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

  .top{display:flex;justify-content:center;padding:32px 0 0;}
  .logo{display:flex;align-items:center;gap:8px;font-weight:800;font-size:18px;letter-spacing:-0.01em;}
  .logo .sq{width:22px;height:22px;border-radius:6px;background:linear-gradient(135deg,#2D82E8,#0847A0);
    display:flex;align-items:center;justify-content:center;}
  .logo .sq span{width:7px;height:7px;border-radius:2px;background:#fff;}

  .wrap{display:flex;justify-content:center;padding:56px 20px 80px;}
  .card{
    width:100%;max-width:460px;background:var(--surface);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow-lg);padding:36px 32px;
  }
  .card h1{font-size:22px;font-weight:800;letter-spacing:-0.01em;text-align:center;margin-bottom:6px;}
  .card .sub{font-size:14px;color:var(--text-dim);text-align:center;margin-bottom:28px;}

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

  .field-error{color:var(--error);font-size:12.5px;margin-bottom:12px;text-align:center;}

  .btn-submit{
    width:100%;margin-top:24px;padding:12px;border:none;border-radius:10px;
    background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;font-weight:700;
    font-size:14.5px;font-family:var(--font);cursor:pointer;box-shadow:var(--shadow-sm);
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .btn-submit:hover{transform:translateY(-1px);box-shadow:var(--shadow-md);}
</style>
</head>
<body>

<div class="top">
  <div class="logo"><img src="{{ asset('images/logo2.svg') }}" alt="CreatorSpot" style="width:66px;height:66px;border-radius:6px;object-fit:contain;">CreatorSpot<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;">Beta</span></div>
</div>

<div class="wrap">
  <div class="card">
    <h1>{{ __('Добредојде на CreatorSpot') }}</h1>
    <p class="sub">{{ __('Што бараш на CreatorSpot?') }}</p>

    <form method="POST" action="{{ route('choose-role.store') }}">
      @csrf

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
        <div class="field-error">{{ $message }}</div>
      @enderror

      <button type="submit" class="btn-submit">{{ __('Продолжи →') }}</button>
    </form>
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
