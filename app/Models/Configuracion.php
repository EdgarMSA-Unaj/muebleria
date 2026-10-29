<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuracion';

    protected $fillable = [
        'nombre_empresa',
        'ruc',
        'direccion',
        'telefono',
        'email',
        'sitio_web',
        'logo',
        'moneda_simbolo',
        'moneda_nombre',
        'porcentaje_igv',
        'serie_boleta',
        'serie_factura',
        'serie_nota_venta',
        'serie_cotizacion',
        'terminos_garantia'
    ];

    protected $casts = [
        'porcentaje_igv' => 'decimal:2'
    ];
}
