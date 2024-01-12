<?php

namespace App\Filament\Resources;

use App\Actions\CreateReportFolder;
use App\Enums\Months;
use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers;
use App\Models\Program;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('year')
                    ->minValue(2023)
                    ->maxValue(fn (Forms\Components\TextInput $component) => (int) date('Y') + 1)
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('month')
                    ->options(Months::list())
                    ->native(false)
                    ->required(),
                Forms\Components\TextInput::make('bg_primary'),
                Forms\Components\TextInput::make('bg_secondary'),
                Forms\Components\Textarea::make('comment')
                ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('month')
                    ->formatStateUsing(fn (Months $state): string => $state->label())
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('year')
                    ->options(Program::pluck('year', 'year')->unique()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('report')
                        ->label('Archivos Visita Super')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->form([
                            Forms\Components\TextInput::make('year')
                            ->required()
                        ])
                        ->action(fn (Collection $records, array $data) => app(CreateReportFolder::class, ['programs' => $records, 'year' => $data['year']])->execute()),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\GroupsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
            'view' => Pages\ViewProgram::route('/{record}/view'),
        ];
    }
}
