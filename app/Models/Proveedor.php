<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'codigo',
        'razon_social',
        'nombre_comercial',
        'ruc',
        'email',
        'telefono',
        'celular',
        'contacto_nombre',
        'direccion',
        'ciudad',
        'pais',
        'banco',
        'cuenta_bancaria',
        'saldo_pendiente',
        'dias_credito',
        'notas',
        'activo'
    ];

    protected $casts = [
        'saldo_pendiente' => 'decimal:2',
        'dias_credito' => 'integer',
        'activo' => 'boolean'
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}
