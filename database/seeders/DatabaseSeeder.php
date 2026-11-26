<?php

namespace Database\Seeders;

use App\Enums\ActividadTurismo;
use App\Models\Categoria;
use App\Models\Lugar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DinosaurSeeder::class);

        DB::transaction(function (): void {
            $categorias = [
                'farmacias' => 'Farmacias',
                'museos' => 'Museos',
                'hoteles' => 'Hoteles',
                'cajeros' => 'Cajeros',
                'turismo' => 'Turismo',
                'transporte' => 'Transporte',
                'restaurantes' => 'Restaurantes',
            ];

            $ids = [];
            foreach ($categorias as $slug => $nombre) {
                $ids[$slug] = Categoria::updateOrCreate(
                    ['slug' => $slug],
                    ['nombre' => $nombre, 'orden' => count($ids) + 1],
                )->id;
            }

            $lugares = [
                ['restaurantes', 'el-ciervo', 'El Ciervo', 'Av. Argentina 219', 'Restaurante y confitería', 'https://elciervo.com.ar/', -38.95267, -68.05882, 'https://mapcarta.com/es/W1278651732'],
                ['hoteles', 'hotel-casino-magic', 'Hotel Casino Magic', 'Teodoro Luis Planas 4005', 'Hotel con spa', 'https://hotel.casinomagic.com.ar/es/contacto.html', -38.95785, -68.11492, 'https://mapcarta.com/W186661960'],
                ['turismo', 'parque-jaime-de-nevares', 'Parque Jaime de Nevares', 'Alderete y Entre Ríos', 'Parque urbano', 'https://infoleg.neuquen.gov.ar/Boletines/bol2412304381E%2030%20dic%20%283%29.pdf'],
                ['farmacias', 'farmacia-del-pueblo-mitre', 'Farmacia del Pueblo · Mitre', 'Mitre 73', 'Farmacia', 'https://www.farmaciadelpueblo.com.ar/farmacias-de-turno'],
                ['museos', 'mnba-neuquen', 'Museo Nacional de Bellas Artes', 'Mitre y Santa Cruz', 'Museo de arte', 'https://www.yvera.gob.ar/calidad/distinguidos?p=87', -38.956589, -68.053733, 'https://es.wikipedia.org/wiki/MNBA_Neuqu%C3%A9n'],
                ['cajeros', 'cajero-bpn-casa-matriz', 'Cajero BPN · Casa Matriz', 'Av. Argentina 41', 'Cajero automático', 'https://www.bpn.com.ar/Extras/BancaNoVidenteCajeros.html'],
                ['transporte', 'terminal-eton', 'Terminal de Ómnibus · ETON', 'Planas y Solalique', 'Terminal de ómnibus', 'https://www.argentina.gob.ar/transporte/cnrt/delegaciones', -38.95747, -68.10534, 'https://mapcarta.com/W183499193'],
                ['farmacias', 'farmacias-global-islas-malvinas', 'Farmacias Global · Islas Malvinas', 'Islas Malvinas, Neuquén', 'Farmacia', 'https://mapcarta.com/es/N1374389776', -38.94745, -68.04048, 'https://mapcarta.com/es/N1374389776'],
                ['cajeros', 'cajero-bpn-mas', 'Cajeros · BPN Más', 'Rivadavia 45', 'Cajero automático', 'https://apiapp.bpn.com.ar/Resources/Files/c29edad546c846caa9aaac5e176070dc.pdf', -38.95423, -68.05838, 'https://mapcarta.com/es/W1279777929'],
                ['turismo', 'plazoleta-junta-estudios-historicos', 'Plazoleta Junta de Estudios Históricos', 'Área de Casa de las Leyes', 'Parque urbano', 'https://mapcarta.com/es/W1081002353', -38.96201, -68.05925, 'https://mapcarta.com/es/W1081002353'],
                ['farmacias', 'farmacias-global-sarmiento', 'Farmacias Global · Sarmiento', 'Domingo Faustino Sarmiento 346', 'Farmacia', 'https://mapcarta.com/es/N6831864830', -38.95721, -68.06424, 'https://mapcarta.com/es/N6831864830'],
                ['farmacias', 'farmacias-global-jose-rosa', 'Farmacias Global · José Rosa', 'José Rosa 993', 'Farmacia', 'https://mapcarta.com/es/N10231747106', -38.96507, -68.04968, 'https://mapcarta.com/es/N10231747106'],
                ['museos', 'museo-gregorio-alvarez', 'Museo Gregorio Álvarez', 'Parque Central', 'Museo', 'https://mapcarta.com/N10906856662', -38.95574, -68.0623, 'https://mapcarta.com/N10906856662'],
                ['museos', 'museo-paraje-confluencia', 'Museo Paraje Confluencia', 'Independencia 286', 'Museo de historia local', 'https://mapcarta.com/W1163516430', -38.95528, -68.05498, 'https://mapcarta.com/W1163516430'],
                ['hoteles', 'hilton-garden-inn-neuquen', 'Hilton Garden Inn Neuquén', 'Av. Argentina 1200–1248', 'Hotel', 'https://www.hilton.com/es/hotels/nqngigi-hilton-garden-inn-neuquen/hotel-location/', -38.94183, -68.05969, 'https://mapcarta.com/es/W1172617884'],
                ['hoteles', 'neuquen-tower-hotel', 'Neuquén Tower Hotel', 'Belgrano 174', 'Hotel', 'https://www.neuquentowerhotel.com.ar/', -38.95095, -68.06158, 'https://mapcarta.com/es/32839870'],
                ['cajeros', 'cajero-bpn-eton', 'Cajero BPN · ETON', 'Teodoro Luis Planas y Solalique', 'Cajero automático en la terminal', 'https://www.bpn.com.ar/Extras/BancaNoVidenteCajeros.html', -38.95747, -68.10534, 'https://mapcarta.com/W183499193'],
                ['transporte', 'estacion-neuquen', 'Estación Neuquén', 'Vuelta de Obligado 55', 'Estación ferroviaria', 'https://es.wikipedia.org/wiki/Estaci%C3%B3n_Neuqu%C3%A9n', -38.955861, -68.058278, 'https://es.wikipedia.org/wiki/Estaci%C3%B3n_Neuqu%C3%A9n'],
                ['transporte', 'aeropuerto-presidente-peron', 'Aeropuerto Internacional Presidente Perón', 'San Martín 5901', 'Terminal aeroportuaria', 'https://www.argentina.gob.ar/sites/default/files/2023/05/iet-nqn_2013.pdf', -38.95211, -68.1402, 'https://mapcarta.com/es/W143946526'],
                ['restaurantes', 'estacion-q', 'Estación Q', 'Juan Bautista Alberdi 297', 'Restaurante', 'https://mapcarta.com/es/W1224670532', -38.95306, -68.05509, 'https://mapcarta.com/es/W1224670532'],
                ['restaurantes', 'la-nonna-francesca', 'La Nonna Francesca', 'Diagonal 9 de Julio 56', 'Restaurante', 'https://mapcarta.com/es/W1108102190', -38.95039, -68.06018, 'https://mapcarta.com/es/W1108102190'],
                ['turismo', 'circuito-blanco-bardas-norte', 'Circuito Blanco Bardas Norte', 'Parque del Observatorio sobre Jesús María', 'Circuito de running', 'https://www.rionegro.com.ar/estar-bien/parque-norte-y-el-circuito-perfecto-de-running-en-las-bardas-de-neuquen-4238013/', -38.93552, -68.06363, 'https://mapcarta.com/es/W738098888',
                    ActividadTurismo::Running, ['distancia' => '7,6 km', 'dificultad' => 'Moderada'], 'El marcador ubica el Observatorio como referencia. El circuito comienza en el estacionamiento del parque.'],
                ['turismo', 'senderismo-paseo-de-la-costa', 'Paseo de la Costa', 'Costanera del río Limay, Neuquén', 'Senderismo urbano', 'https://www.lmneuquen.com/neuquen/neuquen-capital-nacional-del-senderismo-urbano-una-ciudad-disfrutar-ecosistemas-unicos-n1210745/amp', -38.97564, -68.05103, 'https://mapcarta.com/N4173747589',
                    ActividadTurismo::Trekking, null, 'El punto ubica el sector recreativo de la costa. El paseo tiene varios accesos y tramos.'],
            ];

            foreach ($lugares as $datos) {
                [$categoria, $slug, $nombre, $direccion, $tipo, $fuente] = $datos;

                $lugar = Lugar::withTrashed()->firstOrNew(['slug' => $slug]);
                if ($lugar->exists) {
                    continue;
                }

                if ($categoria === 'turismo') {
                    $lugar->actividad = $datos[9] ?? ActividadTurismo::Paseo;
                }

                $lugar->fill([
                    'categoria_id' => $ids[$categoria],
                    'nombre' => $nombre,
                    'direccion' => $direccion,
                    'tipo' => $tipo,
                    'fuente' => $fuente,
                    'latitud' => $datos[6] ?? null,
                    'longitud' => $datos[7] ?? null,
                    'fuente_coordenadas' => $datos[8] ?? null,
                    'indicadores' => $datos[10] ?? null,
                    'descripcion' => $datos[11] ?? null,
                ])->save();
            }
        });
    }
}
