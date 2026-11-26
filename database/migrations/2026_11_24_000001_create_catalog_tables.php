<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->unsignedTinyInteger('orden');
        });

        Schema::create('lugares', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->string('direccion');
            $table->string('tipo');
            $table->decimal('latitud', 9, 6)->nullable();
            $table->decimal('longitud', 9, 6)->nullable();
            $table->text('fuente');
            $table->text('fuente_coordenadas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lugares');
        Schema::dropIfExists('categorias');
    }
};
