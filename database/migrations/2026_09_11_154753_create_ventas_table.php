<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_comprobante', 30)->unique();
            $table->enum('tipo_comprobante', ['boleta', 'factura', 'nota_venta', 'cotizacion'])->default('boleta');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('condicion_pago', ['contado', 'credito_15d', 'credito_30d', 'transferencia', 'tarjeta', 'yape_plin'])->default('contado');
            $table->enum('estado', ['cotizacion', 'pendiente', 'completada', 'anulada', 'en_entrega', 'entregado'])->default('completada');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('monto_pagado', 12, 2)->default(0);
            $table->decimal('saldo', 12, 2)->default(0);
            
            // Logística y Entrega de Muebles
            $table->boolean('requiere_entrega')->default(false);
            $table->boolean('requiere_armado')->default(false);
            $table->date('fecha_entrega_programada')->nullable();
            $table->string('direccion_entrega')->nullable();
            $table->string('referencia_entrega')->nullable();
            $table->decimal('costo_envio', 10, 2)->default(0);
            $table->string('repartidor_asignado')->nullable();
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            $table->index(['fecha_emision', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
