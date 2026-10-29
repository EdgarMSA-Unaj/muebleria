<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->enum('tipo', ['persona_natural', 'empresa'])->default('persona_natural');
            $table->string('nombre', 200);
            $table->string('apellidos', 200)->nullable();
            $table->string('razon_social', 200)->nullable();
            $table->string('documento_tipo', 10)->default('DNI');
            $table->string('documento_numero', 20)->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('pais', 100)->default('Perú');
            $table->date('fecha_nacimiento')->nullable();
            $table->decimal('credito_limite', 12, 2)->default(0);
            $table->decimal('saldo_pendiente', 12, 2)->default(0);
            $table->string('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['activo', 'documento_numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
