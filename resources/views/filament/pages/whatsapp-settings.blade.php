<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-check" size="lg">
                Salvar Configurações
            </x-filament::button>
        </div>
    </form>

    <div class="mt-6">
        {{ $this->testForm }}
    </div>
</x-filament-panels::page>
