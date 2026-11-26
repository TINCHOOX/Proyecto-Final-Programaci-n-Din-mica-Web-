<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->unsignedInteger('points')->default(0);
        });

        Schema::create('traffic_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 24);
            $table->text('description');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamp('expires_at')->index();
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('traffic_report_votes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('traffic_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['traffic_report_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traffic_report_votes');
        Schema::dropIfExists('traffic_reports');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('points');
        });
    }
};
