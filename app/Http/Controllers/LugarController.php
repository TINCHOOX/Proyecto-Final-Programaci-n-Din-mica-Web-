<?php

namespace App\Http\Controllers;

use App\Http\Requests\FiltrosCatalogoRequest;
use App\Models\Categoria;
use App\Models\Lugar;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LugarController
{
    public function index(FiltrosCatalogoRequest $request): View
    {
        $filtros = $request->validated();
        $categorias = Categoria::orderBy('orden')->get();
        $categoria = $categorias->firstWhere('slug', $filtros['categoria'] ?? null);
        $lugares = Lugar::with('categoria')->withCount('reviews')->withAvg('reviews', 'rating')
            ->filtrar($categoria?->slug, $filtros['q'] ?? '', $filtros['actividad'] ?? null)
            ->orderBy('nombre')->paginate(24)->withQueryString();

        return view('lugares.index', compact('categorias', 'categoria', 'filtros', 'lugares'));
    }

    public function show(Request $request, Lugar $lugar): View
    {
        $lugar->load('categoria');
        $viewer = $request->user();
        $consulta = Review::query()->whereBelongsTo($lugar, 'lugar');
        $conteos = (clone $consulta)->selectRaw('rating, count(*) AS cantidad')
            ->groupBy('rating')->pluck('cantidad', 'rating');

        $distribucion = array_fill_keys(range(1, 5), 0);
        foreach ($conteos as $estrellas => $cantidad) {
            $distribucion[$estrellas] = (int) $cantidad;
        }

        $totalResenas = array_sum($distribucion);
        $promedio = $totalResenas ? array_sum(array_map(
            fn (int $estrellas, int $cantidad): int => $estrellas * $cantidad,
            array_keys($distribucion),
            $distribucion,
        )) / $totalResenas : 0;

        $yaReseno = $viewer && (clone $consulta)->where('user_id', $viewer->id)->exists();
        if ($viewer) {
            $consulta->withExists(['votes as viewer_voted' => fn ($votos) => $votos->where('user_id', $viewer->id)]);
        }
        $reviews = $consulta->with('user:id,name')->withCount('votes')->latest()->paginate(12);

        return view('lugares.show', compact(
            'lugar', 'reviews', 'viewer', 'yaReseno', 'distribucion', 'totalResenas', 'promedio',
        ));
    }
}
