<?php

namespace App\Filament\Resources;

use App\Filament\Exports\TaskExporter;
use App\Filament\Resources\TaskResource\Pages;
use App\Models\Department;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\Layout\Split;
use TangoDevIt\FilamentEmojiPicker\EmojiPickerAction;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationLabel = 'Task';
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'Teamspace';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Board Tasks')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Board Title')
                            ->required()
                            ->maxLength(255)
                            ->suffixAction(EmojiPickerAction::make('emoji-title'))
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->label('Description')
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->columnSpanFull(),

                        // =================================================================== //
                        Section::make()
                            ->schema([
                                Select::make('departments')
                                    ->label('Assign Department')
                                    ->native(false)
                                    ->relationship(
                                        name: 'departments',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: function (Builder $query) {
                                            if (auth()->user()->admin_department) {
                                                $query->where('department_id', auth()->user()->admin_department->id);
                                            } else {
                                                $query->get();
                                            }
                                        },
                                    )
                                    ->preload()
                                    ->selectablePlaceholder(false)
                                    ->multiple(auth()->user()->admin_department ? false : true)
                                    ->default(auth()->user()->admin_department?->id),

                                Select::make('admins')
                                    ->label('Assign User')
                                    ->native(false)
                                    ->relationship(
                                        name: 'admins',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: function (Builder $query) {
                                            if (auth()->user()->admin_department) {
                                                $query->whereHas('employee', function ($q) {
                                                    $q->where('department_id', auth()->user()->admin_department->id);
                                                });
                                            } else {
                                                $query->whereDoesntHave('employee')->get();
                                            }
                                        },
                                    )
                                    ->preload()
                                    ->selectablePlaceholder(false)
                                    ->multiple(),


                                // // Select for departments
                                // Select::make('departments')
                                //     ->label('Assign Department')
                                //     ->options(Department::all()->pluck('name', 'id')) 
                                //     ->relationship('departments', 'name')
                                //     ->multiple()
                                //     ->preload(),

                                // // Select for users
                                // Select::make('users')
                                //     ->label('Assign User')
                                //     ->relationship('admins', 'name')
                                //     ->options(User::whereDoesntHave('employee')->pluck('name', 'id'))
                                //     ->multiple()
                                //     ->preload(),
                            ])->columns(),
                        // =================================================================== //
                    ])->columns(),

                Section::make()
                    ->schema([
                        Repeater::make('subtasks')
                            ->label('Tasks')
                            ->relationship()
                            ->schema([
                                RichEditor::make('content')
                                    ->disableToolbarButtons([
                                        'attachFiles',
                                    ])
                                    ->required(),

                                ToggleButtons::make('priority')
                                    ->label('Priority')
                                    ->options([
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High'
                                    ])
                                    ->colors([
                                        'low' => 'gray',
                                        'medium' => 'warning',
                                        'high' => 'danger',
                                    ])
                                    ->default('low')
                                    ->inline()
                                    ->required(),
                            ])
                            ->reorderable(true)
                            ->reorderableWithButtons()
                            ->addActionLabel('Add Task')
                            ->cloneable()
                            ->columnSpanFull(),
                    ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Tables\Columns\TextColumn::make('title')
                        ->label('Task Title')
                        ->wrap()
                        ->searchable(),

                    Tables\Columns\TextColumn::make('descirption')
                        ->markdown()
                        ->wrap()
                        ->limit(50)
                        ->toggleable(isToggledHiddenByDefault: true),


                    Tables\Columns\TextColumn::make('proierty')
                        ->badge(),

                ])
                    ->from('xl')
            ])
            ->filters([])
            ->headerActions([
                ExportAction::make()->exporter(TaskExporter::class)->label('Export')
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->label('Actions')
                    ->icon('heroicon-m-ellipsis-horizontal-circle')
                    ->size(ActionSize::Small)
                    ->color('gray')
                    ->button()
                    ->labeledFrom('md')
                    ->outlined()

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make(fn(?Task $record) => $record ? $record->title : 'Create Board')
                    ->schema([
                        Fieldset::make('Board Information')
                            ->schema([
                                TextEntry::make('description')
                                    ->markdown()
                                    ->columnSpanFull(),

                                TextEntry::make('proierty')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'low' => 'gray',
                                        'medium' => 'warning',
                                        'high' => 'danger',
                                    })
                            ]),
                    ])->columns(2)
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
