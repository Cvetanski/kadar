<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Onboarding — CreatorSpot</title>
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    color-scheme:light;
    --bg:#FFFFFF; --bg-soft:#F6F8FB; --surface:#FFFFFF; --border:#E8EBF0;
    --text:#14171F; --text-dim:#666B76; --text-faint:#9AA0AB;
    --blue:#0B6FE0; --blue-dark:#0958B5; --blue-soft:#EAF2FE;
    --green:#17A673; --green-soft:#E7F8F1; --amber:#F5A524;
    --error:#DC2626;
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
  button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible{outline:2px solid var(--blue);outline-offset:2px;}

  .top{display:flex;justify-content:center;padding:28px 0 0;}
  .logo{display:flex;align-items:center;gap:8px;font-weight:800;font-size:18px;letter-spacing:-0.01em;}
  .logo .sq{width:22px;height:22px;border-radius:6px;background:linear-gradient(135deg,#2D82E8,#0847A0);
    display:flex;align-items:center;justify-content:center;}
  .logo .sq span{width:7px;height:7px;border-radius:2px;background:#fff;}

  .shell{max-width:620px;margin:0 auto;padding:40px 20px 80px;}

  /* progress */
  .progress-head{margin-bottom:28px;}
  .progress-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
  .progress-label{font-size:13px;font-weight:600;color:var(--text-dim);}
  .progress-step-name{font-size:13px;font-weight:700;color:var(--blue);}
  .progress-track{height:6px;background:var(--border);border-radius:999px;overflow:hidden;}
  .progress-fill{height:100%;background:linear-gradient(135deg,#2D82E8,#0958B5);border-radius:999px;
    transition:width .3s ease;}

  .card{background:var(--surface);border:1px solid var(--border);border-radius:20px;
    box-shadow:var(--shadow-lg);padding:36px 32px;}
  .step-title{font-size:21px;font-weight:800;letter-spacing:-0.01em;margin-bottom:6px;}
  .step-sub{font-size:14px;color:var(--text-dim);margin-bottom:28px;}

  /* STEP 1 - categories as tiles */
  .tile-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
  @media(max-width:520px){.tile-grid{grid-template-columns:repeat(2,1fr);}}
  .tile{
    position:relative;border:1.5px solid var(--border);border-radius:14px;padding:20px 16px;
    cursor:pointer;transition:border-color .15s ease, background .15s ease, transform .15s ease;
    text-align:left;
  }
  .tile:hover{transform:translateY(-2px);}
  .tile .icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;
    font-size:18px;margin-bottom:12px;background:var(--bg-soft);}
  .tile .name{font-weight:700;font-size:14.5px;margin-bottom:4px;}
  .tile .desc{font-size:12px;color:var(--text-dim);line-height:1.4;}
  .tile.selected{border-color:var(--blue);background:var(--blue-soft);}
  .tile.selected .icon{background:#fff;}
  .tile-check{
    position:absolute;top:14px;right:14px;width:20px;height:20px;border-radius:6px;
    border:1.5px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;
    font-size:12px;color:#fff;
  }
  .tile.selected .tile-check{background:var(--blue);border-color:var(--blue);}

  /* STEP 2 - skill pills */
  .skill-group{margin-bottom:20px;}
  .skill-group-title{font-size:12.5px;font-weight:700;color:var(--text-faint);text-transform:uppercase;
    letter-spacing:0.03em;margin-bottom:10px;}
  .pill-row{display:flex;flex-wrap:wrap;gap:8px;}
  .pill{
    border:1.5px solid var(--border);border-radius:999px;padding:8px 16px;font-size:13.5px;font-weight:600;
    cursor:pointer;transition:all .15s ease;color:var(--text-dim);
  }
  .pill.selected{background:var(--blue);border-color:var(--blue);color:#fff;}

  /* forms */
  label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;margin-top:16px;}
  label:first-of-type{margin-top:0;}
  input[type=text], input[type=number], input[type=url], select, textarea{
    width:100%;padding:11px 14px;border:1px solid var(--border);border-radius:10px;
    font-family:var(--font);font-size:14.5px;background:var(--bg-soft);color:var(--text);transition:border-color .15s ease;
  }
  input:focus, select:focus, textarea:focus{border-color:var(--blue);background:#fff;}
  @media(max-width:640px){input[type=text], input[type=number], input[type=url], select, textarea{font-size:16px;}}
  option{background:#fff;color:var(--text);}
  textarea{resize:vertical;min-height:90px;}
  .two-col{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
  .checkbox-row{display:flex;align-items:center;gap:8px;margin-top:16px;font-size:14px;color:var(--text-dim);}
  .checkbox-row input{width:auto;}

  .field-error{color:var(--error);font-size:12.5px;margin-top:6px;}

  /* STEP 4 avatar upload */
  .avatar-circle{
    width:112px;height:112px;border-radius:50%;background:var(--bg-soft);border:2px dashed var(--border);
    display:flex;align-items:center;justify-content:center;position:relative;cursor:pointer;
    margin:0 auto 8px;overflow:hidden;transition:border-color .15s ease;
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

  /* STEP 5 portfolio */
  .add-row{display:grid;grid-template-columns:1fr 110px 1fr auto;gap:10px;align-items:end;margin-top:0;}
  .add-row label{margin-top:0;}
  .btn-add{background:var(--blue-soft);color:var(--blue-dark);border:none;border-radius:10px;
    padding:11px 16px;font-weight:700;font-size:13.5px;cursor:pointer;height:fit-content;}
  .portfolio-list{margin-top:20px;display:flex;flex-direction:column;gap:8px;}
  .portfolio-item{display:flex;justify-content:space-between;align-items:center;background:var(--bg-soft);
    border-radius:10px;padding:10px 14px;font-size:13.5px;}
  .portfolio-item .meta{color:var(--text-faint);font-size:12px;}
  .remove-btn{background:none;border:none;color:var(--text-faint);cursor:pointer;font-size:16px;}
  .remove-btn:hover{color:#D64545;}
  .empty-note{font-size:13px;color:var(--text-faint);margin-top:16px;text-align:center;padding:20px;
    border:1.5px dashed var(--border);border-radius:12px;}

  /* STEP 7 review */
  .review-block{border-bottom:1px solid var(--border);padding:16px 0;}
  .review-block:first-child{padding-top:0;}
  .review-block:last-child{border-bottom:none;padding-bottom:0;}
  .review-label{font-size:12px;font-weight:700;color:var(--text-faint);text-transform:uppercase;
    letter-spacing:0.03em;margin-bottom:8px;}
  .review-value{font-size:14.5px;color:var(--text);line-height:1.6;}
  .review-tags{display:flex;flex-wrap:wrap;gap:6px;}
  .review-tag{background:var(--blue-soft);color:var(--blue-dark);font-size:12.5px;font-weight:600;
    padding:4px 10px;border-radius:999px;}
  .verify-note{display:flex;gap:10px;background:var(--green-soft);border-radius:12px;padding:14px 16px;
    margin-top:20px;font-size:13px;color:var(--green);}

  /* nav buttons */
  .nav-row{display:flex;justify-content:space-between;margin-top:32px;}
  .btn{display:inline-flex;align-items:center;gap:8px;font-family:var(--font);font-weight:700;
    font-size:14.5px;padding:12px 22px;border-radius:10px;border:none;cursor:pointer;transition:all .15s ease;}
  .btn-back{background:#fff;color:var(--text-dim);border:1px solid var(--border);}
  .btn-back:hover{border-color:var(--text-faint);}
  .btn-back:disabled{opacity:0.4;cursor:not-allowed;}
  .btn-next{background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;box-shadow:var(--shadow-sm);}
  .btn-next:hover{transform:translateY(-1px);box-shadow:var(--shadow-md);}
</style>
</head>
<body>

<div class="top">
  <div class="logo"><img src="{{ asset('images/logo2.svg') }}" alt="CreatorSpot" style="width:66px;height:66px;border-radius:6px;object-fit:contain;">CreatorSpot<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;">Beta</span></div>
</div>

<div class="shell">
  <livewire:onboarding-wizard />
</div>

</body>
</html>
