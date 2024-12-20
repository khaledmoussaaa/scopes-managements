<?php

namespace App\Filament\Resources;

use App\Filament\Exports\DepartmentExporter;
use App\Filament\Resources\DepartmentResource\Pages;
use App\Models\Department;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section as ComponentsSection;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationLabel = 'Departments';
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Department Managements';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Department Details')
                    ->description('Provide the information of the department.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Department Name')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('description')
                            ->maxLength(255),

                        Select::make('admin_department_id')
                            ->label('Admin of The Department')
                            ->options(User::whereDoesntHave('employee')->get()->pluck('name', 'id'))
                            ->native(false)
                            ->required()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('admin_department.name')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()->exporter(DepartmentExporter::class)->label('Export')
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->tooltip('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make(fn(?Department $record) => $record ? $record->name : 'Create Department')
                    ->schema([
                        TextEntry::make('description'),

                        TextEntry::make('admin_department.name')
                            ->label('Admin Name')
                            ->badge()
                            ->color('info')
                    ]),

                ComponentsSection::make('Admin Information')
                    ->description('The admin contacts')
                    ->schema([
                        TextEntry::make('admin_department.email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->iconColor('primary')
                            ->copyable(),

                        TextEntry::make('admin_department.phone')
                            ->label('Phone')
                            ->icon('heroicon-m-phone')
                            ->iconColor('primary')
                            ->copyable()
                    ])->columns(2)->collapsed()
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            // 'view' => Pages\ViewDepartment::route('/{record}'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
