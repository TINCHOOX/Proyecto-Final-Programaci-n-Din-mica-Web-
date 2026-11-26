<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Dinosaur extends Model
{
    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    protected $fillable = [
        'slug',
        'name',
        'price',
        'image_path',
        'is_starter',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_starter' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
