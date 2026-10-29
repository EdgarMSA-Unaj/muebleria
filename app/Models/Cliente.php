<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'codigo',
        'tipo',
        'nombre',
        'apellidos',
        'razon_social',
        'documento_tipo',
        'documento_numero',
        'email',
        'telefono',
        'celular',
        'direccion',
        'distrito',
        'ciudad',
        'pais',
        'fecha_nacimiento',
        'credito_limite',
        'saldo_pendiente',
        'notas',
        'activo'
    ];

    protected $casts = [
        'credito_limite' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        if ($this->tipo === 'empresa' && $this->razon_social) {
            return $this->razon_social;
        }
        return trim("{$this->nombre} {$this->apellidos}");
    }
}
