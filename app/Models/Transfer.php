<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount', 'record_id','sender_wallet','receiver_wallet'
    ];


    public function record(): BelongsTo
    {
        return $this->belongsTo(Record::class);
    }
}
