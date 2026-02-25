<?php

namespace App\Filament\Resources;

use Filament\Actions;

use App\Filament\Resources\NfseResource\Pages;
use App\Models\Nfse;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class NfseResource extends Resource
{
    protected static ?string $model = Nfse::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static string|\UnitEnum|null $navigationGroup = 'Financeiro';

    protected static ?string $modelLabel = 'Nota Fiscal (NFS-e)';

    protected static ?string $pluralModelLabel = 'Notas Fiscais (NFS-e)';

    protected static ?int $navigationSort = 5;

    public static function getGloballySearchableAttributes(): array
    {
        return ['numero', 'tomador_nome', 'invoice.invoice_number'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Dados Gerais da Nota')->schema([
                Grid::make(3)->schema([
                    Components\Select::make('invoice_id')
                        ->label('Fatura Referente')
                        ->relationship('invoice', 'invoice_number')
                        ->searchable()
                        ->preload(),
                    Components\TextInput::make('numero')->label('Numero (Gerado Auto)')->disabled()->dehydrated(false),
                    Components\TextInput::make('serie')->label('Serie')->default('1')->maxLength(10),
                    Components\DatePicker::make('data_emissao')->label('Data de Emissao')->required()->native(false)->default(now()),
                    Components\TextInput::make('codigo_servico')->label('Codigo de Servico')->required()->default('11.02')->maxLength(50),
                    Components\Select::make('status')->label('Status')->options([
                        'novo' => 'Novo (Nao Transmitida)',
                        'processando' => 'Processando',
                        'autorizado' => 'Autorizado',
                        'cancelado' => 'Cancelado',
                        'erro' => 'Erro',
                    ])->default('novo')->required(),
                ]),
            ]),

            Section::make('Valores e Impostos')->schema([
                Grid::make(3)->schema([
                    Components\TextInput::make('valor_servico')->label('Valor do Servico (R$)')->numeric()->prefix('R$')->required(),
                    Components\TextInput::make('aliquota_iss')->label('Aliquota ISS (%)')->numeric()->suffix('%')->default(3.00),
                    Components\TextInput::make('valor_iss')->label('Valor ISS Calculado (R$)')->numeric()->prefix('R$')->helperText('Auto calculado se vazio'),
                ]),
                Components\Textarea::make('discriminacao')->label('Discriminacao dos Servicos')->required()->columnSpanFull(),
            ]),

            Section::make('Dados do Tomador (Cliente)')->schema([
                Grid::make(2)->schema([
                    Components\TextInput::make('tomador_cnpj_cpf')->label('CPF/CNPJ')->required()->maxLength(20),
                    Components\TextInput::make('tomador_nome')->label('Nome/Razao Social')->required()->maxLength(255),
                    Components\TextInput::make('tomador_email')->label('E-mail')->email()->maxLength(255),
                    Components\TextInput::make('tomador_endereco')->label('Endereco Completo')->maxLength(255),
                ]),
            ]),

            Section::make('Arquivos Fiscais')->schema([
                Grid::make(2)->schema([
                    Components\FileUpload::make('pdf_path')->label('Documento Auxiliar (PDF)')->directory('nfse-pdfs')->acceptedFileTypes(['application/pdf']),
                    Components\FileUpload::make('xml_path')->label('Arquivo XML')->directory('nfse-xmls')->acceptedFileTypes(['text/xml', 'application/xml']),
                ]),
                Components\Textarea::make('observacoes')->label('Observacoes da Prefeitura / Erros')->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')->label('Numero')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('tomador_nome')->label('Tomador')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('data_emissao')->label('Emissao')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('valor_servico')->label('Valor (R$)')->formatStateUsing(fn ($state) => 'R$ '.number_format((float) $state, 2, ',', '.'))->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'novo', 'processando' => 'warning',
                        'autorizado' => 'success',
                        'cancelado', 'erro' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'novo' => 'Novo (Nao Transmitida)',
                    'processando' => 'Processando',
                    'autorizado' => 'Autorizado',
                    'cancelado' => 'Cancelado',
                    'erro' => 'Erro',
                ]),
            ])
            ->actions([
                Actions\EditAction::make(),
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
            'index' => Pages\ListNfses::route('/'),
            'create' => Pages\CreateNfse::route('/create'),
            'edit' => Pages\EditNfse::route('/{record}/edit'),
        ];
    }
}
