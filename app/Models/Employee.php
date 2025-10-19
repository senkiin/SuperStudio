<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory; // Es una buena práctica tener esto

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'position',
        'description',
        'image_path',
    ];

    /**
     * Get the full URL for the employee's image.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return 'https://fotovalerasite.s3.amazonaws.com/empleados/' . $this->image_path;
        }
        return 'https://via.placeholder.com/400x500.png/1a202c/ffffff?text=Sin+Foto';
    }
}
