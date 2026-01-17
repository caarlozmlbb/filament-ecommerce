<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Información de la categoria')
                    ->description('Configure los detalles de su categoria')
                    ->collapsible()
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema(static::create())
            ]);
    }

    public static function create(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->placeholder('Ej. Categoria 1. Tv')
                ->label('Nombre'),
            TextInput::make('summary')
                ->required()
                ->placeholder('Breve descripcion de la categoria')
                ->label('Resumen'),
        ];
    }
}
