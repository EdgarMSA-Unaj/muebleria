<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'caja';

    protected $fillable = [
        'user_id',
        'nombre_caja',
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'monto_ventas_efectivo',
        'monto_ventas_digital',
        'monto_gastos',
        'monto_final_esperado',
        'monto_final_real',
        'diferencia',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'monto_ventas_efectivo' => 'decimal:2',
        'monto_ventas_digital' => 'decimal:2',
        'monto_gastos' => 'decimal:2',
        'monto_final_esperado' => 'decimal:2',
        'monto_final_real' => 'decimal:2',
        'diferencia' => 'decimal:2'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }
}
