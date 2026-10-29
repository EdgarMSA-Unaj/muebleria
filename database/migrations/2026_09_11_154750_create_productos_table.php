<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('restrict');
            $table->string('marca', 100)->nullable();
            $table->string('material', 100)->nullable();
            $table->string('color', 100)->nullable();
            $table->decimal('largo', 8, 2)->nullable()->comment('cm');
            $table->decimal('ancho', 8, 2)->nullable()->comment('cm');
            $table->decimal('alto', 8, 2)->nullable()->comment('cm');
            $table->decimal('peso', 8, 2)->nullable()->comment('kg');
            $table->decimal('precio_compra', 12, 2)->default(0);
            $table->decimal('precio_venta', 12, 2)->default(0);
            $table->decimal('precio_minimo', 12, 2)->default(0);
            $table->decimal('igv', 5, 2)->default(18.00);
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->integer('stock_maximo')->nullable();
            $table->string('unidad_medida', 30)->default('UND');
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('destacado')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['categoria_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
