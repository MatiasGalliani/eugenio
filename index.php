<!-- Eugenio Chat Widget (site-wide) -->
<style>
  /* === styles from your file (unchanged, trimmed only by removing <html><head> wrappers) === */
  *{margin:0;padding:0;box-sizing:border-box}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,sans-serif;background:transparent;color:#0f172a}
  .chat-widget{position:fixed;bottom:30px;right:20px;z-index:100000}
  .chat-toggle{background:#fff;color:#0f172a;border:1px solid #e5e7eb;border-radius:9999px;padding:14px 22px;min-height:52px;cursor:pointer;box-shadow:0 6px 20px rgba(15,23,42,.06);display:flex;align-items:center;justify-content:center;gap:10px;transition:transform .2s,border-color .2s,box-shadow .2s;position:relative}
  .chat-toggle:hover{transform:translateY(-2px);border-color:#cbd5e1;box-shadow:0 10px 28px rgba(15,23,42,.08)}
  .chat-toggle svg{width:24px;height:24px;fill:#0f172a}
  .chat-toggle .toggle-logo{height:32px;width:auto;display:block}
  .chat-toggle.active{background:#fff;box-shadow:0 8px 28px rgba(15,23,42,.12)}
  .chat-toggle.active:hover{box-shadow:0 12px 32px rgba(15,23,42,.14)}
  .notification-badge{position:absolute;top:-5px;right:-5px;width:20px;height:20px;background:#e74c3c;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;color:#fff;font-weight:700;border:2px solid #fff}
  .chat-window{position:absolute;bottom:80px;right:0;width:392px;height:clamp(420px,80vh,680px);background:#fff;border-radius:16px;box-shadow:0 16px 48px rgba(15,23,42,.12);border:1px solid #e5e7eb;display:flex;flex-direction:column;overflow:hidden;transform:translateY(16px) scale(.98);opacity:0;pointer-events:none;transition:transform 220ms ease,opacity 220ms ease}
  .chat-window.active{transform:translateY(0) scale(1);opacity:1;pointer-events:all}
  @media (prefers-reduced-motion: reduce){.chat-window,.chat-toggle,.option-button,.submit-button,.email-input,.text-input,.message-input,.close-btn{transition:none}.chat-window.active{transform:translateY(0) scale(1);opacity:1}.chat-toggle:hover,.option-button:hover,.submit-button:hover{transform:none}}
  .chat-header{background:#fff;color:#0f172a;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e5e7eb}
  .header-logo{height:48px;width:auto;display:block}
  .close-btn{background:#fff;border:1px solid #e5e7eb;color:#0f172a;font-size:18px;cursor:pointer;padding:0;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;transition:background .15s,border-color .15s}
  .close-btn:hover{background:#f8fafc;border-color:#cbd5e1}
  .chat-messages{flex:1;padding:20px;overflow-y:auto;background:#fff}
  .message{margin-bottom:15px;display:flex;flex-direction:column}
  .message.user{align-items:flex-end}
  .message.bot{align-items:flex-start}
  .message-bubble{max-width:85%;padding:12px 16px;border-radius:14px;word-wrap:break-word;transition:background .2s,color .2s,box-shadow .2s,border-color .2s;border:1px solid transparent}
  .message-bubble.has-form{max-width:calc(100% - 40px)}
  .message.user .message-bubble{background:#0f172a;color:#fff;border-bottom-right-radius:8px}
  .message.bot .message-bubble{background:#f3f4f6;color:#0f172a;border:1px solid #e5e7eb;box-shadow:none;border-bottom-left-radius:8px}
  .options-container{display:flex;flex-direction:column;gap:8px;margin-top:12px}
  .option-button{background:#fff;border:1px solid #e5e7eb;color:#0f172a;padding:10px 14px;border-radius:12px;cursor:pointer;font-size:14px;text-align:left;transition:transform .15s,background .15s,color .15s,border-color .15s;display:flex;align-items:center;gap:8px}
  .option-button:hover{background:#f8fafc;color:#0f172a;transform:translateX(4px);border-color:#cbd5e1}
  .option-button:active{transform:translateX(5px) scale(.98)}
  .email-input-form{margin-top:12px;display:flex;flex-direction:column;gap:10px;width:100%}
  .email-input,.text-input,.message-input{padding:14px 16px;border:1px solid #e5e7eb;border-radius:10px;font-size:14px;background:#fff;color:#0f172a;width:100%;outline:none;transition:border-color .15s,box-shadow .15s}
  .email-input:focus,.text-input:focus,.message-input:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.15)}
  .message-input{resize:vertical;min-height:100px;font-family:inherit}
  .consent-container{display:flex;align-items:flex-start;gap:8px;margin-top:8px}
  .consent-checkbox{margin-top:3px;flex-shrink:0}
  .consent-text{font-size:12px;color:#64748b;line-height:1.4}
  .consent-text a{color:#2563eb;text-decoration:underline}
  .error-message{color:#e74c3c;font-size:12px;margin-top:5px}
  .submit-button{background:#0f172a;border:1px solid #0f172a;color:#fff;padding:10px 18px;border-radius:9999px;cursor:pointer;font-size:14px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:8px;transition:transform .15s,box-shadow .15s,background .15s}
  .submit-button:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 6px 16px rgba(15,23,42,.15);background:#111827}
  .submit-button:active:not(:disabled){transform:scale(.98)}
  .submit-button:disabled{opacity:.6;cursor:not-allowed}
  .loader{display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,.3);border-radius:50%;border-top-color:#fff;animation:spin .8s linear infinite}
  @keyframes spin{to{transform:rotate(360deg)}}
  @media (max-width:480px){.chat-window{width:calc(100vw - 40px);height:clamp(420px,80vh,680px);bottom:80px;right:20px;border-radius:16px}.chat-toggle{padding:10px 16px;border-radius:9999px}.email-input-form{width:100%}.message-bubble.has-form{max-width:calc(100% - 40px)}}
</style>

<div class="chat-widget">
  <div class="chat-window" id="chatWindow" role="dialog" aria-modal="true" aria-live="polite">
    <div class="chat-header">
      <div>
        <img src="https://creditplan.it/wp-content/uploads/2025/10/Eugenio.svg" alt="€ugenio" class="header-logo" aria-hidden="true" />
      </div>
      <button class="close-btn" id="closeChat" aria-label="Chiudi chat">×</button>
    </div>
    <div class="chat-messages" id="chatMessages" role="log" aria-live="polite" aria-atomic="false"></div>
    <div class="chat-input-container" hidden>
      <input type="text" class="chat-input" id="chatInput" placeholder="Scrivi un messaggio..." />
      <button class="send-btn" id="sendBtn" aria-label="Invia">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
      </button>
    </div>
  </div>
  <button class="chat-toggle" id="chatToggle" aria-expanded="false" aria-controls="chatWindow" aria-label="Apri chat">
    <img src="https://creditplan.it/wp-content/uploads/2025/10/Eugenio.svg" alt="€ugenio" class="toggle-logo" aria-hidden="true" />
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/></svg>
    <span class="notification-badge" id="notificationBadge" style="display:none" aria-hidden="true">1</span>
  </button>
</div>

<script>
  // ===== Your JS (unchanged except API_URL) =====
  const chatToggle = document.getElementById('chatToggle');
  const chatWindow = document.getElementById('chatWindow');
  const closeChat = document.getElementById('closeChat');
  const chatMessages = document.getElementById('chatMessages');
  const notificationBadge = document.getElementById('notificationBadge');

  let isOpen = false;
  let conversationState = { branch: null, subOption: null, data: {} };
  let lastSubmitTime = 0;
  const SUBMIT_THROTTLE_MS = 2000;
  const COMPANY_NAME = 'Creditplan';

  // Backend API URL - use production URL or localhost based on environment
  const API_URL = (() => {
    const { hostname } = window.location;
    if (hostname === 'localhost' || hostname === '127.0.0.1') {
      return 'http://localhost:3000/api/leads';
    }
    return 'https://eugenio-production.up.railway.app/api/leads';
  })();

  const STORAGE_KEY = 'eugenio_conversation_state';
  function saveState(){ try{ sessionStorage.setItem(STORAGE_KEY, JSON.stringify(conversationState)); }catch(e){} }
  function loadState(){ try{ const v=sessionStorage.getItem(STORAGE_KEY); if(v){ conversationState=JSON.parse(v); return true; } }catch(e){} return false; }
  function clearState(){ try{ sessionStorage.removeItem(STORAGE_KEY); }catch(e){} conversationState={ branch:null, subOption:null, data:{} }; }

  let previouslyFocusedElement = null;
  function trapFocus(event){
    if(!isOpen || !chatWindow.classList.contains('active')) return;
    const sel = 'button:not(:disabled), input:not(:disabled), textarea:not(:disabled), a[href], [tabindex]:not([tabindex="-1"])';
    const nodes = Array.from(chatWindow.querySelectorAll(sel));
    if(!nodes.length) return;
    const first = nodes[0], last = nodes[nodes.length-1];
    if(event.key === 'Tab'){
      if(event.shiftKey && document.activeElement === first){ event.preventDefault(); last.focus(); }
      else if(!event.shiftKey && document.activeElement === last){ event.preventDefault(); first.focus(); }
    } else if(event.key === 'Escape'){ toggleChat(); }
  }
  function openFocusTrap(){
    previouslyFocusedElement = document.activeElement;
    document.addEventListener('keydown', trapFocus);
    setTimeout(()=>{
      const first = chatWindow.querySelector('button, input, textarea, a[href]');
      if(first) first.focus(); else chatToggle.focus();
    }, 100);
  }
  function closeFocusTrap(){
    document.removeEventListener('keydown', trapFocus);
    if(previouslyFocusedElement && previouslyFocusedElement.focus) previouslyFocusedElement.focus();
  }

  function toggleChat(){
    isOpen = !isOpen;
    chatToggle.classList.toggle('active', isOpen);
    chatWindow.classList.toggle('active', isOpen);
    chatToggle.setAttribute('aria-expanded', String(isOpen));
    if(isOpen){
      openFocusTrap();
      notificationBadge.style.display = 'none';
      if(chatMessages.children.length === 0){
        const had = loadState();
        if(had && conversationState.branch){ restoreConversation(); } else { showWelcomeMessage(); }
      }
    } else { closeFocusTrap(); }
  }
  document.addEventListener('keydown', e => { if(e.key === 'Escape' && isOpen) toggleChat(); });

  function addMessage(text, isUser=false, showOptions=false, options=[]){
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${isUser ? 'user' : 'bot'}`;
    const bubble = document.createElement('div');
    bubble.className = 'message-bubble';
    bubble.textContent = text;
    messageDiv.appendChild(bubble);
    if(showOptions && options.length){
      const optionsContainer = document.createElement('div');
      optionsContainer.className = 'options-container';
      options.forEach(option => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'option-button';
        button.textContent = option.text;
        button.onclick = () => handleOptionClick(option.value, option.text);
        optionsContainer.appendChild(button);
      });
      bubble.appendChild(optionsContainer);
    }
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function restoreConversation(){ showWelcomeMessage(); }

  function showWelcomeMessage(){
    const welcomeText = `Ciao 👋! Sono €ugenio, l'assistente virtuale di ${COMPANY_NAME}. In cosa posso aiutarti oggi?`;
    const options = [
      { text: '💰 Mutui e prestiti', value: 'mutui_prestiti' },
      { text: '🏦 Cessioni del quinto', value: 'cessioni_quinto' },
      { text: '🚗 Leasing e finanziamenti auto', value: 'leasing_auto' },
      { text: '🏠 Ristrutturazione e liquidità', value: 'ristrutturazione' },
      { text: '🛡️ Assicurazioni', value: 'assicurazioni' },
      { text: 'ℹ️ Altro / Voglio parlare con un consulente', value: 'altro' }
    ];
    addMessage(welcomeText, false, true, options);
  }

  function handleOptionClick(value, text){
    addMessage(text, true);
    switch(value){
      case 'mutui_prestiti': conversationState.branch='mutui_prestiti'; saveState(); showMutuiPrestitiOptions(); break;
      case 'cessioni_quinto': conversationState.branch='cessioni_quinto'; saveState(); showCessioniQuintoOptions(); break;
      case 'leasing_auto': conversationState.branch='leasing_auto'; saveState(); showLeasingAutoOptions(); break;
      case 'ristrutturazione': conversationState.branch='ristrutturazione'; saveState(); showRistrutturazioneOptions(); break;
      case 'assicurazioni': conversationState.branch='assicurazioni'; saveState(); showAssicurazioniOptions(); break;
      case 'altro': saveState(); showAltroMessage(); break;
      default: handleBranchSpecificOptions(value, text);
    }
  }

  function showMutuiPrestitiOptions(){
    const options = [
      { text: '🏠 Mutuo prima casa', value: 'mutuo_prima_casa' },
      { text: '🔁 Surroga o sostituzione mutuo', value: 'surroga_mutuo' },
      { text: '💸 Prestito personale', value: 'prestito_personale' },
      { text: '🏢 Mutuo per investimento o seconda casa', value: 'mutuo_investimento' }
    ];
    setTimeout(()=>addMessage('Perfetto. Quale soluzione ti interessa?', false, true, options), 500);
  }

  function showCessioniQuintoOptions(){
    const options = [
      { text: '👨‍💼 Dipendente pubblico', value: 'dipendente_pubblico' },
      { text: '🏭 Dipendente privato', value: 'dipendente_privato' },
      { text: '👴 Pensionato', value: 'pensionato' }
    ];
    setTimeout(()=>addMessage('Perfetto. Qual è la tua situazione?', false, true, options), 500);
  }

  function showLeasingAutoOptions(){
    const options = [
      { text: '🚗 Privati', value: 'privati' },
      { text: '🚙 Aziende / Partite IVA', value: 'aziende' }
    ];
    setTimeout(()=>addMessage('Perfetto. Per chi è il finanziamento?', false, true, options), 500);
  }

  function showRistrutturazioneOptions(){
    const options = [
      { text: '🏗️ Prestito per ristrutturazione casa', value: 'ristrutturazione_casa' },
      { text: '💰 Consolidamento debiti', value: 'consolidamento' },
      { text: '🔄 Liquidità su immobile di proprietà', value: 'liquidita_immobile' }
    ];
    setTimeout(()=>addMessage('Quale soluzione ti interessa?', false, true, options), 500);
  }

  function showAssicurazioniOptions(){
    const options = [
      { text: '🚗 Auto / Moto', value: 'assicurazione_auto' },
      { text: '🏡 Casa e famiglia', value: 'assicurazione_casa' },
      { text: '👨‍👩‍👧‍👦 Vita / Infortuni', value: 'assicurazione_vita' },
      { text: '👔 Professionale (RC, polizze aziendali)', value: 'assicurazione_professionale' }
    ];
    setTimeout(()=>addMessage('Quale tipo di assicurazione ti interessa?', false, true, options), 500);
  }

  function showAltroMessage(){
    setTimeout(()=>askNome(), 500);
  }

  function handleBranchSpecificOptions(value, text){
    if (value.startsWith('mutuo_') || value === 'surroga_mutuo' || value === 'prestito_personale') {
      conversationState.subOption = value; conversationState.data.tipo = text; saveState(); askPrivatoAzienda();
    } else if (value === 'dipendente_pubblico' || value === 'dipendente_privato' || value === 'pensionato') {
      conversationState.subOption = value; conversationState.data.tipo = text; saveState(); askCessioneAttiva();
    } else if (value === 'privati' || value === 'aziende') {
      conversationState.subOption = value; conversationState.data.tipo = text; saveState(); askTipoVeicolo();
    } else if (value === 'ristrutturazione_casa' || value === 'consolidamento' || value === 'liquidita_immobile') {
      conversationState.subOption = value; conversationState.data.tipo = text; saveState(); askFinanziamentoCorso();
    } else if (value.startsWith('assicurazione_')) {
      conversationState.subOption = value; conversationState.data.tipo = text; saveState(); askEmail();
    } else if (value === 'privato' || value === 'azienda') {
      conversationState.data.cliente = text; saveState(); askFinanziamentoCorso();
    } else if (value === 'si_finanziamento' || value === 'no_finanziamento') {
      conversationState.data.finanziamento_corso = text; saveState(); askEmail();
    } else if (value === 'si_cessione' || value === 'no_cessione') {
      conversationState.data.cessione_attiva = text; saveState(); askEmail();
    } else if (value === 'auto_nuova' || value === 'auto_usata' || value === 'moto' || value === 'commerciale') {
      conversationState.data.veicolo = text; saveState(); askEmail();
    } else if (value === 'si_messaggio') { askMessage(); }
      else if (value === 'no_messaggio') { askConsent(); }
  }

  function askPrivatoAzienda(){
    const options = [{ text:'Privato', value:'privato'}, { text:'Azienda', value:'azienda'}];
    setTimeout(()=>addMessage('Perfetto. Sei un privato o un\'azienda?', false, true, options), 500);
  }
  function askCessioneAttiva(){
    const options = [{ text:'Sì', value:'si_cessione' }, { text:'No', value:'no_cessione' }];
    setTimeout(()=>addMessage('Hai già una cessione del quinto attiva?', false, true, options), 500);
  }
  function askTipoVeicolo(){
    const options = [
      { text:'Auto nuova', value:'auto_nuova' },
      { text:'Auto usata', value:'auto_usata' },
      { text:'Moto', value:'moto' },
      { text:'Veicolo commerciale', value:'commerciale' }
    ];
    setTimeout(()=>addMessage('Che tipo di veicolo vuoi finanziare?', false, true, options), 500);
  }
  function askFinanziamentoCorso(){
    const options = [{ text:'Sì', value:'si_finanziamento' }, { text:'No', value:'no_finanziamento' }];
    setTimeout(()=>addMessage('Hai già un finanziamento in corso?', false, true, options), 500);
  }
  function askEmail(){ setTimeout(()=>askNome(), 500); }

  function showTextInput(fieldName, placeholder, callback, inputMode='text'){
    const messageDiv = document.createElement('div'); messageDiv.className='message bot';
    const bubble = document.createElement('div'); bubble.className='message-bubble';
    const textForm = document.createElement('form'); textForm.className='email-input-form'; bubble.classList.add('has-form');
    textForm.onsubmit = (e)=>{ e.preventDefault(); const textInput=textForm.querySelector('.text-input'); const value=textInput.value.trim(); if(!value) return; addMessage(value, true); messageDiv.style.display='none'; callback(value); };
    const textInput = document.createElement('input'); textInput.type = (fieldName==='email'?'email':'text'); textInput.className='text-input'; textInput.placeholder=placeholder; textInput.required=true;
    if(fieldName==='nome'){ textInput.autocomplete='given-name'; }
    else if(fieldName==='cognome'){ textInput.autocomplete='family-name'; }
    else if(fieldName==='telefono'){ textInput.autocomplete='tel'; textInput.inputMode='tel'; }
    else if(fieldName==='email'){ textInput.autocomplete='email'; }
    if(inputMode==='tel') textInput.inputMode='tel';
    const submitBtn = document.createElement('button'); submitBtn.type='submit'; submitBtn.className='submit-button'; submitBtn.textContent='Invia';
    textForm.appendChild(textInput); textForm.appendChild(submitBtn); bubble.appendChild(textForm); messageDiv.appendChild(bubble);
    chatMessages.appendChild(messageDiv); chatMessages.scrollTop = chatMessages.scrollHeight; setTimeout(()=>textInput.focus(), 100);
  }

  function askNome(){ addMessage("Perfetto! Per completare la richiesta, mi serve qualche informazione. Qual è il tuo nome?", false); showTextInput('nome','Il tuo nome...', v=>{ conversationState.data.nome=v.trim(); saveState(); askCognome(); }); }
  function askCognome(){ setTimeout(()=>{ addMessage('E il tuo cognome?', false); showTextInput('cognome','Il tuo cognome...', v=>{ conversationState.data.cognome=v.trim(); saveState(); askTelefono(); }); }, 500); }
  function askTelefono(){ setTimeout(()=>{ addMessage('Qual è il tuo numero di telefono?', false); showTextInput('telefono','Il tuo telefono...', v=>{ conversationState.data.telefono=v.trim(); saveState(); askEmailInput(); }, 'tel'); }, 500); }
  function askEmailInput(){ setTimeout(()=>{ addMessage('E la tua email?', false); showEmailInput(); }, 500); }
  
  function askIfMessage(){ setTimeout(()=>{ addMessage('Vuoi inviare un messaggio aggiuntivo?', false, true, [{text:'Sì', value:'si_messaggio'},{text:'No', value:'no_messaggio'}]); }, 500); }

  function showEmailInput(){
    const messageDiv=document.createElement('div'); messageDiv.className='message bot';
    const bubble=document.createElement('div'); bubble.className='message-bubble has-form';
    const emailForm=document.createElement('form'); emailForm.className='email-input-form';
    emailForm.onsubmit=(e)=>{ e.preventDefault(); const submitBtn=emailForm.querySelector('.submit-button'); if(submitBtn.disabled) return; handleEmailSubmit(emailForm); };
    const emailInput=document.createElement('input'); emailInput.type='email'; emailInput.className='email-input'; emailInput.placeholder='La tua email...'; emailInput.required=true; emailInput.autocomplete='email';
    const submitBtn=document.createElement('button'); submitBtn.type='submit'; submitBtn.className='submit-button'; submitBtn.textContent='Invia';
    emailForm.appendChild(emailInput); emailForm.appendChild(submitBtn); bubble.appendChild(emailForm); messageDiv.appendChild(bubble);
    chatMessages.appendChild(messageDiv); chatMessages.scrollTop=chatMessages.scrollHeight; setTimeout(()=>emailInput.focus(),100);
  }
  function isValidEmail(email){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email); }
  function handleEmailSubmit(form){
    const emailInput = form.querySelector('.email-input'); const email = emailInput.value.trim();
    const prev = form.querySelector('.error-message'); if(prev) prev.remove();
    if(!email || !isValidEmail(email)){ const e = document.createElement('div'); e.className='error-message'; e.textContent='Inserisci un indirizzo email valido'; form.appendChild(e); return; }
    conversationState.data.email = email; saveState(); addMessage(email, true);
    const messageDiv = form.closest('.message'); if(messageDiv) messageDiv.style.display='none';
    askIfMessage();
  }

  function askIfMessage(){ setTimeout(()=>{ addMessage('Vuoi inviare un messaggio aggiuntivo?', false, true, [{text:'Sì', value:'si_messaggio'},{text:'No', value:'no_messaggio'}]); }, 500); }
  function askMessage(){ setTimeout(()=>{ addMessage('Perfetto! Scrivi il tuo messaggio:', false); showMessageInput(); }, 500); }
  
  function askConsent(){ setTimeout(()=>{ addMessage('Prima di inviare, conferma il consenso al trattamento dei dati:', false); showConsentForm(); }, 500); }
  function showMessageInput(){
    const messageDiv=document.createElement('div'); messageDiv.className='message bot';
    const bubble=document.createElement('div'); bubble.className='message-bubble has-form';
    const form=document.createElement('form'); form.className='email-input-form';
    form.onsubmit=(e)=>{ e.preventDefault(); const submitBtn=form.querySelector('.submit-button'); if(submitBtn.disabled) return; handleMessageSubmit(form); };
    const ta=document.createElement('textarea'); ta.className='message-input'; ta.placeholder='Scrivi qui il tuo messaggio...'; ta.required=true;
    const btn=document.createElement('button'); btn.type='submit'; btn.className='submit-button'; btn.textContent='Invia';
    form.appendChild(ta); form.appendChild(btn); bubble.appendChild(form); messageDiv.appendChild(bubble);
    chatMessages.appendChild(messageDiv); chatMessages.scrollTop=chatMessages.scrollHeight; setTimeout(()=>ta.focus(),100);
  }
  function handleMessageSubmit(form){
    const ta=form.querySelector('.message-input'); const msg=ta.value.trim(); if(!msg) return;
    conversationState.data.message=msg; saveState(); addMessage(msg,true);
    const messageDiv=form.closest('.message'); if(messageDiv) messageDiv.style.display='none';
    askConsent();
  }
  function showConsentForm(){
    const messageDiv=document.createElement('div'); messageDiv.className='message bot';
    const bubble=document.createElement('div'); bubble.className='message-bubble has-form';
    const consentForm=document.createElement('form'); consentForm.className='email-input-form';
    const consentContainer=document.createElement('div'); consentContainer.className='consent-container';
    const checkbox=document.createElement('input'); checkbox.type='checkbox'; checkbox.className='consent-checkbox'; checkbox.id='consent-checkbox'; checkbox.required=true;
    const consentLabel=document.createElement('label'); consentLabel.className='consent-text'; consentLabel.htmlFor='consent-checkbox';
    consentLabel.innerHTML='Acconsento al <a href="https://creditplan.it/wp-content/uploads/2023/04/Informativa-privacy.pdf" target="_blank" rel="noopener noreferrer">trattamento dei miei dati personali</a> secondo l\'informativa sulla privacy.';
    consentContainer.appendChild(checkbox); consentContainer.appendChild(consentLabel);
    const submitBtn=document.createElement('button'); submitBtn.type='submit'; submitBtn.className='submit-button'; submitBtn.textContent='Conferma e invia'; submitBtn.disabled=true;
    checkbox.addEventListener('change', ()=>{ submitBtn.disabled=!checkbox.checked; });
    consentForm.onsubmit=(e)=>{ e.preventDefault(); if(submitBtn.disabled || !checkbox.checked) return; submitLead(consentForm, submitBtn); };
    consentForm.appendChild(consentContainer); consentForm.appendChild(submitBtn); bubble.appendChild(consentForm); messageDiv.appendChild(bubble);
    chatMessages.appendChild(messageDiv); chatMessages.scrollTop=chatMessages.scrollHeight;
  }

  let isSubmitting=false, hasShownSuccess=false;
  function submitLead(form=null, submitBtn=null){
    if(isSubmitting) return;
    const now=Date.now(); if(now - lastSubmitTime < SUBMIT_THROTTLE_MS) return; lastSubmitTime=now;
    isSubmitting=true;
    if(form){ const wrapper=form.closest('.message'); if(wrapper) wrapper.style.display='none'; }
    const loadingMessageDiv=document.createElement('div'); loadingMessageDiv.className='message bot';
    const loadingBubble=document.createElement('div'); loadingBubble.className='message-bubble';
    const loaderSpan=document.createElement('span'); loaderSpan.className='loader'; loaderSpan.setAttribute('aria-hidden','true');
    loadingBubble.appendChild(loaderSpan); loadingBubble.appendChild(document.createTextNode(' Invio richiesta in corso...'));
    loadingMessageDiv.appendChild(loadingBubble); chatMessages.appendChild(loadingMessageDiv); chatMessages.scrollTop=chatMessages.scrollHeight;
    if(submitBtn) submitBtn.disabled=true;

    fetch(API_URL, { method:'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify(conversationState) })
      .then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
      .then(_=>{
        loadingMessageDiv.remove();
        if(!hasShownSuccess){ hasShownSuccess=true; setTimeout(()=>addMessage('Perfetto! La tua richiesta è stata inviata con successo. Un nostro consulente ti contatterà presto via email.', false), 300); }
      })
      .catch(err=>{
        console.error('Error submitting lead:', err);
        loadingMessageDiv.remove();
        setTimeout(()=>{
          addMessage('Si è verificato un errore nell\'invio della richiesta. Riprova tra qualche istante o contattaci direttamente.', false);
          const retryContainer=document.createElement('div'); retryContainer.className='options-container';
          const retryBtn=document.createElement('button'); retryBtn.type='button'; retryBtn.className='option-button'; retryBtn.textContent='Riprova';
          retryBtn.onclick=()=>{ retryBtn.closest('.message')?.remove(); askConsent(); };
          retryContainer.appendChild(retryBtn);
          const lastBubble=chatMessages.lastElementChild?.querySelector('.message-bubble'); if(lastBubble) lastBubble.appendChild(retryContainer);
        }, 300);
      })
      .finally(()=>{ isSubmitting=false; if(submitBtn) submitBtn.disabled=false; });
  }

  chatToggle.addEventListener('click', toggleChat);
  closeChat.addEventListener('click', toggleChat);
</script>