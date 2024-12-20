<?php

namespace App\Filament\Resources;


use App\Models\Department;
use App\Models\Employee;
use App\Filament\Exports\EmployeeExporter;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\Fieldset;
use Filament\Tables\Actions\ActionGroup;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationLabel = 'Employees';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Users Managements';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->description('Provide the basic personal information of the employee.')
                    ->schema([
                        Group::make()->relationship('user')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\DatePicker::make('birth_date')
                                    ->label('Birth Date')
                                    ->placeholder('dd/mm/yyyy')
                                    ->displayFormat('d/m/Y')
                                    ->native(false),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->required()
                                    ->unique('users', 'phone', fn($record) => $record)
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->unique('users', 'email', fn($record) => $record)
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->required()
                                    ->maxLength(255)
                                    ->hiddenOn('edit')
                                    ->columnSpanFull(),
                            ])->columns(2)
                    ])->columnSpanFull(),

                Section::make('Employment Information')
                    ->description('Provide the work-related information for the user.')
                    ->schema([
                        Group::make()->relationship('user')
                            ->schema([
                                Select::make('position')
                                    ->options([
                                        'CEO' => 'CEO',
                                        'Manager' => 'Manager',
                                        'Finance' => 'Finance',
                                        'Software Engineer' => 'Software Engineer',
                                    ])
                                    ->searchable()
                                    ->native(false),

                                Forms\Components\DatePicker::make('hired_date')
                                    ->label('Hired Date')
                                    ->placeholder('dd/mm/yyyy')
                                    ->displayFormat('d/m/Y')
                                    ->native(false),
                            ])->columns(2),

                        Group::make()->schema([
                            Forms\Components\TextInput::make('address')
                                ->maxLength(255)
                                ->default(null),

                            Forms\Components\TextInput::make('salary')
                                ->default(0),

                            Select::make('experties')
                                ->options([
                                    'fresh' => 'Fresh',
                                    'genior' => 'Genior',
                                    'senior' => 'Senior',
                                    'expert' => 'Expert',
                                ])
                                ->native(false),

                            Select::make('department_id')
                                ->label('Department')
                                ->native(false)
                                ->options(Department::where('id', auth()->user()->admin_department?->id)->get()->pluck('name', 'id'))
                                ->default(auth()->user()->admin_department?->id)
                                ->visible(auth()->user()->admin_department ? true : false)
                                ->selectablePlaceholder(false),

                            Select::make('department_id')
                                ->label('Department')
                                ->relationship('department', 'name')
                                ->native(false)
                                ->required()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->required()
                                        ->label('Department Name')
                                        ->maxLength(255),

                                    TextInput::make('description')
                                        ->maxLength(255),

                                    Select::make('admin_department_id')
                                        ->label('Admin of The Department')
                                        ->options(User::whereDoesntHave('employee')->get()->pluck('name', 'id'))
                                        ->preload()
                                        ->native(false)
                                        ->required(),
                                ])
                                ->columns(2)
                                ->visible(auth()->user()->admin_department ? false : true)
                        ])->columns(2)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups(['department.name', 'department.admin_department.name'])
            ->columns([
                TextColumn::make('user.name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary')
                    ->copyable(),

                TextColumn::make('user.phone')
                    ->label('Phone')
                    ->icon('heroicon-m-phone')
                    ->iconColor('primary')
                    ->copyable(),

                TextColumn::make('user.position')
                    ->label('Position')
                    ->badge()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),


                TextColumn::make('department.name')
                    ->label('Department')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('department.admin_department.name')
                    ->label('Admin Department')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('experties')
                    ->toggleable(isToggledHiddenByDefault: true),


                TextColumn::make('user.hired_date')
                    ->label('Hired Date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user.birth_date')
                    ->label('Birth Date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('salary')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()->exporter(EmployeeExporter::class)->label('Export')
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make(fn(?Employee $record) => $record ? $record->user->name : 'Create User')
                    ->schema([
                        Fieldset::make('Personal Information')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Full Name'),

                                TextEntry::make('user.birth_date')
                                    ->label('Birth Date'),

                                TextEntry::make('user.phone')
                                    ->label('Phone')
                                    ->icon('heroicon-m-phone')
                                    ->iconColor('primary')
                                    ->copyable(),

                                TextEntry::make('user.email')
                                    ->label('Email')
                                    ->icon('heroicon-m-envelope')
                                    ->iconColor('primary')
                                    ->copyable()
                            ])->columns(2),

                        Fieldset::make('Employment Information')
                            ->schema([
                                TextEntry::make('user.position')
                                    ->label('Position')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('user.hired_date')
                                    ->label('Hired Date'),

                                TextEntry::make('address')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('salary')
                                    ->label('Salary')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('experties')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('department.name')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('user.roles.name')
                                    ->label('Role')
                                    ->badge(),
                            ])->columns(2)
                    ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
