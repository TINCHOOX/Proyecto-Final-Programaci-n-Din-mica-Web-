<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Lugar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoAmpliadoTest extends TestCase
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

    public function test_el_listado_usa_el_promedio_real_y_omite_estrellas_sin_resenas(): void
    {
        $lugar = Lugar::where('slug', 'el-ciervo')->firstOrFail();

        foreach ([5, 2] as $indice => $calificacion) {
            $usuario = User::create([
                'name' => 'Cuenta de prueba '.$indice,
                'email' => "catalogo-$indice@dinomaps.test",
                'password' => 'contrasena-de-prueba',
            ]);
            $lugar->reviews()->create([
                'user_id' => $usuario->id,
                'rating' => $calificacion,
                'comment' => 'Reseña de prueba',
            ]);
        }

        $this->seed();

        foreach (['inicio', 'lugares.index'] as $ruta) {
            $this->get(route($ruta, ['categoria' => 'restaurantes', 'q' => 'El Ciervo']))
                ->assertOk()
                ->assertSee('aria-label="3,5 de 5 estrellas"', false)
                ->assertSeeText('2 reseñas');
            $this->get(route($ruta, ['categoria' => 'restaurantes', 'q' => 'Estación Q']))
                ->assertOk()
                ->assertSeeText('Sin reseñas')
                ->assertDontSee('class="calificacion-lugar"', false);
        }
    }

    public function test_las_siete_categorias_tienen_lugares_y_marcadores_con_fichas(): void
    {
        $categorias = Categoria::all();
        $this->assertCount(7, $categorias);

        foreach ($categorias as $categoria) {
            $this->assertGreaterThanOrEqual(3, Lugar::whereBelongsTo($categoria, 'categoria')->count());
            $puntos = $this->getJson(route('mapa.puntos', ['categoria' => $categoria->slug]))
                ->assertOk()
                ->json();

            $this->assertGreaterThanOrEqual(2, count($puntos));
            foreach ($puntos as $punto) {
                $this->assertSame($categoria->slug, $punto['categoria']);
                $this->get($punto['ficha'])->assertOk();
            }
        }
    }
}
