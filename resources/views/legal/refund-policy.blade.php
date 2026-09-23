<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Refund Policy — CreatorSpot</title>
<meta name="description" content="CreatorSpot's refund policy for Pro subscription payments processed by Paddle.com.">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:title" content="Refund Policy — CreatorSpot">
<meta property="og:description" content="CreatorSpot's refund policy for Pro subscription payments processed by Paddle.com.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="CreatorSpot">
<meta property="og:image" content="{{ asset('images/shareImage.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Refund Policy — CreatorSpot">
<meta name="twitter:description" content="CreatorSpot's refund policy for Pro subscription payments processed by Paddle.com.">
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
  <a href="{{ route('welcome') }}" class="logo"><img src="{{ asset('images/logo2.svg') }}" alt="CreatorSpot" style="width:66px;height:66px;border-radius:6px;object-fit:contain;">CreatorSpot<span style="background:transparent;color:#D6249F;font-size:9px;font-weight:800;letter-spacing:0.06em;padding:2px 11px;border-radius:999px;text-transform:uppercase;border:1px solid #D6249F;margin-left:2px;">Beta</span></a>
</div>

<div class="wrap">
  <div class="doc">
    <a href="{{ route('welcome') }}" class="back">← Back to home</a>

    <h1>Refund Policy</h1>
    <p class="updated">Last updated: 23.09.2026</p>

    <h2>1. Who processes your payment</h2>
    <p>CreatorSpot Pro subscription payments are processed by our payment provider, Paddle.com Market Limited ("Paddle"), which acts as the Merchant of Record for all purchases. Paddle handles billing, invoicing, sales tax/VAT, and payment processing on our behalf, and your payment method will show a charge from Paddle rather than directly from CreatorSpot.</p>

    <h2>2. Subscription billing</h2>
    <p>Pro subscriptions are billed in advance for each billing period (monthly or annual) and renew automatically until cancelled. You can cancel anytime from your Billing page — cancelling stops future renewals but does not end your current billing period early; you keep full Pro access until the end of the period you already paid for.</p>

    <h2>3. Our refund approach</h2>
    <p>Because cancelling already prevents any further charges and you retain access through the end of your paid period, we do not offer prorated or partial refunds for unused time within a billing period. That said, we review the following situations on a case-by-case basis and may issue a refund at our discretion:</p>
    <ul>
      <li>A duplicate or accidental charge caused by a technical error.</li>
      <li>A charge that occurred after you had already successfully cancelled your subscription.</li>
      <li>A clear billing mistake on our end (incorrect amount, wrong plan charged, etc.).</li>
    </ul>

    <h2>4. How to request a refund</h2>
    <p>To request a refund, contact us through our <a href="{{ route('contact.create') }}">contact form</a> within 14 days of the charge in question, including the email address on your account and the approximate date of the charge. We'll review your request and respond as quickly as we can. Approved refunds are issued by Paddle back to your original payment method and may take a few business days to appear, depending on your bank or card provider.</p>

    <h2>5. Free plan</h2>
    <p>CreatorSpot's free plan does not require payment, so this refund policy only applies to paid Pro subscriptions.</p>

    <h2>6. Changes to this policy</h2>
    <p>We may update this refund policy from time to time. Significant changes will be reflected on this page with a new "last updated" date.</p>

    <h2>7. Contact</h2>
    <p>For questions about billing or refunds, reach out through our <a href="{{ route('contact.create') }}">contact form</a>.</p>
  </div>
</div>

</body>
</html>
