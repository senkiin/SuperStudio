<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailCampaign extends Model
{
    /** @use HasFactory<\Database\Factories\EmailCampaignFactory> */
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'date_of_send',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_send' => 'datetime',
    ];

    /**
     * La oferta asociada a esta campaña de email.
     * Relación Inversa Uno a Muchos (Muchos a Uno): Una EmailCampaign pertenece a una Offer.
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

}
