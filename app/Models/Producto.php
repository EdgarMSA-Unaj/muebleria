<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'sku',
        'nombre',
        'descripcion',
        'categoria_id',
        'marca',
        'material',
        'color',
        'largo',
        'ancho',
        'alto',
        'peso',
        'precio_compra',
        'precio_venta',
        'precio_minimo',
        'igv',
        'stock_actual',
        'stock_minimo',
        'stock_maximo',
        'unidad_medida',
        'imagen',
        'activo',
        'destacado'
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'precio_minimo' => 'decimal:2',
        'igv' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'largo' => 'decimal:2',
        'ancho' => 'decimal:2',
        'alto' => 'decimal:2',
        'peso' => 'decimal:2',
        'activo' => 'boolean',
        'destacado' => 'boolean'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function ventaDetalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function compraDetalles()
    {
        return $this->hasMany(CompraDetalle::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoStock::class);
    }

    public function getDimensionesAttribute(): string
    {
        if ($this->largo && $this->ancho && $this->alto) {
            return "{$this->largo} x {$this->ancho} x {$this->alto} cm";
        }
        return "N/A";
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_actual <= 0) {
            return 'agotado';
        }
        if ($this->stock_actual <= $this->stock_minimo) {
            return 'bajo';
        }
        return 'disponible';
    }
}
