<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('caja_id')->nullable()->constrained('caja')->nullOnDelete();
            $table->string('categoria_gasto', 100); // Servicios, Transporte, Alquiler, Sueldos, Materiales de taller, Mantenimiento
            $table->string('descripcion', 255);
            $table->decimal('monto', 12, 2);
            $table->string('comprobante_tipo', 50)->nullable();
            $table->string('comprobante_numero', 50)->nullable();
            $table->date('fecha_gasto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
