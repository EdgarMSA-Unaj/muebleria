<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa')->default('Mueblería Artesanal & Diseño');
            $table->string('ruc', 20)->default('20601234567');
            $table->string('direccion')->default('Av. Los Diseñadores 789, Lima');
            $table->string('telefono', 50)->default('+51 987 654 321');
            $table->string('email')->default('contacto@muebleriadiseno.com');
            $table->string('sitio_web')->nullable()->default('https://muebleriadiseno.com');
            $table->string('logo')->nullable();
            $table->string('moneda_simbolo', 10)->default('S/.');
            $table->string('moneda_nombre', 20)->default('Soles');
            $table->decimal('porcentaje_igv', 5, 2)->default(18.00);
            $table->string('serie_boleta', 10)->default('B001');
            $table->string('serie_factura', 10)->default('F001');
            $table->string('serie_nota_venta', 10)->default('NV01');
            $table->string('serie_cotizacion', 10)->default('COT1');
            $table->text('terminos_garantia')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
    }
};
