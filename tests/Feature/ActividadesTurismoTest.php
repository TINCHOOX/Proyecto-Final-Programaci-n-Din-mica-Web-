<?php

namespace Tests\Feature;

use App\Enums\ActividadTurismo;
use App\Models\Lugar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActividadesTurismoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (getenv('DB_CONNECTION') !== 'sqlite' || getenv('DB_DATABASE') !== ':memory:') {
            $this->markTestSkipped('Requiere SQLite en memoria.');
        }

        parent::setUp();
        $this->seed();
    }

    public function test_la_actividad_y_la_busqueda_filtran_catalogo_y_marcadores(): void
    {
        $plazoleta = Lugar::where('slug', 'plazoleta-junta-estudios-historicos')->firstOrFail();
        $trekking = $plazoleta->replicate()->fill([
            'slug' => 'sendero-de-prueba',
            'nombre' => 'Sendero de prueba',
            'actividad' => ActividadTurismo::Trekking,
            'indicadores' => ['desnivel' => '100 m'],
        ]);
        $trekking->save();

        $filtros = ['categoria' => 'turismo', 'actividad' => 'trekking', 'q' => 'Sendero'];

        foreach (['inicio', 'lugares.index'] as $ruta) {
            $this->get(route($ruta, $filtros))
                ->assertOk()
                ->assertSeeText('Sendero de prueba')
                ->assertSeeText('Desnivel')
                ->assertSeeText('100 m')
                ->assertSee('#lugar=sendero-de-prueba')
                ->assertDontSeeText($plazoleta->nombre);
        }

        $this->getJson(route('mapa.puntos', $filtros))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.slug', $trekking->slug);

        $this->getJson(route('mapa.puntos', ['categoria' => 'turismo', 'actividad' => 'invalida']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('actividad');
        $this->getJson(route('mapa.puntos', ['categoria' => 'hoteles', 'actividad' => 'trekking']))
            ->assertOk()
            ->assertJsonPath('0.slug', 'hotel-casino-magic');
    }

    public function test_paseo_mantiene_los_parques_y_solo_ubica_lugares_con_coordenadas(): void
    {
        $filtros = ['categoria' => 'turismo', 'actividad' => 'paseo'];
        $this->get(route('lugares.index', $filtros))
            ->assertOk()
            ->assertSeeText('Parque Jaime de Nevares')
            ->assertSeeText('Plazoleta Junta de Estudios Históricos')
            ->assertSee('#lugar=plazoleta-junta-estudios-historicos')
            ->assertDontSee('#lugar=parque-jaime-de-nevares');

        $this->getJson(route('mapa.puntos', $filtros))
            ->assertOk()
            ->assertJsonCount(1);
        $this->get(route('lugares.index', ['categoria' => 'turismo', 'actividad' => 'running']))
            ->assertOk()
            ->assertSeeText('Circuito Blanco Bardas Norte');

        Lugar::where('actividad', ActividadTurismo::Running->value)->delete();
        $this->get(route('lugares.index', ['categoria' => 'turismo', 'actividad' => 'running']))
            ->assertOk()
            ->assertSeeText('Todavía no hay lugares cargados para esta actividad.');
    }
}
