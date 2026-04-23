<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{-- Seção: Dados da Empresa --}}
        <section class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <header class="flex items-center gap-3 border-b border-gray-200 px-6 py-4 dark:border-white/10">
                <x-filament::icon icon="heroicon-o-building-office-2" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Dados da Empresa</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Informacoes que aparecem nos PDFs de faturas e ordens de servico.</p>
                </div>
            </header>
            <div class="px-6 py-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Razao Social / Nome</label>
                    <input type="text" wire:model="company_name" placeholder="Elite Locadora de Veiculos"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CNPJ</label>
                    <input type="text" wire:model="company_cnpj" placeholder="00.000.000/0001-00"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone</label>
                    <input type="text" wire:model="company_phone" placeholder="(66) 3521-0000"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" wire:model="company_email" placeholder="contato@empresa.com.br"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CEP</label>
                    <input type="text" wire:model="company_zip" placeholder="78550-000"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Endereco</label>
                    <input type="text" wire:model="company_address" placeholder="Rua, numero, bairro"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cidade</label>
                    <input type="text" wire:model="company_city" placeholder="Sinop"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UF</label>
                    <input type="text" wire:model="company_state" placeholder="MT" maxlength="2"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
            </div>
            </div>
        </section>

        {{-- Seção: Dados Bancários e PIX --}}
        <section class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <header class="flex items-center gap-3 border-b border-gray-200 px-6 py-4 dark:border-white/10">
                <x-filament::icon icon="heroicon-o-banknotes" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Dados Bancarios e PIX</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Informacoes de pagamento que aparecem nas faturas e paginas de confirmacao.</p>
                </div>
            </header>
            <div class="px-6 py-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo Chave PIX</label>
                    <select wire:model="pix_type"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500">
                        <option value="CNPJ">CNPJ</option>
                        <option value="CPF">CPF</option>
                        <option value="E-mail">E-mail</option>
                        <option value="Telefone">Telefone</option>
                        <option value="Aleatoria">Chave Aleatoria</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chave PIX</label>
                    <input type="text" wire:model="pix_key" placeholder="00.000.000/0001-00"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titular PIX</label>
                    <input type="text" wire:model="pix_holder" placeholder="Nome do titular"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banco</label>
                    <input type="text" wire:model="bank_name" placeholder="Banco do Brasil"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Agencia</label>
                    <input type="text" wire:model="bank_agency" placeholder="0001"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Conta Corrente</label>
                    <input type="text" wire:model="bank_account" placeholder="12345-6"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500" />
                </div>
            </div>
            </div>
        </section>

        {{-- Seção: Textos de Documentos --}}
        <section class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <header class="flex items-center gap-3 border-b border-gray-200 px-6 py-4 dark:border-white/10">
                <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                <div>
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">Textos de Documentos</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Textos personalizados que aparecem nos PDFs gerados pelo sistema.</p>
                </div>
            </header>
            <div class="px-6 py-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Instrucoes de Pagamento</label>
                    <textarea wire:model="invoice_terms" rows="3" placeholder="Apos o pagamento, envie o comprovante..."
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Aparece no PDF da fatura, abaixo dos dados de pagamento</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rodape dos Documentos</label>
                    <textarea wire:model="invoice_footer" rows="3" placeholder="Este documento nao possui validade fiscal..."
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Aparece no final dos PDFs de faturas e OS</p>
                </div>
            </div>
            </div>
        </section>

        {{-- Botão Salvar --}}
        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-check" size="lg">
                Salvar Configuracoes
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
