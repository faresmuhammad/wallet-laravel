<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StrategyRule extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'rules';

    public function strategy(): BelongsTo
    {
        return $this->belongsTo(Strategy::class);
    }
}
