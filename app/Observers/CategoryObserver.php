<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\User;
use Filament\Notifications\Notification;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */

    // Despues de crear el registro
    public function created(Category $category): void
    {
        $superAdmins =User::role('super_admin')->get();

        Notification::make()
            ->title('Categoria creada con exito')
            ->body("Se ha creado la categoria: {$category->name}")
            ->success()
            ->icon('heroicon-o-check-circle')
            ->sendToDatabase($superAdmins);
    }

    // Antes de crear el registro
    public function creating(Category $category): void{

    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        //
    }

    public function updating(Category $category): void
    {

    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }
}
