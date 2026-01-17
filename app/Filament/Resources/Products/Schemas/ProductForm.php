<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Resources\Categories\Schemas\CategoryForm;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Información del Producto')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_active')
                            ->columnSpan(3)
                            ->label('¿Activo?')
                            ->required()
                            ->default(true),

                        TextInput::make('code')
                            ->label('Código')
                            ->placeholder('Ej: PROD-001')
                            ->required()
                            ->alphaDash(),  //letras may, minus, - 0-9

                        TextInput::make('name')
                            ->label('Nombre')
                            ->placeholder('Ej: Telefono, Televisor')
                            ->required(),

                        TextInput::make('summary')
                            ->label('Resumen')
                            ->required()
                            ->placeholder('Resumen del producto'),

                        TextInput::make('price')
                            ->label('Precio')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->required()
                            ->step(0,01),     //esto esta de prueba

                        Select::make('category_id')
                            ->label('Crear nueva Categoria')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(CategoryForm::create())
                            ->createOptionModalHeading('Crear nueva categoria')

                    ]),

                Section::make('Imagen del producto')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Imagen')
                            ->disk('public')
                            ->directory('products')
                            ->image()
                            ->acceptedFileTypes(['image/*'])
                            ->required()
                            ->maxSize(2048)
                    ]),

                Section::make('Descripción detallada')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Descripción')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'h1',
                                'h2',
                                'h3',
                                'link',
                            ])
                            ->required()
                            ->helperText('Agregar una descripcion'),

                    ])
            ]);
    }
}
