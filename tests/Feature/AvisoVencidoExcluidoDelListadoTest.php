<?php

namespace Tests\Feature;

use App\Models\TrafficReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AvisoVencidoExcluidoDelListadoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (getenv('DB_CONNECTION') !== 'sqlite' || getenv('DB_DATABASE') !== ':memory:') {
            $this->markTestSkipped('Requiere DB_CONNECTION=sqlite y DB_DATABASE=:memory:.');
        }

        parent::setUp();
    }

    public function test_el_listado_excluye_el_aviso_vencido_y_muestra_el_vigente(): void
    {
        $publicadoEn = Carbon::parse('2026-01-01 12:00:00', 'UTC');
        $this->travelTo($publicadoEn);

        $autor = User::create([
            'name' => 'Autora del listado',
            'email' => 'autora.listado-vencido@dinomaps.test',
            'password' => 'contrasena-de-prueba',
        ]);

        $vencido = TrafficReport::create([
            'user_id' => $autor->getKey(),
            'type' => 'obras',
            'description' => 'Aviso vencido de prueba de listado',
            'latitude' => -38.9556,
            'longitude' => -68.0591,
            'expires_at' => $publicadoEn->copy()->addHours(TrafficReport::LIFETIME_HOURS),
        ]);

        $this->travelTo($publicadoEn->copy()->addHours(TrafficReport::LIFETIME_HOURS)->addMinutes(30));

        $vigente = TrafficReport::create([
            'user_id' => $autor->getKey(),
            'type' => 'radares',
            'description' => 'Aviso vigente de prueba de listado',
            'latitude' => -38.9548,
            'longitude' => -68.0585,
            'expires_at' => now()->addHours(TrafficReport::LIFETIME_HOURS),
        ]);

        $respuesta = $this->getJson(route('reports.index'));

        $respuesta->assertOk();

        $ids = collect($respuesta->json('data'))->pluck('id')->all();

        $this->assertDatabaseCount('traffic_reports', 2);
        $this->assertTrue($vencido->expires_at->lessThanOrEqualTo(now()));
        $this->assertTrue($vigente->expires_at->greaterThan(now()));
        $this->assertFalse(in_array($vencido->getKey(), $ids, true));
        $this->assertTrue(in_array($vigente->getKey(), $ids, true));
        $this->assertSame([$vigente->getKey()], $ids);

        $vigentesSegunPredicado = TrafficReport::query()
            ->where('expires_at', '>', now())
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $this->assertSame([$vigente->getKey()], $vigentesSegunPredicado);
    }
}
