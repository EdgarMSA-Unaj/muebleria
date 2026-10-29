<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenProduccion extends Model
{
    use HasFactory;

    protected $table = 'ordenes_produccion';

    protected $fillable = [
        'codigo',
        'producto_id',
        'venta_id',
        'maestro_carpintero',
        'maestro_tapicero',
        'fecha_inicio',
        'fecha_estimada_termino',
        'fase',
        'avance_porcentaje',
        'especificaciones_acabado',
        'observaciones_taller'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_estimada_termino' => 'date',
        'avance_porcentaje' => 'integer'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function getFaseLabelAttribute(): string
    {
        return match($this->fase) {
            'corte_madera' => 'Corte de Madera & Habilitado',
            'armado_esqueleto' => 'Armado de Estructura / Esqueleto',
            'laqueado_pintura' => 'Pintura & Laqueado Poliuretano',
            'tapizado' => 'Tapizado & Encolado de Espuma',
            'control_calidad' => 'Control de Calidad & Acabados',
            'terminado' => 'Terminado / Listo para Entrega',
            default => ucfirst(str_replace('_', ' ', $this->fase))
        };
    }
}
