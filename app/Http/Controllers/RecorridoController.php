<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use App\Models\TrafficReport;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecorridoController
{
    public function show(Request $request, Lugar $lugar): JsonResponse
    {
        $origen = $request->validate([
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
            'modo' => ['sometimes', 'in:drive,walk,bicycle'],
        ]);

        abort_if($lugar->latitud === null || $lugar->longitud === null, 422, 'Este lugar todavía no tiene una ubicación verificada.');
        $key = config('services.geoapify.key');
        abort_if(! $key, 503, 'El trazado dentro de DinoMaps requiere configurar Geoapify.');

        try {
            $respuesta = Http::acceptJson()->timeout(8)->get('https://api.geoapify.com/v1/routing', [
                'waypoints' => sprintf('%F,%F|%F,%F', $origen['latitud'], $origen['longitud'], $lugar->latitud, $lugar->longitud),
                'mode' => $origen['modo'] ?? 'drive',
                'format' => 'geojson',
                'apiKey' => $key,
            ]);
        } catch (ConnectionException) {
            abort(503, 'No pudimos contactar al servicio de recorridos.');
        }

        abort_if($respuesta->failed(), 503, 'El servicio de recorridos no pudo calcular esta ruta.');
        $resultado = $respuesta->json('features.0');
        $geometria = $resultado['geometry'] ?? [];
        abort_unless(($geometria['type'] ?? null) === 'MultiLineString', 422, 'No encontramos un recorrido para este destino.');

        return response()->json([
            'geometria' => $geometria,
            'metros' => $resultado['properties']['distance'] ?? null,
            'segundos' => $resultado['properties']['time'] ?? null,
            'avisos' => $this->avisosCercanos($geometria['coordinates'] ?? []),
        ], 200, ['Cache-Control' => 'private, no-store']);
    }

    private function avisosCercanos(array $lineas): array
    {
        $puntos = array_merge([], ...$lineas);
        if (count($puntos) < 2) {
            return [];
        }

        $latitudes = array_column($puntos, 1);
        $longitudes = array_column($puntos, 0);

        return TrafficReport::query()->where('expires_at', '>', now())
            ->whereBetween('latitude', [min($latitudes) - .002, max($latitudes) + .002])
            ->whereBetween('longitude', [min($longitudes) - .002, max($longitudes) + .002])
            ->latest()->limit(100)->get()
            ->filter(fn (TrafficReport $aviso) => $this->distanciaMinima($aviso, $lineas) <= 80)
            ->map(fn (TrafficReport $aviso) => [
                'type' => $aviso->type, 'description' => $aviso->description,
                'latitude' => $aviso->latitude, 'longitude' => $aviso->longitude,
            ])->values()->all();
    }

    private function distanciaMinima(TrafficReport $aviso, array $lineas): float
    {
        $minima = INF;
        $escalaLongitud = 111_320 * cos(deg2rad($aviso->latitude));

        foreach ($lineas as $linea) {
            for ($i = 1, $total = count($linea); $i < $total; $i++) {
                [$lonA, $latA] = $linea[$i - 1];
                [$lonB, $latB] = $linea[$i];
                $x = ($lonA - $aviso->longitude) * $escalaLongitud;
                $y = ($latA - $aviso->latitude) * 111_320;
                $dx = ($lonB - $lonA) * $escalaLongitud;
                $dy = ($latB - $latA) * 111_320;
                $largo = $dx ** 2 + $dy ** 2;
                $t = $largo > 0 ? max(0, min(1, -($x * $dx + $y * $dy) / $largo)) : 0;
                $minima = min($minima, hypot($x + $t * $dx, $y + $t * $dy));
            }
        }

        return $minima;
    }
}
