<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\ContractTemplateResource\Pages;
use App\Models\ContractTemplate;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ContractTemplateResource extends Resource
{
    protected static ?string $model = ContractTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Operacional';

    protected static ?string $modelLabel = 'Template de Contrato';

    protected static ?string $pluralModelLabel = 'Templates de Contrato';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Configuracoes Gerais')->schema([
                Grid::make(2)->schema([
                    Components\Select::make('branch_id')->label('Filial')->relationship('branch', 'name')->searchable()->preload()->required(),
                    Components\TextInput::make('name')->label('Nome do Template')->required()->maxLength(255),
                ]),
                Grid::make(2)->schema([
                    Components\Toggle::make('is_default')->label('Define como Padrao')->default(false),
                    Components\Toggle::make('is_active')->label('Ativo')->default(true),
                ]),
            ])->columns(1),

            Section::make('Variaveis Disponiveis')->schema([
                Components\Placeholder::make('variables_help')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString('
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; font-size: 12px;">
                            <div style="background: #1e293b; color: #f8fafc; border-radius: 6px; padding: 10px;">
                                <strong style="color: #fbbf24;">📋 Contrato</strong><br>
                                <code>{{contrato_numero}}</code><br>
                                <code>{{contrato_inicio}}</code><br>
                                <code>{{contrato_fim}}</code><br>
                                <code>{{contrato_valor_mensal}}</code><br>
                                <code>{{contrato_valor_total}}</code><br>
                                <code>{{contrato_caucao}}</code><br>
                                <code>{{contrato_km_livre}}</code><br>
                                <code>{{contrato_km_excedente}}</code><br>
                                <code>{{contrato_data_assinatura}}</code>
                            </div>
                            <div style="background: #1e293b; color: #f8fafc; border-radius: 6px; padding: 10px;">
                                <strong style="color: #34d399;">👤 Cliente</strong><br>
                                <code>{{cliente_nome}}</code><br>
                                <code>{{cliente_cpf}}</code><br>
                                <code>{{cliente_cnpj}}</code><br>
                                <code>{{cliente_rg}}</code><br>
                                <code>{{cliente_email}}</code><br>
                                <code>{{cliente_telefone}}</code><br>
                                <code>{{cliente_endereco}}</code><br>
                                <code>{{cliente_cidade}}</code><br>
                                <code>{{cliente_estado}}</code><br>
                                <code>{{cliente_cep}}</code><br>
                                <code>{{cliente_cnh}}</code>
                            </div>
                            <div style="background: #1e293b; color: #f8fafc; border-radius: 6px; padding: 10px;">
                                <strong style="color: #60a5fa;">🚗 Veiculo</strong><br>
                                <code>{{veiculo_placa}}</code><br>
                                <code>{{veiculo_marca}}</code><br>
                                <code>{{veiculo_modelo}}</code><br>
                                <code>{{veiculo_ano}}</code><br>
                                <code>{{veiculo_cor}}</code><br>
                                <code>{{veiculo_chassi}}</code><br>
                                <code>{{veiculo_renavam}}</code><br>
                                <code>{{veiculo_km}}</code><br>
                                <code>{{veiculo_combustivel}}</code>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; font-size: 12px; margin-top: 6px;">
                            <div style="background: #1e293b; color: #f8fafc; border-radius: 6px; padding: 10px;">
                                <strong style="color: #f472b6;">🏢 Empresa</strong><br>
                                <code>{{empresa_nome}}</code><br>
                                <code>{{empresa_cnpj}}</code><br>
                                <code>{{empresa_telefone}}</code><br>
                                <code>{{empresa_email}}</code><br>
                                <code>{{empresa_endereco}}</code><br>
                                <code>{{empresa_cidade}}</code><br>
                                <code>{{empresa_estado}}</code>
                            </div>
                            <div style="background: #1e293b; color: #f8fafc; border-radius: 6px; padding: 10px;">
                                <strong style="color: #a78bfa;">📅 Datas e Outros</strong><br>
                                <code>{{data_atual}}</code><br>
                                <code>{{data_extenso}}</code><br>
                                <code>{{filial_nome}}</code><br>
                                <code>{{filial_endereco}}</code><br>
                                <code>{{assinatura_locador}}</code><br>
                                <code>{{assinatura_locatario}}</code>
                            </div>
                        </div>
                        <p style="font-size: 11px; color: #6b7280; margin-top: 8px;">💡 Copie e cole as variaveis acima no conteudo do template. Elas serao substituidas automaticamente ao gerar o contrato.</p>
                    ')),
            ])->collapsible(),

            Section::make('Conteudo do Contrato')->schema([
                Components\RichEditor::make('content')
                    ->label('')
                    ->columnSpanFull()
                    ->required()
                    ->extraInputAttributes(['style' => 'min-height: 600px;'])
                    ->toolbarButtons([
                        'attachFiles', 'blockquote', 'bold', 'bulletList',
                        'codeBlock', 'h2', 'h3', 'italic', 'link',
                        'orderedList', 'redo', 'strike', 'underline', 'undo',
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('branch.name')->label('Filial')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_default')->label('Padrao')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')->label('Filial')->relationship('branch', 'name'),
            ])
            ->actions([
                Actions\EditAction::make(),

                // Exportar PDF de preview
                Actions\Action::make('export_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->action(function (ContractTemplate $record) {
                        $setting = fn ($key, $default = '') => \App\Models\Setting::get($key, $default);

                        // Substituir variáveis com dados de exemplo
                        $replacements = [
                            '{{contrato_numero}}' => 'PREVIEW-001',
                            '{{contrato_inicio}}' => now()->format('d/m/Y'),
                            '{{contrato_fim}}' => now()->addMonths(12)->format('d/m/Y'),
                            '{{contrato_valor_mensal}}' => 'R$ 2.500,00',
                            '{{contrato_valor_total}}' => 'R$ 30.000,00',
                            '{{contrato_caucao}}' => 'R$ 3.000,00',
                            '{{contrato_km_livre}}' => '3.000 km/mes',
                            '{{contrato_km_excedente}}' => 'R$ 0,50/km',
                            '{{contrato_data_assinatura}}' => now()->format('d/m/Y'),
                            '{{cliente_nome}}' => 'JOAO DA SILVA (EXEMPLO)',
                            '{{cliente_cpf}}' => '000.000.000-00',
                            '{{cliente_cnpj}}' => '00.000.000/0001-00',
                            '{{cliente_rg}}' => '0000000 SSP/MT',
                            '{{cliente_email}}' => 'cliente@exemplo.com',
                            '{{cliente_telefone}}' => '(66) 99999-0000',
                            '{{cliente_endereco}}' => 'Rua Exemplo, 123, Centro',
                            '{{cliente_cidade}}' => 'Sinop',
                            '{{cliente_estado}}' => 'MT',
                            '{{cliente_cep}}' => '78550-000',
                            '{{cliente_cnh}}' => '00000000000',
                            '{{veiculo_placa}}' => 'ABC-1D23',
                            '{{veiculo_marca}}' => 'Toyota',
                            '{{veiculo_modelo}}' => 'Corolla XEi 2.0',
                            '{{veiculo_ano}}' => '2025/2026',
                            '{{veiculo_cor}}' => 'Prata',
                            '{{veiculo_chassi}}' => '9BWZZZ377VT004251',
                            '{{veiculo_renavam}}' => '00000000000',
                            '{{veiculo_km}}' => '15.230 km',
                            '{{veiculo_combustivel}}' => 'Flex',
                            '{{empresa_nome}}' => $setting('company_name', 'Elite Locadora de Veiculos'),
                            '{{empresa_cnpj}}' => $setting('company_cnpj', '00.000.000/0001-00'),
                            '{{empresa_telefone}}' => $setting('company_phone', '(66) 3521-0000'),
                            '{{empresa_email}}' => $setting('company_email', 'contato@elitelocadora.com.br'),
                            '{{empresa_endereco}}' => $setting('company_address', ''),
                            '{{empresa_cidade}}' => $setting('company_city', 'Sinop'),
                            '{{empresa_estado}}' => $setting('company_state', 'MT'),
                            '{{data_atual}}' => now()->format('d/m/Y'),
                            '{{data_extenso}}' => now()->translatedFormat('d \d\e F \d\e Y'),
                            '{{filial_nome}}' => $record->branch?->name ?? 'Filial',
                            '{{filial_endereco}}' => '',
                            '{{assinatura_locador}}' => '___________________________________',
                            '{{assinatura_locatario}}' => '___________________________________',
                        ];

                        $content = str_replace(array_keys($replacements), array_values($replacements), $record->content);

                        $logoBase64 = null;
                        $logoPath = public_path('images/logo-elite.png');
                        if (file_exists($logoPath)) {
                            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                        }

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.contract-template-preview', [
                            'template' => $record,
                            'content' => $content,
                            'logoBase64' => $logoBase64,
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'Preview-' . $record->name . '.pdf',
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractTemplates::route('/'),
            'create' => Pages\CreateContractTemplate::route('/create'),
            'edit' => Pages\EditContractTemplate::route('/{record}/edit'),
        ];
    }
}
