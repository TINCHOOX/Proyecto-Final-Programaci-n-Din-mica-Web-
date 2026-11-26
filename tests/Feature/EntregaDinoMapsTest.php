<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Dinosaur;
use App\Models\Lugar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntregaDinoMapsTest extends TestCase
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

    public function test_el_registro_no_permite_elegir_el_rol_y_la_cuenta_comun_no_puede_gestionar_lugares(): void
    {
        $this->get(route('admin.lugares.index'))->assertRedirect(route('login'));
        $this->post(route('register.store'), [
            'name' => 'Visitante registrado',
            'email' => 'miembro@dinomaps.test',
            'password' => 'contrasena-de-prueba',
            'password_confirmation' => 'contrasena-de-prueba',
            'is_admin' => true,
        ])->assertRedirect();

        $this->assertFalse(User::where('email', 'miembro@dinomaps.test')->firstOrFail()->is_admin);
        $lugar = Lugar::firstOrFail();
        $this->get(route('cuenta.index'))->assertOk()->assertDontSeeText('Gestionar lugares');
        $this->get(route('admin.lugares.index'))->assertForbidden();
        $this->get(route('admin.lugares.create'))->assertForbidden();
        $this->get(route('admin.lugares.edit', $lugar))->assertForbidden();
        $this->postJson(route('admin.lugares.store'))->assertForbidden();
        $this->patchJson(route('admin.lugares.update', $lugar))->assertForbidden();
        $this->deleteJson(route('admin.lugares.destroy', $lugar))->assertForbidden();
    }

    public function test_el_admin_gestiona_lugares_y_archivar_preserva_resenas(): void
    {
        $user = $this->usuario();
        $this->artisan('dinomaps:administrador', ['email' => $user->email])->assertSuccessful();
        $this->actingAs($user->refresh());

        $this->get(route('cuenta.index'))->assertOk()->assertSeeText('Gestionar lugares');
        $this->get(route('admin.lugares.index'))->assertOk()->assertSeeText('Gestionar lugares');
        $this->get(route('admin.lugares.create'))->assertOk()->assertSeeText('Agregar lugar');

        $datos = [
            'categoria_id' => Categoria::where('slug', 'turismo')->value('id'),
            'nombre' => 'Parque de prueba',
            'direccion' => 'Dirección de prueba',
            'tipo' => 'Parque',
            'actividad' => 'paseo',
            'fuente' => 'https://example.com/lugar',
            'latitud' => '-38.953',
            'longitud' => '-68.059',
            'fuente_coordenadas' => 'https://example.com/mapa',
        ];
        $this->postJson(route('admin.lugares.store'), array_replace($datos, ['longitud' => null, 'fuente' => 'javascript:alert(1)']))
            ->assertUnprocessable()->assertJsonValidationErrors(['fuente', 'longitud']);
        $this->post(route('admin.lugares.store'), $datos)->assertRedirect(route('admin.lugares.index'));
        $lugar = Lugar::where('slug', 'parque-de-prueba')->firstOrFail();
        $resena = $lugar->reviews()->create(['user_id' => $user->id, 'rating' => 5, 'comment' => 'Reseña de prueba.']);

        $this->get(route('admin.lugares.edit', $lugar))->assertOk()->assertSeeText('Archivar lugar');
        $this->patch(route('admin.lugares.update', $lugar), array_replace($datos, ['nombre' => 'Parque actualizado']))
            ->assertRedirect(route('admin.lugares.index'));
        $this->get(route('lugares.show', $lugar))->assertOk()->assertSeeText('Parque actualizado');
        $this->getJson(route('mapa.puntos', ['q' => 'Parque actualizado']))->assertJsonCount(1);

        $this->delete(route('admin.lugares.destroy', $lugar))->assertRedirect(route('admin.lugares.index'));
        $this->assertSoftDeleted($lugar);
        $this->assertDatabaseHas('reviews', ['id' => $resena->id, 'lugar_id' => $lugar->id]);
        $this->get(route('lugares.show', $lugar))->assertNotFound();
        $this->post(route('reviews.votes.store', $resena))->assertNotFound();
        $this->getJson(route('mapa.puntos', ['q' => 'Parque actualizado']))->assertJsonCount(0);

        $catalogoOriginal = Lugar::where('slug', 'el-ciervo')->firstOrFail();
        $catalogoOriginal->update(['nombre' => 'Nombre editado por admin']);
        $archivado = Lugar::where('slug', 'mnba-neuquen')->firstOrFail();
        $archivado->delete();
        $this->seed();
        $this->assertSoftDeleted($archivado);
        $this->assertSame('Nombre editado por admin', $catalogoOriginal->refresh()->nombre);
    }

    public function test_el_canje_asocia_al_dinosaurio_y_descuenta_el_saldo_una_vez(): void
    {
        $user = $this->usuario();
        $dinosaurio = Dinosaur::where('is_starter', false)->firstOrFail();
        $user->forceFill(['points' => $dinosaurio->price])->save();
        $this->actingAs($user)->from(route('shop.index'));

        $this->post(route('shop.equip', $dinosaurio))->assertSessionHasErrors('dinosaur');
        $this->post(route('shop.redeem', $dinosaurio))->assertSessionHasNoErrors();
        $this->assertTrue($user->dinosaurs()->whereKey($dinosaurio->id)->exists());
        $this->assertTrue($dinosaurio->owners()->whereKey($user->id)->exists());
        $this->assertSame(0, (int) $user->refresh()->points);
        $this->get(route('shop.collection'))->assertOk()->assertSeeText($dinosaurio->name);
        $this->post(route('shop.equip', $dinosaurio))->assertSessionHasNoErrors();
        $this->post(route('shop.redeem', $dinosaurio))->assertSessionHasErrors('dinosaur');
        $this->assertSame(1, $user->dinosaurs()->count());
        $this->assertSame(0, (int) $user->refresh()->points);
    }

    private function usuario(): User
    {
        return User::create([
            'name' => 'Cuenta de prueba',
            'email' => 'defensa@dinomaps.test',
            'password' => 'contrasena-de-prueba',
        ]);
    }
}
