<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalancePerDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'value', 'date','wallet_id','balance_id'
    ];

    protected $table = 'balances_per_date';
    public $timestamps = false;
}
