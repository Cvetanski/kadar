<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  .kf-wrap{
    font-family:'Inter', sans-serif;
    background-image:radial-gradient(circle, rgba(20,23,31,0.07) 1.2px, transparent 1.2px);
    background-size:20px 20px;
    border-radius:20px;
    padding:20px 16px;
    margin:-24px 0 0;
    max-width:100%;
    overflow-x:hidden;
  }
  @media(min-width:640px){ .kf-wrap{ padding:32px; margin:-24px -24px 0; } }

  .kf-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:32px;}
  @media(min-width:768px){ .kf-stats{grid-template-columns:repeat(4,1fr);} }
  .kf-stat{background:#fff;border:1px solid #E8EBF0;border-radius:16px;padding:18px 20px;
    box-shadow:0 1px 2px rgba(20,23,31,.05), 0 1px 1px rgba(20,23,31,.04);}
  .kf-stat .kf-num{font-size:26px;font-weight:800;color:#14171F;letter-spacing:-0.02em;line-height:1.1;}
  .kf-stat .kf-num.kf-text{font-size:15px;line-height:1.3;}
  .kf-stat .kf-label{font-size:12.5px;color:#666B76;margin-top:6px;font-weight:600;}
  .kf-stat.kf-accent .kf-num{color:#0B6FE0;}
  .kf-stat.kf-verified .kf-num{color:#17A673;}
  .kf-stat.kf-pending .kf-num{color:#F5A524;}

  .kf-section{margin-bottom:32px;}
  .kf-section-title{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
  .kf-section-title h3{font-size:18px;font-weight:800;letter-spacing:-0.01em;color:#14171F;margin:0;}
  .kf-section-title a{font-size:13.5px;font-weight:700;color:#0B6FE0;white-space:nowrap;}
  .kf-section-title a:hover{color:#0958B5;}

  .kf-grid{display:grid;grid-template-columns:1fr;gap:14px;}
  @media(min-width:768px){ .kf-grid{grid-template-columns:repeat(2,1fr);} }
  @media(min-width:1100px){ .kf-grid{grid-template-columns:repeat(3,1fr);} }

  .kf-card{background:#fff;border:1px solid #E8EBF0;border-radius:16px;padding:20px;
    box-shadow:0 1px 2px rgba(20,23,31,.05);transition:transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    display:block;color:inherit;}
  .kf-card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(20,23,31,.08), 0 2px 6px rgba(20,23,31,.04);border-color:#B8D3F7;}

  .kf-onboarding-reminder{display:flex;align-items:center;gap:16px;margin-bottom:24px;padding:18px 20px;border-radius:16px;
    background:linear-gradient(135deg,#EAF2FE,#F6FAFF);border:1px solid #CFE2FB;
    box-shadow:0 1px 2px rgba(20,23,31,.05);}
  .kf-onboarding-reminder-icon{flex-shrink:0;width:44px;height:44px;border-radius:12px;background:#fff;
    display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 1px 2px rgba(20,23,31,.08);}
  .kf-onboarding-reminder-body{flex:1;min-width:0;}
  .kf-onboarding-reminder-title{font-weight:700;font-size:14.5px;color:#14171F;margin-bottom:2px;}
  .kf-onboarding-reminder-desc{font-size:12.5px;color:#3A5A85;line-height:1.5;}
  @media(max-width:640px){
    .kf-onboarding-reminder{flex-wrap:wrap;}
    .kf-onboarding-reminder-body{flex-basis:100%;order:2;}
    .kf-onboarding-reminder a.kf-btn{order:3;flex-basis:100%;text-align:center;}
  }

  .kf-cat-icon{width:36px;height:36px;border-radius:10px;background:#F6F8FB;display:flex;align-items:center;
    justify-content:center;font-size:16px;margin-bottom:12px;}
  .kf-card-title{font-weight:700;font-size:15px;color:#14171F;margin-bottom:4px;line-height:1.35;}
  .kf-card-meta{font-size:12.5px;color:#666B76;margin-bottom:6px;}
  .kf-card-location{display:flex;align-items:center;gap:4px;font-size:12.5px;font-weight:700;color:#0B6FE0;margin-bottom:10px;}
  .kf-card-desc{font-size:13.5px;color:#666B76;line-height:1.55;margin-bottom:14px;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}

  .kf-card-foot{display:flex;justify-content:space-between;align-items:center;padding-top:14px;border-top:1px solid #E8EBF0;}
  .kf-budget{font-weight:700;font-size:13.5px;color:#14171F;}

  .kf-posted-by{display:flex;align-items:center;gap:6px;margin-bottom:14px;font-size:12.5px;color:#666B76;font-weight:600;}

  .kf-pill{display:inline-flex;align-items:center;gap:6px;background:#EAF2FE;color:#0958B5;font-size:11.5px;
    font-weight:700;padding:5px 11px;border-radius:999px;}

  .kf-status{font-size:11.5px;font-weight:700;padding:4px 10px;border-radius:999px;white-space:nowrap;}
  .kf-status-pending{background:#FEF3C7;color:#B45309;}
  .kf-status-accepted{background:#E7F8F1;color:#17A673;}
  .kf-status-rejected{background:#FEE2E2;color:#DC2626;}
  .kf-status-withdrawn{background:#F3F4F6;color:#666B76;}
  .kf-status-open{background:#E7F8F1;color:#17A673;}
  .kf-status-in_progress{background:#EAF2FE;color:#0958B5;}
  .kf-status-completed{background:#F3F4F6;color:#666B76;}
  .kf-status-cancelled{background:#FEE2E2;color:#DC2626;}

  .kf-filter-bar{display:flex;flex-wrap:wrap;align-items:end;gap:12px;margin-bottom:24px;background:#fff;
    border:1px solid #E8EBF0;border-radius:16px;padding:18px 20px;}
  .kf-filter-bar label{display:block;font-size:12px;font-weight:700;color:#666B76;margin-bottom:6px;}
  .kf-filter-bar select, .kf-filter-bar input[type=number], .kf-filter-bar input[type=text]{
    padding:9px 12px;border:1px solid #E8EBF0;border-radius:10px;font-family:'Inter',sans-serif;
    font-size:13.5px;background:#F6F8FB;min-width:120px;}
  .kf-filter-bar select:focus, .kf-filter-bar input:focus{outline:2px solid #0B6FE0;outline-offset:1px;}
  @media(max-width:767px){
    .kf-filter-bar select, .kf-filter-bar input[type=number], .kf-filter-bar input[type=text]{font-size:16px;}
  }
  .kf-btn{background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;border:none;border-radius:10px;
    padding:10px 20px;font-weight:700;font-size:13.5px;font-family:'Inter',sans-serif;cursor:pointer;
    box-shadow:0 1px 2px rgba(20,23,31,.05);}
  .kf-btn:hover{transform:translateY(-1px);}
  .kf-check{display:flex;align-items:center;gap:8px;font-size:13px;color:#666B76;font-weight:600;padding-bottom:9px;cursor:pointer;}
  .kf-check input[type=checkbox]{width:16px;height:16px;flex-shrink:0;accent-color:#0B6FE0;}
  .kf-clear{display:inline-flex;align-items:center;font-size:13px;color:#666B76;font-weight:600;
    padding:10px 16px;border:1px solid #E8EBF0;border-radius:10px;background:#fff;
    text-decoration:none;transition:all .15s ease;}
  .kf-clear:hover{background:#F6F8FB;border-color:#B8D3F7;color:#14171F;}

  .kf-empty{text-align:center;padding:48px 20px;border:1.5px dashed #E8EBF0;border-radius:16px;color:#9AA0AB;
    font-size:14px;background:#fff;}

  .kf-pagination{margin-top:24px;}
  .kf-pagination nav{font-family:'Inter',sans-serif;}

  /* avatar upload */
  .kf-avatar-circle{width:96px;height:96px;border-radius:50%;background:#F6F8FB;border:2px dashed #E8EBF0;
    display:flex;align-items:center;justify-content:center;position:relative;cursor:pointer;
    overflow:hidden;transition:border-color .15s ease;flex-shrink:0;}
  .kf-avatar-circle:hover{border-color:#0B6FE0;}
  .kf-avatar-circle.has-image{border:2px solid #E8EBF0;}
  .kf-avatar-circle img{width:100%;height:100%;object-fit:cover;}
  .kf-avatar-circle .kf-avatar-placeholder{font-size:28px;color:#9AA0AB;}
  .kf-avatar-cam{position:absolute;bottom:0;right:0;width:28px;height:28px;border-radius:50%;
    background:linear-gradient(135deg,#2D82E8,#0958B5);display:flex;align-items:center;justify-content:center;
    font-size:12px;color:#fff;border:3px solid #fff;}

  /* form fields */
  .kf-form label{display:block;font-size:13px;font-weight:700;color:#14171F;margin-bottom:6px;}
  .kf-form input[type=text], .kf-form input[type=number], .kf-form input[type=url], .kf-form textarea{
    width:100%;padding:11px 14px;border:1px solid #E8EBF0;border-radius:10px;font-family:'Inter',sans-serif;
    font-size:14.5px;background:#F6F8FB;transition:border-color .15s ease;}
  .kf-form input:focus, .kf-form textarea:focus{outline:none;border-color:#0B6FE0;background:#fff;}
  @media(max-width:767px){
    .kf-form input[type=text], .kf-form input[type=number], .kf-form input[type=url], .kf-form textarea{font-size:16px;}
  }
  .kf-form textarea{resize:vertical;min-height:100px;}
  .kf-field{margin-bottom:20px;}
  .kf-two-col{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;}
  .kf-three-col{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;}
  @media(max-width:640px){ .kf-three-col{grid-template-columns:1fr;} }
  .kf-checkbox-row{display:flex;align-items:center;gap:8px;font-size:14px;color:#666B76;font-weight:600;cursor:pointer;}
  .kf-checkbox-row input[type=checkbox]{width:16px;height:16px;flex-shrink:0;accent-color:#0B6FE0;}
  .kf-form-actions{display:flex;gap:12px;margin-top:8px;}

  /* tile/pill checkbox selectors — CSS-only, no JS */
  .kf-tile-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px;}
  .kf-tile-checkbox{position:absolute;opacity:0;width:0;height:0;}
  .kf-tile-label{display:block;cursor:pointer;}
  .kf-tile{border:1.5px solid #E8EBF0;border-radius:12px;padding:14px;transition:all .15s ease;}
  .kf-tile-checkbox:checked + .kf-tile{border-color:#0B6FE0;background:#EAF2FE;}
  .kf-tile .kf-tile-icon{font-size:18px;margin-bottom:6px;}
  .kf-tile .kf-tile-name{font-weight:700;font-size:13.5px;color:#14171F;}

  .kf-pill-row{display:flex;flex-wrap:wrap;gap:8px;}
  .kf-pill-checkbox{position:absolute;opacity:0;width:0;height:0;}
  .kf-pill-label{cursor:pointer;}
  .kf-pill-btn{border:1.5px solid #E8EBF0;border-radius:999px;padding:7px 15px;font-size:13px;font-weight:600;
    color:#666B76;transition:all .15s ease;display:inline-block;}
  .kf-pill-checkbox:checked + .kf-pill-btn{background:#0B6FE0;border-color:#0B6FE0;color:#fff;}

  /* social — edit inputs */
  .kf-social-row{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
  .kf-social-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;
    font-size:16px;flex-shrink:0;color:#fff;}
  .kf-social-icon.kf-instagram{background:linear-gradient(135deg,#F58529,#DD2A7B,#8134AF);}
  .kf-social-icon.kf-facebook{background:#1877F2;}
  .kf-social-icon.kf-website{background:linear-gradient(135deg,#0B6FE0,#0958B5);}
  .kf-social-row input{flex:1;margin:0;}

  /* social — display pills on public profile */
  .kf-social-links{display:flex;flex-wrap:wrap;gap:10px;}
  .kf-social-link{display:inline-flex;align-items:center;gap:8px;background:#F6F8FB;border:1px solid #E8EBF0;
    border-radius:999px;padding:8px 16px;font-size:13px;font-weight:600;color:#14171F;transition:all .15s ease;}
  .kf-social-link:hover{border-color:#B8D3F7;background:#EAF2FE;}
</style>
