<?php

namespace App\Filament\Resources\Ejercicios\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;


class EjercicioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('carrito')
                    ->columns(1)
                    ->schema([

                        Repeater::make('miembros')

                            ->columns(3)
                            ->reactive()
                            ->schema([
                                TextInput::make('precio')
                                    ->label('Precio')
                                    ->prefix('$')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $set('sub_total', (($get('cantidad') ?? 0) * $state));
                                    }),


                                TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $set('sub_total', (($get('precio') ?? 0) * ($state)));
                                    }),


                                TextInput::make('sub_total')
                            ])

                            ->afterStateUpdated(function(Set $set, Get $get){
                                $total = collect($get('miembros'))
                                    ->sum(fn ($miembro) => $miembro['sub_total'] ?? 0);

                                $set('total', $total);
                            })
                    ]),

                TextInput::make('total')


            ]);
    }
}
