<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prize extends Model
{
    private const REDUCED_ODDS_REGULAR_WEIGHT = 10;

    protected $fillable = ['name', 'stock', 'is_grand', 'sort_order'];

    protected $casts = [
        'stock'      => 'integer',
        'is_grand'   => 'boolean',
        'sort_order' => 'integer',
    ];

    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    /** Hadiah yang tampil di roda: stok > 0, urut sesuai sort_order. */
    public static function forWheel(): Collection
    {
        return static::query()
            ->where('stock', '>', 0)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /** Bentuk data yang dikirim ke JavaScript. */
    public function toWheel(): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'stock'    => (int) $this->stock,
            'is_grand' => (bool) $this->is_grand,
        ];
    }

    public function drawWeight(bool $reduceGrandPrizeOdds): int
    {
        if ($reduceGrandPrizeOdds && $this->is_grand) {
            return 1;
        }

        return $reduceGrandPrizeOdds ? self::REDUCED_ODDS_REGULAR_WEIGHT : 1;
    }

    public static function areGrandPrizeOddsReduced(): bool
    {
        return now('Asia/Jakarta')->lt(
            CarbonImmutable::parse(config('wheel.grand_prize_reduction_until'))
        );
    }
}
