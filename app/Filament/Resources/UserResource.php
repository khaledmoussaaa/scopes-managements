<?php

namespace App\Filament\Resources;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'User Admins';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Users Managements';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->description('Provide the basic personal information of the user.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Birth Date')
                            ->placeholder('mm,dd,yy')
                            ->native(false),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->unique('users', 'email', fn($record) => $record)
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->required()
                            ->unique('users', 'phone', fn($record) => $record)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->hiddenOn('edit'),
                    ])->columns(2),

                Section::make('Employment Information')
                    ->description('Provide the work-related information for the user.')
                    ->schema([
                        Select::make('position')
                            ->options([
                                'CEO' => 'CEO',
                                'Manager' => 'Manager',
                            ])
                            ->searchable()
                            ->native(false),

                        Forms\Components\DatePicker::make('hired_date')
                            ->label('Hired Date')
                            ->placeholder('mm,dd,yy')
                            ->native(false),

                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->required()
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->iconColor('primary'),

                TextColumn::make('phone')
                    ->icon('heroicon-m-phone')
                    ->iconColor('primary'),

                TextColumn::make('roles.name')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('hired_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('position')
                    ->badge()
                    ->searchable(),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->getStateUsing(fn($record) => $record->email_verified_at !== null)
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()->exporter(UserExporter::class)->label('Export')
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make(fn(?User $record) => $record ? $record->name : 'Create User')
                    ->schema([
                        Fieldset::make('Personal Information')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Full Name'),

                                TextEntry::make('email')
                                    ->copyable(),

                                TextEntry::make('phone'),

                                TextEntry::make('birth_date')->label('Birth Date')
                            ])->columns(2),

                        Fieldset::make('Employment Information')
                            ->schema([
                                TextEntry::make('position')->badge()->color('gray'),

                                TextEntry::make('hired_date')->label('Hired Date'),

                                TextEntry::make('roles.name')->badge(),
                            ])->columns(2)
                    ])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
