<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_balance', 'receiver_balance', 'amount', 'record_id','sender_wallet','receiver_wallet'
    ];


    public function record()
    {
        return $this->belongsTo(Record::class);
    }
}
