<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ProductCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Los productos que pertenecen a esta categoría.
     * Relación Uno a Muchos: Una ProductCategory tiene muchos Products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id'); // Especificar clave foránea si no es 'product_category_id'
    }

    


}
