<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden', 30)->unique();
            $table->string('factura_proveedor', 50)->nullable();
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('almacen_id')->nullable()->constrained('almacenes')->nullOnDelete();
            $table->date('fecha_emision');
            $table->date('fecha_entrega_esperada')->nullable();
            $table->enum('condicion_pago', ['contado', 'credito_15d', 'credito_30d', 'credito_60d'])->default('contado');
            $table->enum('estado', ['solicitado', 'aprobado', 'recibido_parcial', 'recibido', 'anulado'])->default('recibido');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('monto_pagado', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
