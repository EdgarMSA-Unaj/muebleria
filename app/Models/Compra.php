<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compras';

    protected $fillable = [
        'numero_orden',
        'factura_proveedor',
        'proveedor_id',
        'user_id',
        'almacen_id',
        'fecha_emision',
        'fecha_entrega_esperada',
        'condicion_pago',
        'estado',
        'subtotal',
        'igv',
        'total',
        'monto_pagado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_entrega_esperada' => 'date',
        'subtotal' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
        'monto_pagado' => 'decimal:2'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function detalles()
    {
        return $this->hasMany(CompraDetalle::class);
    }
}
