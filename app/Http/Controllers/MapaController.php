<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltrosCatalogoRequest;
use App\Models\Categoria;
use App\Models\Lugar;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MapaController
{
    public function index(FiltrosCatalogoRequest $request): View
    {
        $filtros = $request->validated();

        return view('inicio', [
            'categorias' => Categoria::orderBy('orden')->get(),
            'esTransito' => false,
            'filtros' => $filtros,
            'lugares' => Lugar::with('categoria')->withCount('reviews')->withAvg('reviews', 'rating')
                ->filtrar($filtros['categoria'] ?? null, $filtros['q'] ?? '', $filtros['actividad'] ?? null)
                ->orderBy('id')->paginate(30)->withQueryString(),
        ]);
    }

    public function transito(): View
    {
        return view('inicio', [
            'categorias' => Categoria::orderBy('orden')->get(),
            'filtros' => ['categoria' => 'transporte'],
            'esTransito' => true,
            'tiposAviso' => [
                'concurrencia' => 'Alta concurrencia',
                'obras' => 'Calles en obras',
                'radares' => 'Radares',
                'accidentes' => 'Accidentes',
            ],
        ]);
    }

    public function recorrido(Lugar $lugar): View
    {
        abort_unless($lugar->latitud !== null && $lugar->longitud !== null, 404);

        return view('inicio', [
            'categorias' => Categoria::orderBy('orden')->get(),
            'filtros' => ['categoria' => $lugar->categoria->slug],
            'esTransito' => false,
            'destino' => $lugar,
        ]);
    }

    public function puntos(FiltrosCatalogoRequest $request): JsonResponse
    {
        $filtros = $request->validated();
        $puntos = Lugar::filtrar($filtros['categoria'] ?? null, $filtros['q'] ?? '', $filtros['actividad'] ?? null)
            ->whereNotNull(['latitud', 'longitud'])
            ->orderBy('id')
            ->limit(300)
            ->with('categoria:id,slug,nombre')
            ->get(['id', 'slug', 'nombre', 'latitud', 'longitud', 'categoria_id'])
            ->map(fn (Lugar $lugar) => [
                'nombre' => $lugar->nombre,
                'slug' => $lugar->slug,
                'latitud' => $lugar->latitud,
                'longitud' => $lugar->longitud,
                'ficha' => route('lugares.show', $lugar),
                'categoria' => $lugar->categoria->slug,
                'categoria_nombre' => $lugar->categoria->nombre,
            ]);

        return response()->json($puntos);
    }
}
