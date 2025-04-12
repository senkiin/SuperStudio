<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'product_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    /**
     * El pedido al que pertenece este ítem.
     * Relación Inversa Uno a Muchos (Muchos a Uno): Un OrderItem pertenece a un Order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * El producto asociado a este ítem de pedido.
     * Relación Inversa Uno a Muchos (Muchos a Uno): Un OrderItem pertenece a un Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}
