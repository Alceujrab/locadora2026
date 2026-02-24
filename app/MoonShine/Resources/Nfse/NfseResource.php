<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Nfse;
use App\Models\Nfse;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\UI\Components\ActionButton;
use MoonShine\Laravel\MoonShineUI;
use MoonShine\Laravel\Http\Requests\MoonShineRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
#[Icon('document-check')]
class NfseResource extends ModelResource
{
    protected string $model = Nfse::class;
    protected ?PageType $redirectAfterSave = PageType::INDEX;
    protected string $title = 'Notas Fiscais (NFS-e)';
    protected string $column = 'numero';
    protected SortDirection $sortDirection = SortDirection::DESC;
    public function getActiveActions(): array
    {
        return [
            \MoonShine\Support\Enums\Action::CREATE,
            \MoonShine\Support\Enums\Action::VIEW,
            \MoonShine\Support\Enums\Action::UPDATE,
            \MoonShine\Support\Enums\Action::DELETE,
            \MoonShine\Support\Enums\Action::MASS_DELETE,
        ];
    }
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Néºmero', 'numero')
                ->sortable(),
            BelongsTo::make('Fatura', 'invoice', fn($item) => $item->invoice_number ?? 'N/A')
                ->sortable(),
            Text::make('Tomador', 'tomador_nome')
                ->sortable(),
            Text::make('CPF/CNPJ', 'tomador_cnpj_cpf'),
            Number::make('Valor Servié§o (R$)', 'valor_servico')
                ->sortable(),
            Number::make('ISS (R$)', 'valor_iss'),
            Date::make('Emissé£o', 'data_emissao')
                ->format('d/m/Y')
                ->sortable(),
            Select::make('Status', 'status')
                ->options([
                    'rascunho' => 'Rascunho',
                    'emitida' => 'Emitida',
                    'cancelada' => 'Cancelada',
                ])
                ->badge(fn(string $value) => match($value) {
                    'rascunho' => 'warning',
                    'emitida' => 'success',
                    'cancelada' => 'error',
                    default => 'secondary',
                }),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            ID::make(),
            BelongsTo::make('Fatura Vinculada', 'invoice', fn($item) => $item->invoice_number ?? '#' . $item->id)
                ->nullable()
                ->searchable(),
            Text::make('Néºmero NFS-e', 'numero')
                ->hint('Deixe vazio para auto-gerar'),
            Text::make('Sé©rie', 'serie')
                ->default('A1'),
            Date::make('Data de Emissé£o', 'data_emissao')
                ->required(),
            Text::make('Cé³digo do Servié§o (CNAE)', 'codigo_servico')
                ->hint('Ex: 7020'),
            Textarea::make('Discriminação do Servié§o', 'discriminacao')
                ->required(),
            Number::make('Valor do Servié§o (R$)', 'valor_servico')
                ->min(0)
                ->step(0.01)
                ->required(),
            Number::make('Alé­quota ISS (%)', 'aliquota_iss')
                ->min(0)
                ->step(0.01)
                ->default(5.00),
            Number::make('Valor ISS (R$)', 'valor_iss')
                ->min(0)
                ->step(0.01)
                ->hint('Calculado automaticamente se vazio'),
            Text::make('CPF/CNPJ do Tomador', 'tomador_cnpj_cpf')
                ->required(),
            Text::make('Nome do Tomador', 'tomador_nome')
                ->required(),
            Text::make('Endereé§o do Tomador', 'tomador_endereco'),
            Text::make('E-mail do Tomador', 'tomador_email'),
            Select::make('Status', 'status')
                ->options([
                    'rascunho' => 'Rascunho',
                    'emitida' => 'Emitida',
                    'cancelada' => 'Cancelada',
                ])
                ->default('rascunho')
                ->required(),
            Textarea::make('Observações', 'observacoes'),
        ];
    }
    protected function detailFields(): iterable
    {
        return $this->formFields();
    }
    protected function filters(): iterable
    {
        return [
            Text::make('Néºmero', 'numero'),
            Select::make('Status', 'status')
                ->options([
                    'rascunho' => 'Rascunho',
                    'emitida' => 'Emitida',
                    'cancelada' => 'Cancelada',
                ])
                ->nullable(),
            Date::make('Emissé£o De', 'data_emissao')
                ->nullable(),
        ];
    }
    public function indexButtons(): iterable
    {
        return [
            ActionButton::make('Gerar PDF', '#')
                ->icon('document-arrow-down')
                ->primary()
                ->method('generatePdf')
                ->canSee(fn($item) => $item->status === 'emitida'),
            ActionButton::make('Baixar PDF', fn($item) => Storage::disk('public')->url($item->pdf_path))
                ->icon('arrow-down-tray')
                ->blank()
                ->canSee(fn($item) => $item->pdf_path !== null),
        ];
    }
    public function detailButtons(): iterable
    {
        return $this->indexButtons();
    }
    protected function beforeSave(Model $item): Model
    {
        if (empty($item->numero)) {
            $item->numero = Nfse::generateNumero();
        }
        if (empty($item->valor_iss)) {
            $item->valor_iss = $item->calculateIss();
        }
        return $item;
    }
    public function generatePdf(MoonShineRequest $request): mixed
    {
        $item = $request->getResource()->getItem();
        // Carrega o invoice e contraro vinculado para pegar a filial/empresa
        $item->load('invoice.contract.branch');
        $branch = $item->invoice?->contract?->branch;
        $data = [
            'nfse' => $item,
            'branch' => $branch,
        ];
        // Gera o PDF
        $pdf = Pdf::loadView('admin.nfse.pdf', $data);
        $fileName = 'nfse_' . $item->numero . '.pdf';
        // Garante o direté³rio
        if (!Storage::disk('public')->exists('nfse')) {
            Storage::disk('public')->makeDirectory('nfse');
        }
        $path = 'nfse/' . $fileName;
        Storage::disk('public')->put($path, $pdf->output());
        $item->update(['pdf_path' => $path]);
        MoonShineUI::toast('PDF da NFS-e gerado com sucesso!', 'success');
        return back();
    }
}
