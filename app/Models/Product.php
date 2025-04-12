<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'type_product',
        'represntative_image',
        'category_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * La categoría a la que pertenece este producto (opcional).
     * Relación Inversa Uno a Muchos (Muchos a Uno): Un Product pertenece a una ProductCategory.
     */
    public function category(): BelongsTo
    {
        // Asume que la clave foránea es 'category_id'
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Los ítems de pedido asociados a este producto.
     * Relación Uno a Muchos: Un Product puede estar en muchos OrderItems.
     */
    public function orderItems(): HasMany

    {
        return $this->hasMany(OrderItem::class);
    }

}
