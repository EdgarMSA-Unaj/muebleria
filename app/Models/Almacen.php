<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacenes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'principal',
        'activo'
    ];

    protected $casts = [
        'principal' => 'boolean',
        'activo' => 'boolean'
    ];

    public function movimientos()
    {
        return $this->hasMany(MovimientoStock::class);
    }
}
