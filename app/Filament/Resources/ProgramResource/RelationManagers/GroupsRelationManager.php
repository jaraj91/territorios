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
use Illuminate\Support\Carbon;
use Livewire\Component as Livewire;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $label = 'Grupo';

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
                    ->default(function (Livewire $livewire) {
                        ['year' => $year, 'month' => $month] = $livewire->getOwnerRecord()->getAttributes();
                        return Carbon::make("$year-$month-01 10:00");
                    })
                    ->minDate(function (Livewire $livewire) {
                        ['year' => $year, 'month' => $month] = $livewire->getOwnerRecord()->getAttributes();
                        return Carbon::make("$year-$month-01");
                    })
                    ->maxDate(function (Livewire $livewire) {
                        ['year' => $year, 'month' => $month] = $livewire->getOwnerRecord()->getAttributes();
                        return Carbon::make("$year-$month-01")?->endOfMonth();
                    })
                    ->required(),
                Forms\Components\Toggle::make('only_comment')
                    ->label('¿Ingresar sólo comentario?')
                    ->formatStateUsing(fn (?Model $record) => $record?->only_comment)

                    ->live(),
                Forms\Components\Textarea::make('comment')
                    ->label('Comentario')
                    ->visible(fn (Forms\Get $get) => $get('only_comment')),
                Forms\Components\Select::make('address_id')
                    ->label('Dirección')
                    ->relationship('address', 'address')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->required(),
                    ])
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->hidden(fn (Forms\Get $get) => $get('only_comment')),
                Forms\Components\Select::make('captain_id')
                    ->label('Capitán')
                    ->relationship('captain', 'name')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                    ])
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required()
                    ->hidden(fn (Forms\Get $get) => $get('only_comment')),
                Forms\Components\Select::make('territories')
                    ->label('Territorios')
                    ->hidden(fn (Forms\Get $get, string $operation) => $operation !== 'create' || $get('only_comment'))
                    ->multiple()
                    ->options(Territory::orderByRaw('CONVERT(name, SIGNED) asc')->pluck('name', 'id'))
                    ->required()
                    ->preload(),
                Forms\Components\Select::make('territory_id')
                    ->label('Territorio')
                    ->hidden(fn (Forms\Get $get, string $operation) => $operation !== 'edit' || $get('only_comment'))
                    ->options(Territory::orderByRaw('CONVERT(name, SIGNED) asc')->pluck('name', 'id'))
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Grupos de servicio')
                    ->options([
                        'General' => 'General',
                        'Por grupos' => 'Por grupos',
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
                    ->required()
                    ->hidden(fn (Forms\Get $get) => $get('only_comment')),
                Forms\Components\Toggle::make('is_highlight_day')
                    ->label('¿Resaltar día completo?'),
                Forms\Components\Toggle::make('is_highlight_hour')
                    ->label('¿Resaltar solo horario?')
                    ->hidden(fn (Forms\Get $get) => $get('only_comment')),
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
                        $useOnlyComment = $data['only_comment'];
                        unset($data['only_comment']);
                        $data['program_id'] = $livewire->getOwnerRecord()->id;

                        if ($useOnlyComment) {
                            $lastObject = $model::create($data);
                        } else {
                            $territories = $data['territories'];
                            unset($data['territories']);
                            foreach ($territories as $territory) {
                                $data['territory_id'] = $territory;
                                $lastObject = $model::create($data);
                            }
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
                                    $sections = $record->territory?->sections ?? [];
                                    return array_combine($sections, $sections);
                                })
                                ->columns(2)
                                ->label('')
                                ->bulkToggleable(),
                        ]),
                    Tables\Actions\Action::make('remove-progress')
                        ->label('Borrar Progreso')
                        ->icon('heroicon-o-bookmark-slash')
                        ->requiresConfirmation()
                        ->action(function (Model $record) {
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
                        ->action(fn (Collection $records) => $records->each(fn (Group $group) => $group->progress ?? $group->update(['progress' => $group->territory?->sections ?? []])))
                ]),
            ]);
    }
}
