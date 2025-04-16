<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OAuthClientResource\Pages;
use App\Filament\Resources\OAuthClientResource\RelationManagers;
use App\OAuth\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OAuthClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $label = 'OAuth Clients';

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('redirect')
                    ->required(),
                Forms\Components\Select::make('revoked')
                    ->options([true => 'Yes', false => 'No'])
                    ->default(false),
                Forms\Components\Select::make('requires_user_key')
                    ->label('Has access to vaults')
                    ->options([true => 'Yes', false => 'No'])
                    ->default(false),
                Forms\Components\TextInput::make('secret')
                    ->readOnly()
                    ->visible('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('redirect'),
                Tables\Columns\IconColumn::make('personal_access_client')->boolean(),
                Tables\Columns\IconColumn::make('revoked')->boolean(),
                Tables\Columns\IconColumn::make('requires_user_key')
                    ->label('Has access to vaults')
                    ->boolean(),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOAuthClients::route('/'),
            'create' => Pages\CreateOAuthClient::route('/create'),
            'edit' => Pages\EditOAuthClient::route('/{record}/edit'),
        ];
    }
}
