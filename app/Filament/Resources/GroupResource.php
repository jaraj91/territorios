<?php

namespace App\Filament\Resources;

use App\Enums\GroupStatus;
use App\Filament\Filters\DateFilter;
use App\Filament\Resources\GroupResource\Pages;
use App\Models\Group;
use App\Models\Territory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $modelLabel = 'Progreso';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\CheckboxList::make('progress')
                    ->options(function (Model $record) {
                        $sections = $record->territory->sections ?? [];
                        return array_combine($sections, $sections);
                    })
                    ->columns(2)
                    ->label('')
                    ->bulkToggleable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address.address')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('captain.name'),
                Tables\Columns\TextColumn::make('territory.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('progress')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn (GroupStatus $state) => $state->value)
                    ->badge()
                    ->color(fn (GroupStatus $state): string => $state?->color()),
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function (Model $record) {
                        $record->update(['progress' => null]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGroups::route('/'),
        ];
    }
}
