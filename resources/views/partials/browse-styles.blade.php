<style>
    .br-wrap{font-family:'Inter',sans-serif;padding:0 16px;}
    @media (min-width:640px){ .br-wrap{padding:0;} }

    .br-toolbar{display:flex;gap:12px;margin-bottom:16px;align-items:center;}
    .br-searchbox{flex:1;position:relative;display:flex;align-items:center;}
    .br-searchbox svg{position:absolute;left:14px;width:16px;height:16px;color:#9AA0AB;pointer-events:none;}
    .br-searchbox input{
        width:100%;padding:11px 14px 11px 40px;border:1px solid #E8EBF0;border-radius:12px;
        font-family:'Inter',sans-serif;font-size:14px;background:#fff;
    }
    .br-searchbox input:focus{outline:2px solid #0B6FE0;outline-offset:1px;}
    .br-filters-toggle{
        display:none;background:#fff;border:1px solid #E8EBF0;border-radius:12px;padding:11px 18px;
        font-weight:700;font-size:13.5px;color:#14171F;cursor:pointer;flex-shrink:0;font-family:'Inter',sans-serif;
    }

    .br-chips{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;}
    .br-chip{
        background:#fff;border:1px solid #E8EBF0;border-radius:999px;padding:7px 14px;font-size:12.5px;
        font-weight:700;color:#666B76;cursor:pointer;font-family:'Inter',sans-serif;transition:all .15s ease;
    }
    .br-chip:hover{border-color:#B8D3F7;}
    .br-chip.is-active{background:#EAF2FE;color:#0958B5;border-color:#0B6FE0;}
    .br-chip-sm{padding:5px 11px;font-size:11.5px;}

    .br-layout{display:grid;grid-template-columns:280px minmax(320px,1fr) minmax(340px,440px);
        grid-template-areas:"filters list details";gap:20px;align-items:start;}

    .br-filters{grid-area:filters;background:#fff;border:1px solid #E8EBF0;border-radius:16px;padding:18px;
        position:sticky;top:16px;}
    .br-filters-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;
        font-weight:800;font-size:13.5px;color:#14171F;}
    .br-reset{background:none;border:none;color:#0B6FE0;font-weight:700;font-size:12.5px;cursor:pointer;
        font-family:'Inter',sans-serif;padding:0;}
    .br-reset:hover{color:#0958B5;}
    .br-filter-group{margin-bottom:16px;}
    .br-filter-group label{display:block;font-size:12px;font-weight:700;color:#666B76;margin-bottom:6px;}
    .br-filter-group select,.br-filter-group input{
        width:100%;padding:9px 10px;border:1px solid #E8EBF0;border-radius:10px;font-family:'Inter',sans-serif;
        font-size:13px;background:#F6F8FB;
    }
    .br-budget-row{display:flex;align-items:center;gap:8px;}
    .br-budget-row span{color:#9AA0AB;}
    .br-toggle{display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:13px;
        font-weight:600;color:#14171F;cursor:pointer;}
    .br-switch{position:relative;display:inline-block;width:38px;height:22px;flex-shrink:0;}
    .br-switch input{position:absolute;opacity:0;width:100%;height:100%;margin:0;cursor:pointer;z-index:1;}
    .br-switch-track{position:absolute;inset:0;background:#E8EBF0;border-radius:999px;transition:background .15s ease;}
    .br-switch-track::before{content:'';position:absolute;top:2px;left:2px;width:18px;height:18px;
        background:#fff;border-radius:50%;transition:transform .15s ease;box-shadow:0 1px 2px rgba(20,23,31,.15);}
    .br-switch input:checked + .br-switch-track{background:#0B6FE0;}
    .br-switch input:checked + .br-switch-track::before{transform:translateX(16px);}

    .br-skill-group{margin-bottom:10px;}
    .br-skill-group-label{font-size:10.5px;font-weight:700;color:#9AA0AB;margin-bottom:6px;
        text-transform:uppercase;letter-spacing:.03em;}
    .br-skill-chips{display:flex;flex-wrap:wrap;gap:6px;}

    .br-list{grid-area:list;min-width:0;}
    .br-count{font-size:13px;font-weight:700;color:#666B76;margin-bottom:12px;}
    .br-empty{background:#fff;border:1px dashed #E8EBF0;border-radius:16px;padding:40px 20px;text-align:center;
        color:#9AA0AB;font-size:13.5px;}
    .br-rows{display:flex;flex-direction:column;gap:12px;}
    .br-row{
        display:block;width:100%;text-align:left;background:#fff;border:1px solid #E8EBF0;border-radius:16px;
        padding:16px 18px;cursor:pointer;font-family:'Inter',sans-serif;transition:all .15s ease;
    }
    .br-row:hover{border-color:#B8D3F7;box-shadow:0 4px 14px rgba(20,23,31,.06);}
    .br-row.is-active{border-color:#0B6FE0;background:#F8FBFF;box-shadow:0 0 0 1px #0B6FE0;}
    .br-row-top{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:4px;}
    .br-row-title{font-weight:700;font-size:14.5px;color:#14171F;margin:0;line-height:1.4;}
    .br-badge-new{display:inline-block;margin-left:6px;background:#E7F8F1;color:#17A673;font-size:10px;
        font-weight:800;letter-spacing:.03em;padding:2px 8px;border-radius:999px;vertical-align:middle;}
    .br-row-cats{font-size:12px;color:#666B76;margin:0 0 6px;}
    .br-row-cats-bold{font-size:12px;font-weight:700;color:#14171F;margin:0 0 6px;}
    .br-row-desc{font-size:13px;color:#666B76;line-height:1.5;margin:0 0 12px;
        display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
    .br-row-foot{display:flex;flex-wrap:wrap;gap:14px;align-items:center;padding-top:12px;border-top:1px solid #F1F3F6;}
    .br-budget{font-weight:700;font-size:13px;color:#14171F;}
    .br-row-location{font-size:12px;font-weight:700;color:#0B6FE0;}
    .br-row-time{font-size:12px;color:#9AA0AB;margin-left:auto;}

    .br-row-person{display:flex;gap:12px;align-items:flex-start;margin-bottom:4px;}
    .br-row-person-info{min-width:0;flex:1;}
    .br-verified{display:inline-flex;align-items:center;gap:4px;vertical-align:middle;margin-left:4px;
        font-size:11px;font-weight:700;color:#0095F6;white-space:nowrap;}
    .br-verified svg{width:15px;height:15px;flex-shrink:0;}
    .br-verified circle{fill:#0095F6;}

    .br-details{grid-area:details;background:#fff;border:1px solid #E8EBF0;border-radius:16px;padding:22px;
        position:sticky;top:16px;max-height:calc(100vh - 140px);overflow-y:auto;}
    .br-details-empty{color:#9AA0AB;font-size:13.5px;text-align:center;padding:60px 16px;}
    .br-details-head{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:4px;}
    .br-details-head h2{font-size:18px;font-weight:800;color:#14171F;margin:0;line-height:1.35;}
    .br-details-posted{font-size:12px;color:#9AA0AB;margin:0 0 16px;}
    .br-details-budget{font-size:22px;font-weight:800;color:#14171F;margin-bottom:12px;letter-spacing:-.01em;}
    .br-details-meta{display:flex;flex-direction:column;gap:6px;font-size:13px;color:#666B76;font-weight:600;
        margin-bottom:16px;}
    .br-pill-row{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:18px;}
    .br-section-title{font-size:13px;font-weight:800;color:#14171F;margin:0 0 8px;}
    .br-details-desc{font-size:13.5px;color:#666B76;line-height:1.6;white-space:pre-line;margin-bottom:20px;}

    .br-details-person{display:flex;gap:14px;align-items:center;margin-bottom:6px;}
    .br-details-person-info{min-width:0;}
    .br-details-person-info h2{margin-bottom:2px;}

    .br-actions{display:flex;gap:10px;margin-bottom:10px;}
    .br-btn-primary{
        flex:1;text-align:center;background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;border:none;
        border-radius:12px;padding:12px 16px;font-weight:700;font-size:13.5px;font-family:'Inter',sans-serif;
        box-shadow:0 1px 2px rgba(20,23,31,.05);transition:transform .15s ease;
    }
    .br-btn-primary:hover{transform:translateY(-1px);color:#fff;}
    .br-btn-bookmark{
        width:44px;flex-shrink:0;background:#fff;border:1px solid #E8EBF0;border-radius:12px;font-size:18px;
        color:#9AA0AB;cursor:pointer;
    }
    .br-btn-bookmark.is-saved{color:#F5A524;border-color:#F5A524;background:#FFFBEB;}
    .br-btn-secondary{
        width:100%;background:#fff;border:1px solid #E8EBF0;border-radius:12px;padding:11px 16px;
        font-weight:700;font-size:13.5px;color:#14171F;font-family:'Inter',sans-serif;cursor:pointer;
        margin-bottom:18px;
    }
    .br-btn-secondary:hover{background:#F6F8FB;}

    .br-client{display:flex;gap:12px;align-items:flex-start;padding-top:16px;border-top:1px solid #E8EBF0;}
    .br-client-info{min-width:0;}
    .br-client-name{display:block;font-weight:700;font-size:13.5px;color:#14171F;margin-bottom:4px;}
    .br-client-name:hover{color:#0B6FE0;}
    .br-client-sub{font-size:11.5px;color:#9AA0AB;margin:4px 0 0;}

    @media (max-width:1024px){
        .br-layout{grid-template-columns:280px 1fr;
            grid-template-areas:"filters list" "filters details";}
        .br-details{position:static;max-height:none;margin-top:0;}
    }

    @media (max-width:767px){
        .br-filters-toggle{display:inline-block;}
        .br-layout{grid-template-columns:1fr;grid-template-areas:"list" "details";}

        .br-filters-backdrop{
            position:fixed;inset:0;background:rgba(20,23,31,.5);z-index:59;
            opacity:0;pointer-events:none;transition:opacity .25s ease;
        }
        .br-filters-backdrop.is-open{opacity:1;pointer-events:auto;}

        .br-filters{
            position:fixed;left:0;right:0;bottom:0;top:auto;width:auto;max-height:50vh;
            border-radius:20px 20px 0 0;z-index:60;overflow-y:auto;
            padding-top:26px;box-shadow:0 -8px 30px rgba(20,23,31,.18);
            transform:translateY(100%);transition:transform .3s ease;
        }
        .br-filters::before{
            content:'';position:absolute;top:10px;left:50%;transform:translateX(-50%);
            width:40px;height:4px;border-radius:999px;background:#E8EBF0;
        }
        .br-filters.is-open{transform:translateY(0);}
    }
</style>
