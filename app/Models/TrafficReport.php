<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrafficReport extends Model
{
    use MassPrunable;

    public const TYPES = ['concurrencia', 'obras', 'radares', 'accidentes', 'corte'];

    public const LABELS = [
        'concurrencia' => 'Alta concurrencia',
        'obras' => 'Calles en obras',
        'radares' => 'Radares',
        'accidentes' => 'Accidentes',
        'corte' => 'Corte de calle',
    ];

    public const LIFETIME_HOURS = 36;

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'latitude',
        'longitude',
        'end_latitude',
        'end_longitude',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
            'end_latitude' => 'float',
            'end_longitude' => 'float',
            'expires_at' => 'immutable_datetime',
        ];
    }

    public function votes(): HasMany
    {
        return $this->hasMany(TrafficReportVote::class);
    }

    public function prunable(): Builder
    {
        return static::query()->where('expires_at', '<=', now());
    }
}
