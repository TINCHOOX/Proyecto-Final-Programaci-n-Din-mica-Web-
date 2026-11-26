<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('traffic_reports', function (Blueprint $table): void {
            $table->decimal('end_latitude', 10, 7)->nullable();
            $table->decimal('end_longitude', 10, 7)->nullable();
        });

        $this->refreshExpiration(36);
    }

    public function down(): void
    {
        $this->refreshExpiration(24);

        Schema::table('traffic_reports', function (Blueprint $table): void {
            $table->dropColumn(['end_latitude', 'end_longitude']);
        });
    }

    private function refreshExpiration(int $hours): void
    {
        DB::table('traffic_reports')->select('id', 'created_at')->whereNotNull('created_at')
            ->chunkById(500, function ($reports) use ($hours): void {
                foreach ($reports as $report) {
                    DB::table('traffic_reports')->where('id', $report->id)->update([
                        'expires_at' => CarbonImmutable::parse($report->created_at)->addHours($hours),
                    ]);
                }
            });
    }
};
