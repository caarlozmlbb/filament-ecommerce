<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->columns(1)
            ->components([

                Section::make('Información de la venta')
                    ->columns(2)
                    ->schema([
                        Select::make('warehouse_id')
                            ->relationship('warehouse', 'name')
                            ->live()
                            ->default(null),

                        Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->live()
                            ->default(null),


                        Textarea::make('notes')
                            ->default(null)
                            ->columnSpanFull(),


                    ]),

                Section::make('Carrito de compras')
                    ->columns(1)
                    ->hidden(function(Get $get): bool{
                        $isVisible = (empty($get('warehouse_id')) || (empty($get('customer_id'))));

                        return $isVisible;
                    })
                    ->schema([
                        Repeater::make('orderProducts')
                            ->columns(3)
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Producto')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->relationship('product', 'name')
                                    ->options(function(Get $get): array{

                                        $warehouseId = $get('../../warehouse_id');

                                        $product = Product::whereHas('inventories' , function ($query) use ( $warehouseId){
                                            $query->where('warehouse_id',  $warehouseId);
                                        })
                                        ->pluck('name', 'id')->toArray();

                                        return $product;
                                    }),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->label('Cantidad'),

                                TextInput::make('sub_total')
                                    ->numeric()
                                    ->minValue(0)
                                    ->label('SubTotal')
                            ])
                    ])
            ]);
    }
}
