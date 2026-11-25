<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Winner extends Model
{
    protected $fillable = ['prize_id', 'prize_name', 'is_grand'];

    protected $casts = ['is_grand' => 'boolean'];

    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }
}
