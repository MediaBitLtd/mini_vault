<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->required(),
                Forms\Components\TextInput::make('last_name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('is_admin')
                    ->label('Admin')
                    ->options([true => 'Yes', false => 'No'])
                    ->default(false),
                Forms\Components\TextInput::make('password')
                    ->label('Initial password')
                    ->required()
                    ->password()
                    ->visibleOn('create'),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->required()
                    ->password()
                    ->hidden(static fn (Forms\Get $get) => $get('id') !== auth()->id()),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Confirm password')
                    ->required()
                    ->password()
                    ->same('password')
                    ->hidden(static fn (Forms\Get $get) => $get('id') !== auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name'),
                Tables\Columns\TextColumn::make('last_name'),
                Tables\Columns\TextColumn::make('email'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
