<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'description',
        'type',
        'balance_before',
        'balance_after',
        'balance_id',
        'category_id',
        'wallet_id',
        'currency_id',
        'date'
    ];

    protected $casts =[
      'date'=>'date'
    ];

    public $timestamps = false;

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function balance()
    {
        return $this->belongsTo(Balance::class);
    }

    public function transfer()
    {
        return $this->hasOne(Transfer::class);
    }
}
