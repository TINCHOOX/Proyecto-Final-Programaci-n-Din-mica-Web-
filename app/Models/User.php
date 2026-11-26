<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public const COLOR_MODES = [
        'estandar' => 'Estándar',
        'protanopia' => 'Protanopía',
        'deuteranopia' => 'Deuteranopía',
        'tritanopia' => 'Tritanopía',
    ];

    public function equippedDinosaur(): BelongsTo
    {
        return $this->belongsTo(Dinosaur::class, 'equipped_dinosaur_id');
    }

    public function dinosaurs(): BelongsToMany
    {
        return $this->belongsToMany(Dinosaur::class);
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $attributes = ['is_admin' => false];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
}
