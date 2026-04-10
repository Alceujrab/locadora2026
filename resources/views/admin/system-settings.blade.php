<form id="settings-form" method="POST" action="{{ $saveUrl }}">
    @csrf

    {{-- ============================================== --}}
    {{-- SEÇÃO 1: VISUAL E CORES DO PAINEL              --}}
    {{-- ============================================== --}}
    <div style="margin-bottom: 32px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <span style="font-size: 1.6rem; color: #3b82f6;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.6rem;height:1.6rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" /></svg></span>
            <div>
                <h2 style="margin: 0; font-size: 1.3rem; font-weight: 700; color: var(--primary);">Visual e Cores do Painel</h2>
                <p style="margin: 4px 0 0; font-size: 0.85rem; opacity: 0.6;">Personalize as cores e aparência do painel administrativo</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            {{-- COR PRIMÁRIA --}}
            <div style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; border: 1px solid rgba(255,255,255,0.06);">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Cor Primária</label>
                <p style="font-size: 0.78rem; opacity: 0.5; margin-bottom: 12px;">Botões, links e elementos de destaque</p>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <input type="color" name="PANEL_COLOR_PRIMARY" value="{{ $settings['PANEL_COLOR_PRIMARY'] }}"
                           id="color-primary"
                           style="width: 56px; height: 56px; border: none; border-radius: 10px; cursor: pointer; background: transparent; padding: 0;"
                           onchange="document.getElementById('hex-primary').value = this.value; updatePreview();">
                    <div style="flex: 1;">
                        <input type="text" id="hex-primary" value="{{ $settings['PANEL_COLOR_PRIMARY'] }}"
                               style="width: 100%; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 10px 14px; color: inherit; font-family: monospace; font-size: 0.95rem;"
                               oninput="if(this.value.match(/^#[0-9a-fA-F]{6}$/)) { document.getElementById('color-primary').value = this.value; document.querySelector('[name=PANEL_COLOR_PRIMARY]').value = this.value; updatePreview(); }"
                               placeholder="#d97706">
                    </div>
                </div>
            </div>

            {{-- COR SECUNDÁRIA --}}
            <div style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; border: 1px solid rgba(255,255,255,0.06);">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Cor Secundária</label>
                <p style="font-size: 0.78rem; opacity: 0.5; margin-bottom: 12px;">Fundo de cards, menus e painéis</p>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <input type="color" name="PANEL_COLOR_SECONDARY" value="{{ $settings['PANEL_COLOR_SECONDARY'] }}"
                           id="color-secondary"
                           style="width: 56px; height: 56px; border: none; border-radius: 10px; cursor: pointer; background: transparent; padding: 0;"
                           onchange="document.getElementById('hex-secondary').value = this.value; updatePreview();">
                    <div style="flex: 1;">
                        <input type="text" id="hex-secondary" value="{{ $settings['PANEL_COLOR_SECONDARY'] }}"
                               style="width: 100%; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 10px 14px; color: inherit; font-family: monospace; font-size: 0.95rem;"
                               oninput="if(this.value.match(/^#[0-9a-fA-F]{6}$/)) { document.getElementById('color-secondary').value = this.value; document.querySelector('[name=PANEL_COLOR_SECONDARY]').value = this.value; updatePreview(); }"
                               placeholder="#1e293b">
                    </div>
                </div>
            </div>

            {{-- COR ACCENT --}}
            <div style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; border: 1px solid rgba(255,255,255,0.06);">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Cor de Destaque (Accent)</label>
                <p style="font-size: 0.78rem; opacity: 0.5; margin-bottom: 12px;">Badges, hover states e destaques</p>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <input type="color" name="PANEL_COLOR_ACCENT" value="{{ $settings['PANEL_COLOR_ACCENT'] }}"
                           id="color-accent"
                           style="width: 56px; height: 56px; border: none; border-radius: 10px; cursor: pointer; background: transparent; padding: 0;"
                           onchange="document.getElementById('hex-accent').value = this.value; updatePreview();">
                    <div style="flex: 1;">
                        <input type="text" id="hex-accent" value="{{ $settings['PANEL_COLOR_ACCENT'] }}"
                               style="width: 100%; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 10px 14px; color: inherit; font-family: monospace; font-size: 0.95rem;"
                               oninput="if(this.value.match(/^#[0-9a-fA-F]{6}$/)) { document.getElementById('color-accent').value = this.value; document.querySelector('[name=PANEL_COLOR_ACCENT]').value = this.value; updatePreview(); }"
                               placeholder="#f59e0b">
                    </div>
                </div>
            </div>
        </div>

        {{-- PREVIEW AO VIVO --}}
        <div id="theme-preview" style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 24px; border: 1px solid rgba(255,255,255,0.06);">
            <label style="display: block; font-weight: 600; margin-bottom: 16px; font-size: 0.9rem;">Pré-visualização</label>
            <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                <div id="preview-primary" style="width: 120px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; font-size: 0.8rem; background: {{ $settings['PANEL_COLOR_PRIMARY'] }};">Primária</div>
                <div id="preview-secondary" style="width: 120px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; font-size: 0.8rem; background: {{ $settings['PANEL_COLOR_SECONDARY'] }}; border: 1px solid rgba(255,255,255,0.1);">Secundária</div>
                <div id="preview-accent" style="width: 120px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #000; font-weight: 600; font-size: 0.8rem; background: {{ $settings['PANEL_COLOR_ACCENT'] }};">Accent</div>
                <div style="flex: 1; display: flex; gap: 8px; flex-wrap: wrap;">
                    <button type="button" id="preview-btn" style="padding: 10px 24px; border-radius: 8px; border: none; color: #fff; font-weight: 600; cursor: pointer; background: {{ $settings['PANEL_COLOR_PRIMARY'] }};">Botão Primário</button>
                    <button type="button" id="preview-btn-outline" style="padding: 10px 24px; border-radius: 8px; background: transparent; font-weight: 600; cursor: pointer; border: 2px solid {{ $settings['PANEL_COLOR_PRIMARY'] }}; color: {{ $settings['PANEL_COLOR_PRIMARY'] }};">Botão Outline</button>
                    <span id="preview-badge" style="padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; color: #000; background: {{ $settings['PANEL_COLOR_ACCENT'] }};">Badge</span>
                </div>
            </div>
        </div>

        {{-- OPÇÕES ADICIONAIS DO TEMA --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
            <div style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; border: 1px solid rgba(255,255,255,0.06);">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Nome/Logo (Texto)</label>
                <input type="text" name="PANEL_LOGO_TEXT" value="{{ $settings['PANEL_LOGO_TEXT'] }}"
                       style="width: 100%; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 12px 14px; color: inherit; font-size: 0.95rem;"
                       placeholder="Elite Locadora">
            </div>
            <div style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 20px; border: 1px solid rgba(255,255,255,0.06);">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 0.9rem;">Tema Escuro</label>
                <div style="display: flex; align-items: center; gap: 12px; margin-top: 8px;">
                    <label style="position: relative; display: inline-block; width: 52px; height: 28px;">
                        <input type="hidden" name="PANEL_DARK_MODE" value="0">
                        <input type="checkbox" name="PANEL_DARK_MODE" value="1" {{ $settings['PANEL_DARK_MODE'] == '1' ? 'checked' : '' }}
                               style="opacity: 0; width: 0; height: 0;"
                               onchange="this.previousElementSibling.value = this.checked ? '0' : '1';">
                        <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: {{ $settings['PANEL_DARK_MODE'] == '1' ? 'var(--primary, #d97706)' : '#555' }}; border-radius: 28px; transition: 0.3s;"
                              onclick="let cb = this.previousElementSibling; cb.checked = !cb.checked; cb.dispatchEvent(new Event('change')); this.style.background = cb.checked ? 'var(--primary, #d97706)' : '#555'; this.querySelector('span').style.transform = cb.checked ? 'translateX(24px)' : 'translateX(0)'; ">
                            <span style="position: absolute; width: 22px; height: 22px; left: 3px; top: 3px; background: #fff; border-radius: 50%; transition: 0.3s; {{ $settings['PANEL_DARK_MODE'] == '1' ? 'transform: translateX(24px);' : '' }}"></span>
                        </span>
                    </label>
                    <span style="font-size: 0.85rem; opacity: 0.7;">Ativar modo escuro por padrão</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================== --}}
    {{-- SEÇÃO 2: INTEGRAÇÕES E APIS                    --}}
    {{-- ============================================== --}}
    <div style="margin-bottom: 32px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <span style="font-size: 1.6rem; color: #f97316;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.6rem;height:1.6rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg></span>
            <div>
                <h2 style="margin: 0; font-size: 1.3rem; font-weight: 700; color: var(--primary);">Integrações e APIs</h2>
                <p style="margin: 4px 0 0; font-size: 0.85rem; opacity: 0.6;">Configure as chaves de acesso para serviços externos</p>
            </div>
        </div>

        {{-- SOCIAL LOGIN --}}
        <div style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 24px; border: 1px solid rgba(255,255,255,0.06); margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <span style="font-size: 1.2rem; color: #60a5fa;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.2rem;height:1.2rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" /></svg></span>
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Social Login (OAuth)</h3>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Google Client ID</label>
                    <input type="text" name="GOOGLE_CLIENT_ID" value="{{ $settings['GOOGLE_CLIENT_ID'] }}" class="settings-input" placeholder="xxx.apps.googleusercontent.com">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Google Client Secret</label>
                    <input type="password" name="GOOGLE_CLIENT_SECRET" value="{{ $settings['GOOGLE_CLIENT_SECRET'] }}" class="settings-input" placeholder="••••••••••">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Google Redirect URI</label>
                    <input type="text" name="GOOGLE_REDIRECT_URI" value="{{ $settings['GOOGLE_REDIRECT_URI'] }}" class="settings-input" placeholder="https://seusite.com/callback/google">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Facebook App ID</label>
                    <input type="text" name="FACEBOOK_CLIENT_ID" value="{{ $settings['FACEBOOK_CLIENT_ID'] }}" class="settings-input" placeholder="123456789">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Facebook App Secret</label>
                    <input type="password" name="FACEBOOK_CLIENT_SECRET" value="{{ $settings['FACEBOOK_CLIENT_SECRET'] }}" class="settings-input" placeholder="••••••••••">
                </div>
            </div>
        </div>

        {{-- WHATSAPP / EVOLUTION GO --}}
        <div style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 24px; border: 1px solid rgba(255,255,255,0.06); margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <span style="font-size: 1.2rem; color: #22c55e;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.2rem;height:1.2rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg></span>
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">WhatsApp (Evolution Go)</h3>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">URL da API</label>
                    <input type="text" name="EVOLUTION_API_URL" value="{{ $settings['EVOLUTION_API_URL'] }}" class="settings-input" placeholder="https://api.evolution.com">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Token da Instância</label>
                    <input type="password" name="EVOLUTION_API_KEY" value="{{ $settings['EVOLUTION_API_KEY'] }}" class="settings-input" placeholder="••••••••••">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Nome da Instância</label>
                    <input type="text" name="EVOLUTION_INSTANCE_NAME" value="{{ $settings['EVOLUTION_INSTANCE_NAME'] }}" class="settings-input" placeholder="elitelocadora">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Nº WhatsApp</label>
                    <input type="text" name="WHATSAPP_NUMBER" value="{{ $settings['WHATSAPP_NUMBER'] }}" class="settings-input" placeholder="5566999999999">
                </div>
            </div>
        </div>

        {{-- GATEWAY DE PAGAMENTO --}}
        <div style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 24px; border: 1px solid rgba(255,255,255,0.06);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                <span style="font-size: 1.2rem; color: #a78bfa;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.2rem;height:1.2rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg></span>
                <h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Gateway de Pagamento</h3>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Gateway</label>
                    <select name="PAYMENT_GATEWAY" class="settings-input">
                        <option value="stripe" {{ $settings['PAYMENT_GATEWAY'] == 'stripe' ? 'selected' : '' }}>Stripe</option>
                        <option value="mercadopago" {{ $settings['PAYMENT_GATEWAY'] == 'mercadopago' ? 'selected' : '' }}>Mercado Pago</option>
                        <option value="pagseguro" {{ $settings['PAYMENT_GATEWAY'] == 'pagseguro' ? 'selected' : '' }}>PagSeguro</option>
                        <option value="asaas" {{ $settings['PAYMENT_GATEWAY'] == 'asaas' ? 'selected' : '' }}>Asaas</option>
                        <option value="cielo" {{ $settings['PAYMENT_GATEWAY'] == 'cielo' ? 'selected' : '' }}>Cielo</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">API Key</label>
                    <input type="password" name="PAYMENT_API_KEY" value="{{ $settings['PAYMENT_API_KEY'] }}" class="settings-input" placeholder="pk_live_...">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Secret Key</label>
                    <input type="password" name="PAYMENT_SECRET_KEY" value="{{ $settings['PAYMENT_SECRET_KEY'] }}" class="settings-input" placeholder="sk_live_...">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Webhook Secret</label>
                    <input type="password" name="PAYMENT_WEBHOOK_SECRET" value="{{ $settings['PAYMENT_WEBHOOK_SECRET'] }}" class="settings-input" placeholder="whsec_...">
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================== --}}
    {{-- SEÇÃO 3: DADOS DA EMPRESA                      --}}
    {{-- ============================================== --}}
    <div style="margin-bottom: 32px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <span style="font-size: 1.6rem; color: #3b82f6;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.6rem;height:1.6rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg></span>
            <div>
                <h2 style="margin: 0; font-size: 1.3rem; font-weight: 700; color: var(--primary);">Dados da Empresa</h2>
                <p style="margin: 4px 0 0; font-size: 0.85rem; opacity: 0.6;">Informações gerais utilizadas em contratos e documentos</p>
            </div>
        </div>

        <div style="background: rgba(255,255,255,0.03); border-radius: 12px; padding: 24px; border: 1px solid rgba(255,255,255,0.06);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Razão Social / Nome da Empresa</label>
                    <input type="text" name="COMPANY_NAME" value="{{ $settings['COMPANY_NAME'] }}" class="settings-input" placeholder="Elite Locadora de Veículos Ltda">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">CNPJ</label>
                    <input type="text" name="COMPANY_DOCUMENT" value="{{ $settings['COMPANY_DOCUMENT'] }}" class="settings-input" placeholder="12.345.678/0001-90">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Telefone Principal</label>
                    <input type="text" name="COMPANY_PHONE" value="{{ $settings['COMPANY_PHONE'] }}" class="settings-input" placeholder="(66) 99999-9999">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">E-mail Principal</label>
                    <input type="email" name="COMPANY_EMAIL" value="{{ $settings['COMPANY_EMAIL'] }}" class="settings-input" placeholder="contato@elitelocadora.com.br">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; opacity: 0.7;">Fuso Horário</label>
                    <select name="TIMEZONE" class="settings-input">
                        <option value="America/Sao_Paulo" {{ $settings['TIMEZONE'] == 'America/Sao_Paulo' ? 'selected' : '' }}>Brasília (GMT-3)</option>
                        <option value="America/Manaus" {{ $settings['TIMEZONE'] == 'America/Manaus' ? 'selected' : '' }}>Manaus (GMT-4)</option>
                        <option value="America/Cuiaba" {{ $settings['TIMEZONE'] == 'America/Cuiaba' ? 'selected' : '' }}>Cuiabá (GMT-4)</option>
                        <option value="America/Belem" {{ $settings['TIMEZONE'] == 'America/Belem' ? 'selected' : '' }}>Belém (GMT-3)</option>
                        <option value="America/Fortaleza" {{ $settings['TIMEZONE'] == 'America/Fortaleza' ? 'selected' : '' }}>Fortaleza (GMT-3)</option>
                        <option value="America/Recife" {{ $settings['TIMEZONE'] == 'America/Recife' ? 'selected' : '' }}>Recife (GMT-3)</option>
                        <option value="America/Bahia" {{ $settings['TIMEZONE'] == 'America/Bahia' ? 'selected' : '' }}>Bahia (GMT-3)</option>
                        <option value="America/Porto_Velho" {{ $settings['TIMEZONE'] == 'America/Porto_Velho' ? 'selected' : '' }}>Porto Velho (GMT-4)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================== --}}
    {{-- BOTÃO SALVAR                                   --}}
    {{-- ============================================== --}}
    <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.1);">
        <button type="button" onclick="window.location.reload();"
                style="padding: 12px 32px; border-radius: 8px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: inherit; font-weight: 600; cursor: pointer; font-size: 0.95rem; transition: all 0.2s;"
                onmouseover="this.style.background='rgba(255,255,255,0.12)'" onmouseout="this.style.background='rgba(255,255,255,0.06)'">
            Cancelar
        </button>
        <button type="submit" id="save-btn"
                style="padding: 12px 40px; border-radius: 8px; background: var(--primary, #d97706); border: none; color: #fff; font-weight: 700; cursor: pointer; font-size: 0.95rem; transition: all 0.2s; display: flex; align-items: center; gap: 8px;"
                onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
            Salvar Configurações
        </button>
    </div>
</form>

{{-- ============================================== --}}
{{-- ESTILOS E SCRIPTS                              --}}
{{-- ============================================== --}}
<style>
    .settings-input {
        width: 100%;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 8px;
        padding: 10px 14px;
        color: inherit;
        font-size: 0.9rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .settings-input:focus {
        outline: none;
        border-color: var(--primary, #d97706);
        box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
    }
    .settings-input::placeholder {
        opacity: 0.35;
    }
    .settings-input option {
        background: #1a1a2e;
        color: #fff;
    }
    .save-toast {
        position: fixed;
        top: 24px;
        right: 24px;
        padding: 16px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.4s ease;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    }
    .save-toast.success {
        background: linear-gradient(135deg, #059669, #10b981);
        color: #fff;
    }
    .save-toast.error {
        background: linear-gradient(135deg, #dc2626, #ef4444);
        color: #fff;
    }
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
</style>

<script>
    function updatePreview() {
        const primary = document.getElementById('color-primary').value;
        const secondary = document.getElementById('color-secondary').value;
        const accent = document.getElementById('color-accent').value;

        document.getElementById('preview-primary').style.background = primary;
        document.getElementById('preview-secondary').style.background = secondary;
        document.getElementById('preview-accent').style.background = accent;
        document.getElementById('preview-btn').style.background = primary;
        document.getElementById('preview-btn-outline').style.borderColor = primary;
        document.getElementById('preview-btn-outline').style.color = primary;
        document.getElementById('preview-badge').style.background = accent;
    }

    // AJAX form submission
    document.getElementById('settings-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('save-btn');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="animate-spin"><circle cx="12" cy="12" r="10" opacity="0.3"/><path d="M12 2a10 10 0 0 1 10 10"/></svg> Salvando...';
        btn.disabled = true;

        try {
            const formData = new FormData(this);
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (result.success) {
                showToast('Configurações salvas com sucesso!', 'success');
                // Refresh after 1.5s to apply theme changes
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast('Erro ao salvar: ' + (result.message || 'Tente novamente'), 'error');
            }
        } catch (err) {
            showToast('Erro de conexão: ' + err.message, 'error');
        } finally {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    });

    function showToast(message, type) {
        const existing = document.querySelector('.save-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'save-toast ' + type;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.4s ease forwards';
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }
</script>
