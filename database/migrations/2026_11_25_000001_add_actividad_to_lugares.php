<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lugares', function (Blueprint $table): void {
            $table->string('actividad', 20)->nullable();
            $table->text('descripcion')->nullable();
            $table->json('indicadores')->nullable();
        });

        DB::table('lugares')
            ->whereIn('categoria_id', DB::table('categorias')->select('id')->where('slug', 'turismo'))
            ->whereIn('slug', ['parque-jaime-de-nevares', 'plazoleta-junta-estudios-historicos'])
            ->update(['actividad' => 'paseo']);
    }

    public function down(): void
    {
        Schema::table('lugares', function (Blueprint $table): void {
            $table->dropColumn(['actividad', 'descripcion', 'indicadores']);
        });
    }
};
