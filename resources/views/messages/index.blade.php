<x-app-layout :title="__('Пораки')" :description="__('Твоите разговори на CreatorSpot.')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Пораки') }}</h2>
    </x-slot>

    <style>
      .msg-shell{
        display:flex;background:#fff;border:1px solid #E8EBF0;border-radius:16px;overflow:hidden;
        box-shadow:0 1px 2px rgba(20,23,31,.05);height:calc(100vh - 260px);min-height:420px;
      }
      .msg-sidebar{width:320px;flex-shrink:0;border-right:1px solid #E8EBF0;display:flex;flex-direction:column;}
      .msg-sidebar-search{padding:14px;border-bottom:1px solid #E8EBF0;}
      .msg-sidebar-search input{
        width:100%;padding:9px 12px;border:1px solid #E8EBF0;border-radius:10px;font-family:'Inter',sans-serif;
        font-size:16px;background:#F6F8FB;
      }
      .msg-sidebar-search input:focus{outline:2px solid #0B6FE0;outline-offset:1px;background:#fff;}
      .msg-list{flex:1;overflow-y:auto;}
      .msg-list-item{
        display:flex;align-items:center;gap:10px;padding:14px;border-bottom:1px solid #F6F8FB;cursor:pointer;
        transition:background .15s ease;text-align:left;width:100%;background:none;border:none;border-left:3px solid transparent;
      }
      .msg-list-item:hover{background:#F6F8FB;}
      .msg-list-item.active{background:#EAF2FE;border-left-color:#0B6FE0;}
      .msg-list-name{font-weight:700;font-size:13.5px;color:#14171F;margin:0;}
      .msg-list-preview{font-size:12.5px;color:#666B76;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;margin:0;}
      .msg-list-meta{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;}
      .msg-list-time{font-size:11px;color:#9AA0AB;white-space:nowrap;}
      .msg-list-dot{width:9px;height:9px;border-radius:50%;background:#0B6FE0;}
      .msg-empty-list{padding:32px 20px;text-align:center;color:#9AA0AB;font-size:13.5px;}

      .msg-chat{flex:1;display:flex;flex-direction:column;min-width:0;}
      .msg-chat-header{display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid #E8EBF0;}
      .msg-chat-header .back-btn{display:none;background:none;border:none;font-size:18px;color:#666B76;cursor:pointer;margin-right:4px;padding:0;}
      .msg-chat-name{font-weight:700;font-size:14.5px;color:#14171F;margin:0;}
      .msg-chat-role{font-size:12px;color:#666B76;margin:0;}
      .msg-chat-role a{color:#0B6FE0;font-weight:600;}

      .msg-project-banner{display:flex;align-items:center;gap:12px;padding:14px 20px;background:#F6F8FB;
        border-bottom:1px solid #E8EBF0;}
      .msg-project-icon{width:34px;height:34px;border-radius:9px;background:#fff;border:1px solid #E8EBF0;
        display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;}
      .msg-project-title{font-weight:700;font-size:13.5px;color:#14171F;margin:0;}
      .msg-project-meta{font-size:12px;color:#666B76;margin:0;}
      .msg-project-link{font-size:12.5px;font-weight:700;color:#0B6FE0;white-space:nowrap;flex-shrink:0;}
      .msg-project-link:hover{color:#0958B5;}

      .msg-proposal-actions{display:flex;align-items:center;justify-content:space-between;gap:12px;
        padding:12px 20px;background:#FFFBEB;border-bottom:1px solid #FDE68A;}
      .msg-proposal-label{font-size:11.5px;color:#92400E;font-weight:700;text-transform:uppercase;
        letter-spacing:0.03em;margin:0;}
      .msg-proposal-price{font-size:14.5px;font-weight:800;color:#14171F;margin:0;}
      .msg-proposal-buttons{display:flex;gap:8px;flex-shrink:0;}
      .msg-btn-accept, .msg-btn-reject{border:none;border-radius:8px;padding:8px 16px;font-weight:700;
        font-size:12.5px;font-family:'Inter',sans-serif;cursor:pointer;transition:all .15s ease;}
      .msg-btn-accept{background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;}
      .msg-btn-accept:hover{transform:translateY(-1px);}
      .msg-btn-reject{background:#fff;color:#DC2626;border:1px solid #FCA5A5;}
      .msg-btn-reject:hover{background:#FEF2F2;}

      .msg-thread{flex:1;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:10px;background:#FBFCFE;}
      .msg-date-sep{text-align:center;font-size:11.5px;color:#9AA0AB;font-weight:600;margin:8px 0;text-transform:uppercase;letter-spacing:0.03em;}
      .msg-system{align-self:center;background:#E7F8F1;color:#17A673;font-size:12.5px;font-weight:600;
        padding:8px 16px;border-radius:999px;text-align:center;max-width:85%;margin:6px 0;}
      .msg-row{display:flex;gap:8px;align-items:flex-end;}
      .msg-row.mine{justify-content:flex-end;}
      .msg-bubble{max-width:70%;padding:10px 14px;border-radius:14px;font-size:13.5px;line-height:1.5;}
      .msg-row.theirs .msg-bubble{background:#F1F3F6;color:#14171F;border-bottom-left-radius:4px;}
      .msg-row.mine .msg-bubble{background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;border-bottom-right-radius:4px;}
      .msg-bubble p{margin:0;}
      .msg-bubble-time{font-size:10.5px;margin-top:4px;opacity:.7;}

      .msg-empty-chat{flex:1;display:flex;align-items:center;justify-content:center;color:#9AA0AB;font-size:14px;text-align:center;padding:40px;}

      .msg-compose{border-top:1px solid #E8EBF0;padding:14px 20px;display:flex;gap:10px;align-items:flex-end;
        position:sticky;bottom:0;background:#fff;}
      .msg-compose textarea{
        flex:1;resize:none;border:1px solid #E8EBF0;border-radius:12px;padding:10px 14px;font-family:'Inter',sans-serif;
        font-size:16px;background:#F6F8FB;max-height:120px;
      }
      .msg-compose textarea:focus{outline:none;border-color:#0B6FE0;background:#fff;}
      .msg-send-btn{
        background:linear-gradient(135deg,#2D82E8,#0958B5);color:#fff;border:none;border-radius:10px;
        padding:10px 20px;font-weight:700;font-size:13.5px;font-family:'Inter',sans-serif;cursor:pointer;
        box-shadow:0 1px 2px rgba(20,23,31,.05);flex-shrink:0;
      }
      .msg-send-btn:hover{transform:translateY(-1px);}

      @media(max-width:767px){
        .msg-shell{height:calc(100vh - 220px);}
        .msg-sidebar{width:100%;}
        .msg-sidebar.has-selected{display:none;}
        .msg-chat{display:none;}
        .msg-chat.is-active{display:flex;}
        .msg-chat-header .back-btn{display:inline-block;}
      }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:message-inbox :selected-conversation-id="$conversation?->id" />
        </div>
    </div>
</x-app-layout>
