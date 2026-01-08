<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Imagen')
                    ->disk('public')
                    ->imageSize(50),

                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable()
                    ->label('Categoria'),

                TextColumn::make('summary')
                    ->label('Resumen'),

                TextColumn::make('is_active')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(bool $state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn(bool $state) => $state ? 'Activo' : 'Inactivo'),

                TextColumn::make('price')
                    ->label('Precio')
                    ->money('USD', true)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault:true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault:true),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
