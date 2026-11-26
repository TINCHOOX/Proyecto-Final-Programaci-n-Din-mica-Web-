<?php

namespace App\Models;

use App\Enums\ActividadTurismo;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lugar extends Model
{
    use SoftDeletes;

    protected $table = 'lugares';

    protected $fillable = [
        'categoria_id', 'slug', 'nombre', 'direccion', 'tipo',
        'latitud', 'longitud', 'fuente', 'fuente_coordenadas',
        'actividad', 'descripcion', 'indicadores',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'float',
            'longitud' => 'float',
            'actividad' => ActividadTurismo::class,
            'indicadores' => 'array',
        ];
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    #[Scope]
    protected function filtrar(Builder $query, ?string $categoria, string $busqueda, ?string $actividad = null): void
    {
        if ($categoria !== null) {
            $query->whereRelation('categoria', 'slug', $categoria);
        }

        if ($categoria === 'turismo' && $actividad !== null) {
            $query->where('actividad', $actividad);
        }

        if ($busqueda !== '') {
            $query->where(function (Builder $query) use ($busqueda): void {
                $patron = '%'.$busqueda.'%';
                $query->whereLike('nombre', $patron)
                    ->orWhereLike('direccion', $patron)
                    ->orWhereLike('tipo', $patron);
            });
        }
    }
}
