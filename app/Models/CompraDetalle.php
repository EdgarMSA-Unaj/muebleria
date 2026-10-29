<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    use HasFactory;

    protected $table = 'compra_detalles';

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad',
        'cantidad_recibida',
        'costo_unitario',
        'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'cantidad_recibida' => 'integer',
        'costo_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
