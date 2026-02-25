<x-filament-panels::page>
    <form wire:submit="save">
        {{-- Seção: Conexão Evolution API --}}
        <x-filament::section heading="Conexao com Evolution API" description="Configure os dados de acesso a sua instancia Evolution API para envio de mensagens WhatsApp." icon="heroicon-o-signal">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL da API</label>
                    <input type="url" wire:model="evolution_api_url" placeholder="https://api.seudominio.com"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                    <p class="text-xs text-gray-500 mt-1">Ex: https://evo.seusistema.com.br</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">API Key</label>
                    <input type="password" wire:model="evolution_api_key" placeholder="Sua chave de API"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                    <p class="text-xs text-gray-500 mt-1">Chave global ou da instancia</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome da Instancia</label>
                    <input type="text" wire:model="evolution_instance_name" placeholder="minha-instancia"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                    <p class="text-xs text-gray-500 mt-1">Nome configurado na Evolution</p>
                </div>
            </div>

            <div class="mt-4 flex gap-3">
                <x-filament::button type="button" wire:click="testConnection" color="info" icon="heroicon-o-signal">
                    Testar Conexao
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Seção: Ativar/Desativar --}}
        <x-filament::section heading="Ativacao do WhatsApp" description="Ative ou desative o envio de notificacoes via WhatsApp para cada modulo do sistema." icon="heroicon-o-bell" class="mt-6">
            <div class="space-y-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="whatsapp_enabled"
                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 w-5 h-5" />
                    <div>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">WhatsApp Ativo</span>
                        <p class="text-xs text-gray-500">Ativar/desativar todo o envio via WhatsApp no sistema</p>
                    </div>
                </label>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Notificacoes por Modulo</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <input type="checkbox" wire:model="whatsapp_notify_contracts"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 w-4 h-4" />
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Contratos</span>
                                <p class="text-xs text-gray-500">Enviar contratos e assinaturas por WhatsApp</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <input type="checkbox" wire:model="whatsapp_notify_invoices"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 w-4 h-4" />
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Faturas</span>
                                <p class="text-xs text-gray-500">Notificar sobre faturas pendentes e vencidas</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <input type="checkbox" wire:model="whatsapp_notify_service_orders"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 w-4 h-4" />
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Ordens de Servico</span>
                                <p class="text-xs text-gray-500">Enviar OS para assinatura digital via WhatsApp</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <input type="checkbox" wire:model="whatsapp_notify_reservations"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 w-4 h-4" />
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Reservas</span>
                                <p class="text-xs text-gray-500">Confirmar reservas e lembretes por WhatsApp</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- Seção: Mensagens Padrão --}}
        <x-filament::section heading="Mensagens Padrao" description="Personalize o cabecalho e rodape das mensagens enviadas por WhatsApp." icon="heroicon-o-document-text" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cabecalho da Mensagem</label>
                    <textarea wire:model="whatsapp_default_message_header" rows="3" placeholder="Elite Locadora"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Texto adicionado no inicio de cada mensagem</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rodape da Mensagem</label>
                    <textarea wire:model="whatsapp_default_message_footer" rows="3" placeholder="Elite Locadora - Aluguel de Veiculos"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Texto adicionado no final de cada mensagem</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Botão Salvar --}}
        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-check" size="lg">
                Salvar Configuracoes
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
