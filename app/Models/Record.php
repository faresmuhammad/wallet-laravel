<?php

namespace App\Models;

use App\Enums\RecordType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Record extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime',
        'type' => RecordType::class
    ];





    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function transfer(): HasOne
    {
        return $this->hasOne(Transfer::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
