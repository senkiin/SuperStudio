<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VideoSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'title',
        'description',
    ];

    public function videoEntries(): HasMany
    {
        return $this->hasMany(VideoEntry::class)->orderBy('order_column');
    }
}
