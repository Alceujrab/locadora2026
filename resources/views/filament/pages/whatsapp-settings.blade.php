<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">

        {{-- ===== Conexao WuzAPI ===== --}}
        <section class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <header class="flex items-center gap-3 border-b border-gray-200 px-6 py-4 dark:border-white/10">
                <x-filament::icon icon="heroicon-o-signal" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Conexao com WuzAPI</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure a URL da API e o token do usuario gerado no painel do WuzAPI.</p>
                </div>
            </header>
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL da API</label>
                        <input type="url" wire:model="wuzapi_api_url" placeholder="https://wuzapi.seudominio.com.br"
                            class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ex: https://wuzapi.seudominio.com.br ou http://localhost:8080</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Token do Usuario</label>
                        <input type="password" wire:model="wuzapi_token" placeholder="token gerado no painel do WuzAPI"
                            class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enviado no header <code class="px-1 rounded bg-gray-100 dark:bg-white/10">token</code> de todas as requisicoes.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rotulo da Instancia</label>
                        <input type="text" wire:model="wuzapi_instance_label" placeholder="principal"
                            class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Campo opcional (identificacao visual).</p>
                    </div>
                </div>

                <div class="mt-4 flex gap-3">
                    <x-filament::button type="button" wire:click="testConnection" color="info" icon="heroicon-o-signal">
                        Testar Conexao
                    </x-filament::button>
                </div>
            </div>
        </section>

        {{-- ===== Ativacao ===== --}}
        <section class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <header class="flex items-center gap-3 border-b border-gray-200 px-6 py-4 dark:border-white/10">
                <x-filament::icon icon="heroicon-o-bell" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Ativacao do WhatsApp</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Ative ou desative o envio de notificacoes por modulo.</p>
                </div>
            </header>
            <div class="px-6 py-5 space-y-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="whatsapp_enabled"
                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 w-5 h-5" />
                    <div>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">WhatsApp Ativo</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ativar/desativar todo o envio via WhatsApp no sistema.</p>
                    </div>
                </label>

                <div class="border-t border-gray-200 dark:border-white/10 pt-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Notificacoes por Modulo</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5">
                            <input type="checkbox" wire:model="whatsapp_notify_contracts"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 w-4 h-4" />
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Contratos</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Enviar contratos e assinaturas por WhatsApp.</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5">
                            <input type="checkbox" wire:model="whatsapp_notify_invoices"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 w-4 h-4" />
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Faturas</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Notificar sobre faturas pendentes e vencidas.</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5">
                            <input type="checkbox" wire:model="whatsapp_notify_service_orders"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 w-4 h-4" />
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Ordens de Servico</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Enviar OS para assinatura digital via WhatsApp.</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5">
                            <input type="checkbox" wire:model="whatsapp_notify_reservations"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 w-4 h-4" />
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Reservas</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Confirmar reservas e lembretes por WhatsApp.</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== Mensagens Padrao ===== --}}
        <section class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <header class="flex items-center gap-3 border-b border-gray-200 px-6 py-4 dark:border-white/10">
                <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Mensagens Padrao</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Cabecalho e rodape anexados as mensagens enviadas.</p>
                </div>
            </header>
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cabecalho da Mensagem</label>
                        <textarea wire:model="whatsapp_default_message_header" rows="3" placeholder="Elite Locadora"
                            class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"></textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Texto adicionado no inicio de cada mensagem.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rodape da Mensagem</label>
                        <textarea wire:model="whatsapp_default_message_footer" rows="3" placeholder="Elite Locadora - Aluguel de Veiculos"
                            class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"></textarea>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Texto adicionado no final de cada mensagem.</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-check" size="lg">
                Salvar Configuracoes
            </x-filament::button>
        </div>
    </form>

    {{-- ===== Enviar mensagem de teste ===== --}}
    <section class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mt-6">
        <header class="flex items-center gap-3 border-b border-gray-200 px-6 py-4 dark:border-white/10">
            <x-filament::icon icon="heroicon-o-paper-airplane" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
            <div>
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">Enviar mensagem de teste</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Dispara uma mensagem de texto pelo WuzAPI usando o cabecalho/rodape configurados acima. Salve as configuracoes antes de testar.</p>
            </div>
        </header>
        <div class="px-6 py-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numero de destino</label>
                    <input type="text" wire:model="test_phone" placeholder="66999998888"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">DDD + numero. O prefixo 55 e adicionado automaticamente.</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Texto da mensagem</label>
                    <textarea wire:model="test_message" rows="3"
                        class="w-full rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-white/5 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"></textarea>
                </div>
            </div>
            <div class="mt-4 flex gap-3">
                <x-filament::button type="button" wire:click="sendTestMessage" color="success" icon="heroicon-o-paper-airplane">
                    Enviar teste
                </x-filament::button>
            </div>
        </div>
    </section>
</x-filament-panels::page>
