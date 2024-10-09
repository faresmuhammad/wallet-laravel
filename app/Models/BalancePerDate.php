<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalancePerDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'value', 'date','wallet_id'
    ];

    protected $table = 'balance_per_dates';
    public $timestamps = false;
}
