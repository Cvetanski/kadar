<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('Дополни го профилот') }} — CreatorSpot</title>
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    color-scheme:light;
    --bg:#FFFFFF; --bg-soft:#F6F8FB; --surface:#FFFFFF; --border:#E8EBF0;
    --text:#14171F; --text-dim:#666B76; --text-faint:#9AA0AB;
    --blue:#0B6FE0; --blue-dark:#0958B5; --blue-soft:#EAF2FE;
    --green:#17A673; --green-soft:#E7F8F1; --error:#DC2626;
    --shadow-sm:0 1px 2px rgba(20,23,31,0.05), 0 1px 1px rgba(20,23,31,0.04);
    --shadow-md:0 8px 24px rgba(20,23,31,0.08), 0 2px 6px rgba(20,23,31,0.04);
    --shadow-lg:0 20px 50px rgba(20,23,31,0.12), 0 4px 12px rgba(20,23,31,0.06);
    --radius:16px; --font:'Inter', sans-serif;
  }
  *{margin:0;padding:0;box-sizing:border-box;}
  body{
    background:var(--bg);color:var(--text);font-family:var(--font);-webkit-font-smoothing:antialiased;
    background-image:radial-gradient(circle, rgba(20,23,31,0.12) 1.4px, transparent 1.4px);
    background-size:22px 22px;
    min-height:100vh;
  }
  a{text-decoration:none;color:inherit;}
  button:focus-visible, input:focus-visible, select:focus-visible{outline:2px solid var(--blue);outline-offset:2px;}

  .top{display:flex;justify-content:center;padding:32px 0 0;}
  .logo{display:flex;align-items:center;gap:8px;font-weight:800;font-size:18px;letter-spacing:-0.01em;}
  .logo .sq{width:22px;height:22px;border-radius:6px;background:linear-gradient(135deg,#2D82E8,#0847A0);
    display:flex;align-items:center;justify-content:center;}
  .logo .sq span{width:7px;height:7px;border-radius:2px;background:#fff;}

  .wrap{display:flex;justify-content:center;padding:64px 20px 80px;}
  .card{
    width:100%;max-width:420px;background:var(--surface);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow-lg);padding:40px 32px;text-align:center;
  }
  .pill{display:inline-flex;align-items:center;gap:8px;background:var(--green-soft);
    color:var(--green);font-size:12.5px;font-weight:700;padding:6px 12px;border-radius:999px;margin-bottom:20px;}

  h1{font-size:21px;font-weight:800;letter-spacing:-0.01em;margin-bottom:6px;}
  .sub{font-size:14px;color:var(--text-dim);margin-bottom:28px;line-height:1.5;}

  .avatar-circle{
    width:112px;height:112px;border-radius:50%;background:var(--bg-soft);border:2px dashed var(--border);
    display:flex;align-items:center;justify-content:center;position:relative;cursor:pointer;
    margin:0 auto 24px;overflow:hidden;transition:border-color .15s ease;
  }
  .avatar-circle:hover{border-color:var(--blue);}
  .avatar-circle.has-image{border:2px solid var(--border);}
  .avatar-circle img{width:100%;height:100%;object-fit:cover;}
  .avatar-circle .placeholder-icon{font-size:34px;color:var(--text-faint);}
  .avatar-cam{
    position:absolute;bottom:2px;right:2px;width:32px;height:32px;border-radius:50%;
    background:linear-gradient(135deg,#2D82E8,#0958B5);display:flex;align-items:center;justify-content:center;
    font-size:13px;color:#fff;border:3px solid #fff;
  }

  label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;text-align:left;}
  select{
    width:100%;padding:11px 14px;border:1px solid var(--border);border-radius:10px;
    font-family:var(--font);font-size:14.5px;background:var(--bg-soft);color:var(--text);margin-bottom:8px;
    transition:border-color .15s ease;
  }
  select:focus{border-color:var(--blue);background:#fff;}
  option{background:#fff;color:var(--text);}
  .field-error{color:var(--error);font-size:12.5px;margin-bottom:12px;text-align:left;}

  .btn-submit{
    width:100%;padding:12px;border:none;border-radius:10px;margin-top:8px;
    background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;font-weight:700;
    font-size:14.5px;font-family:var(--font);cursor:pointer;box-shadow:var(--shadow-sm);
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .btn-submit:hover{transform:translateY(-1px);box-shadow:var(--shadow-md);}

  .skip-link{display:block;margin-top:16px;font-size:13.5px;color:var(--text-faint);cursor:pointer;}
  .skip-link:hover{color:var(--text-dim);}
</style>
</head>
<body>

<div class="top">
  <div class="logo"><span class="sq"><span></span></span>CreatorSpot<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;">Beta</span></div>
</div>

<div class="wrap">
  <div class="card">
    <div class="pill">✓ {{ __('Акаунтот е создаден') }}</div>
    <h1>{{ __('Дополни го профилот') }}</h1>
    <p class="sub">{{ __('Профил со слика добива многу повеќе доверба и одговори од креативците.') }}</p>

    <form method="POST" action="{{ route('client-welcome.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="avatar-circle" id="avatar-circle" onclick="document.getElementById('avatar-input').click()">
            <span class="placeholder-icon" id="avatar-placeholder">🙂</span>
            <div class="avatar-cam">📷</div>
        </div>
        <input type="file" name="avatar" id="avatar-input" accept="image/*" style="display:none" onchange="previewAvatar(event)">
        @error('avatar') <div class="field-error" style="text-align:center;">{{ $message }}</div> @enderror

        <label for="city_id">{{ __('Град (опционално)') }}</label>
        <select id="city_id" name="city_id">
            <option value="">{{ __('Избери град') }}</option>
            @foreach ($countries as $country)
                <optgroup label="{{ $country->name }}">
                    @foreach ($country->cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        @error('city_id') <div class="field-error">{{ $message }}</div> @enderror

        <button type="submit" class="btn-submit">{{ __('Продолжи кон почетна →') }}</button>
    </form>
    <a href="{{ route('dashboard') }}" class="skip-link">{{ __('Прескокни за сега') }}</a>
  </div>
</div>

<script>
function previewAvatar(event){
  const file = event.target.files[0];
  if(!file) return;
  const reader = new FileReader();
  reader.onload = e=>{
    const circle = document.getElementById('avatar-circle');
    circle.classList.add('has-image');
    document.getElementById('avatar-placeholder').style.display = 'none';
    let img = circle.querySelector('img');
    if(!img){
      img = document.createElement('img');
      img.alt = {{ Illuminate\Support\Js::from(__('Преглед на профилна слика')) }};
      circle.insertBefore(img, circle.firstChild);
    }
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
}
</script>

</body>
</html>
