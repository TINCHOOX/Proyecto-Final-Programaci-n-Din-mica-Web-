<?php

namespace App\Http\Requests;

use App\Enums\ActividadTurismo;
use App\Models\Categoria;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GuardarLugarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('gestionar-catalogo') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->route('lugar') && !$this->filled('slug') && is_string($this->input('nombre'))) {
            $this->merge(['slug' => Str::slug($this->input('nombre'))]);
        }
    }

    public function rules(): array
    {
        $categoria = filter_var($this->input('categoria_id'), FILTER_VALIDATE_INT);
        $esTurismo = $categoria !== false && Categoria::whereKey($categoria)->where('slug', 'turismo')->exists();

        return [
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'slug' => [Rule::excludeIf($this->route('lugar') !== null), 'required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:lugares,slug'],
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', 'max:255'],
            'fuente' => ['required', 'url:http,https', 'max:2048'],
            'latitud' => ['required_with:longitud', 'nullable', 'numeric', 'between:-90,90', 'decimal:0,6'],
            'longitud' => ['required_with:latitud', 'nullable', 'numeric', 'between:-180,180', 'decimal:0,6'],
            'fuente_coordenadas' => ['required_with:latitud,longitud', 'nullable', 'url:http,https', 'max:2048'],
            'actividad' => [Rule::excludeIf(!$esTurismo), 'nullable', Rule::enum(ActividadTurismo::class)],
            'descripcion' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
