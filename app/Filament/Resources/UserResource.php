<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\PermissionsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Administrative');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('user.name'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label(__('user.email'))
                        ->email()
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('password')
                        ->label(__('user.password'))
                        ->password()
                        ->maxLength(255)
                        ->dehydrateStateUsing(
                            static fn (null|string $state): null|string => filled($state) ? Hash::make($state) : null
                        )
                        ->dehydrated(static fn (null|string $state): bool => filled($state))
                        ->required(static fn (Page $livewire): bool => $livewire instanceof CreateUser)
                        ->label(
                            static fn (
                                Page $livewire
                            ): string => ($livewire instanceof EditUser) ? __('user.new_password') : __('user.password')
                        ),

                    Forms\Components\DateTimePicker::make('email_verified_at')
                        ->label(__('user.email_verified_at')),

                    Forms\Components\Select::make('roles')
                        ->label(__('user.roles'))
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('permissions')
                        ->label(__('user.permissions'))
                        ->multiple()
                        ->relationship('permissions', 'name')
                        ->searchable()
                        ->preload(),

                    Forms\Components\Toggle::make('is_active')
                        ->label(__('user.is_active'))
                        ->default(true),

                    Forms\Components\Toggle::make('is_admin')
                        ->label(__('user.is_admin')),

                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('user.name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_admin')
                    ->label(__('user.is_admin'))
                    ->boolean()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('user.roles'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('user.email'))
                    ->sortable()
                    ->searchable(),

                Auth::user()->can('update user') ?
                    Tables\Columns\ToggleColumn::make('is_active')
                        ->label(__('user.is_active'))
                        ->sortable()
                        ->searchable()
                        ->toggleable() :
                    Tables\Columns\IconColumn::make('is_active')
                        ->label(__('user.is_active'))
                        ->boolean()
                        ->sortable()
                        ->searchable()
                        ->toggleable(),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label(__('user.email_verified_at'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('user.created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('user.updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label(__('user.deleted'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label(__('user.is_admin')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('user.is_active')),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label(__('user.email_verified_at')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
            PermissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getModelLabel(): string
    {
        return __('user.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user.users');
    }
}
