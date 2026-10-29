<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    protected $table = 'gastos';

    protected $fillable = [
        'user_id',
        'caja_id',
        'categoria_gasto',
        'descripcion',
        'monto',
        'comprobante_tipo',
        'comprobante_numero',
        'fecha_gasto'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_gasto' => 'date'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
}
