<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarLugarRequest;
use App\Models\Categoria;
use App\Models\Lugar;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminLugarController
{
    public function index(): View
    {
        $lugares = Lugar::with('categoria')->orderBy('nombre')->orderBy('id')->paginate(20);

        return view('admin.lugares.index', compact('lugares'));
    }

    public function create(): View
    {
        return $this->formulario(new Lugar);
    }

    public function store(GuardarLugarRequest $request): RedirectResponse
    {
        Lugar::create($request->validated());

        return to_route('admin.lugares.index')->with('status', 'Lugar agregado al catálogo.');
    }

    public function edit(Lugar $lugar): View
    {
        return $this->formulario($lugar);
    }

    public function update(GuardarLugarRequest $request, Lugar $lugar): RedirectResponse
    {
        $datos = $request->validated();
        $datos['actividad'] ??= null;
        if ($lugar->actividad?->value !== $datos['actividad']) {
            $datos['indicadores'] = null;
        }

        $lugar->update($datos);

        return to_route('admin.lugares.index')->with('status', 'Lugar actualizado.');
    }

    public function destroy(Lugar $lugar): RedirectResponse
    {
        $lugar->delete();

        return to_route('admin.lugares.index')->with('status', 'Lugar archivado y retirado del catálogo.');
    }

    private function formulario(Lugar $lugar): View
    {
        $categorias = Categoria::orderBy('orden')->get();

        return view('admin.lugares.formulario', compact('lugar', 'categorias'));
    }
}
