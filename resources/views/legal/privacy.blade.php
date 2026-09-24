<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Privacy Policy — CreatorSpot</title>
<meta name="description" content="CreatorSpot's Privacy Policy — how we handle your data on the platform.">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:title" content="Privacy Policy — CreatorSpot">
<meta property="og:description" content="CreatorSpot's Privacy Policy — how we handle your data on the platform.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="CreatorSpot">
<meta property="og:image" content="{{ asset('images/shareImage.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Privacy Policy — CreatorSpot">
<meta name="twitter:description" content="CreatorSpot's Privacy Policy — how we handle your data on the platform.">
<meta name="twitter:image" content="{{ asset('images/shareImage.jpg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    color-scheme:light;
    --bg:#FFFFFF; --bg-soft:#F6F8FB; --surface:#FFFFFF; --border:#E8EBF0;
    --text:#14171F; --text-dim:#666B76; --text-faint:#9AA0AB;
    --blue:#0B6FE0; --blue-dark:#0958B5; --blue-soft:#EAF2FE;
    --shadow-sm:0 1px 2px rgba(20,23,31,0.05), 0 1px 1px rgba(20,23,31,0.04);
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
  a{color:var(--blue);text-decoration:none;}
  a:hover{color:var(--blue-dark);}

  .top{display:flex;justify-content:center;padding:32px 0 0;}
  .logo{display:flex;align-items:center;gap:8px;font-weight:800;font-size:18px;letter-spacing:-0.01em;color:var(--text);}
  .logo .sq{width:22px;height:22px;border-radius:6px;background:linear-gradient(135deg,#2D82E8,#0847A0);
    display:flex;align-items:center;justify-content:center;}
  .logo .sq span{width:7px;height:7px;border-radius:2px;background:#fff;}

  .wrap{display:flex;justify-content:center;padding:48px 20px 80px;}
  .doc{
    width:100%;max-width:720px;background:var(--surface);border:1px solid var(--border);
    border-radius:var(--radius);box-shadow:var(--shadow-lg);padding:44px 40px;
  }
  .doc h1{font-size:26px;font-weight:800;letter-spacing:-0.01em;margin-bottom:6px;}
  .doc .updated{font-size:13px;color:var(--text-faint);margin-bottom:32px;}
  .doc h2{font-size:16px;font-weight:800;margin:28px 0 10px;color:var(--text);}
  .doc p{font-size:14.5px;line-height:1.7;color:var(--text-dim);margin-bottom:12px;}
  .doc ul{margin:0 0 12px 20px;}
  .doc li{font-size:14.5px;line-height:1.7;color:var(--text-dim);margin-bottom:6px;}
  .doc h2:first-of-type{margin-top:0;}

  .back{display:inline-flex;align-items:center;gap:6px;font-size:13.5px;color:var(--text-dim);margin-bottom:24px;}
  .back:hover{color:var(--text);}

  @media(max-width:600px){.doc{padding:32px 24px;}}
</style>
</head>
<body>

<div class="top">
  <a href="{{ route('welcome') }}" class="logo"><img src="{{ asset('images/logo-c.png') }}" alt="CreatorSpot" style="width:66px;height:66px;border-radius:6px;object-fit:contain;">CreatorSpot<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;margin-left:2px;">Beta</span></a>
</div>

<div class="wrap">
  <div class="doc">
    <a href="{{ route('welcome') }}" class="back">← Back to home</a>

    <h1>Privacy Policy</h1>
    <p class="updated">Last updated: 23.09.2026</p>

    <h2>1. What Data We Collect</h2>
    <ul>
      <li>Basic data: name, email, password (or your Google profile when signing in via Google).</li>
      <li>Profile data: phone, location (country/city), profile photo, bio, skills, portfolio, pricing.</li>
      <li>Content you create: project listings, applications, messages, reviews.</li>
      <li>Technical data: IP address (for automatic country detection at registration), browser language.</li>
    </ul>

    <h2>2. How We Use Your Data</h2>
    <ul>
      <li>To make the platform function (profiles, listings, applications, messages).</li>
      <li>To connect you with the right clients or creatives.</li>
      <li>To send notifications related to your activity (messages, verification, application status).</li>
      <li>To improve the quality and security of the platform.</li>
    </ul>

    <h2>3. Automatic Location Detection</h2>
    <p>At registration, we temporarily use your IP address to suggest a country in the form (via an external geo-lookup service). This is only a suggestion you can always change manually; your IP address is not shown publicly and is not stored permanently linked to your profile.</p>

    <h2>4. Cookies</h2>
    <p>We use essential cookies to maintain your session (login) and to remember your selected language. We don't use cookies for advertising tracking.</p>

    <h2>5. Google Sign-In</h2>
    <p>If you sign in via Google, we receive basic data from your Google profile (name, email, profile photo) with your permission, through Google's standard sign-in process. We don't get access to your Gmail inbox or other Google services.</p>

    <h2>6. Sharing With Third Parties</h2>
    <p>We don't sell your data. We only share it with services necessary for the platform to operate (e.g. an email service provider for sending notifications, a geo-lookup service at registration, and Paddle.com for processing Pro subscription payments), always limited to what's needed for that service.</p>

    <h2>7. Data Retention</h2>
    <p>We keep your data while your account is active. If you delete your account, personal data is removed or anonymized within a reasonable time, unless there's a legal obligation to retain it longer.</p>

    <h2>8. Your Rights</h2>
    <ul>
      <li>Access and correct your data directly through your profile settings.</li>
      <li>Request deletion of your account and data.</li>
      <li>Request a copy of the data we hold about you.</li>
    </ul>
    <p>To exercise these rights, contact us through our contact form.</p>

    <h2>9. Security</h2>
    <p>Passwords are stored encrypted. Access to data is limited to CreatorSpot staff who need it to operate the platform.</p>

    <h2>10. Changes to This Policy</h2>
    <p>We may update this policy from time to time. Significant changes will be posted on this page with a new "last updated" date.</p>

    <h2>11. Contact</h2>
    <p>For questions about privacy, reach out through our <a href="{{ route('contact.create') }}">contact form</a>.</p>
  </div>
</div>

</body>
</html>
