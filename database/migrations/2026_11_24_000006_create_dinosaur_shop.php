<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dinosaurs', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->unsignedInteger('price');
            $table->string('image_path')->nullable();
            $table->boolean('is_starter')->default(false);
            $table->unsignedInteger('sort_order');
            $table->timestamps();
        });

        Schema::create('dinosaur_user', function (Blueprint $table): void {
            $table->foreignId('dinosaur_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'dinosaur_id']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('equipped_dinosaur_id')->nullable()->constrained('dinosaurs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('equipped_dinosaur_id');
        });

        Schema::dropIfExists('dinosaur_user');
        Schema::dropIfExists('dinosaurs');
    }
};
