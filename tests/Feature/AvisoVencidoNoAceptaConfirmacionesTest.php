<?php

namespace Tests\Feature;

use App\Models\TrafficReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AvisoVencidoNoAceptaConfirmacionesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (getenv('DB_CONNECTION') !== 'sqlite' || getenv('DB_DATABASE') !== ':memory:') {
            $this->markTestSkipped('Requiere DB_CONNECTION=sqlite y DB_DATABASE=:memory:.');
        }

        parent::setUp();
    }

    public function test_un_aviso_vencido_no_acepta_confirmaciones_ni_genera_puntos(): void
    {
        $publicadoEn = Carbon::parse('2026-01-01 12:00:00', 'UTC');
        $this->travelTo($publicadoEn);

        $autor = User::create([
            'name' => 'Autora de prueba',
            'email' => 'autora.aviso-vencido@dinomaps.test',
            'password' => 'contrasena-de-prueba',
        ]);
        $votante = User::create([
            'name' => 'Votante de prueba',
            'email' => 'votante.aviso-vencido@dinomaps.test',
            'password' => 'contrasena-de-prueba',
        ]);

        $venceEn = $publicadoEn->copy()->addHours(TrafficReport::LIFETIME_HOURS);
        $aviso = TrafficReport::create([
            'user_id' => $autor->getKey(),
            'type' => 'obras',
            'description' => 'Aviso de prueba con vencimiento',
            'latitude' => -38.9556,
            'longitude' => -68.0591,
            'expires_at' => $venceEn,
        ]);

        $this->assertTrue($aviso->expires_at->equalTo($venceEn));

        $this->travelTo($venceEn->copy()->addMinute());

        $respuesta = $this->actingAs($votante)->postJson(
            route('reports.votes.store', ['report' => $aviso]),
        );

        $respuesta->assertStatus(410)->assertJson(['message' => 'El aviso ya venció.']);

        $this->assertDatabaseCount('traffic_report_votes', 0);
        $this->assertDatabaseHas('traffic_reports', [
            'id' => $aviso->getKey(),
            'rewarded_at' => null,
        ]);
        $this->assertSame(0, (int) $autor->fresh()->points);
    }
}
