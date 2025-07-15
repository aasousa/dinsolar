<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ResidenceResource\Pages;
use App\Filament\Admin\Resources\ResidenceResource\RelationManagers;
use App\Models\Residence;
use App\Models\Location;
use App\Models\Panel;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResidenceResource extends Resource
{
    protected static ?string $model = Residence::class;

    protected static ?string $modelLabel = 'residência';

    protected static ?string $pluralModelName = 'residências';

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->label('Rótulo')
                    ->required()
                    ->placeholder('Casa, apartamento, etc.'),

                Forms\Components\Select::make('location_id')
                    ->label('Localidade')
                    ->required()
                    ->relationship('location', 'full_name')
                    ->getOptionLabelFromRecordUsing(fn(Location $record) => $record->full_name)
                    ->preload()
                    ->searchable(),

                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->label('Rótulo'),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Cidade')
                    ->searchable(),

                Tables\Columns\TextColumn::make('location.state')
                    ->label('Estado')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable()
                    ->hidden(!auth()->user()->hasRole('admin')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('calc')
                    ->label('Dimensionar')
                    ->modalHeading('Dimensionar Sistema Fotovoltaico')
                    ->modalSubmitAction(false)
                    ->icon('heroicon-o-calculator')
                    ->steps([
                        Step::make('Detalhes')
                            ->schema([
                                Fieldset::make('dados')
                                    ->label('Dados da Unidade Consumidora')
                                    ->columns(4)
                                    ->schema([
                                        Placeholder::make('consumo')
                                            ->label('Consumo Médio')
                                            ->content(fn(Residence $record): string => intval($record->averageKwh()) . " kWh/mês"),
                                        Placeholder::make('localizacao')
                                            ->label('Localização')
                                            ->content(fn(Residence $record): string => $record->location->full_name),
                                        Placeholder::make('irradiacao')
                                            ->label('Irradiação Anual')
                                            ->content(fn(Residence $record): string => intval($record->location->annual_irradiation) / 1000 . " Wh/m²"),
                                        Placeholder::make('potencia')
                                            ->label('Potência de Pico')
                                            ->content(fn(Residence $record): string => $record->potenciaPico() . " kWp")
                                    ])
                            ]),
                        Step::make('Equipamentos')
                            ->schema([
                                Fieldset::make('equipamentos')
                                    ->label('Equipamentos do Sistema')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('panel_id')
                                            ->label('Painel')
                                            ->helperText('Considerando a perda de eficiência máxima de 25% em 25 anos.')
                                            ->required()
                                            ->options(
                                                Panel::all()->mapWithKeys(fn($panel) => [
                                                    $panel->id => $panel->selectLabel()
                                                ])
                                            )
                                            ->native(false)
                                            ->columnSpanFull()
                                            ->live(),
                                        Placeholder::make('paineis')
                                            ->label('Quantidade necessária')
                                            ->content(function (Residence $record, Get $get) {
                                                $painel = Panel::find($get('panel_id'));
                                                if ($painel) {
                                                    $quantidade = ceil($record->potenciaPico() * 1000 * 1.25 / $painel->power);
                                                    $preco = round($quantidade * $painel->price, 2);
                                                    return "{$quantidade} paineis (R$ {$preco})";
                                                }
                                                return "Nenhum painel selecionado";
                                            }),
                                        Placeholder::make('inversor')
                                            ->label('Potência do Inversor')
                                            ->content(function (Residence $record) {
                                                $potencia = ceil($record->potenciaPico() * 1.25);
                                                return "Maior o igual a {$potencia} kW";
                                            }),
                                    ])
                            ]),
                    ]),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()

            ->when(
                !auth()->user()->hasRole('admin'),
                fn(Builder $query) => $query
                    ->where('user_id', auth()->id())
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageResidences::route('/'),
        ];
    }
}
