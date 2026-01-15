<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Inventory;
use App\Models\Product;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                            ->live()
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Producto')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required()
                                    ->disabled(function(Get $get){
                                        $warehouseId = $get('../../warehouse_id');

                                        if(!$warehouseId){
                                            return true;
                                        }

                                        return !Product::whereHas('inventories' , function ($query) use ( $warehouseId){
                                            $query->where('warehouse_id',  $warehouseId);
                                        })->exists();
                                    })
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
                                    ->live()
                                    ->minValue(1)
                                    ->label('Cantidad')
                                    ->helperText(function(Get $get){
                                        $productId = $get('product_id');
                                        $warehouseId = $get('../../warehouse_id');

                                        $stock = Inventory::where('product_id', $productId)
                                            ->where('warehouse_id', $warehouseId)
                                            ->value('quantity') ?? 0;

                                        return "Stock disponible: {$stock}";
                                    })
                                    ->afterStateUpdated(function(Get $get, Set $set, $state){
                                        $productId = $get('product_id');
                                        $quantity = $get('quantity');

                                        $product = Product::find($productId);

                                        $subTotal = ($quantity * $product->price) ?? 0;

                                        $set('sub_total', $subTotal);
                                    })
                                    ->rule(function(Get $get){
                                        $productId = $get('product_id');
                                        $warehouseId = $get('../../warehouse_id');


                                         $stock = Inventory::where('product_id', $productId)
                                            ->where('warehouse_id', $warehouseId)
                                            ->value('quantity') ?? 0;

                                        return "max:$stock";

                                    })
                                    ->validationMessages([
                                        'max' => 'No hay stock suficiente',
                                    ]),

                                TextInput::make('sub_total')
                                    ->numeric()
                                    ->minValue(0)
                                    ->label('SubTotal')
                            ])
                            ->afterStateUpdated(function(Get $get, Set $set, $state){
                                $total = 0;

                                foreach($state as $item){
                                    $productId = $item['product_id'];
                                    $quantity = $item['quantity'] ?? 0;

                                    $product = Product::find($productId);

                                    $total += (float) $quantity * (float)($product->price ?? 0);
                                }

                                $set('total', $total);
                            })
                    ]),

                Section::make('Resumen de la Orden')
                    ->hidden(function(Get $get): bool{
                        $isVisible = (empty($get('warehouse_id')) || (empty($get('customer_id'))));

                        return $isVisible;
                    })
                    ->schema([
                        TextInput::make('total')
                            ->label('Total')
                            ->required()
                            ->readOnly()
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('Total de la orden de salida'),
                    ])

            ]);
    }
}
