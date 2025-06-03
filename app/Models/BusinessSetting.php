<?php
// app/Models/BusinessSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'opening_hour',
        'closing_hour',
        'lunch_start_hour',
        'lunch_end_hour',
        'disabled_dates', // Se guardará como JSON
        'daily_hours',    // Se guardará como JSON
    ];

    protected $casts = [
        'disabled_dates' => 'array', // Conversión automática a/desde array PHP
        'daily_hours' => 'array',    // Conversión automática a/desde array PHP
    ];
}
