<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_produccion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $table->string('maestro_carpintero')->nullable();
            $table->string('maestro_tapicero')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_estimada_termino')->nullable();
            $table->enum('fase', [
                'corte_madera',
                'armado_esqueleto',
                'laqueado_pintura',
                'tapizado',
                'control_calidad',
                'terminado'
            ])->default('corte_madera');
            $table->integer('avance_porcentaje')->default(10);
            $table->string('especificaciones_acabado')->nullable();
            $table->text('observaciones_taller')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_produccion');
    }
};
