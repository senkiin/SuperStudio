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
        'campaign_name',
        'offer_id',
        'email_subject',
        'email_body_html',
        'attachment_paths',
        'date_of_send',
        'recipients_snapshot',
        'status',
    ];

    protected $casts = [
        'date_of_send' => 'datetime',
        'attachment_paths' => 'array', // Para guardar múltiples rutas de adjuntos
            'recipients_snapshot' => 'array',

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
