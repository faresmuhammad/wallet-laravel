<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function records(): BelongsToMany
    {
        return $this->belongsToMany(Record::class, 'label_record_pivot', 'label_id', 'record_id');
    }


}
