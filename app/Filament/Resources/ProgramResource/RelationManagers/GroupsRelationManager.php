<?php

namespace App\Filament\Resources\ProgramResource\RelationManagers;

use App\Filament\Filters\DateFilter;
use App\Models\Territory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('date')
                    ->native(false)
                    ->seconds(false)
                    ->minutesStep(15)
                    ->required(),
                Forms\Components\Select::make('address_id')
                    ->relationship('address', 'address')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('address')
                            ->required(),
                    ])
                    ->required(),
                Forms\Components\Select::make('captain_id')
                    ->relationship('captain', 'name')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                    ])
                    ->required(),
                Forms\Components\Select::make('territories')
                    ->hidden(fn (string $operation) => $operation !== 'create')
                    ->multiple()
                    ->options(Territory::orderByRaw('CONVERT(name, SIGNED) asc')->pluck('name', 'id'))
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('territory_id')
                    ->hidden(fn (string $operation) => $operation !== 'edit')
                    ->options(Territory::orderByRaw('CONVERT(name, SIGNED) asc')->pluck('name', 'id'))
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'General' => 'General',
                        'G1' => 'G1',
                        'G2' => 'G2',
                        'G3' => 'G3',
                        'G4' => 'G4',
                        'G5' => 'G5',
                        'G6' => 'G6',
                        'G7' => 'G7',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address.address'),
                Tables\Columns\TextColumn::make('captain.name'),
                Tables\Columns\TextColumn::make('territory.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
