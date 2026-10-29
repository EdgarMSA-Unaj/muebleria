<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'numero_comprobante',
        'tipo_comprobante',
        'cliente_id',
        'user_id',
        'fecha_emision',
        'fecha_vencimiento',
        'condicion_pago',
        'estado',
        'subtotal',
        'igv',
        'descuento',
        'total',
        'monto_pagado',
        'saldo',
        'requiere_entrega',
        'requiere_armado',
        'fecha_entrega_programada',
        'direccion_entrega',
        'referencia_entrega',
        'costo_envio',
        'repartidor_asignado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_entrega_programada' => 'date',
        'requiere_entrega' => 'boolean',
        'requiere_armado' => 'boolean',
        'subtotal' => 'decimal:2',
        'igv' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'saldo' => 'decimal:2',
        'costo_envio' => 'decimal:2'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }
}
