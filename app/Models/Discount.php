<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'type',
        'amount',
        'is_active',
        'is_automatic',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'is_automatic' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('applied_at')
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $inner) use ($now): void {
                $inner->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $inner) use ($now): void {
                $inner->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }
}
