<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    /** @use HasFactory<\Database\Factories\OfferFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'discount_percentage',
        'type',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_percentage' => 'decimal:2', // Ajusta precisión si es necesario
    ];

    /**
     * Las campañas de email asociadas a esta oferta.
     * Relación Uno a Muchos: Una Offer puede tener muchas EmailCampaigns.
     */
    public function emailCampaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class);
    }

    /**
     * Los usuarios asociados a esta oferta (a través de la tabla pivot user_offers).
     * Relación Muchos a Muchos: Una Offer puede tener muchos Users y viceversa.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_offers')
                    ->withPivot('received_at', 'accepted_at')
                    ->withTimestamps();
    }
}
