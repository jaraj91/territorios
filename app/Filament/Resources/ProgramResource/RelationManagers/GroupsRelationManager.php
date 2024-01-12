<?php

namespace App\Filament\Resources\ProgramResource\RelationManagers;

use App\Enums\GroupStatus;
use App\Filament\Filters\DateFilter;
use App\Models\Group;
use App\Models\Territory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $label = 'Grupo de predicación';

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\DateTimePicker::make('date')
                    ->label('Fecha y Hora')
                    ->native(false)
                    ->seconds(false)
                    ->minutesStep(15)
                    ->displayFormat('d-m-Y H:i')
                    ->closeOnDateSelection()
                    ->default(now()->format('d-m-Y 09:15'))
                    ->required(),
                Forms\Components\Select::make('address_id')
                    ->label('Dirección')
                    ->relationship('address', 'address')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('address')
                            ->required(),
                    ])
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('captain_id')
                    ->label('Capitán')
                    ->relationship('captain', 'name')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                    ])
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('territories')
                    ->label('Territorios')
                    ->hidden(fn (string $operation) => $operation !== 'create')
                    ->multiple()
                    ->options(Territory::orderByRaw('CONVERT(name, SIGNED) asc')->pluck('name', 'id'))
                    ->required()
                    ->preload(),
                Forms\Components\Select::make('territory_id')
                    ->label('Territorio')
                    ->hidden(fn (string $operation) => $operation !== 'edit')
                    ->options(Territory::orderByRaw('CONVERT(name, SIGNED) asc')->pluck('name', 'id'))
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Grupos de servicio')
                    ->options([
                        'General' => 'General',
                        'Grupo 1' => 'Grupo 1',
                        'Grupo 2' => 'Grupo 2',
                        'Grupo 3' => 'Grupo 3',
                        'Grupo 4' => 'Grupo 4',
                        'Grupo 5' => 'Grupo 5',
                        'Grupo 6' => 'Grupo 6',
                        'Grupo 7' => 'Grupo 7',
                    ])
                    ->default('General')
                    ->native(false)
                    ->searchable()
                    ->required(),
                Forms\Components\Toggle::make('is_highlight_day')
                    ->label('¿Resaltar día completo?'),
                Forms\Components\Toggle::make('is_highlight_hour')
                ->label('¿Resaltar solo horario?'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->dateTime()
                    ->formatStateUsing(fn ($state) => $state->format('d-m-Y H:i'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('address.address')
                    ->label('Dirección'),
                Tables\Columns\TextColumn::make('captain.name')
                    ->label('Capitán'),
                Tables\Columns\TextColumn::make('territory.name')
                    ->label('Territorio')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progreso')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn (GroupStatus $state) => $state->value)
                    ->badge()
                    ->color(fn (GroupStatus $state): string => $state?->color()),
                Tables\Columns\TextColumn::make('type')
                    ->label('Grupo de servicio')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('territory')
                    ->multiple()
                    ->relationship(
                        'territory',
                        'name',
                        fn (Builder $query) => $query->orderByRaw('CONVERT(name, SIGNED) asc')
                    )
                    ->searchable()
                    ->preload(),
                DateFilter::make('date'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model, HasTable $livewire): Model {
                        $territories = $data['territories'];
                        unset($data['territories']);
                        $data['program_id'] = $livewire->getOwnerRecord()->id;
                        foreach ($territories as $territory) {
                            $data['territory_id'] = $territory;
                            $lastObject = $model::create($data);
                        }
                        return $lastObject;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make('edit')->iconButton(),
                Tables\Actions\DeleteAction::make('delete')->iconButton(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make('add-progress')
                        ->label('Ingresar Progreso')
                        ->icon('heroicon-o-forward')
                        ->color('success')
                        ->form([
                            Forms\Components\CheckboxList::make('progress')
                                ->options(function (Model $record) {
                                    $sections = $record->territory->sections ?? [];
                                    return array_combine($sections, $sections);
                                })
                                ->columns(2)
                                ->label('')
                                ->bulkToggleable(),
                        ]),
                    Tables\Actions\DeleteAction::make('remove-progress')
                        ->label('Borrar Progreso')
                        ->using(function (Model $record) {
                            $record->update(['progress' => null]);
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('defaultProgress')
                        ->label('Ingresar Progreso Completo')
                        ->icon('heroicon-o-forward')
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each(fn (Group $group) => $group->progress ?? $group->update(['progress' => $group->territory->sections])))
                ]),
            ]);
    }
}
