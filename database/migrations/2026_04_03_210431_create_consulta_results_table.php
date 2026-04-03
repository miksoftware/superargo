<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consulta_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->constrained()->onDelete('cascade');
            $table->string('cedula', 20);
            $table->string('tipo_documento', 50)->nullable();
            $table->string('primer_nombre', 100)->nullable();
            $table->string('segundo_nombre', 100)->nullable();
            $table->string('primer_apellido', 100)->nullable();
            $table->string('segundo_apellido', 100)->nullable();
            $table->string('departamento', 150)->nullable();
            $table->string('municipio', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('regimen', 100)->nullable();
            $table->string('poblacion_especial', 150)->nullable();
            $table->string('grupo_etnico', 150)->nullable();
            $table->string('paciente_riesgo', 150)->nullable();
            $table->string('otros_riesgos', 255)->nullable();
            $table->string('celular', 50)->nullable();
            $table->string('telefono_fijo', 50)->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('estado_afiliado', 255)->nullable();
            $table->string('sede', 255)->nullable();
            $table->string('ips', 255)->nullable();
            $table->string('eps_nombre', 150)->nullable();
            $table->string('fecha_nacimiento', 20)->nullable();
            $table->integer('edad')->nullable();
            $table->tinyInteger('sexo')->nullable();
            $table->boolean('processed')->default(false);
            $table->boolean('found')->default(false);
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consulta_results');
    }
};
