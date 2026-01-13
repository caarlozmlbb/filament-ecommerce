<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{

    // protected $table = 'productos';

    protected $fillable = [
        'name',
        'code',
        'slug',
        'summary',
        'description',
        'price',
        'image',
        'is_active',
        'category_id',
    ];

      protected static function booted()
    {
        static::creating(function($category){
            $category->slug = Str::slug($category->name);
        });

        static::updating(function($category){
            $category->slug = Str::slug($category->name);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }
}
