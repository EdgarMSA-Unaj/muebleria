<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('almacen_id')->constrained('almacenes');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('tipo_movimiento', ['entrada_compra', 'salida_venta', 'ajuste_positivo', 'ajuste_negativo', 'traslado_origen', 'traslado_destino', 'devolucion_cliente', 'devolucion_proveedor']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->string('referencia_tipo')->nullable(); // Venta, Compra, Ajuste
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->text('motivo')->nullable();
            $table->timestamps();
            $table->index(['producto_id', 'almacen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_stock');
    }
};
