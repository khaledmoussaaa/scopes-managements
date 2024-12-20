<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use App\Models\SubTask;
use App\TaskStatus;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubtasksRelationManager extends RelationManager
{
    protected static string $relationship = 'subtasks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(fn(?SubTask $record) => $record ? $record->task->title : 'Create New Task')
                    ->description()
                    ->schema([
                        RichEditor::make('content')
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->required(),

                        ToggleButtons::make('status')
                            ->options(TaskStatus::class)
                            ->inline()
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('content')
                    ->markdown()
                    ->wrap()
                    ->limit(500),

                TextColumn::make('status')
                    ->badge(),

            ])

            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('New Task'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public function isReadOnly(): bool
    {
        return false;
    }
}
