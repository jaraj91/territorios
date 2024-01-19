<?php

namespace App\Filament\Resources;

use App\Enums\Months;
use App\Filament\Resources\ProgramResource\Pages;
use App\Filament\Resources\ProgramResource\RelationManagers;
use App\Models\Group;
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

    protected static ?string $label = 'Programa';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('year')
                    ->label('Año')
                    ->minValue(2023)
                    ->maxValue(fn (Forms\Components\TextInput $component) => (int) date('Y') + 1)
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('month')
                    ->label('Mes')
                    ->options(Months::list())
                    ->native(false)
                    ->required(),
                Forms\Components\TextInput::make('bg_primary')
                    ->label('Color principal'),
                Forms\Components\TextInput::make('bg_secondary')
                    ->label('Color secundario'),
                Forms\Components\Textarea::make('comment')
                    ->label('Comentario')
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->sortable(),
                Tables\Columns\TextColumn::make('month')
                    ->label('Mes')
                    ->formatStateUsing(fn (Months $state): string => $state->label())
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Año')
                    ->options(Program::pluck('year', 'year')->unique()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('defaultProgress')
                    ->label('Ingresar Progreso Completo')
                    ->icon('heroicon-o-forward')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Program $record) => $record->groups->each(fn (Group $group) => $group->progress ?? $group->update(['progress' => $group->territory->sections])))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('program_pdf')
                        ->label('Ver PDF programa')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->action(fn (Collection $records) => redirect(route('pdf_program', ['ids' => $records->pluck('id')->all()]))),
                    Tables\Actions\BulkAction::make('s13form_pdf')
                        ->label('Ver PDF formulario s13')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->form([
                            Forms\Components\TextInput::make('year')
                                ->required()
                        ])
                        ->action(fn (Collection $records, array $data) => redirect(route('pdf_s13form', ['ids' => $records->pluck('id')->all(), 'year' => $data['year']]))),
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
