<?php

namespace App\Http\Requests;

use App\Enums\ActividadTurismo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiltrosCatalogoRequest extends FormRequest
{
    protected $redirectRoute = 'inicio';

    public function rules(): array
    {
        return [
            'categoria' => ['nullable', 'string', 'max:40', 'exists:categorias,slug'],
            'actividad' => ['exclude_unless:categoria,turismo', 'nullable', 'string', Rule::enum(ActividadTurismo::class)],
            'q' => ['nullable', 'string', 'max:100'],
        ];
    }
}
