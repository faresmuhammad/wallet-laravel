<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'name',
        'pfx_symbol',
        'sfx_symbol',
        'unit_name',
        'cent_name',
        'scale',
        'symbol_name',
    ];



    public function records()
    {
        return $this->hasMany(Record::class);

    }
}
