<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
    ];

    /**
     * El usuario (cliente) que realizó este pedido.
     * Relación Inversa Uno a Muchos (Muchos a Uno): Un Order pertenece a un User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Los ítems (detalles) que componen este pedido.
     * Relación Uno a Muchos: Un Order tiene muchos OrderItems.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Alias común para 'items'
    public function orderItems(): HasMany
    {
        return $this->items();
    }

}
